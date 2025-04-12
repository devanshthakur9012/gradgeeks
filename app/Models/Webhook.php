<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Webhook extends Model
{
    protected $fillable = [
        'module',
        'url',
        'method',
        'created_by',
    ];
    public static function module()
    {
        $webmodule = [
            'New Project' => 'New Project',
            'New Task' => 'New Task',
            'Task Stage Updated' => 'Task Stage Updated',
            'New Milestone' => 'New Milestone',
            'Milestone Status Updated' => 'Milestone Status Updated',
            'New Task Comment' => 'New Task Comment',
            'New Invoice' => 'New Invoice',
            'Invoice Status Updated' => 'Invoice Status Updated',

        ];
        return $webmodule;
    }
    public static function method()
    {
        $method = [
            'POST' => 'POST',
            'GET' => 'GET',
        ];
        return $method;
    }
}









