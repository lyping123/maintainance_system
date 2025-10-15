<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id"=>$this->id,
            "s_name"=>(string)$this->s_name,
            "s_ic"=>(string)$this->s_ic,
            "places"=>$this->places,
            "report_type"=>(string)$this->report_type,
            "report_issue"=>(string)$this->report_issue,
            "status"=>(string)$this->status,
            "emergency"=>$this->emergency,
            "report_progress" => $this->whenLoaded('report_progress', function () {
                return $this->report_progress->map(function ($p) {
                    return [
                        'id' => $p->id,
                        'p_id' => $p->p_id,
                        'technician_name' => optional($p->technician)->name ?? null,
                        'field' => optional($p->technician)->field ?? null,
                        'solution' => $p->solution,
                        'created_at' => $p->created_at,
                        'created_date' => optional($p->created_at)->format('Y-m-d'),
                    ];
                });
            }),
            "created_at" => optional($this->created_at)->format('Y-m-d H:i:s'),
            "updated_at" => optional($this->updated_at)->format('Y-m-d H:i:s'),
        ];
    }
}
