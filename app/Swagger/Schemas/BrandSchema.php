<?php


namespace App\Swagger\Schemas;

/**
 * @OA\Schema(
 *     schema="Brand",
 *     type="object",
 *     title="Brand",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="ORIGINAL"),
 *     @OA\Property(property="slug", type="string", example="original"),
 *     @OA\Property(property="description", type="string", example="ORIGINAL Desc"),
 *     @OA\Property(property="logo", type="string", example="brands/original.png"),
 *     @OA\Property(property="is_active", type="integer", example=1),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-05-17T10:19:07.000000Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-05-17T10:19:07.000000Z"),
 *     @OA\Property(property="deleted_at", type="string", format="date-time", nullable=true, example=null),
 * )
 */
class BrandSchema
{
}
