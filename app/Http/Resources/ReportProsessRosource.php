<?php

namespace App\Http\Resources;

use App\Models\technical_person;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReportProsessRosource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        
        return [
            'id' => $this->id,
            'report'=>(new ReportResource($this->report)),
            'solution'=>$this->solution,
            'status'=>$this->status,
            'person_incharge'=>new TechnicalPersonResource($this->p_id),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at'=>$this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
