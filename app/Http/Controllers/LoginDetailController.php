<?php

namespace App\Http\Controllers;

use App\Models\LoginDetail;
use Illuminate\Http\Request;
use App\Models\Utility;
use App\Models\Workspace;
use App\Models\User;
use App\Models\Client;
use App\Models\UserWorkspace;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class LoginDetailController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $slug = '')
    {
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $user_logs = LoginDetail::orderByDesc('date')->with('user', 'client')->get();

        if (isset($request) && $request->user && $request->user != '') {

            $users = explode(',', $request->user);

            $user_logs = LoginDetail::where('user_id', '=', $users[0])->where('type', '=', $users[1])->with('user', 'client')->get();
        }

        if (isset($request) && $request->start_date) {

            $start_date = Carbon::parse($request->start_date);
            $end_date = Carbon::parse($request->end_date);
            $user_logs = $user_logs->where('date', '>=', $start_date->format('Y-m-d'))->where('date', '<=', $end_date->format('Y-m-d'));
        }

        foreach ($user_logs as $user_log) {

            if ($user_log->type == 'user') {
                // $user = User::find($user_log->user_id);
                $user = $user_log->user;
            } else {
                // $user = Client::find($user_log->user_id);
                $user = $user_log->client;
            }
            $data = json_decode($user_log->details);
            $user_log->name = $user->name;
            $user_log->email = $user->email;
            $user_log->role = $user_log->type;
            $user_log->ip = $user_log->ip;
            $user_log->date = $user_log->date;
            $user_log->country = !empty($data->country) ? $data->country : '';
            $user_log->device_type = !empty($data->device_type) ? $data->device_type : '';
            $user_log->os_name = !empty($data->os_name) ? $data->os_name : '';
        }

        $users = User::select('users.*', 'user_workspaces.permission', 'user_workspaces.is_active')->join('user_workspaces', 'user_workspaces.user_id', '=', 'users.id');
        $users->where('user_workspaces.workspace_id', '=', $currentWorkspace->id);
        $users->where('user_workspaces.permission', '=', 'Member');
        $users = $users->get();

        $clients = Client::select(['clients.*', 'client_workspaces.is_active',])->join('client_workspaces', 'client_workspaces.client_id', '=', 'clients.id');
        $clients->where('client_workspaces.workspace_id', '=', $currentWorkspace->id);
        $clients = $clients->get();

        return view('user_logs.index', compact('user_logs', 'currentWorkspace', 'users', 'clients'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\LoginDetail  $loginDetail
     * @return \Illuminate\Http\Response
     */
    public function show($slug, $id)
    {
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $user_log = LoginDetail::find($id);
        $user = json_decode($user_log->details);

        return view('user_logs.show', compact('currentWorkspace', 'user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\LoginDetail  $loginDetail
     * @return \Illuminate\Http\Response
     */
    public function edit(LoginDetail $loginDetail)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\LoginDetail  $loginDetail
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, LoginDetail $loginDetail)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\LoginDetail  $loginDetail
     * @return \Illuminate\Http\Response
     */
    public function destroy($slug, $id)
    {
        $user_log = LoginDetail::find($id);
        $objUser = Auth::user();
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);

        if ($currentWorkspace->created_by == $objUser->id) {
            $user_log->delete();
            return redirect()->back()->with('success', __('User Log Deleted Successfully.!'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
