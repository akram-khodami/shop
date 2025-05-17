<?php

namespace App\Services;

use App\Models\Attribute;
use App\Repositories\AttributeRepository;
use Illuminate\Support\Facades\DB;

class AttributeService
{
    protected $attributeRepository;

    public function __construct(AttributeRepository $attributeRepository)
    {
        $this->attributeRepository = $attributeRepository;
    }

    public function allAttributes()
    {
        $attributes = $this->attributeRepository->allWithOptions();

        return $attributes;
    }

    public function store($validatedData)
    {
        return DB::transaction(function () use ($validatedData) {

            // ایجاد ویژگی بدون گزینه‌ها (حذف options از داده‌های ذخیره‌شده)
            $options = $validatedData['options'] ?? null;
            unset($validatedData['options']);

            //===step1
            $attribute = $this->attributeRepository->create($validatedData);

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

                $this->attributeRepository->createOption($optionsData);

            }

            $attribute->load('options');

            return $attribute;

        });
    }

    public function update($validatedData, Attribute $attribute)
    {

        return DB::transaction(function () use ($validatedData, $attribute) {

            $options = $validatedData['options'] ?? null;
            unset($validatedData['options']);

            //===step1
            $attribute->update($validatedData);

            //===step2
            if ($attribute->type === 'select' && $options) {

                $this->attributeRepository->deleteOptions(['attribute_id' => $attribute->id]);

                $optionsData = array_map(function ($option) use ($attribute) {
                    return [
                        'attribute_id' => $attribute->id,
                        'value' => $option,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }, $options);

                $this->attributeRepository->createOption($optionsData);

            }

            $attribute->load('options');

            return $attribute;

        });
    }

    public function destroy(Attribute $attribute)
    {
        return DB::transaction(function () use ($attribute) {

            //step1:delete row
            $this->attributeRepository->delete($attribute);

            //step2:delete value

        });
    }
}
