<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\AttributeResource;
use App\Http\Requests\StoreAttributeRequest;
use App\Http\Requests\UpdateAttributeRequest;
use App\Models\Attribute;
use App\Models\AttributeOption;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AttributeController extends Controller
{
    public function index()
    {
        $attributes = Attribute::with('options')->paginate(15);

        return AttributeResource::collection($attributes);

    }

    public function store(StoreAttributeRequest $request)
    {
        try {

            return DB::transaction(function () use ($request) {

                // ایجاد ویژگی بدون گزینه‌ها (حذف options از داده‌های ذخیره‌شده)
                $validatedData = $request->validated();
                $options = $validatedData['options'] ?? null;
                unset($validatedData['options']);

                //===step1
                $attribute = Attribute::create($validatedData);

                //===step2
                if ($attribute->type === 'select' && $options) {

                    $optionsData = array_map(function ($option) use ($attribute) {
                        return [
                            'attribute_id' => $attribute->id,
                            'value' => $option,
                            'created_at' => now(),
                            'updated_at' => now()
                        ];
                    }, $options);

                    DB::table('attribute_options')->insert($optionsData);

                    //==way2
//                    $optionsData = array_map(function ($option) {
//                        return ['value' => $option];
//                    }, $options);
//
//                    $attribute->options()->createMany($optionsData);
                }

                $attribute->load('options');

                return new AttributeResource($attribute);

            });

        } catch (QueryException $e) {

            Log::error("Database error while storing attribute: " . $e->getMessage());

            return response()->json(
                [
                    'message' => 'error is happend in database.',
                    'error' => config('app.debug') ? $e->getMessage() : null
                ], 500);

        } catch (\Exception $e) {

            Log::error("Unexpected error while storing attribute: " . $e->getMessage());

            return response()->json(
                [
                    'message' => 'unexpected error while storing attribute',
                    'error' => config('app.debug') ? $e->getMessage() : null
                ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Attribute $attribute)
    {
        return new AttributeResource($attribute);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Attribute $attribute)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAttributeRequest $request, Attribute $attribute)
    {
        try {

            return DB::transaction(function () use ($request, $attribute) {

                $validatedData = $request->validated();
                $options = $validatedData['options'] ?? null;
                unset($validatedData['options']);

                //===step1
                $attribute->update($validatedData);

                //===step2
                if ($attribute->type === 'select' && $options) {

                    AttributeOption::where(['attribute_id' => $attribute->id])->delete();

                    $optionsData = array_map(function ($option) use ($attribute) {
                        return [
                            'attribute_id' => $attribute->id,
                            'value' => $option,
                            'created_at' => now(),
                            'updated_at' => now()
                        ];
                    }, $options);

                    DB::table('attribute_options')->insert($optionsData);

                    //==way2
//                    $optionsData = array_map(function ($option) {
//                        return ['value' => $option];
//                    }, $options);
//
//                    $attribute->options()->createMany($optionsData);
                }

                $attribute->load('options');

                return new AttributeResource($attribute);

            });

        } catch (QueryException $e) {

            Log::error("Database error while storing product: " . $e->getMessage());

            return response()->json(
                [
                    'message' => 'error is happend in database.',
                    'error' => config('app.debug') ? $e->getMessage() : null
                ], 500);

        } catch (\Exception $e) {

            Log::error("Unexpected error while updating attribute: " . $e->getMessage());

            return response()->json(
                [
                    'message' => 'unexpected error while updating product',
                    'error' => config('app.debug') ? $e->getMessage() : null
                ], 500);
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Attribute $attribute)
    {
        return DB::transaction(function () use ($attribute) {

            //step1:delete row
            $attribute->delete();

            //step2:delete value

            //===
            return response()->json(
                [
                    'message' => 'attribute is deleted',
                    'error' => ''
                ], 200);
        });
    }
}
