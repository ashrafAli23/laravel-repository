<?php

namespace Laravel\Repository\Eloquent;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Arr;
use Laravel\Repository\IRepository;

abstract class BaseRepository implements IRepository
{
    protected Model $model;


    protected Model $originalModel;

    public function __construct(Model $model)
    {
        $this->model = $model;
        $this->originalModel = $model;
    }

    public function getModel(): Model
    {
        return $this->model;
    }

    public function setModel(Model $model): self
    {
        $this->model = $model;

        return $this;
    }

    public function getTable(): string
    {
        return $this->model->getTable();
    }

    public function findById(int $id, array $with = [])
    {
        $data = $this->make($with)->where('id', $id);

        return $this->applyBeforeExecuteQuery($data, true)->first();
    }

    public function make(array $with = [])
    {
        if (!empty($with)) {
            $this->model = $this->model->with($with);
        }

        return $this->model;
    }

    public function applyBeforeExecuteQuery($data, bool $isSingle = false)
    {
        $this->resetModel();

        return $data;
    }

    /**
     * @return self
     */
    public function resetModel(): self
    {
        $this->model = new $this->originalModel();

        return $this;
    }

    /**
     * @param int $id
     * @param array $with
     * @return void
     */
    public function findOrFail($id, array $with = [])
    {
        $data = $this->make($with)->where('id', $id);

        $result = $this->applyBeforeExecuteQuery($data, true)->first();

        if (!empty($result)) {
            return $result;
        }

        throw (new ModelNotFoundException())->setModel(get_class($this->originalModel), $id);
    }

    /**
     * @param array $with
     * @return void
     */
    public function all(array $with = [])
    {
        $data = $this->make($with);

        return $this->applyBeforeExecuteQuery($data)->get();
    }


    public function pluck(string $column, $key = null, array $condition = []): array
    {
        $this->applyConditions($condition);

        $select = [$column];
        if (!empty($key)) {
            $select = [$column, $key];
        }

        $data = $this->model->select($select);

        return $this->applyBeforeExecuteQuery($data)->pluck($column, $key)->all();
    }

    /**
     * @param array $condition
     * @param array $with
     * @param array $select
     * @return array|null
     */
    public function allBy(array $condition, array $with = [], array $select = ['*']): ?array
    {
        $this->applyConditions($condition);

        $data = $this->make($with)->select($select);

        return $this->applyBeforeExecuteQuery($data)->get();
    }

    /**
     * @param array $where
     * @param null|Eloquent $model
     */
    private function applyConditions(array $where, &$model = null)
    {
        if (!$model) {
            $newModel = $this->model;
        } else {
            $newModel = $model;
        }

        foreach ($where as $field => $value) {
            if ($value instanceof Closure) {
                $newModel = $value($newModel);

                continue;
            }

            if (is_array($value)) {
                [$field, $condition, $val] = $value;
                switch (strtoupper($condition)) {
                    case 'IN':
                        $newModel = $newModel->whereIn($field, $val);

                        break;
                    case 'NOT_IN':
                        $newModel = $newModel->whereNotIn($field, $val);

                        break;
                    default:
                        $newModel = $newModel->where($field, $condition, $val);

                        break;
                }
            } else {
                $newModel = $newModel->where($field, $value);
            }
        }

        if (!$model) {
            $this->model = $newModel;
        } else {
            $model = $newModel;
        }
    }

    public function create(array $data)
    {
        $data = $this->model->create($data);

        $this->resetModel();

        return $data;
    }

    public function createOrUpdate($data, array $condition = [])
    {
        /**
         * @var Model $item
         */
        if (is_array($data)) {
            if (empty($condition)) {
                $item = new $this->model();
            } else {
                $item = $this->getFirstBy($condition);
            }

            if (empty($item)) {
                $item = new $this->model();
            }

            $item = $item->fill($data);
        } elseif ($data instanceof Model) {
            $item = $data;
        } else {
            return false;
        }

        $this->resetModel();

        if ($item->save()) {
            return $item;
        }

        return false;
    }

    public function getFirstBy(array $condition = [], array $select = ['*'], array $with = [])
    {
        $this->make($with);

        $this->applyConditions($condition);

        if (!empty($select)) {
            $data = $this->model->select($select);
        } else {
            $data = $this->model->select('*');
        }

        return $this->applyBeforeExecuteQuery($data, true)->first();
    }

    public function delete(Model $model): bool
    {
        return $model->delete();
    }

    public function firstOrCreate(array $data, array $with = [])
    {
        $data = $this->model->firstOrCreate($data, $with);

        $this->resetModel();

        return $data;
    }

    public function update(array $condition, array $data)
    {
        $this->applyConditions($condition);

        $data = $this->model->update($data);

        $this->resetModel();

        return $data;
    }


    public function select(array $select = ['*'], array $condition = [])
    {
        $this->applyConditions($condition);

        $data = $this->model->select($select);

        return $this->applyBeforeExecuteQuery($data);
    }

    public function deleteBy(array $condition = [])
    {
        $this->applyConditions($condition);

        $data = $this->model->get();

        if (empty($data)) {
            return false;
        }

        foreach ($data as $item) {
            $item->delete();
        }

        $this->resetModel();

        return true;
    }

    public function count(array $condition = []): int
    {
        $this->applyConditions($condition);

        $data = $this->model->count();

        $this->resetModel();

        return $data;
    }


    public function getByWhereIn($column, array $value = [], array $args = [])
    {
        $data = $this->model->whereIn($column, $value);

        if (!empty(Arr::get($args, 'where'))) {
            $this->applyConditions($args['where']);
        }

        $data = $this->applyBeforeExecuteQuery($data);

        if (!empty(Arr::get($args, 'paginate'))) {
            return $data->paginate((int)$args['paginate']);
        } elseif (!empty(Arr::get($args, 'limit'))) {
            return $data->limit((int)$args['limit']);
        }

        return $data->get();
    }

    public function advancedGet(array $params = [])
    {
        $params = array_merge([
            'condition' => [],
            'order_by' => [],
            'take' => null,
            'paginate' => [
                'per_page' => null,
                'current_paged' => 1,
            ],
            'select' => ['*'],
            'with' => [],
            'withCount' => [],
            'withAvg' => [],
        ], $params);

        $this->applyConditions($params['condition']);

        $data = $this->model;

        if ($params['select']) {
            $data = $data->select($params['select']);
        }

        foreach ($params['order_by'] as $column => $direction) {
            if (!in_array(strtolower($direction), ['asc', 'desc'])) {
                continue;
            }

            if ($direction !== null) {
                $data = $data->orderBy($column, $direction);
            }
        }

        if (!empty($params['with'])) {
            $data = $data->with($params['with']);
        }

        if (!empty($params['withCount'])) {
            $data = $data->withCount($params['withCount']);
        }

        if (!empty($params['withAvg'])) {
            $data = $data->withAvg($params['withAvg'][0], $params['withAvg'][1]);
        }

        if ($params['take'] == 1) {
            $result = $this->applyBeforeExecuteQuery($data, true)->first();
        } elseif ($params['take']) {
            $result = $this->applyBeforeExecuteQuery($data)->take((int)$params['take'])->get();
        } elseif ($params['paginate']['per_page']) {
            $paginateType = 'paginate';
            if ($params['paginate']['per_page'] > 100) {
                $params['paginate']['per_page'] = 100;
            }

            // if (Arr::get($params, 'paginate.type') && method_exists(
            //     $data,
            //     Arr::get($params, 'paginate.type')
            // )) {
            //     $paginateType = Arr::get($params, 'paginate.type');
            // }

            $result = $this->applyBeforeExecuteQuery($data)
                ->$paginateType(
                    (int)Arr::get($params, 'paginate.per_page', 10),
                    [$this->originalModel->getTable() . '.' . $this->originalModel->getKeyName()],
                    'page',
                    (int)Arr::get($params, 'paginate.current_paged', 1)
                );
        } else {
            $result = $this->applyBeforeExecuteQuery($data)->get();
        }

        return $result;
    }

    public function forceDelete(array $condition = [])
    {
        $this->applyConditions($condition);

        $item = $this->model->withTrashed()->first();
        if (!empty($item)) {
            $item->forceDelete();
        }

        $this->resetModel();
    }


    public function restoreBy(array $condition = [])
    {
        $this->applyConditions($condition);

        $item = $this->model->withTrashed()->first();
        if (!empty($item)) {
            $item->restore();
        }

        $this->resetModel();
    }


    public function getFirstByWithTrash(array $condition = [], array $select = [])
    {
        $this->applyConditions($condition);

        $query = $this->model->withTrashed();

        if (!empty($select)) {
            return $query->select($select)->first();
        }

        return $this->applyBeforeExecuteQuery($query, true)->first();
    }


    public function insert(array $data): bool
    {
        return $this->model->insert($data);
    }


    public function firstOrNew(array $condition)
    {
        $this->applyConditions($condition);

        $result = $this->model->first() ?: new $this->originalModel();

        $this->resetModel();

        return $result;
    }
}
