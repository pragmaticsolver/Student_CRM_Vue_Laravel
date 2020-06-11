<?php

namespace App\Console\Commands;

use App\Helpers\AutomatedTagsHelper;
use App\Students;
use Illuminate\Console\Command;

class RefreshAutomatedTags extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'student:refresh_automated_tags';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalulate logic to attach or detach automated tags for each student';

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
        $students = Students::all();
        foreach($students as $student)
        {
            $automatedTagsHelper = new AutomatedTagsHelper($student);
            $automatedTagsHelper->refreshUpcommingBirthdayTag();
            $automatedTagsHelper->refreshNewStudentTag();
            $automatedTagsHelper->refreshDueTodoTag();
            $automatedTagsHelper->refreshLongTimeStudentTag();
        }
        
        dump('done');
    }
}
