<?php

namespace App\Http\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface RepositoryContract
{
    public function all(): Collection;

    public function show($id, array $select = ['*'], array $with = []): ?Model;

    public function showOrFail($id, array $select = ['*'], array $with = []): Model;

    public function create(array $data): Model;

    public function firstOrCreate(array $data): Model;

    public function update(Model|string $id, array $data): Model;

    public function delete($id): bool;
}
