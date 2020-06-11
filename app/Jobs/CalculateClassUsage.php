<?php

namespace App\Jobs;

use App\ClassUsage;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class CalculateClassUsage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $customer_id;
    private $month_year;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($customer_id, $month_year = NULL)
    {
        $this->customer_id = $customer_id;
        $this->month_year = $month_year;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        ClassUsage::calculate($this->customer_id, $this->month_year);
    }
}
