<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class report extends Model
{
    protected $fillable = [
        's_name', 's_ic', 'places','attachment', 'report_type', 'report_issue', 'status', 'emergency'
    ];

    public function report_progress()
    {
        return $this->hasMany(report_progress::class,'report_id', 'id');
    }
}
