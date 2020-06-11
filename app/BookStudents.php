<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BookStudents extends Model
{
    protected $table = 'book_students';

    protected $fillable = [
        'student_id', 'book_id', 'checkin_date', 'checkout_date', 'status', 'expected_checkin_date'
    ];

    public $timestamps = false;

    public static function get_checkins($book_id) {
        return self::where('book_id', $book_id)->where('status', 0)->get();
    }

    public static function get_checkouts($book_id) {
        return self::where('book_id', $book_id)->where('status', 1)->get();
    }

    public function student() {
        return $this->hasOne('App\Students', 'id', 'student_id');
    }

    public function book() {
        return $this->hasOne('App\Books', 'id', 'book_id');
    }

    public static function get_by_student_book($student_id, $book_id) {
        $book_students = self::where('student_id', $student_id)
                            ->where('book_id', $book_id)
                            ->where('status', 0)
                            ->get();
        if($book_students->count() == 0) throw new\Exception(__('messages.checkout-empty'));

        return $book_students;
    }

    public static function get_history_columns($page) {
        $columns = [];

        if($page == 'student') {
            $columns[] = __('messages.bookname');
        } elseif ($page == 'book') {
            $columns[] = __('messages.student');
        }

        $columns[] = __('messages.checkoutdate');
        $columns[] = __('messages.expectedcheckindate');
        $columns[] = __('messages.status');
        $columns[] = __('messages.checkindate');

        return $columns;
    }

    public function get_history_column_values($page) {
        $column_values = [];

        if($page == 'student') {
            $column_values[] = $this->book->name;
        } elseif ($page == 'book') {
            $column_values[] = $this->student->get_kanji_name();
        }

        $column_values[] = $this->checkout_date;
        $column_values[] = $this->expected_checkin_date;
        $column_values[] = $this->get_status_name();
        $column_values[] = $this->checkin_date;

        return $column_values;
    }

    public function get_status_name() {
        switch($this->status) {
            case 0:
                return __('messages.not-checkedin');
            case 1:
                return __('messages.checkedin');

        }
    }
}
