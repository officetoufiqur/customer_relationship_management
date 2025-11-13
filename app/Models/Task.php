<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
     use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'attachment',
        'deadline',
        'status'
    ];

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function taskUsers()
    {
        return $this->hasMany(TaskUser::class);
    }
}
