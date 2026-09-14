<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status,
            'project_id' => $this->project_id,
            'assignee' => new UserResource($this->whenLoaded('assignee')),
            'due_date' => $this->due_date?->format('Y-m-d'),
            'comments_count' => $this->whenCounted('comments'),
        ];
    }
}
