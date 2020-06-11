<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Attendances;
use App\ClassUsage;
use App\Helpers\CommonHelper;
use App\Students;
use App\Yoyaku;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function store(Request $request)
    {
        // date_default_timezone_set("Asia/Tokyo");

    	$request->validate([
            'customer_id'=>'required',
            'yoyaku_id'=>'required',
            'class_id'=>'required',
            'teacher_id'=>'required',
            'schedule_id'=>'required',
        ]);

        $attendance = new Attendances([
        	'customer_id' => $request->get('customer_id'),
            'yoyaku_id' => $request->get('yoyaku_id'),
            'class_id' => $request->get('class_id'),
            'teacher_id' => $request->get('teacher_id'),
            'schedule_id' => $request->get('schedule_id'),
            'payment_plan_id' => $request->get('payment_plan_id'),
            'start_date' => $request->get('start_date'),
            'end_date' => $request->get('end_date'),
            'date' => Carbon::now(CommonHelper::getSchoolTimezone())->format('Y-m-d')
        ]);

        $attendance->save();

        if($request->get('taiken') == '1') {
            $student = Students::find($request->get('customer_id'));
            $student->status = 1;

            $student->save();
        }

        $yoyaku = Yoyaku::find($request->get('yoyaku_id'));
        $yoyaku->status = 1;

        $yoyaku->save();
        ClassUsage::ClassUsed($yoyaku);

        if($request->ajax())
        {
            $out['status'] = 1;
            $out['row_html'] = view('student-row', compact('yoyaku'))->render();
            $out['message'] =__('messages.signedin-successfully');
            return $out;
        }

        return redirect('/');
    }
}
