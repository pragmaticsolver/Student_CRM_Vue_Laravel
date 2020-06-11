<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use App\PaymentPlans;
use App\CancellationPolicies;

class PaymentPlanController extends Controller
{
    public function index()
    {
        $plans = DB::table('payment_plans')->get();
        return view('plan.list', array('plans' => $plans));
    }

    public function create()
    {   
        $cancel_types = DB::table('cancel_types')->get();
        return view('plan.create', array('cancel_types' => $cancel_types));
    }

    public function store(Request $request)
    {
        // date_default_timezone_set("Asia/Tokyo");
        $request->validate([
            'cost'=>'required|integer',
            'cost_to_teacher'=> 'required|integer',
            'points'=>'required'        
        ]);

        $plan = new PaymentPlans([
            'cost' => $request->get('cost'),
            'cost_to_teacher'=> $request->get('cost_to_teacher'),
            'points'=> $request->get('points')
        ]);

        $plan->save();

        $payment_plan_id = $plan->id;
        if($request->get('cancel_type_1') != null && $request->get('points_1') != null && $request->get('salary_1') != null) {
            $cancellation_policy = new CancellationPolicies([
                'cancel_type_id' => $request->get('cancel_type_1'),
                'payment_plan_id' => $payment_plan_id,
                'points' => $request->get('points_1'),
                'salary' => $request->get('salary_1')
            ]);
            $cancellation_policy->save();
        }

        if($request->get('cancel_type_2') != null && $request->get('points_2') != null && $request->get('salary_2') != null) {
            $cancellation_policy = new CancellationPolicies([
                'cancel_type_id' => $request->get('cancel_type_2'),
                'payment_plan_id' => $payment_plan_id,
                'points' => $request->get('points_2'),
                'salary' => $request->get('salary_2')
            ]);
            $cancellation_policy->save();
        }

        if($request->get('cancel_type_3') != null && $request->get('points_3') != null && $request->get('salary_3') != null) {
            $cancellation_policy = new CancellationPolicies([
                'cancel_type_id' => $request->get('cancel_type_3'),
                'payment_plan_id' => $payment_plan_id,
                'points' => $request->get('points_3'),
                'salary' => $request->get('salary_3')
            ]);
            $cancellation_policy->save();
        }

        return redirect('/accounting/plan/list')->with('success', 'Payment plan has been added!');
    }

    public function destroy($id)
    {
        $plan = PaymentPlans::find($id);
        $plan->delete();

        return redirect('accounting/plan/list')->with('success', 'Payment plan has been deleted successfully!');
    }
}
