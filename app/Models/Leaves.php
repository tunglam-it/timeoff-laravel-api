<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Leaves extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'employee_id',
        'estimate',
        'type',
        'start_date',
        'end_date',
        'reason',
        'comment',
        'status'
    ];

    public function employees(){
        return $this->belongsTo(Employees::class, 'employee_id','id');
    }
}
