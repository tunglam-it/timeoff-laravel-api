<?php

namespace App\Repositories\Leave;

use App\Models\Leaves;
use App\Repositories\BaseRepository;

class LeaveRepository extends BaseRepository implements LeaveRepositoryInterface
{

    /**
     * get model
     * @return mixed
     */
    public function getModel()
    {
        return Leaves::class;
    }

    /**
     * get blog
     * @return mixed
     */
    public function getBlog()
    {
        return $this->model->select('title')->get();
    }

}
