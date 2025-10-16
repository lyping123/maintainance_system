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

    /**
     * Scope a query to apply common filters: search, date range, status, hostel.
     * Usage: report::filter($filters)->get();
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array|null $filters
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilter($query, $filters)
    {
        if (!$filters || !is_array($filters)) {
            return $query;
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('s_name', 'like', "%{$search}%")
                  ->orWhere('report_issue', 'like', "%{$search}%")
                  ->orWhere('places', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $query->whereBetween('created_at', [$filters['start_date'], $filters['end_date']]);
        } elseif (!empty($filters['start_date'])) {
            $query->whereDate('created_at', '>=', $filters['start_date']);
        } elseif (!empty($filters['end_date'])) {
            $query->whereDate('created_at', '<=', $filters['end_date']);
        }

        // Hostel filter: depends on your DB schema. If reports table has a 'hostel_id' or 'places' stores hostel name,
        // try to match against 'places' as fallback. Adjust if you have a foreign key.
        if (!empty($filters['hostel'])) {
            $hostel = $filters['hostel'];
            $query->where(function ($q) use ($hostel) {
                $q->where('places', 'like', "%{$hostel}%");
                // If there's a 'hostel_id' column, uncomment the following line:
                // $q->orWhere('hostel_id', $hostel);
            });
        }

        return $query;
    }
}
