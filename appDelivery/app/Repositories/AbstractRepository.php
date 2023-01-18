<?php

namespace App\Repositories;
//typehinting
use Illuminate\Database\Eloquent\Model;

class AbstractRepository {

    protected $model;
    
    public function __construct(Model $model) {
        $this->model = $model;
    }

    public function selectAtributtesRegistersRelationship ($attributes) {
       // Container para guardar o estado
        $this->model = $this->model->with($attributes);
    }

    public function filter($filters) {

        $filters = explode(';', $filters);

        foreach ($filters as $key => $condicao) {
            $c = explode(':', $condicao);
            $this->model = $this->model->where($c[0], $c[1], $c[2]);
        }
    }

    public function selectAttributes($attributes) {

        $this->model = $this->model->select($attributes);
    }

    public function getResult() {
        return $this->model->get();
    }

}