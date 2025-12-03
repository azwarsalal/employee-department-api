<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmployeeAddress extends Model
{
     use HasFactory;

    protected $fillable = [
        'employee_id', 
        'address_line1', 
        'address_line2', 
        'city', 
        'state', 
        'country', 
        'zip_code'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
