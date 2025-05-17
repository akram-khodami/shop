<?php


namespace App\Repositories;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class BaseRepository
{
    protected Model $model;

    public function __construct()
    {
        $this->model = app($this->model());
    }

    abstract protected function model(): string;

    public function all($columns = ['*']): Collection
    {
        return $this->model->orderBy('id', 'desc')->get($columns);
    }

    public function paginate($limit = 10): LengthAwarePaginator
    {
        return $this->model->orderBy('id', 'desc')->paginate($limit);
    }

    public function getBy($col, $value, $limit = 15)
    {
        return $this->model->where($col, $value)->limit($limit)->get();
    }

    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    public function firstOrCreate(array $search_data, array $data)
    {
        return $this->model->firstOrCreate($search_data, $data);
    }

    public function find($id): ?Model
    {
        return $this->model->find($id);
    }

    public function update($model, array $data): bool
    {
        return $model->update($data);
    }

    public function delete($model): bool
    {
        return $model->delete();
    }

    public function exists($id): bool
    {
        return $this->model->newQuery()->where('id', $id)->exists();
    }

    /**
     * @param array $conditions
     * example1: $conditions=[['column1', 'operator1', 'value1'],['column2', 'operator2', 'value2'],...],
     * example2: $conditions=['column1'=>'value1','column2'=>'value2',...]
     * @param array $data
     * @return mixed
     */
    public function updateWhere(array $conditions, array $data)
    {
        $query = $this->model->newQuery();

        foreach ($conditions as $key => $value) {

            if (is_array($value)) {

                $query->where(...$value);

            } else {

                $query->where($key, $value);

            }

        }

        return $query->update($data);

    }

    protected function newQuery(): Builder
    {
        return $this->model->newQuery();//make new object
    }
}
