<?php


namespace App\Repositories;


use App\Models\Attribute;
use App\Models\AttributeOption;
use Illuminate\Pagination\LengthAwarePaginator;

class AttributeRepository extends BaseRepository
{
    public function model(): string
    {
        return Attribute::class;
    }

    public function allWithOptions(): LengthAwarePaginator
    {
        return $this->newQuery()->with('options')->paginate(15);
    }

    public function createOption(array $optionsData): void
    {
        AttributeOption::insert($optionsData);
    }


    public function deleteOptions(array $coditions): int
    {
        return AttributeOption::where($coditions)->delete();
    }
}
