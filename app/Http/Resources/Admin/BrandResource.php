<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BrandResource extends JsonResource
{
    /**
     * @OA\Schema(
     *     schema="BrandResource",
     *     type="object",
     *     title="Brand Resource",
     *     @OA\Property(property="id", type="integer", example=1),
     *     @OA\Property(property="name", type="string", example="ORIGINAL"),
     *     @OA\Property(property="slug", type="string", example="original"),
     *     @OA\Property(property="description", type="string", example="ORIGINAL company", nullable=true),
     *     @OA\Property(property="logo", type="string", example="brands/original.png", nullable=true),
     *     @OA\Property(property="is_active", type="boolean", example=true),
     *     @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T12:00:00Z"),
     *     @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T12:00:00Z"),
     *     @OA\Property(property="deleted_at", type="string", format="date-time", nullable=true, example=null),
     * )
     */
    public function toArray(Request $request): array
    {
        return parent::toArray($request);
    }
}
