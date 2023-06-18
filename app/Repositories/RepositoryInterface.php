<?php

namespace App\Repositories;

interface RepositoryInterface
{
    /**
     * Get all data
     * @return mixed
     */
    public function getAll();

    /**
     * find data by id
     * @param $id
     * @return mixed
     */
    public function find($id);

    /**
     * Insert data
     * @param array $attributes
     * @return mixed
     */
    public function create($attributes = []);

    /**
     * Update data
     * @param $id
     * @param array $attributes
     * @return mixed
     */
    public function update($id, $attributes = []);

    /**
     * Delete data
     * @param $id
     * @return mixed
     */
    public function delete($id);
}

