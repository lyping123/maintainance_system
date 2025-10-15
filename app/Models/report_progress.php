<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class report_progress extends Model
{
    protected $fillable=['report_id', 'solution', 'status', 'p_id'];
    
    public function report()
    {
        return $this->belongsTo(report::class,'report_id',"id");
    }
    public function person()
    {
        return $this->belongsTo(technical_person::class, 'p_id',"id");
    }
}
