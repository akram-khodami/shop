<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use function Carbon\this;

class AttributeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
//        return parent::toArray($request);

        return [
            'id'=>$this->id,
            'unit'=>$this->unit,
            'name'=>$this->name,
            'is_required'=>$this->is_required,
            'is_filterable'=>$this->is_filterable,
            'is_public'=>$this->is_public,
            'type' => $this->type,
            'order'=>$this->order,
//            'options' => $this->whenLoaded('options', fn() => $this->options),
            'options' => $this->whenLoaded('options', function () {
                return $this->options->map(function ($option) {
                    return [
                        'id' => $option->id,
                        'value' => $option->value
                    ];
                });
            }),
        ];
    }
}
