<?php

namespace App;

use App\Helpers\CommonHelper;
use App\Jobs\CalculateClassUsage;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ClassUsage extends Model
{
    protected $table = 'class_usages';
    
    public static function ClassUsed($yoyaku)
    {
        $month_year = Carbon::createFromFormat('Y-m-d', $yoyaku->date, CommonHelper::getSchoolTimezone())->firstOfMonth()->format('Y-m-d');
        CalculateClassUsage::dispatch($yoyaku->customer_id,$month_year);
    }

    public static function UndoClassUsage($yoyaku)
    {
        $month_year = Carbon::createFromFormat('Y-m-d', $yoyaku->date, CommonHelper::getSchoolTimezone())->firstOfMonth()->format('Y-m-d');
        CalculateClassUsage::dispatch($yoyaku->customer_id,$month_year);
    }

    public static function PaymentDeleted($customer_id, $period)
    {
        $month_year = $period.'-01';
        CalculateClassUsage::dispatch($customer_id,$month_year);
    }

    public static function paymentPaid($monthlyPayment)
    {
        if($monthlyPayment->period)
        {
            $customer_id = $monthlyPayment->customer_id;
            $month_year = $monthlyPayment->period.'-01';
            CalculateClassUsage::dispatch($customer_id,$month_year);
        }
    }

    public static function paymentUpdated($newPayment, $oldPayment)
    {
        // status will not be updated via edit functionality so it can be checked either of new record or old record.
        // if period exist it can be changed but can not be removed.
        if($newPayment->status == 'paid' && $newPayment->period) 
        {
            $oldMonthYear = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $oldPayment->period.'-01 00:00:00');
            $newMonthYear = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $newPayment->period.'-01 00:00:00');
            
            $min = $newMonthYear;
            if($oldMonthYear->lessThan($newMonthYear))
            {
                $min = $oldMonthYear;
            }

            $customer_id = $newPayment->customer_id;
            $month_year = $min->format('Y-m-d');
            CalculateClassUsage::dispatch($customer_id,$month_year);
        }
    }

    public static function reservationCancelled($yoyaku)
    {
        $month_year = Carbon::createFromFormat('Y-m-d', $yoyaku->date, CommonHelper::getSchoolTimezone())->firstOfMonth()->format('Y-m-d');
        CalculateClassUsage::dispatch($yoyaku->customer_id,$month_year);
    }

    public static function reservationDeleted($yoyaku)
    {
        $month_year = Carbon::createFromFormat('Y-m-d', $yoyaku->date, CommonHelper::getSchoolTimezone())->firstOfMonth()->format('Y-m-d');
        CalculateClassUsage::dispatch($yoyaku->customer_id,$month_year);
    }
    
    public static function calculate($customer_id, $month_year = NULL)
    {
        $leftover_class_expiration_period = Settings::get_value('leftover_class_expiration_period');

        // 1. Delete existing records before calulations
        $classUsageQuery = ClassUsage::where('customer_id',$customer_id);
        if($month_year)
        {
            $classUsageQuery->where('month_year','>=',$month_year);
        }
        $classUsageQuery->delete();
        // clean left over class usage from previous months (if used any)
        if($month_year)
        {
            ClassUsage::where('customer_id',$customer_id)->where('used_month_year','>=',$month_year)->update([
                'used_month_year' => NULL,
                'yoyaku_id' => NULL
            ]);
        }


        // 2. Create Payment Entries from given months
        $MonthlyPaymentsQuery = MonthlyPayments::
                                selectRaw("STR_TO_DATE(CONCAT(period,'-01'), '%Y-%m-%d') as custom_date, SUM(number_of_lessons) as number_of_lessons")
                                ->where('customer_id',$customer_id)
                                ->where('period','!=',NULL)
                                ->where('status','paid');
        if($month_year)
        {
            $MonthlyPaymentsQuery->whereRaw("STR_TO_DATE(CONCAT(period,'-01'), '%Y-%m-%d') >= '$month_year'");
        }

        $payment_records = $MonthlyPaymentsQuery->groupBy('custom_date')->get()->toArray();
        foreach($payment_records as $payment)
        {
            $_month_year = $payment['custom_date'];
            $paid_no_of_lessons = $payment['number_of_lessons'];

            $expiry_month_year = Carbon::createFromFormat('Y-m-d',$_month_year)->addMonths($leftover_class_expiration_period)->format('Y-m-d');

            for($i = 0; $i < $paid_no_of_lessons; $i++)
            {
                $classUsage = new ClassUsage();
                $classUsage->customer_id = $customer_id;
                $classUsage->month_year = $_month_year;
                $classUsage->expiry_month_year = $expiry_month_year;
                $classUsage->is_paid = 1;
                $classUsage->save();
            }
        }

        // 3. Assign class ussage.
        $school_off_days = SchoolOffDays::pluck('date')->toArray(); // Need to fetch all school of days as used below to find min and max dates.

        $yoyakusQuery = Yoyaku::with('attendance.cancellationPolicy.cancelType')
                ->where('customer_id', $customer_id)
                ->where('taiken', 0)
                ->where(function($query){
                    $query->where('status', 1);
                    $query->orWhere(function($query){
                        $query->whereHas('attendance.cancellationPolicy.cancelType', function($query){
                            $query->where('alias','full-penalty-cancel');
                        });
                    });
                })
                ->whereNotIn('date', $school_off_days)
                ->whereHas('schedule', function($query){
                    $query->whereIn('type', Schedules::CLASS_TYPES);
                });

        if($month_year)
        {
            $yoyakusQuery->where('date','>=',$month_year);
        }

        $yoyakus = $yoyakusQuery->orderBy('date','ASC')->get();
        foreach($yoyakus as $yoyaku)
        {
            // Check for available class for current month
            $_month_year = Carbon::createFromFormat('Y-m-d',$yoyaku->date)->firstOfMonth()->format('Y-m-d');
            $expiry_month_year = Carbon::createFromFormat('Y-m-d',$_month_year)->addMonths($leftover_class_expiration_period)->format('Y-m-d');

            $classUsage = ClassUsage::where('customer_id', $yoyaku->customer_id)
                            ->where('month_year', $_month_year)
                            ->where('used_month_year', NULL)
                            ->where('is_paid', 1)->first();
            if($classUsage)
            {
                $classUsage->used_month_year = $_month_year;
                $classUsage->yoyaku_id = $yoyaku->id;
                $classUsage->save();
            }
            else
            {
                //Check for available left over classes from pervious month
                $classUsage = ClassUsage::where('customer_id', $yoyaku->customer_id)
                                ->where('month_year','<',$_month_year)
                                ->where('used_month_year',NULL)
                                ->where('expiry_month_year','>=',$_month_year)
                                ->where('is_paid',1)
                                ->orderBy('month_year','ASC')
                                ->first();
                if($classUsage)
                {
                    $classUsage->used_month_year = $_month_year;
                    $classUsage->yoyaku_id = $yoyaku->id;
                    $classUsage->save();
                }
                else
                {
                    // Create entry in class usage with is_paid = 0
                    $classUsage = new ClassUsage();
                    $classUsage->customer_id = $yoyaku->customer_id;
                    $classUsage->month_year = $_month_year;
                    $classUsage->expiry_month_year = $expiry_month_year;
                    $classUsage->is_paid = 0;
                    $classUsage->used_month_year = $_month_year;
                    $classUsage->yoyaku_id = $yoyaku->id;
                    $classUsage->save();
                }
            }
        }


        // 4. Update data on class_usage_summaries table.
        $from_month_year = $to_month_year = NULL;

        if($month_year)
        {
            $from_month_year = Carbon::createFromFormat('Y-m-d', $month_year, CommonHelper::getSchoolTimezone());
        }
        else
        {
            $min_class_usage = ClassUsage::where('customer_id',$customer_id)->orderBy('month_year','ASC')->first();
            if($min_class_usage)
            {
                $from_month_year = Carbon::createFromFormat('Y-m-d',$min_class_usage->month_year, CommonHelper::getSchoolTimezone());
            }

            // Handling for case if only cancelled records exists for user (in such case no records will be there in class_usages table but still we need to create record in class_usage_summaries table to display cancelled counts)
            $first_cancelled_yoyaku = Yoyaku::where('customer_id',$customer_id)
                                        ->where('status',2)
                                        ->whereHas('attendance.cancellationPolicy.cancelType', function($query){
                                            $query->where('alias','!=','full-penalty-cancel');
                                        })
                                        ->whereNotIn('date', $school_off_days)
                                        ->orderBy('date','ASC')
                                        ->first();
            if($first_cancelled_yoyaku)
            {
                $from_month_year_cancelled = Carbon::createFromFormat('Y-m-d',$first_cancelled_yoyaku->date, CommonHelper::getSchoolTimezone())->firstOfMonth();

                if($from_month_year_cancelled)
                {
                    if(!$from_month_year)
                    {
                        $from_month_year = $from_month_year_cancelled;
                    }
                    else // if both from dates exists - choose minimum one
                    {
                        if($from_month_year_cancelled->lessThan($from_month_year))
                        {
                            $from_month_year = $from_month_year_cancelled;
                        }
                    }
                }
            }
        }

        $max_class_usage = ClassUsage::where('customer_id',$customer_id)->orderBy('expiry_month_year','DESC')->first();
        if($max_class_usage)
        {
            $to_month_year = Carbon::createFromFormat('Y-m-d',$max_class_usage->expiry_month_year, CommonHelper::getSchoolTimezone());
        }
        // Handling for case if only cancelled records exists for user (in such case no records will be there in class_usages table but still we need to create record in class_usage_summaries table to display cancelled counts)
        $last_cancelled_yoyaku = Yoyaku::where('customer_id',$customer_id)
                                    ->where('status',2)
                                    ->whereHas('attendance.cancellationPolicy.cancelType', function($query){
                                        $query->where('alias','!=','full-penalty-cancel');
                                    })
                                    ->whereNotIn('date', $school_off_days)
                                    ->orderBy('date','DESC')
                                    ->first();
        if($last_cancelled_yoyaku)
        {
            $to_month_year_cancelled = Carbon::createFromFormat('Y-m-d',$last_cancelled_yoyaku->date, CommonHelper::getSchoolTimezone())->firstOfMonth();
            if($to_month_year_cancelled)
            {
                if(!$to_month_year)
                {
                    $to_month_year = $to_month_year_cancelled;
                }
                else // if both from dates exists - choose maximum one
                {
                    if($to_month_year_cancelled->greaterThan($to_month_year))
                    {
                        $to_month_year = $to_month_year_cancelled;
                    }
                }
            }
        }

        if($from_month_year && $to_month_year)
        {
            $iterate_month_year = (clone $from_month_year);
            do{
                $_month_year = (clone $iterate_month_year)->format('Y-m-d');
                self::updateClassUsageSummary($customer_id, $_month_year, $school_off_days);
                $iterate_month_year->addMonth()->firstOfMonth();
            }
            while($iterate_month_year->lessThanOrEqualTo($to_month_year));

            // Remove Old Entries if any from class_usage_summaries table.
            ClassUsageSummary::where('customer_id',$customer_id)->where('month_year','>',(clone $to_month_year)->format('Y-m-d'))->delete();

            // Remove previous entries only if caluclating for all month - years.
            // as in this case from_month_year will be mimimum month-year which has data.
            if(!$month_year)
            {
                ClassUsageSummary::where('customer_id',$customer_id)->where('month_year','<',(clone $from_month_year)->format('Y-m-d'))->delete();
            }
        }
        else
        {
            // Remove Old Entries if any from class_usage_summaries table.
            ClassUsageSummary::where('customer_id',$customer_id)->delete();
        }
    }

    private static function updateClassUsageSummary($customer_id, $month_year, $school_off_days)
    {
        $from_date = Carbon::createFromFormat('Y-m-d',$month_year, CommonHelper::getSchoolTimezone());
        $update = array();
        $update['paid'] = ClassUsage::where('customer_id',$customer_id)->where('month_year',$month_year)->where('is_paid',1)->count();
        $update['unpaid'] = ClassUsage::where('customer_id',$customer_id)->where('month_year',$month_year)->where('is_paid',0)->where('used_month_year','!=',NULL)->count();
        $update['used'] = ClassUsage::where('customer_id',$customer_id)->where('month_year',$month_year)->where('used_month_year',$month_year)->count();
        $update['used_leftovers'] = ClassUsage::where('customer_id',$customer_id)->where('month_year','!=',$month_year)->where('used_month_year',$month_year)->count();

        $update['new_leftovers'] = ClassUsage::where('customer_id',$customer_id)
            ->where('month_year',$month_year)
            ->where(function($query) use($month_year){
                $query->where('used_month_year',NULL);
                $query->orWhere('used_month_year','>',$month_year);
            })
            ->where('expiry_month_year','>=',$month_year)->count();

        $update['leftovers'] = ClassUsage::where('customer_id',$customer_id)
            ->where('month_year','<=',$month_year)
            ->where(function($query) use($month_year){
                $query->where('used_month_year',NULL);
                $query->orWhere('used_month_year','>',$month_year);
            })
            ->where('expiry_month_year','>=',$month_year)->count();

        $update['expiring'] = ClassUsage::where('customer_id',$customer_id)->where('expiry_month_year','=',$month_year)->where('used_month_year',NULL)->count();

        $update['cancelled'] = Yoyaku::where('customer_id',$customer_id)
                                    ->where('status',2)
                                    ->whereHas('attendance.cancellationPolicy.cancelType', function($query){
                                        $query->where('alias','!=','full-penalty-cancel');
                                    })
                                    ->whereBetween('date', [
                                        (clone $from_date)->format('Y-m-d'),
                                        (clone $from_date)->lastOfMonth()->format('Y-m-d')
                                    ])
                                    ->whereNotIn('date', $school_off_days)
                                    ->count();

        $classUsageSummary = ClassUsageSummary::where('customer_id',$customer_id)->where('month_year',$month_year)->first();
        if($classUsageSummary)
        {
            // eloquent save method is not working for update dueto missing id key (and composite primary key) on table.
            ClassUsageSummary::where('customer_id',$customer_id)->where('month_year',$month_year)->update($update);
        }
        else
        {
            $classUsageSummary = new ClassUsageSummary();
            $classUsageSummary->customer_id = $customer_id;
            $classUsageSummary->month_year = $month_year;
            $classUsageSummary->fill($update);
            $classUsageSummary->save();
        }
    }
}
