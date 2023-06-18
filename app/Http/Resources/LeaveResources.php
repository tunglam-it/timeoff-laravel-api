<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LeaveResources extends JsonResource
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
            'type'=>$this->type,
            'reason' => $this->reason,
            'status' => $this->status,
            'employee_id' => $this->employee_id,
            'employees' => new EmployeesResource($this->employees), //ket noi category voi  CategoryResources
            'comment' => $this->comment,
            'start_date' => $this->start_date,
            'end_date' =>$this->end_date,
            'estimate' =>$this->estimate,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
