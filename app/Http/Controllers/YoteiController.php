<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use App\Students;
use App\OneShotYoteis;

class YoteiController extends Controller
{
	public function store(Request $request)
    {
	    // date_default_timezone_set("Asia/Tokyo");
	        
		$request->validate([
	        'guest'=>'required',
	        'date'=>'required',
	        'start_time'=>'required',        
	        'end_time'=>'required',        
	        'teacher_id'=>'required'        
	    ]);

	    $yotei = new OneShotYoteis([
	    	'name' => 'Level Check',
	    	'guest' => $request->get('guest'),
	        'date' => $request->get('date'),
	        'start_time' => $request->get('start_time'),
	        'end_time' => $request->get('end_time'),
	        'teacher_id' => $request->get('teacher_id')
	    ]);

	    $yotei->save();

	    return redirect('/student/'.$request->get('guest'));
	}

	public function update(Request $request)
    {
	    // date_default_timezone_set("Asia/Tokyo");
	        
		$request->validate([
	        'lastname_kanji'=>'required',
	        'firstname_kanji'=>'required',
	        'lastname_furigana'=>'required',        
	        'firstname_furigana'=>'required',        
	        'lastname'=>'required',        
	        'firstname'=>'required',        
	        'email'=>'required',        
	        'home_phone'=>'required',        
	        'mobile_phone'=>'required',        
	        'toiawase_referral'=>'required'        
	    ]);

	    $student = Students::find($request->get('guest'));
        $student->lastname_kanji = $request->get('lastname_kanji');
        $student->firstname_kanji = $request->get('firstname_kanji');
        $student->lastname_furigana = $request->get('lastname_furigana');
        $student->firstname_furigana = $request->get('firstname_furigana');
        $student->lastname = $request->get('lastname');
        $student->firstname = $request->get('firstname');
        $student->email = $request->get('email');
        $student->home_phone = $request->get('home_phone');
        $student->mobile_phone = $request->get('mobile_phone');
        $student->toiawase_referral = $request->get('toiawase_referral');

        $student->save();

        $yotei = OneShotYoteis::find($request->get('yotei_id'));
        $yotei->status = 1;

        $yotei->save();

	    return redirect('/student/'.$request->get('guest'));
	}
}
