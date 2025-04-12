<?php

namespace App\Http\Controllers;

use App\Models\ClientProject;
use App\Models\Stage;
use App\Models\Task;
use App\Models\User;
use App\Models\UserProject;
use App\Models\UserWorkspace;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{

    public function __construct()
    {
        $this->middleware('2fa');
    }

    public function landingPage()
    {
        if (!file_exists(storage_path() . "/installed")) {
            header('location:install');
            die;
        }
        $setting = Utility::getAdminPaymentSettings();

        if ($setting['display_landing'] == 'on' && \Schema::hasTable('landing_page_settings')) {

            return view('landingpage::layouts.landingpage');
            // return view('layouts.landing');

        } else {
            return redirect('login');
        }
    }
    public function LoginWithAdmin(Request $request, User $user, $id)
    {
        $user = User::find($id);
        $from = \Auth::user();
        if ($user && auth()->check()) {
            $manager = app('impersonate');
            $manager->take($from, $user);

            return redirect('dashboard');
        }
    }

    public function ExitAdmin(Request $request)
    {
        Auth::user()->leaveImpersonation($request->user());
        return redirect('/home');
    }

    public function index($slug = '')
    {
        $userObj = Auth::user();
        if ($userObj->type == 'admin') {
            $users = User::select('users.*')->join('user_workspaces', 'user_workspaces.user_id', '=', 'users.id')
                ->where('user_workspaces.permission', '=', 'Owner')->distinct()->get();
            return view('users.index', compact('users'));
        }

        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        if ($currentWorkspace) {
            $doneStage = Stage::where('workspace_id', '=', $currentWorkspace->id)->where('complete', '=', '1')->first();
            if ($userObj->getGuard() == 'client') {

                $totalProject = ClientProject::join("projects", "projects.id", "=", "client_projects.project_id")->where("client_id", "=", $userObj->id)->where('projects.workspace', '=', $currentWorkspace->id)->count();
                $totalBugs = ClientProject::join("bug_reports", "bug_reports.project_id", "=", "client_projects.project_id")->join("projects", "projects.id", "=", "client_projects.project_id")->where('projects.workspace', '=', $currentWorkspace->id)->count();
                $totalTask = ClientProject::join("tasks", "tasks.project_id", "=", "client_projects.project_id")->join("projects", "projects.id", "=", "client_projects.project_id")->where('projects.workspace', '=', $currentWorkspace->id)->where("client_id", "=", $userObj->id)->count();
                $completeTask = ClientProject::join("tasks", "tasks.project_id", "=", "client_projects.project_id")->join("projects", "projects.id", "=", "client_projects.project_id")->where('projects.workspace', '=', $currentWorkspace->id)->where("client_id", "=", $userObj->id)->where('tasks.status', '=', $doneStage->id)->count();
                $tasks = Task::select([
                    'tasks.*',
                    'stages.name as status',
                    'stages.complete',
                ])->join("client_projects", "tasks.project_id", "=", "client_projects.project_id")->join("projects", "projects.id", "=", "client_projects.project_id")->join("stages", "stages.id", "=", "tasks.status")->where('projects.workspace', '=', $currentWorkspace->id)->where("client_id", "=", $userObj->id)->orderBy('tasks.id', 'desc')->with('project')->limit(5)->get();
                $totalMembers = UserWorkspace::where('workspace_id', '=', $currentWorkspace->id)->count();

                $projectProcess = ClientProject::join("projects", "projects.id", "=", "client_projects.project_id")->where('projects.workspace', '=', $currentWorkspace->id)->where("client_id", "=", $userObj->id)->groupBy('projects.status')->selectRaw('count(projects.id) as count, projects.status')->pluck('count', 'projects.status');
                $arrProcessPer = [];
                $arrProcessLabel = [];
                $arrProcessClass = [];
                foreach ($projectProcess as $lable => $process) {
                    $arrProcessLabel[] = $lable;
                    if ($totalProject == 0) {
                        $arrProcessPer[] = 0.00;
                    } else {
                        $arrProcessPer[] = round(($process * 100) / $totalProject, 2);
                        $arrProcessClass[] = $lable == 'Ongoing' ? 'rgb(111, 217, 67)' : ($lable == 'Finished' ? 'rgb(255, 162, 29)' : ($lable == 'OnHold' ? 'rgb(255, 58, 110)' : ''));
                    }
                }

                $chartData = app('App\Http\Controllers\ProjectController')->getProjectChart([
                    'workspace_id' => $currentWorkspace->id,
                    'duration' => 'week',
                ]);
                $tasksUsers = Task::orderBy('tasks.id', 'desc')
                    ->join("client_projects", "tasks.project_id", "=", "client_projects.project_id")
                    ->join("projects", "projects.id", "=", "client_projects.project_id")
                    ->join("stages", "stages.id", "=", "tasks.status")
                    ->join("users", function ($join) {
                        $join->on("users.id", "=", \DB::raw("CAST(SUBSTRING_INDEX(tasks.assign_to, ',', 1) AS SIGNED)"))
                            ->orWhere("users.id", "=", \DB::raw("CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(tasks.assign_to, ',', -2), ',', 1) AS SIGNED)"))
                            ->orWhere("users.id", "=", \DB::raw("CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(tasks.assign_to, ',', -1), ',', 1) AS SIGNED)"));
                    })
                    ->where("client_id", "=", $userObj->id)
                    ->where('projects.workspace', '=', $currentWorkspace->id)
                    ->limit(5)
                    ->select(['users.name', 'users.id'])
                    ->distinct()
                    ->get()
                    ->pluck('name', 'id')
                    ->toArray();
                return view('home', compact('currentWorkspace', 'totalProject', 'totalBugs', 'totalTask', 'totalMembers', 'arrProcessLabel', 'arrProcessPer', 'arrProcessClass', 'completeTask', 'tasks', 'chartData', 'tasksUsers'));
            } else {
                $totalProject = UserProject::join("projects", "projects.id", "=", "user_projects.project_id")->where("user_id", "=", $userObj->id)->where('projects.workspace', '=', $currentWorkspace->id)->count();

                if ($currentWorkspace->permission == 'Owner') {
                    $totalBugs = UserProject::join("bug_reports", "bug_reports.project_id", "=", "user_projects.project_id")->join("projects", "projects.id", "=", "user_projects.project_id")->where("user_id", "=", $userObj->id)->where('projects.workspace', '=', $currentWorkspace->id)->count();
                    $totalTask = UserProject::join("tasks", "tasks.project_id", "=", "user_projects.project_id")->join("projects", "projects.id", "=", "user_projects.project_id")->where("user_id", "=", $userObj->id)->where('projects.workspace', '=', $currentWorkspace->id)->count();
                    $completeTask = UserProject::join("tasks", "tasks.project_id", "=", "user_projects.project_id")->join("projects", "projects.id", "=", "user_projects.project_id")->where("user_id", "=", $userObj->id)->where('projects.workspace', '=', $currentWorkspace->id)->where('tasks.status', '=', $doneStage->id)->count();
                    $tasks = Task::select([
                        'tasks.*',
                        'stages.name as status',
                        'stages.complete',
                    ])->join("user_projects", "tasks.project_id", "=", "user_projects.project_id")->join("projects", "projects.id", "=", "user_projects.project_id")->join("stages", "stages.id", "=", "tasks.status")->where("user_id", "=", $userObj->id)->where('projects.workspace', '=', $currentWorkspace->id)->orderBy('tasks.id', 'desc')->with('project')->limit(5)->get();
                } else {
                    $totalBugs = UserProject::join("bug_reports", "bug_reports.project_id", "=", "user_projects.project_id")->join("projects", "projects.id", "=", "user_projects.project_id")->where("user_id", "=", $userObj->id)->where('projects.workspace', '=', $currentWorkspace->id)->where('bug_reports.assign_to', '=', $userObj->id)->count();
                    $totalTask = UserProject::join("tasks", "tasks.project_id", "=", "user_projects.project_id")->join("projects", "projects.id", "=", "user_projects.project_id")->where("user_id", "=", $userObj->id)->where('projects.workspace', '=', $currentWorkspace->id)->whereRaw("find_in_set('" . $userObj->id . "',tasks.assign_to)")->count();
                    $completeTask = UserProject::join("tasks", "tasks.project_id", "=", "user_projects.project_id")->join("projects", "projects.id", "=", "user_projects.project_id")->where("user_id", "=", $userObj->id)->where('projects.workspace', '=', $currentWorkspace->id)->whereRaw("find_in_set('" . $userObj->id . "',tasks.assign_to)")->where('tasks.status', '=', $doneStage->id)->count();
                    $tasks = Task::select([
                        'tasks.*',
                        'stages.name as status',
                        'stages.complete',
                    ])->join("user_projects", "tasks.project_id", "=", "user_projects.project_id")->join("projects", "projects.id", "=", "user_projects.project_id")->join("stages", "stages.id", "=", "tasks.status")->where("user_id", "=", $userObj->id)->where('projects.workspace', '=', $currentWorkspace->id)->whereRaw("find_in_set('" . $userObj->id . "',tasks.assign_to)")->orderBy('tasks.id', 'desc')->with('project')->limit(5)->get();
                }

                $totalMembers = UserWorkspace::where('workspace_id', '=', $currentWorkspace->id)->count();

                $projectProcess = UserProject::join("projects", "projects.id", "=", "user_projects.project_id")->where("user_id", "=", $userObj->id)->where('projects.workspace', '=', $currentWorkspace->id)->groupBy('projects.status')->selectRaw('count(projects.id) as count, projects.status')->pluck('count', 'projects.status');
                $arrProcessPer = [];
                $arrProcessLabel = [];
                $arrProcessClass = [];
                foreach ($projectProcess as $lable => $process) {
                    $arrProcessLabel[] = $lable;
                    if ($totalProject == 0) {
                        $arrProcessPer[] = 0.00;
                    } else {
                        $arrProcessPer[] = round(($process * 100) / $totalProject, 2);
                        $arrProcessClass[] = $lable == 'Ongoing' ? 'rgb(111, 217, 67)' : ($lable == 'Finished' ? 'rgb(255, 162, 29)' : ($lable == 'OnHold' ? 'rgb(255, 58, 110)' : ''));
                    }
                }

                $chartData = app('App\Http\Controllers\ProjectController')->getProjectChart([
                    'workspace_id' => $currentWorkspace->id,
                    'duration' => 'week',
                ]);

                $tasksUsers = Task::orderBy('tasks.id', 'desc')
                    ->join("user_projects", "tasks.project_id", "=", "user_projects.project_id")
                    ->join("projects", "projects.id", "=", "user_projects.project_id")
                    ->join("stages", "stages.id", "=", "tasks.status")
                    ->join("users", function ($join) {
                        $join->on("users.id", "=", \DB::raw("CAST(SUBSTRING_INDEX(tasks.assign_to, ',', 1) AS SIGNED)"))
                            ->orWhere("users.id", "=", \DB::raw("CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(tasks.assign_to, ',', -2), ',', 1) AS SIGNED)"))
                            ->orWhere("users.id", "=", \DB::raw("CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(tasks.assign_to, ',', -1), ',', 1) AS SIGNED)"));
                    })
                    ->where("user_id", "=", $userObj->id)
                    ->where('projects.workspace', '=', $currentWorkspace->id)
                    ->limit(5)
                    ->select(['users.name', 'users.id'])
                    ->distinct()
                    ->get()
                    ->pluck('name', 'id')
                    ->toArray();

                return view('home', compact('currentWorkspace', 'totalProject', 'totalBugs', 'totalTask', 'totalMembers', 'arrProcessLabel', 'arrProcessPer', 'arrProcessClass', 'completeTask', 'tasks', 'chartData', 'tasksUsers'));
            }
        } else {
            return view('home', compact('currentWorkspace'));
        }
    }
}
