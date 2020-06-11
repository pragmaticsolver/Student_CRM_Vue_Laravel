<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NotificationText extends Model
{
    protected $table = 'notification_texts';

    public $timestamps = false;

    public const TYPE_BUTTON_TEXT = 1;
    public const TYPE_LINE_TEXT = 2;

    protected $fillable = [
        'email_template_id',
        'type',
        'key',
        'text_en',
        'text_ja'
    ];

    public function getDisplayKeyAttribute()
    {
        return ucfirst(str_replace("-"," ",$this->key));
    }

    public function getInputFieldName($field)
    {
        return "button_text[$this->id][$field]";
    }

    public function getLarvelFieldName($field)
    {
        return "button_text.$this->id.$field";
    }

    public function getValidationParams()
    {
        $validate_params = [];
        $validate_params[$this->getLarvelFieldName('text_en')] = 'required|max:191';
        $validate_params[$this->getLarvelFieldName('text_ja')] = 'required|max:191';
        return $validate_params;
    }
}
