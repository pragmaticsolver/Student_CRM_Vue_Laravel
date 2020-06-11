<?php

namespace App\Http\Controllers;

use App\BookStudents;
use Illuminate\Http\Request;
use App\Books;
use App\File;
use App\Students;
use App\Settings;

class BookController extends Controller
{
    public function index()
    {
        return view('book.list', ['books' => Books::all()]);
    }

    public function create()
    {
        return view('book.create', [
            'today' => today()->format('Y-m-d'), 'book_levels' => explode(',', Settings::get_value('library_book_levels'))
        ]);
    }

    public function store(Request $request)
    {
        $request->validate(Books::get_validate_params());

        try {
            $request->image_name = null;
            if($request->image) {
                $feature_image = (new File())->setFile($request->image)
                                             ->setPath('books/')
                                             ->setName();
                $request->image_name = $feature_image->store() ? $feature_image->getName() : null;
            }

            $book = new Books([
                'name' => $request->name,
                'quantity' => $request->quantity,
                'author_name' => $request->author_name,
                'level' => $request->level,
                'date' => $request->date,
                'barcode' => $request->barcode,
                'thumbnail' => $request->image_name
            ]);

            $book->save();

            return redirect('/book')->with('success', __('messages.addbook-successfully'));
        } catch(\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function show($id)
    {
        return view('book.details', ['book' => Books::find($id)]);
    }

    public function edit($id)
    {
        return view('book.edit', [
            'book' => Books::find($id), 'book_levels' => explode(',', Settings::get_value('library_book_levels'))
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate(Books::get_validate_params($id));

        try {
            $request->image_name = null;
            if($request->update_image == 'true' && $request->image) {
                $feature_image = (new File())->setFile($request->image)
                                             ->setPath('books/')
                                             ->setName();
                $request->image_name = $feature_image->store() ? $feature_image->getName() : null;
            }

            $book = Books::find($id);
            $book->name = $request->name;
            $book->quantity = $request->quantity;
            $book->author_name = $request->author_name;
            $book->level = $request->level;
            $book->date = $request->date;
            $book->barcode = $request->barcode;
            if($request->image_name) $book->thumbnail = $request->image_name;

            $book->save();

            return redirect('/book')->with('success', __('messages.editbook-successfully'));
        } catch(\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $book = Books::find($id);
        $book->delete();

        return redirect('/book')->with('success', __('messages.deletebook-successfully'));
    }

    public function checkin(Request $request)
    {
        return view('book.checkin', [
            'students' => Students::all(), 'today' => today()->format('Y-m-d'),
            'student_id' => $request->student_id
        ]);
    }

    public function checkout(Request $request)
    {
        try {
            $expected_checkin_days = Settings::get_value('library_expected_checkin_days') ? Settings::get_value('library_expected_checkin_days') : 0;

            return view('book.checkout', [
                'students' => Students::all(), 'default_checkin_date' => now()->addDays($expected_checkin_days)->format('Y-m-d'),
                'today' => today()->format('Y-m-d'),
                'student_id' => $request->student_id
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function update_checkin(Request $request)
    {
        $request->validate([
            'barcode' => 'required',
            'checkin_date'    => 'required|date',
            'student_id' => 'required',
        ]);

        try {
            $book = Books::get_by_barcode($request->barcode);

            $book_students = BookStudents::get_by_student_book($request->student_id, $book->id);
            $book_student = $book_students->first();

            $book_student->checkin_date = $request->checkin_date;
            $book_student->status = 1;
            $book_student->save();

            $book->increase_quantity();


            if($request->ajax())
            {
                $out['status'] = 1;
                $out['message'] = __('messages.checkin-successfully');
                return $out;
            }
            else
            {
                return redirect()->back()->with('success', __('messages.checkin-successfully'));
            }


        } catch (\Exception $e) {

            if($request->ajax())
            {
                $out['status'] = 0;
                $out['message'] = $e->getMessage();
                return $out;
            }
            else
            {
                return redirect()->back()->with('error', $e->getMessage());
            }
        }
    }

    public function update_checkout(Request $request)
    {
        $request->validate([
            'barcode' => 'required',
            'student_id' => 'required',
            'checkout_date'    => 'required|date',
            'expected_checkin_date' => 'required|date',
        ]);

        try {
            $book = Books::get_by_barcode($request->barcode);

            $book->check_quantity();

            $book_student = new BookStudents([
                'book_id' => $book->id,
                'student_id' => $request->student_id,
                'checkout_date' => $request->checkout_date,
                'expected_checkin_date' => $request->expected_checkin_date,
                'status' => 0,
            ]);
            $book_student->save();

            $book->decrease_quantity();

            if($request->ajax())
            {
                $out['status'] = 1;
                $out['message'] = __('messages.checkout-successfully');
                return $out;
            }
            else
            {
                return redirect()->back()->with('success', __('messages.checkout-successfully'));
            }

        } catch (\Exception $e) {

            if($request->ajax())
            {
                $out['status'] = 0;
                $out['message'] = $e->getMessage();
                return $out;
            }
            else
            {
                return redirect()->back()->with('error', $e->getMessage());
            }
        }
    }
}
