<?php

namespace App\Console\Commands;

use App\Helpers\CommonHelper;
use App\MonthlyPayments;
use Illuminate\Console\Command;

class MigrateMonthlyPaymentRecords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'onetime:migrate_monthly_payment_recrods';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'One time script to set new fields for existing data in monthly payment records table';

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
        $monthlyPayments = MonthlyPayments::all();
        dump("Count : ".$monthlyPayments->count());
        foreach($monthlyPayments as $monthlyPayment)
        {
            if($monthlyPayment->date == NULL && $monthlyPayment->price == 0)
            {
                $monthlyPayment->rest_month = 1;
                $monthlyPayment->status = 'draft';
                $monthlyPayment->save();
            }
            else if($monthlyPayment->date != NULL)
            {
                $date = $monthlyPayment->date;
                if($monthlyPayment->date == '0217-12-05')
                {
                    $date = '2017-12-05';
                }

                $monthlyPayment->status = 'paid';
                $monthlyPayment->payment_recieved_at = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $date.' 00:00:00', CommonHelper::getSchoolTimezone())->setTimezone("UTC")->format('Y-m-d H:i:s');
                $monthlyPayment->save();
            }
        }
        dump("Done");
    }
}
