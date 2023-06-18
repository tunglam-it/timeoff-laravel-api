<?php

namespace App\Repositories;

use App\Repositories\RepositoryInterface;

abstract class BaseRepository implements RepositoryInterface
{
    protected $model;

    public function __construct()
    {
        $this->setModel();
    }

    /**
     * Get model
     * @return mixed
     */
    abstract public function getModel();

    /**
     * Set Model
     * @return mixed
     */
    public function setModel()
    {
        $this->model = app()->make(($this->getModel()));
    }

    /**
     * Get all data
     * @return mixed
     */
    public function getAll()
    {
        return $this->model->all();
    }

    /**
     * Find by id
     * @param $id
     * @return mixed
     */
    public function find($id)
    {
        $result = $this->model->find($id);
        return $result;
    }

    /**
     * Insert data
     * @param array $attributes
     * @return mixed
     */
    public function create($attributes = [])
    {
        return $this->model->create($attributes);
    }

    /**
     * Update data
     * @param $id
     * @param array $attributes
     * @return mixed
     */
    public function update($id, $attributes = [])
    {
        $result = $this->find($id);
        if($result){
            $result->update($attributes);
            return $result;
        }
        return false;
    }

    /**
     * Delete data
     * @param $id
     * @return mixed
     */
    public function delete($id)
    {
        $result = $this->find($id);
        if($result){
            $result->delete();
            return true;
        }
        return false;
    }
}

