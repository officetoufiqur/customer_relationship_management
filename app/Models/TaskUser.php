<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskUser extends Model
{
     use HasFactory;

    protected $fillable = [
        'task_id',
        'assigned_to',
        'delay_reason',
        'transferred_to',
        'transferred_note'
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function transferredTo()
    {
        return $this->belongsTo(User::class, 'transferred_to');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
