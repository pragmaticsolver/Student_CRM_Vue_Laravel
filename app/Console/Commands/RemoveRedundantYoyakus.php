<?php

namespace App\Console\Commands;

use App\ClassesOffDays;
use App\ClassUsage;
use App\Schedules;
use App\Yoyaku;
use Illuminate\Console\Command;

class RemoveRedundantYoyakus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'onetime:remove_redandant_yoyakus';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove record from yoyakus for which class has been cancelled';

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
        $deleted = 0;
        foreach(Yoyaku::all() as $yoyaku){
            $schedule = $yoyaku->schedule;
            if($schedule->type == Schedules::CLASS_REPEATED_TYPE)
            {
                if(ClassesOffDays::where('schedule_id', $schedule->id)->where('date', $yoyaku->date)->exists())
                {
                    echo('.');
                    $deleted++;
                    ClassUsage::reservationDeleted($yoyaku);
                    $yoyaku->delete();
                }
            }
        }

        dump("");
        dump("Deleted ".$deleted." yoyakus");
        dump("Done");
    }
}
