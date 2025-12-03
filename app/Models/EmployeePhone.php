<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmployeePhone extends Model
{
    use HasFactory;

    protected $fillable = ['employee_id', 'phone', 'type'];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
