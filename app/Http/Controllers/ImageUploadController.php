<?php

namespace App\Http\Controllers;

use App\File;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\LessonFile;
use DB;
use App\Students;
use App\Lessons;
use App\ScheduleFile;
use Illuminate\Support\Facades\Storage;

class ImageUploadController extends Controller
{
    private $lessonFiles = [
        'pdf' => 'pdf_file',
        'audio' => 'audio_file',
        'video' => 'video_file'
    ];

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function fileStore(Request $request, $id)
    {
        $studentImage = (new File())->setFile($request->file)
                                    ->setPath('students/')
                                    ->setName(pathinfo($request->file->getClientOriginalName(), PATHINFO_FILENAME));
        $imageName = $studentImage->store() ? $studentImage->getName() : null;

        $student = Students::find($id);
        $student->image = $imageName;
        $student->save();

        return response()->json(['success'=>$imageName]);
    }

    public function fileDestroy(Request $request, $id)
    {
        $filename =  $request->get('filename');
        $student = Students::find($id);
        $student->image = '';
        $student->save();

        @Storage::disk('public')->delete('students/'.$filename);
        return $filename;
    }

    public function uploadLessonFile(Request $request, $section ,$lesson_id)
    {
        try {
            $file = $request->file('file');
            $fileName = $file->getClientOriginalName();
            $file_path = Storage::disk('public')->putFileAs('lesson_files', $file, (\Auth::user()->id.time().'__').$fileName);
            $lessonFile = new LessonFile();
            $lessonFile->lesson_id = $lesson_id;
            $lessonFile->section = $section;
            $lessonFile->file_path = $file_path;
            $lessonFile->file_name = $fileName;
            $lessonFile->save();

            return response()->json(['name' => $fileName, 'id' => $lessonFile->id]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function deleteLessonFile($id)
    {
        $lessonFile = LessonFile::find($id);
        if($lessonFile)
        {
            @Storage::disk('public')->delete($lessonFile->file_path);
            $lessonFile->delete();
        }

        return response()->json(['success' => __('messages.deletefilesuccessfully')]);
    }

    public function upload_temp_file(Request $request)
    {
        try {
            $file = $request->file('file');
            $fileName = $file->getClientOriginalName();
            $file_path = Storage::disk('local')->putFileAs('temp_uploads', $file, $fileName);

            return response()->json(['file_path' => $file_path]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function updateFileName(Request $request, $id)
    {
        $type = $request->get('type');
        $html = "";
        if ($type == "lesson") {
            $lessonFile = LessonFile::find($id);
            if($lessonFile)
            {
                $lessonFile->file_name = $request->get('file_name');
                $lessonFile->save();
                $lesson = $lessonFile->lesson;
                if ($lessonFile->section == 1) {
                    $html = $lesson->the_downloadable_files_url();
                } else if ($lessonFile->section == 2) {
                    $html = $lesson->the_pdf_files_url();
                } else if ($lessonFile->section == 3) {
                    $html = $lesson->the_audio_files_url();
                } else if ($lessonFile->section == 4) {
                    $html = $lesson->the_extramaterial_files_url();
                } else if ($lessonFile->section == 5) {
                    $html = $lesson->the_video_url();
                }        
            }
        } else {
            $scheduleFile = ScheduleFile::find($id);
            if($scheduleFile)
            {
                $scheduleFile->file_name = $request->get('file_name');
                $scheduleFile->save();
                $html = $scheduleFile->getAttachment();
            }
        }

        return response()->json(['status' => 1, 'message' => __('messages.updatefilesuccessfully'), 'html' => $html]);
    }
}
