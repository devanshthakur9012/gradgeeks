<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserEmailTemplate extends Model
{
    protected $fillable = [
        'template_id',
        'user_id',
        'workspace_id',
        'is_active',
    ];

    public function templateName()
    {
        return $this->hasOne('App\Models\EmailTemplate', 'id', 'template_id');
    }
}
