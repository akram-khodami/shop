<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\AttributeResource;
use App\Http\Requests\StoreAttributeRequest;
use App\Http\Requests\UpdateAttributeRequest;
use App\Models\Attribute;
use  App\Services\AttributeService;

class AttributeController extends Controller
{
    protected $attributeService;

    public function __construct(AttributeService $attributeService)
    {
        $this->attributeService = $attributeService;
    }

    public function index()
    {
        $attributes = $this->attributeService->allAttributes();

        return AttributeResource::collection($attributes);
    }

    public function store(StoreAttributeRequest $request)
    {
        $attribute = $this->attributeService->store($request->validated());

        return new AttributeResource($attribute);
    }

    public function show(Attribute $attribute)
    {
        return new AttributeResource($attribute);
    }

    public function update(UpdateAttributeRequest $request, Attribute $attribute)
    {
        $attribute = $this->attributeService->update($request->validated(), $attribute);

        return new AttributeResource($attribute);
    }

    public function destroy(Attribute $attribute)
    {
        $this->attributeService->destroy($attribute);

        return response()->json(
            [
                'success' => true,
                'message' => 'attribute deleted',
                'error' => ''
            ], 200);
    }
}
