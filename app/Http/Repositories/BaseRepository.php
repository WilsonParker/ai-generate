<?php

namespace App\Http\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class BaseRepository implements RepositoryContract
{

    /**
     * @param string $model
     * @example AIGenerate\Models\User\User::class
     */
    public function __construct(protected string $model = '') {}

    public function all(): Collection
    {
        return $this->model::all();
    }

    public function show($id, array $select = ['*'], array $with = []): ?Model
    {
        return $this->model::select($select)->with($with)->find($id);
    }

    public function query()
    {
        return $this->model::query();
    }

    public function create(array $data): Model
    {
        return $this->model::create($data);
    }

    public function first(array $order = []): Model
    {
        return $this->model::when($order, function ($query) use ($order) {
            foreach ($order as $key => $value) {
                $query->orderBy($key, $value);
            }
        })->first();
    }

    public function firstOrCreate(array $data): Model
    {
        return $this->model::firstOrCreate($data);
    }

    public function update(Model|string $id, array $data): Model
    {
        if ($id instanceof Model)
            $model = $id;
        else
            $model = $this->model::findOrFail($id);
        foreach ($data as $key => $value) {
            $model->$key = $value;
        }
        $model->save();
        return $model;
    }

    public function delete($id): bool
    {
        $model = $this->showOrFail($id);
        return $model->delete();
    }

    public function showOrFail($id, array $select = ['*'], array $with = []): Model
    {
        return $this->model::select($select)->with($with)->findOrFail($id);
    }


}
