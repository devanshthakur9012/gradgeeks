<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stage extends Model
{
    protected $fillable = ['name', 'color', 'complete', 'workspace_id', 'order'];
    
    public static function getTaskCountsForDate($workspaceId, $projectId, $date)
    {
        return static::leftJoin('tasks', 'stages.id', '=', 'tasks.status')
            ->where('stages.workspace_id', $workspaceId)
            ->whereDate('tasks.updated_at', $date)
            ->when($projectId !== null, function ($query) use ($projectId) {
                return $query->where('tasks.project_id', $projectId);
            })
            ->groupBy('stages.id')
            ->select('stages.id', \DB::raw('count(tasks.id) as task_count'))
            ->get()
            ->pluck('task_count', 'id')
            ->toArray();
    }
}
