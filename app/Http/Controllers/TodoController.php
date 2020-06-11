<?php

namespace App\Http\Controllers;

use App\Helpers\ActivityEnum;
use App\Helpers\ActivityLogHelper;
use App\Helpers\AutomatedTagsHelper;
use App\Helpers\CommonHelper;
use App\Students;
use App\Todo;
use App\TodoAccess;
use App\TodoFile;
use App\TodoTask;
use App\TodoTaskNote;
use App\TodoTaskStatus;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TodoController extends Controller
{
    public function index(Request $request)
    {
        $todos = Todo::all();

        return view('todo.list', array(
            'todos' => $todos,
        ));
    }

    public function create()
    {
        return view('todo.form', [
            'date' => Carbon::now(CommonHelper::getSchoolTimezone())->format('Y-m-d'),
            'tasks' => json_encode([]),
            'users' => User::all(),
            'students' => Students::all(),
            'access_users' => [],
            'access_students' => []
        ]);
    }

    public function store(Request $request)
    {
        $todo = new Todo();
        $todo = $this->setData($todo, $request);

        ActivityLogHelper::create(
            ActivityEnum::TODO_CREATED,
            CommonHelper::getMainLoggedInUserId(),
            ActivityLogHelper::getTodoCreatedParams($todo)
        );

        return redirect(route('todo.index'))->with('success', __('messages.todo-created-successfully'));
    }

    private function setData($todo, $request)
    {
        $todo->title = $request->title;
        $todo->due_days = $request->due_days;
        $todo->start_alert_before_days = $request->start_alert_before_days ? $request->start_alert_before_days : NULL;
        $todo->save();

        $old_student_ids = $todo->todoAccess()->forStudents()->pluck('student_id')->toArray();

        // sync tasks
        $todo_tasks = $request->todo_task_id ? $request->todo_task_id : [];
        $todo->todoTasks()->whereNotIn('id',array_filter($todo_tasks))->delete();
        foreach($todo_tasks as $key => $todo_task_id)
        {
            $todoTask = $todo_task_id ? TodoTask::find($todo_task_id) : new TodoTask();
            $todoTask->task = $request->todo_task[$key];
            $todoTask->todo_id = $todo->id;
            $todoTask->position = 0;
            $todoTask->save();
        }

        // update duedate for existing access.
        $school_timezone_diff = Carbon::now(CommonHelper::getSchoolTimezone())->format('P');
        $todo->todoAccess()->update([
            'due_date' => DB::raw("DATE_ADD(DATE(CONVERT_TZ(created_at,'+00:00','$school_timezone_diff')), INTERVAL $todo->due_days DAY)")
        ]);

        $due_date = Carbon::now(CommonHelper::getSchoolTimezone())->addDays($todo->due_days)->format('Y-m-d');

        // sync access for users
        $todo_users = $request->user_id ? $request->user_id : [];
        $todo->todoAccess()->forUsers()->whereNotIn('user_id',array_filter($todo_users))->delete();
        foreach($todo_users as $key => $user_id)
        {
            $exists = $todo->todoAccess()->forUsers()->where('user_id',$user_id)->exists();
            if(!$exists)
            {
                $todoAccess = new TodoAccess();
                $todoAccess->todo_id = $todo->id;
                $todoAccess->user_id = $user_id;
                $todoAccess->due_date = $due_date;
                $todoAccess->save();
            }
        }

        // sync access for students
        $todo_access_students = $request->student_id ? $request->student_id : [];
        $todo->todoAccess()->forStudents()->whereNotIn('student_id',array_filter($todo_access_students))->delete();
        foreach($todo_access_students as $key => $student_id)
        {
            $exists = $todo->todoAccess()->forStudents()->where('student_id',$student_id)->exists();
            if(!$exists)
            {
                $todoAccess = new TodoAccess();
                $todoAccess->todo_id = $todo->id;
                $todoAccess->student_id = $student_id;
                $todoAccess->due_date = $due_date;
                $todoAccess->save();
            }
        }

        // sync files
        $old_files = $request->old_todo_ids ? $request->old_todo_ids : [];
        $files_to_be_removed = $todo->todoFiles()->whereNotIn('id',$old_files)->get();
        foreach($files_to_be_removed as $file)
        {
            @Storage::disk('public')->delete($file->file_path);
            $file->delete();
        }
        $files = $request->todo_files ? $request->todo_files : [];
        foreach($files as $file)
        {
            $new_file_name = time().'-'.rand(111,999).'.'.pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);
            $file_path = Storage::disk('public')->putFileAs('todo_files', $file, $new_file_name);

            $todoFile = new TodoFile();
            $todoFile->todo_id = $todo->id;
            $todoFile->file_path = $file_path;
            $todoFile->name = $file->getClientOriginalName();;
            $todoFile->save();
        }

        // Update due todo tag.
        $new_student_ids = $todo->todoAccess()->forStudents()->pluck('student_id')->toArray();
        $all_student_ids = array_merge($old_student_ids, $new_student_ids);
        $students = Students::whereIn('id', $all_student_ids)->get();
        foreach($students as $student)
        {
            $automatedTagsHelper = new AutomatedTagsHelper($student);
            $automatedTagsHelper->refreshDueTodoTag();
        }

        return $todo;
    }

    public function edit($id)
    {
        $todo = Todo::find($id);

        return view('todo.form', array(
            'date' => Carbon::now(CommonHelper::getSchoolTimezone())->format('Y-m-d'),
            'todo' => $todo,
            'tasks' => json_encode($todo->todoTasks->toArray()),
            'users' => User::all(),
            'students' => Students::all(),
            'access_users' => $todo->todoAccess()->forUsers()->pluck('user_id')->toArray(),
            'access_students' => $todo->todoAccess()->forStudents()->pluck('student_id')->toArray()
        ));
    }

    public function update(Request $request, $id)
    {
        $todo = Todo::find($id);
        $this->setData($todo, $request);
        return redirect(route('todo.index'))->with('success', __('messages.todo-updated-successfully'));
    }

    public function destroy($id)
    {
        $todo = Todo::find($id);

        $files_to_be_removed = $todo->todoFiles()->get();
        foreach($files_to_be_removed as $file)
        {
            @Storage::disk('public')->delete($file->file_path);
        }

        $students = Students::whereHas('todoAccess', function($query) use($todo){
            $query->where('todo_id', $todo->id);
        })->get();

        $todo->delete();

        // Update Student Tags
        foreach($students as $student)
        {
            $automatedTagsHelper = new AutomatedTagsHelper($student);
            $automatedTagsHelper->refreshDueTodoTag();
        }

        return redirect(route('todo.index'))->with('success',  __('messages.todo-deleted-successfully'));
    }

    // keeps track of either particular task is done or not.
    public function update_task_status(Request $request)
    {
        $out['status'] = 1;
        $out['message'] = '';

        $todoTaskStatus = TodoTaskStatus::where('todo_access_id',$request->todo_access_id)
                                    ->where('todo_task_id', $request->todo_task_id)
                                    ->first();
        if(!$todoTaskStatus)
        {
            $todoTaskStatus = New TodoTaskStatus();
            $todoTaskStatus->todo_access_id = $request->todo_access_id;
            $todoTaskStatus->todo_task_id = $request->todo_task_id;
        }

        if($request->is_done == 1)
        {
            if($todoTaskStatus->status == 1)
            {
                $out['status'] = 0;
                $out['message'] = __('messages.task-is-already-completed');
            }
            else
            {
                $todoTaskStatus->status = 1;
                $todoTaskStatus->updated_by = \Auth::user()->id;
                $todoTaskStatus->save();
            }

            if($todoTaskStatus->todoAccess->is_completed)
            {
                ActivityLogHelper::create(
                    ActivityEnum::TODO_COMPLETED,
                    CommonHelper::getMainLoggedInUserId(),
                    ActivityLogHelper::getTodoCompletedParams($todoTaskStatus->todoAccess)
                );
            }
        }
        else
        {
            if($todoTaskStatus->status == 0)
            {
                $out['status'] = 0;
                $out['message'] = __('messages.task-is-already-incompleted');
            }
            else
            {
                $todoTaskStatus->status = 0;
                $todoTaskStatus->updated_by = \Auth::user()->id;
                $todoTaskStatus->save();
            }
        }

        $student = Students::whereHas('todoAccess', function($query) use($request){
                                $query->where('id', $request->todo_access_id);
                            })->first();
        if($student)
        {
            $automatedTagsHelper = new AutomatedTagsHelper($student);
            $automatedTagsHelper->refreshDueTodoTag();
        }

        return $out;
    }

    public function update_task_note(Request $request)
    {
        $out['status'] = 1;
        $out['message'] = '';

        $todoTaskNote = TodoTaskNote::where('todo_access_id',$request->todo_access_id)
                                    ->where('todo_task_id', $request->todo_task_id)
                                    ->first();
        if(!$todoTaskNote)
        {
            $todoTaskNote = New TodoTaskNote();
            $todoTaskNote->todo_access_id = $request->todo_access_id;
            $todoTaskNote->todo_task_id = $request->todo_task_id;
        }

        $todoTaskNote->note_text = $request->note_text;
        $todoTaskNote->updated_by = \Auth::user()->id;
        $todoTaskNote->save();

        return $out;
    }

    public function update_duedate(Request $request)
    {
        $todoAccess = TodoAccess::find($request->todo_access_id);
        if($request->due_date < $todoAccess->getLocalCreatedAt()->format('Y-m-d'))
        {
            $out['status'] = 0;
            $out['message'] =  __('messages.duedate-cant-be-before-assigned-date');
            return $out;
        }

        $todoAccess->custom_due_date = $request->due_date;
        $todoAccess->save();

        $student = $todoAccess->student;
        if($student)
        {
            $automatedTagsHelper = new AutomatedTagsHelper($student);
            $automatedTagsHelper->refreshDueTodoTag();
        }

        $out['status'] = 1;
        return $out;
    }

    public function mytodos()
    {
        $todoAccessList = TodoAccess::where('user_id',\Auth::user()->id)
                                ->orderByRaw("IFNULL(custom_due_date,due_date) ASC")->get();

        return view('todo.mytodos', array(
            'todoAccessList' => $todoAccessList,
        ));
    }

    public function details(Request $request)
    {
        $todoAccess = TodoAccess::find($request->todo_access_id);
        $display_details = $request->display_details;
        $loaded_from_page = $request->loaded_from_page;
        $date = Carbon::now(CommonHelper::getSchoolTimezone())->format('Y-m-d');

        $student_todo_alert_count = 0;
        if($todoAccess->student_id != NULL)
        {
            $student = Students::find($todoAccess->student_id);
            $student_todo_alert_count = $student->todo_alert_count($date);
        }

        $out['status'] = 1;
        $out['html'] = view('todo.todo-section', compact('todoAccess','display_details','loaded_from_page'))->render();
        $out['my_todo_alert_count'] = \Auth::user()->my_todo_alert_count($date);
        $out['all_student_todo_alert_count'] = Students::all_student_todo_alert_count($date);
        $out['student_todo_alert_count'] = $student_todo_alert_count;
        return $out;
    }

    public function progress_details($id, Request $request)
    {
        $todo = Todo::find($id);
        $todoAccessList = TodoAccess::where('todo_id', $todo->id)->get();
        return view('todo.progress-details', compact('todo','todoAccessList'));
    }

    public function progress(Request $request)
    {
        $sql  = "SELECT
                    todo_accesses.user_id,
                    todo_accesses.student_id,
                    COUNT(*) as assigned_todos,
                    IFNULL(SUM(task_counts.no_of_tasks),0) as total_tasks,
                    IFNULL(SUM(done_task_counts.no_of_done_tasks),0) as done_tasks
                    FROM `todo_accesses`
                LEFT JOIN (
                    SELECT todo_id, COUNT(*) as no_of_tasks from todo_tasks GROUP BY todo_id
                ) as task_counts
                ON todo_accesses.todo_id = task_counts.todo_id
                LEFT JOIN (
                    SELECT todo_access_id, COUNT(*) as no_of_done_tasks from todo_task_statuses WHERE status = 1 GROUP BY todo_access_id
                ) as done_task_counts
                ON todo_accesses.id = done_task_counts.todo_access_id
                WHERE 1
                GROUP BY todo_accesses.user_id, todo_accesses.student_id";

        $records = \DB::select(DB::raw($sql));

        $final_records = [];
        foreach($records as $record){
            $temp = $record;
            $temp->name = '';

            if($record->student_id)
            {
                $temp->student = Students::find($record->student_id);
            }
            else
            {
                $temp->user = User::find($record->user_id);
            }
            $temp->assigned_todos = $record->assigned_todos;
            $temp->progress_percentage = round(( $record->done_tasks / $record->total_tasks * 100));
            $final_records[] = $temp;
        }

        return view('todo.progress', array(
            'records' => $final_records,
        ));
    }
}
