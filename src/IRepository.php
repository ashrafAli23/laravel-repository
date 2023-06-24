<?php

namespace Laravel\Repository;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

interface IRepository
{
    public function applyBeforeExecuteQuery($data, bool $isSingle = false);

    /**
     * Runtime override of the model.
     *
     * @param Model $model
     * @return self
     */
    public function setModel(Model $model): self;

    /**
     * Get empty model.
     *
     * @return Model
     */
    public function getModel(): Model;

    /**
     * Get table name.
     *
     * @return string
     */
    public function getTable(): string;

    /**
     * Make a new instance of the entity to query on.
     *
     * @param array $with
     */
    public function make(array $with = []);

    /**
     * Find a single entity by key value.
     *
     * @param array $condition
     * @param array $select
     * @param array $with
     * @return mixed
     */
    public function getFirstBy(array $condition = [], array $select = [], array $with = []);

    /**
     * Retrieve model by id regardless of status.
     *
     * @param int $id
     * @param array $with
     * @return mixed
     */
    public function findById(int $id, array $with = []);

    /**
     * @param int $id
     * @param array $with
     * @return mixed
     */
    public function findOrFail(int $id, array $with = []);

    /**
     * @param string $column
     * @param mixed $key
     * @param array $condition
     * @return array
     */
    public function pluck(string $column, $key = null, array $condition = []);

    /**
     * Get all models.
     *
     * @param array $with Eager load related models
     * @return Collection
     */
    public function all(array $with = []);

    /**
     * Get all models by key/value.
     *
     * @param array $condition
     * @param array $with
     * @param array $select
     *
     */
    public function allBy(array $condition, array $with = [], array $select = ['*']);

    /**
     * @param array $data
     * @return mixed
     */
    public function create(array $data);

    /**
     * Create a new model.
     *
     * @param Model|array $data
     * @param array $condition
     * @return false|Model
     */
    public function createOrUpdate($data, array $condition = []);

    /**
     * Delete model.
     *
     * @param Model $model
     * @return bool
     * @throws Exception
     */
    public function delete(Model $model): bool;

    /**
     * @param array $data
     * @param array $with
     * @return mixed
     */
    public function firstOrCreate(array $data, array $with = []);

    /**
     * @param array $condition
     * @param array $data
     * @return mixed
     */
    public function update(array $condition, array $data);

    /**
     * @param array $select
     * @param array $condition
     * @return mixed
     */
    public function select(array $select = ['*'], array $condition = []);

    /**
     * @param array $condition
     * @return mixed
     * @throws Exception
     */
    public function deleteBy(array $condition = []);

    /**
     * @param array $condition
     * @return int
     */
    public function count(array $condition = []): int;

    /**
     * @param $column
     * @param array $value
     * @param array $args
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getByWhereIn($column, array $value = [], array $args = []);

    /**
     * @param array $params
     */
    public function advancedGet(array $params = []);

    /**
     * @param array $condition
     */
    public function forceDelete(array $condition = []);

    /**
     * @param array $condition
     * @return mixed
     */
    public function restoreBy(array $condition = []);

    /**
     * Find a single entity by key value.
     *
     * @param array $condition
     * @param array $select
     * @return mixed
     */
    public function getFirstByWithTrash(array $condition = [], array $select = []);

    /**
     * @param array $data
     * @return bool
     */
    public function insert(array $data): bool;

    /**
     * @param array $condition
     * @return mixed
     */
    public function firstOrNew(array $condition);
}
