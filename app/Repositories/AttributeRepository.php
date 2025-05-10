<?php


namespace App\Repositories;


use App\Models\Attribute;
use App\Models\AttributeOption;
use Illuminate\Support\Facades\DB;

class AttributeRepository
{
    public function all2()
    {
        return Attribute::query()->orderBy('name')->get();
    }

    public function all()
    {
        return Attribute::with('options')->paginate(15);
    }

    public function create(array $data)
    {
        return Attribute::create($data);
    }

    public function destroy(Attribute $attribute)
    {
        $attribute->delete();
    }

    public function createOption(array $optionsData)
    {
        DB::table('attribute_options')->insert($optionsData);
    }

    public function destroyOption(array $codition)
    {
        AttributeOption::where($codition)->delete();
    }
}
