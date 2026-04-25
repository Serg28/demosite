<?php

namespace App\Interfaces;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

interface BaseRepositoryInterface
{
    public function makeModel(): void;

    public function getCollection(array $columns = ['*']): Collection;

    public function getCollectionByIds(array $ids, array $columns = ['*']): Collection;

    public function create(array $data = []): Model;

    public function update(int $id, string $attribute = 'id', array $data = []): bool;

    public function delete(array|int $ids): int;

    public function findById(int $id, array $columns = ['*'], array $with = []): Model|ModelNotFoundException;

    public function findOneByAttribute(string $attribute, string $value, array $columns = ['*']): Model;

    public function findWhere(array $where, array $columns = ['*'], array $with = []): Collection;

    public function getCollectionWhereIn(string $attribute, array $values, array $columns = ['*']): Collection;

    public function getCollectionWhereBetween(string $attribute, array $values, array $columns = ['*']): Collection;

    public function count(): int;
}
