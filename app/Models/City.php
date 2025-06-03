<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'postal_code', 'population', 'department_id', 'latitude', 'longitude'];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}