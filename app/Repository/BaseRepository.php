<?php

namespace App\Repository;

use App\Interfaces\BaseRepositoryInterface;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository implements BaseRepositoryInterface
{
    protected Model $model;

    abstract public function getModelName(): string;

    public function __construct()
    {
        $this->makeModel();
    }

    public function save(Model $model): bool
    {
        return $model->save();
    }

    public function makeModel(): void
    {
        $model = app($this->getModelName());

        if (! $model instanceof Model) {
            throw new Exception("Class {$model} must be an instance of Illuminate\Database\Eloquent\Model");
        }

        $this->model = $model;
    }

    public function create(array $data = []): Model
    {
        return $this->model->create($data);
    }

    public function getCollection(array $columns = ['*']): Collection
    {
        return $this->model::get($columns);
    }

    public function getCollectionByIds(array $ids, array $columns = ['*']): Collection
    {
        return $this->model::findMany($ids, $columns);
    }

    public function update(int $id, string $attribute = 'id', array $data = []): bool
    {
        $model = $this->model::where($attribute, '=', $id)->first();

        if (! $model) {
            return false;
        }

        $model->fill($data);

        $updated = $model->save();

        return $updated ? true : false;
    }

    public function delete(array|int $ids): int
    {
        return $this->model::destroy($ids);
    }

    public function findById(int $id, array $columns = ['*'], array $with = []): Model
    {
        return $this->model::with($with)->findOrFail($id, $columns);
    }

    public function findOneByAttribute(string $attribute, string $value, array $columns = ['*']): Model
    {
        return $this->model::where($attribute, '=', $value)->firstOrFail($columns);
    }

    public function findWhere(array $where, array $columns = ['*'], array $with = []): Collection
    {
        return $this->model::select($columns)->where($where)->with($with)->get();
    }

    public function getCollectionWhereIn(string $attribute, array $values, array $columns = ['*']): Collection
    {
        return $this->model::whereIn($attribute, $values)->get($columns);
    }

    public function getCollectionWhereBetween(string $attribute, array $values, array $columns = ['*']): Collection
    {
        return $this->whereBetween($attribute, $values)->get($columns);
    }

    public function count(): int
    {
        return $this->model::count();
    }

    public function load(int $id, array $columns = ['*']): ?Model
    {
        return $this->model->find($id, $columns);
    }

    protected function exception(string $message, int $code = 0): void
    {
        throw new Exception($message, $code);
    }

    public function __call(string $name, array $arguments): mixed
    {
        return call_user_func_array([$this->model, $name], $arguments);
    }
}
