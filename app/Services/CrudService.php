<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;

class CrudService
{
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function read($id)
    {
        return $this->model->find($id);
    }

    public function update($id, array $data)
    {
        $record = $this->model->find($id);
        if ($record) {
            $record->update($data);
        }
        return $record;
    }

    public function delete($id)
    {
        $record = $this->model->find($id);
        if ($record) {
            $record->delete();
            return true;
        }
        return false;
    }

    public function all()
    {
        return $this->model->all();
    }
}
