<?php

namespace App\Http\Controllers;

use App\AssessmentQuestions;
use App\AssessmentUserQuestions;
use App\AssessmentUsers;
use App\AssessmentUserTimeslot;
use App\AvailabilitySelectionCalendar;
use App\Helpers\CommonHelper;
use App\SelectionCalenderTimeSlot;
use App\Settings;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AvailabilitySelectionCalendarController extends Controller
{
    public function index()
    {
        $permissions = [
            'manage-availability-timeslots' => Auth::user()->can('manage-availability-timeslots') ? 1 : 0,
            'manage-availability-selection-calendars'  => Auth::user()->can('manage-availability-selection-calendars') ? 1 : 0,
            'view-availability-responses'  => Auth::user()->can('view-availability-responses') ? 1 : 0,
        ];
        return view('availability_selection_calendars.index', [
            'permissions' => json_encode($permissions),
        ]);
    }

    public function saveRecord(Request $request)
    {
        $request->validate([
            'name' => 'required|max:191|unique:availability_selection_calendars,name,'. $request->id
        ]);

        $availableSelectionCalendar = $request->id ? AvailabilitySelectionCalendar::findOrFail($request->id) : new AvailabilitySelectionCalendar();
        $availableSelectionCalendar->name = $request->name;
        $availableSelectionCalendar->save();

        $availableSelectionCalendar = AvailabilitySelectionCalendar::find($availableSelectionCalendar->id);
        $out['status'] = 1;
        $out['record'] = $availableSelectionCalendar;
        return $out;
    }

    public function getRecords()
    {
        return AvailabilitySelectionCalendar::get();
    }

    public function deleteRecord($id)
    {
        $availableSelectionCalendar = AvailabilitySelectionCalendar::findOrFail($id);
        $availableSelectionCalendar->delete();
        return $availableSelectionCalendar;
    }

    public function editCalendar($id)
    {
        $availableSelectionCalendar = AvailabilitySelectionCalendar::findOrFail($id);
        return view('availability_selection_calendars.edit_calendar.index', [
            'availableSelectionCalendar' => $availableSelectionCalendar
        ]);
    }

    public function getCalendarData($id)
    {
        $availableSelectionCalendar = AvailabilitySelectionCalendar::findOrFail($id);
        $out['name'] = $availableSelectionCalendar->name;
        $out['calendarSettings'] = CommonHelper::getFullcalendarCommonSettings();
        
        $events = $availableSelectionCalendar->selectionCalendarTimeSlots;
        $final_events = [];
        foreach($events as $event){
            $final_events[] = $event->toFullcalendarFormat();
        }
        $out['events'] = $final_events;
        return $out;
    }

    public function saveTimeSlot(Request $request)
    {
        $selectionCalenderTimeSlot = $request->id ? SelectionCalenderTimeSlot::findOrFail($request->id) : new SelectionCalenderTimeSlot();
        $selectionCalenderTimeSlot->calendar_id = $request->calender_id;
        $selectionCalenderTimeSlot->day_of_week = $request->day_of_week;
        $selectionCalenderTimeSlot->from = $request->from;
        $selectionCalenderTimeSlot->to = $request->to;
        $selectionCalenderTimeSlot->save();

        $out['status'] = 1;
        $out['event'] = $selectionCalenderTimeSlot->toFullcalendarFormat();
        return $out;
    }

    public function deleteTimeSlot($id)
    {
        $selectionCalenderTimeSlot = SelectionCalenderTimeSlot::findOrFail($id);
        $selectionCalenderTimeSlot->delete();

        $out['status'] = 1;
        return $out;
    }
   
    public function getTimeslotPickerData($assessment_question_id)
    {
        $assessmentQuestion = AssessmentQuestions::findOrFail($assessment_question_id);
        
        $availableSelectionCalendar = $assessmentQuestion->availabilitySelectionCalendar;
        
        $events = $availableSelectionCalendar->selectionCalendarTimeSlots;
        $final_events = [];
        foreach($events as $event){
            $final_events[] = $event->toFullcalendarFormat();
        }
        
        $out['events'] = $final_events;
        $out['calendarSettings'] = CommonHelper::getFullcalendarCommonSettings();
        return $out;
    }

    public function responses($id)
    {
        $availabilitySelectionCalendar = AvailabilitySelectionCalendar::findOrFail($id);
        return view('availability_selection_calendars.responses', [
            'availabilitySelectionCalendar' => $availabilitySelectionCalendar
        ]);
    }

    public function responseData($id)
    {
        $availabilitySelectionCalendar = AvailabilitySelectionCalendar::findOrFail($id);
        $out['calendarSettings'] = CommonHelper::getFullcalendarCommonSettings();
        $out['calendar_name'] = $availabilitySelectionCalendar->name;
        
        $assessmentUsers = AssessmentUsers::whereHas('assessmentUserTimeslots', function($query) use ($availabilitySelectionCalendar){
            $query->whereHas('assessment_question', function($query) use ($availabilitySelectionCalendar){
                $query->whereHas('availabilitySelectionCalendar', function($query) use ($availabilitySelectionCalendar){
                    $query->where('id', $availabilitySelectionCalendar->id);
                });
            });
        })->get();

        $users = [];
        $all_levels = [];
        $all_user_ids = [];
        foreach($assessmentUsers as $assessmentUser){
            $user = $assessmentUser->user;
            
            $temp = [];
            $temp['id'] = $user->id;
            $all_user_ids[] = $user->id;
            if($user->student)
            {
                $student_levels = explode(",", $user->student->levels);

                $temp['name'] = $user->student->get_kanji_name();
                $temp['levels'] = $student_levels;
                $all_levels = array_merge($all_levels, $student_levels);
            }
            else if($user->teacher)
            {
                $temp['name'] = $user->teacher->nickname;
                $temp['levels'] = [];
            }
            else
            {
                continue;
            }
            $users [] = $temp;
        }

        $all_levels = array_values(array_unique($all_levels));

        $out['filter_options']['users'] = $users;
        $out['filter_options']['levels'] = $all_levels;

        $session = session('avail_selection_response_filter_'.$availabilitySelectionCalendar->id);
        if($session)
        {
            $out['applied_filters']['users'] = isset($session['user_ids']) ? array_map('intval',$session['user_ids']) : [];
            $out['applied_filters']['levels'] = isset($session['levels']) ? $session['levels'] : [];
        }
        else
        {
            $out['applied_filters']['users'] = $all_user_ids;
            $out['applied_filters']['levels'] = $all_levels;
        }
        return $out;
    }

    public function responseEvents($id, Request $request)
    {
        $availabilitySelectionCalendar = AvailabilitySelectionCalendar::findOrFail($id);

        $events = $availabilitySelectionCalendar->selectionCalendarTimeSlots;
        $final_events = [];
        foreach($events as $event){
            $event = $event->toFullcalendarFormat();
            $users = $this->_prepareUsersArray($event['id'], $request);
            $event['title'] = count($users)." ".__('messages.users');
            $event['users'] = $users;
            $final_events[] = $event;
        }
        $out['events'] = $final_events;
        
        $session['levels'] = $request->levels;
        $session['user_ids'] = $request->user_ids;
        session()->put('avail_selection_response_filter_'.$availabilitySelectionCalendar->id, $session);
        return $out;
    }

    private function _prepareUsersArray($timeslot_id, $request)
    {
        $students = [];
        if(!$request->levels)
        {
            return $students;
        }
        if(!$request->user_ids)
        {
            return $students;
        }

        $assessmentUserTimeslotsQuery = AssessmentUserTimeslot::select('assessment_user_timeslots.*')
                            ->where('timeslot_id', $timeslot_id);

        // Class Level Filter
        $assessmentUserTimeslotsQuery->whereHas('assessment_user.user', function($query) use($request){
            $query->whereIn('id', (array)$request->user_ids);
            $query->whereHas('student', function($query) use($request){
                $query->where(function($query)  use($request){
                    foreach($request->levels as $filter_class_level){
                        $query->orWhereRaw("FIND_IN_SET('$filter_class_level', levels) ");
                    }
                });
            });
        });

        $assessmentUserTimeslots = $assessmentUserTimeslotsQuery->get();
        
        foreach($assessmentUserTimeslots as $assessmentUserTimeslot)
        {
            $assessment_user = $assessmentUserTimeslot->assessment_user;
            $user = $assessment_user->user;
            
            $temp = [];
            if($user->student)
            {
                $temp['name'] = $user->student->get_kanji_name();
                $temp['profile_url'] = route('student.show', $user->student->id);
                $temp['type'] = __('messages.student');
                $temp['levels'] = $user->student->levels;
            }
            else if($user->teacher)
            {
                $temp['name'] = $user->teacher->nickname;
                $temp['profile_url'] = route('teacher.show', $user->teacher->id);
                $temp['type'] = __('messages.teacher');
                $temp['levels'] = "";
            }
            else
            {
                continue;
            }
            
            $temp['assessment_name'] = $assessment_user->assessment->name;
            $temp['view_assessment_url'] = route('assessment_user.show', $assessment_user->id); 
            $students[] = $temp;
        }

        return $students;
    }
}
