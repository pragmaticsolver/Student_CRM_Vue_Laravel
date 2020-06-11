<?php

namespace App\Console\Commands;

use App\LessonFile;
use App\Lessons;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class AdjustDBScript extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'adjust:db';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'One time script to adjust database (code might change for each deployment)';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $lessons = Lessons::all();
        foreach($lessons as $lesson)
        {
            if($lesson->pdf_file)
            {
                $file_exists = Storage::disk('public')->exists('images/lesson/pdf/'.$lesson->pdf_file);
                if($file_exists)
                {

                    Storage::disk('public')->move('images/lesson/pdf/'.$lesson->pdf_file, 'lesson_files/'.$lesson->pdf_file);

                    $lessonFile = new LessonFile();
                    $lessonFile->lesson_id = $lesson->id;
                    $lessonFile->section = 2;
                    $lessonFile->file_path = 'lesson_files/'.$lesson->pdf_file;
                    $lessonFile->save();
                }
                else
                {
                    dump("File Not Found (might be already transfered) - ".$lesson->pdf_file);
                }
            }

            if($lesson->audio_file)
            {
                $file_exists = Storage::disk('public')->exists('images/lesson/audio/'.$lesson->audio_file);
                if($file_exists)
                {
                    dump($lesson->audio_file);
                    Storage::disk('public')->move('images/lesson/audio/'.$lesson->audio_file, 'lesson_files/'.$lesson->audio_file);

                    $lessonFile = new LessonFile();
                    $lessonFile->lesson_id = $lesson->id;
                    $lessonFile->section = 3;
                    $lessonFile->file_path = 'lesson_files/'.$lesson->audio_file;
                    $lessonFile->save();
                }
                else
                {
                    dump("File Not Found (might be already transfered) - ".$lesson->audio_file);
                }
            }

            if($lesson->downloadable_files)
            {
                @$files = json_decode($lesson->downloadable_files,1);
                if(is_array($files))
                {
                    foreach($files as $file)
                    {
                        $file_exists = Storage::disk('public')->exists('images/lesson/downloadable_files/'.$file);
                        if($file_exists)
                        {
                            dump($file);
                            Storage::disk('public')->move('images/lesson/downloadable_files/'.$file, 'lesson_files/'.$file);

                            $lessonFile = new LessonFile();
                            $lessonFile->lesson_id = $lesson->id;
                            $lessonFile->section = 1;
                            $lessonFile->file_path = 'lesson_files/'.$file;
                            $lessonFile->save();
                        }
                        else
                        {
                            dump("File Not Found (might be already transfered) - ".$file);
                        }
                    }
                }
            }
        }
    }
}
