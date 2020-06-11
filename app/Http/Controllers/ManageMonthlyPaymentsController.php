<?php

namespace App\Http\Controllers;

use App\Helpers\CommonHelper;
use App\Helpers\PaymentHelper;
use App\MonthlyPayments;
use App\Settings;
use App\Students;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ManageMonthlyPaymentsController extends Controller
{
    public function index($month_year = NULL)
    {
        if (!$month_year) {
            $month_year = Carbon::now(CommonHelper::getSchoolTimezone())->format('Y-m');
        }
        return view('accounting.manage_monthly_payments', compact('month_year'));
    }

    public function data($month_year)
    {
        $student_role_ids = explode(",", Settings::get_value('generate_payment_info_for_roles'));

        $students = Students::join('model_has_roles',function($join){
                        $join->on('model_has_roles.model_id','=','students.user_id')
                            ->where('model_has_roles.model_type','=','App\User');
                    })
                    ->whereIn('model_has_roles.role_id', $student_role_ids)
                    ->orderBy('firstname','ASC')
                    ->orderBy('lastname','ASC')
                    ->get();

        $final_students = [];
        foreach($students as $student){
            $temp = [
                'id' => $student->id,
                'fullname' => $student->fullname,
                'payment_settings' => [
                    'price' =>  $student->paymentSetting ? $student->paymentSetting->price : "",
                    'payment_method' =>  $student->paymentSetting ? $student->paymentSetting->payment_method : "",
                    'no_of_lessons' =>  $student->paymentSetting ? $student->paymentSetting->no_of_lessons : "",
                ]
            ];
            $final_students[] = $temp;
        }

        $payments = MonthlyPayments::OnlyMonthyPayments()->where('period', $month_year)->get();
        $generated_payments = [];
        foreach($payments as $payment)
        {
            $generated_payments[] = $payment->formatForManagePaymetsPage(\Auth::user());
        }

        $out['students'] = $final_students;
        $out['generated_payments'] = $generated_payments;
        $out['payment_methods'] = explode(',', Settings::get_value('payment_methods'));
        $out['date'] = \Carbon\carbon::now(\App\Helpers\CommonHelper::getSchoolTimezone())->format('Y-m-d');
        $out['time'] = \Carbon\carbon::now(\App\Helpers\CommonHelper::getSchoolTimezone())->format('H:i');
        return $out;
    }

    public function generatePaymentRecords(Request $request)
    {
        $month_year = $request->month_year;

        // Validate request
        $customer_ids = collect($request->payments)->pluck('customer_id')->all();
        $payments = MonthlyPayments::OnlyMonthyPayments()->where('period', $month_year)->whereIn('customer_id', $customer_ids)->get();

        if(count($payments) > 0)
        {        
            $student_names = [];
            foreach($payments as $payment) {
                $student_names[] = $payment->student->fullname;
            }

            $student_names = array_unique($student_names);

            return [
                'status' => 0,
                'message' => 'Payment record already exists for following student(s): ' . implode(", ", $student_names)
            ];
        }

        foreach($request->payments as $payment)
        {
            $customer_id = $payment['customer_id'];            
            if($payment['rest_month'] == 1)
            {
                PaymentHelper::createRestMonthPaymentRecord($customer_id, $month_year);
            }
            else
            {
                $price = $payment['price'];
                $number_of_lessons = $payment['no_of_lessons'];
                $memo = $payment['memo'];
                $payment_method =  $payment['payment_method'];
                PaymentHelper::createMonthlyPaymentRecord($customer_id, $price, $month_year, $number_of_lessons, $memo, $payment_method);
            }
        }

        return [
            'status' => 1,
            'message' => __('messages.payment-records-generated-successfully')
        ];
    }
}
