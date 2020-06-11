<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Books extends Model
{
    protected $table = 'books';
    
    protected $fillable = [
        'name', 'quantity', 'author_name', 'level', 'thumbnail', 'date', 'barcode'
    ];

    public $timestamps = false;

    public function students() {
        return $this->belongsToMany('App\Students', 'book_students', 'book_id', 'student_id');
    }

    public function book_students() {
        return $this->hasMany('App\BookStudents', 'book_id', 'id');
    }

    public function get_image() {
        return Storage::disk('public')->url('books/'.$this->thumbnail);
    }

    public function the_image() {
        return '<img src="'.$this->get_image().'" width=100 height=100>';
    }

    public static function get_validate_params($id = null) {
        if($id) {
            return [
                'name' => 'required|unique:books,name,'.$id,
                'quantity' => 'required|numeric|min:0',
                'author_name' => 'required',
                'level' => 'required',
                'date' => 'required|date',
                'barcode' => 'required|unique:books,barcode,'.$id
            ];
        } else {
            return [
                'name' => 'required|unique:books,name',
                'quantity' => 'required|numeric|min:0',
                'author_name' => 'required',
                'level' => 'required',
                'date' => 'required|date',
                'barcode' => 'required|unique:books,barcode'
            ];
        }
    }

    public static function get_by_barcode($barcode) {
        $books = self::where('barcode', $barcode)->get();
        if($books->count() == 0) throw new \Exception(__('messages.bookbarcode-empty'));

        return $books->first();
    }

    public function check_quantity() {
        if($this->quantity <= 0) throw new \Exception(__('messages.book-outofstock'));
    }

    public function decrease_quantity() {
        $this->quantity -= 1;
        $this->save();
    }

    public function increase_quantity() {
        $this->quantity += 1;
        $this->save();
    }
}
