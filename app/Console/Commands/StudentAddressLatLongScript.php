<?php

namespace App\Console\Commands;

use App\Students;
use Illuminate\Console\Command;

class StudentAddressLatLongScript extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'student:update_lat_long';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update address lat long';

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
        $students = Students::where('addr_latitude',NULL)
                    ->orWhere('addr_longitude',NULL)->get();

        foreach($students as $student)
        {
            $student->updateAddressLatLong();
        }
    }
}
