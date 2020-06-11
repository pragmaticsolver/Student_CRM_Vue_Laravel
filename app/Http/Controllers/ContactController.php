<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use App\Contacts;
use App\Helpers\ActivityEnum;
use App\Helpers\ActivityLogHelper;
use App\Helpers\CommonHelper;
use App\Students;
use Carbon\Carbon;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        $data = array();
        $students = Students::orderBy('firstname','ASC')->orderBy('lastname','ASC')->get();
        
        $contactsQuery = Contacts::with('student','createdBy')->whereHas('student');
        
        if($request->date)
        {
            $data['date'] = $request->date;
            $utc_date = Carbon::createFromFormat('Y-m-d', $request->date, CommonHelper::getSchoolTimezone())->setTimezone('UTC')->format('Y-m-d');
            $contactsQuery->where('contacts.date','LIKE','%'.$utc_date.'%');
        }

        if($request->name)
        {
            $data['name'] = $name = $request->name;
            $contactsQuery->whereHas('student', function($query) use ($name){
                $query->where('firstname','LIKE','%'.$name.'%')
                    ->orWhere('lastname','LIKE','%'.$name.'%')
                    ->orWhere(\DB::raw("CONCAT(firstname,' ',lastname)"),'LIKE','%'.$name.'%')
                    ->orWhere(\DB::raw("CONCAT(lastname,' ',firstname)"),'LIKE','%'.$name.'%');
            });
        }

        $contacts = $contactsQuery->orderby('date','desc')->get();
        
        $data['students'] = $students;
        $data['contacts'] = $contacts;
        return view('contact.list', $data);
    }

    public function store(Request $request)
    {
        // date_default_timezone_set("Asia/Tokyo");
        $request->validate([
            'customer_id'=>'required',
            'type'=> 'required',
            'message'=> 'required'
        ]);

        $contact = new Contacts([
            'customer_id' => $request->get('customer_id'),
            'user_id' => $request->user()->id,
            'type' => $request->get('type'),
            'message' => $request->get('message'),
            'status'=> 0,
            'date' => date('Y-m-d H:i:s')
        ]);

        $contact->save();

        ActivityLogHelper::create(
            ActivityEnum::CONTACT_CREATED,
            CommonHelper::getMainLoggedInUserId(),
            ActivityLogHelper::getContactCreatedParams($contact)
        );

        $message = __('messages.contact-created-successfully');

        if($request->ajax())
        {
            $out['status'] = 1;
            $out['message'] = $message;
            return $out;
        }
        else
        {
            return redirect('/contact/list')->with('success', $message);
        }
    }

    public function destroy($id)
    {
        $contact = Contacts::find($id);
        $contact->delete();

        return redirect('/contact/list');
    }

    public function getFromData()
    {
        $students = Students::orderBy('firstname','ASC')->orderBy('lastname','ASC')->get();
        $final_students = [];
        foreach($students as $student){
            $temp = [
                'id' => $student->id,
                'fullname' => $student->fullname
            ];
            $final_students[] = $temp;
        }

        $out['students'] = $final_students;
        return $out;
    }
}
