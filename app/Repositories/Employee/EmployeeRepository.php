<?php

namespace App\Repositories\Employee;

use App\Models\Employees;
use App\Repositories\BaseRepository;

class EmployeeRepository extends BaseRepository implements EmployeeRepositoryInterface
{

    /**
     * get model
     * @return mixed
     */
    public function getModel()
    {
        return Employees::class;
    }

    public function filterAdmin($name)
    {
        if ($name) {
            $employees = Employees::whereIn('roles', [1, 2])->where('name', 'like', '%' . $name . '%')->get();
        } else {
            $employees = Employees::whereIn('roles', [1, 2])->get();
        }
        return $employees;
    }
}
