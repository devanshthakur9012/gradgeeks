<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = [
        'comment',
        'created_by',
        'task_id',
        'user_type'
    ];
    public function user()
    {
        return $this->hasOne('App\Models\User', 'id', 'created_by');
    }
    
    public function client()
    {
        return $this->hasOne('App\Models\Client', 'id', 'created_by');
    }
}
