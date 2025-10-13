<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class report extends Model
{
    protected $fillable = [
        's_name', 's_ic', 'places', 'report_type', 'report_issue', 'status', 'emergency'
    ];
}
