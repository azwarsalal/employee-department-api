<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'department_id', 
        'first_name', 
        'last_name', 
        'email', 
        'dob'
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function phones()
    {
        //return $this->hasMany(EmployeePhone::class);
    }

    public function addresses()
    {
       // return $this->hasMany(EmployeeAddress::class);
    }
}
