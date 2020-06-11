<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use App\Yoyaku;
use App\Attendances;
use App\ClassUsage;
use App\Helpers\ActivityEnum;
use App\Helpers\CommonHelper;
use App\User;
use Illuminate\Support\Facades\Auth;

class YoyakuController extends Controller
{
    public function create($type)
    {
    	$students = DB::table('students')->get();
        $weekly_schedules = DB::table('schedules')->select('schedules.id','schedules.start_time', 'schedules.end_time', 'schedules.day_of_week', 'classes.title')->join('classes','schedules.class_id','=','classes.id')->where('type','=','0')->get();
        $once_schedules = DB::table('schedules')->select('schedules.id','schedules.start_time', 'schedules.end_time', 'schedules.date', 'classes.title')->join('classes','schedules.class_id','=','classes.id')->where('type','=','1')->get();
        return view('yoyaku.create', array('type' => $type, 'students' => $students, 'weekly_schedules' => $weekly_schedules, 'once_schedules' => $once_schedules));
    }

    public function store(Request $request, $type)
    {
        // date_default_timezone_set("Asia/Tokyo");

    	$request->validate([
            'customer_id'=>'required',
            'schedule_id'=>'required',
            'date'=>'required'
        ]);

        if($request->get('taiken')) {
        	$taiken = 1;
        } else {
        	$taiken = 0;
        }

        $yoyaku = new Yoyaku([
        	'customer_id' => $request->get('customer_id'),
            'schedule_id' => $request->get('schedule_id'),
            'date' => $request->get('date'),
            'taiken' => $taiken
        ]);

        $yoyaku->save();

        return redirect('/');
    }

    public function cancel(Request $request, $type = 'api')
    {
        $yoyaku = Yoyaku::find($request->yoyaku_id);

        $cancel_future_reservations = $request->cancel_future_reservations == 1 ? 1 : 0;
        if($cancel_future_reservations)
        {
            // Get all future reservations
            $yoyakus = Yoyaku::where('customer_id', $yoyaku->customer_id)
                        ->where('schedule_id', $yoyaku->schedule_id)
                        ->where('date','>=',$yoyaku->date)
                        ->where('status','=',0)
                        ->where('waitlist','=',0)->get();
        }
        else
        {
            $yoyakus = [$yoyaku];
        }

        $send_email = 1;
        if(isset($request->send_email) && $request->send_email == 0)
        {
            $send_email = 0;
        }

        $res = CommonHelper::cancelReservation($yoyakus,$request->cancel_type, CommonHelper::getMainLoggedInUserId(), ActivityEnum::RESERVATION_CANCELLED, $send_email);
        if($res['error'] != '')
        {
            $status = 0;
            $message = $res['error'];
        }
        else
        {
            $status = 1;
            $message = __('messages.reservation-cancelled-successfully');
        }

        if($type == 'api')
        {
            return response()->json(['success'=> $status ? true : false ,'message'=> $message, 'error' => $message]);
        }
        else if($type == 'home_page')
        {
            $out['status'] = $status;
            $out['message'] = $message;
            $yoyaku = Yoyaku::find($request->yoyaku_id);
            $out['row_html'] = view('student-row', compact('yoyaku'))->render();
            return $out;
        }
        else
        {
            $key = $status ? 'success' : 'error';
            return redirect()->back()->with($key, $message);
        }
    }

    public function simple_cancel(Request $request)
    {
        $yoyaku = Yoyaku::find($request->get('yoyaku_id'));
        $res = CommonHelper::cancelReservation([$yoyaku], NULL, CommonHelper::getMainLoggedInUserId(),ActivityEnum::RESERVATION_CANCELLED);
        if($res['error'] != '')
        {
            $out['success'] = false;
            $out['error'] = $res['error'];
        }
        else
        {
            $out['success'] = true;
            $out['message'] = __('messages.reservation-cancelled-successfully');
        }

        return response()->json($out);
    }

    public function undo_attendance(Request $request)
    {
        $yoyaku = Yoyaku::find($request->yoyaku_id);

        if($yoyaku->status == 1)
        {
            $message = __('messages.signin-undone-successfully');
        }
        else if($yoyaku->status == 2)
        {
            $message = __('messages.cancel-undone-successfully');
        }

        $yoyaku->status = 0;
        $yoyaku->save();

        Attendances::where('yoyaku_id', $yoyaku->id)->delete();
        ClassUsage::UndoClassUsage($yoyaku);

        $out['status'] = 1;
        if($request->home_page == 1)
        {
            $out['row_html'] = view('student-row', compact('yoyaku'))->render();
        }
        $out['message'] = $message;
        return $out;
    }

    public function deleteReservation(Request $request)
    {
        $yoyaku = Yoyaku::where('id',$request->yoyaku_id)->where('status',2)->first();
        if(!$yoyaku)
        {
            abort(404);
        }

        $delete_future_reservations = $request->delete_future_reservations == 1 ? 1 : 0;
        if($delete_future_reservations)
        {
            // Get all future reservations
            $yoyakus = Yoyaku::where('customer_id', $yoyaku->customer_id)
                        ->where('schedule_id', $yoyaku->schedule_id)
                        ->where('date','>=',$yoyaku->date)
                        ->where('status','=',2)
                        ->where('waitlist','=',0)->get();

            foreach($yoyakus as $_yoyaku)
            {
                CommonHelper::deleteReservation($_yoyaku, CommonHelper::getMainLoggedInUserId(), ActivityEnum::RESERVATION_DELETED);
            }
            $status = 1;
            $message = __('messages.future-reservations-deleted-successfully');
        }
        else
        {
            CommonHelper::deleteReservation($yoyaku, CommonHelper::getMainLoggedInUserId(), ActivityEnum::RESERVATION_DELETED);
            $status = 1;
            $message = __('messages.reservation-deleted-successfully');
        }

        $out['status'] = $status;
        $out['message'] = $message;
        return $out;
    }
}
