<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ResourceStudentList extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'name'             => $this->name,
            'student_id'       => $this->student_id,
            'phone'            => $this->phone,
            'teacher_info'     => $this->teacher_info,
            'group_info'       => $this->group_info,
            'status'           => $this->status_type,
            'group_id'         => $this->group_id,
            'check_url'        => $this->check_url,
            'group_time'       => $this->group_time,
        ];
    }
}
