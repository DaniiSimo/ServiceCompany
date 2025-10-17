<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ErrorResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'description' => $this->description,
            'errors' => $this->when(condition: isset($this->errors), value: $this->errors)
        ];
    }
}
