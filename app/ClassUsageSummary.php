<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ClassUsageSummary extends Model
{
    protected $table = 'class_usage_summaries';
    
    public $fillable = [
        'customer_id',
        'month_year',
        'paid',
        'unpaid',
        'used',
        'used_leftovers',
        'new_leftovers',
        'leftovers',
        'expiring',
        'cancelled'
    ];
}

