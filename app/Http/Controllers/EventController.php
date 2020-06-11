<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Events;
use App\Schedules;
use App\Classes;
use App\ClassCategories;
use App\Settings;
use App\Http\Requests\EventRequest;

class EventController extends Controller
{
    public function index() {
        return view('class.event.list', ['events' => Events::all()]);
    }

    public function create(Request $request) {
        return view('class.event.create', [
            'category_id' => $request->category_id ? $request->category_id : null,
            'categories' => ClassCategories::all(),
            'class_student_levels' => explode(',', Settings::get_value('class_student_levels'))
        ]);
    }

    public function show($id) {
        return view('class.event.details', ['event' => Events::find($id), 'schedule' => Schedules::where('class_id', $id)->first()]);
    }

    public function destroy($id)
    {
        $event = Events::find($id);
        $event->delete();

        return redirect('/event')->with('success', 'Event has been deleted successfully!');
    }

    public function store(EventRequest $request) {
        try {
            $event = Events::create([
                'title' => $request->title,
                'description' => $request->description,
                'status' => 0,
                'class_type' => Classes::EVENT_TYPE,
                'cost' => $request->cost,
                'size' => $request->size,
                'category_id' => $request->category_id,
                'level' => $request->level
            ]);

            Schedules::create([
                'class_id' => $event->id,
                'date' => $request->date,
                'type' => $request->allday ? Schedules::EVENT_ALLDAY_TYPE : Schedules::EVENT_TIME_TYPE,
                'start_time' => $request->allday ? '00:00:00' : $request->start_time,
                'end_time' => $request->allday ? '23:59:00' : $request->end_time
            ]);

            return redirect('/event')->with('success', 'Event has been added!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function edit($id) {
        $event = Events::find($id);
        $schedule = Schedules::where('class_id', $event->id)->first();
        return view('class.event.edit', [
            'event' => $event, 'schedule' => $schedule,
            'categories' => ClassCategories::all(),
            'class_student_levels' => explode(',', Settings::get_value('class_student_levels'))
        ]);
    }

    public function update(EventRequest $request, $id) {
        try {
            Events::find($id)->update([
                'title' => $request->title,
                'description' => $request->description,
                'cost' => $request->cost,
                'size' => $request->size,
                'category_id' => $request->category_id,
                'level' => $request->level
            ]);

            Schedules::where('class_id', $id)->first()->update([
                'date' => $request->date,
                'type' => $request->allday ? Schedules::EVENT_ALLDAY_TYPE : Schedules::EVENT_TIME_TYPE,
                'start_time' => $request->allday ? '00:00:00' : $request->start_time,
                'end_time' => $request->allday ? '23:59:00' : $request->end_time
            ]);

            return redirect('/event/'.$id.'/edit')->with('success', 'Event has been updated!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
