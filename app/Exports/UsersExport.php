<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;


class UsersExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    // public function collection()
    // {
    //      $data = User::get();
    //     foreach($data as $k => $users)
    //     {
    //         unset($users->created_by, $users->is_plan_purchased,$users->active_status, $users->two_factor_secret,
    //             $users->two_factor_recovery_codes,$users->remember_token,

    //             $users->dark_mode,$users->messenger_color, $users->email_verified_at, $users->payment_subscription_id,$users->is_trial_done,
    //             $users->plan,$users->requested_plan,   $users->is_register_trial, $users->interested_plan_id,
    //             $users->password,$users->plan_expire_date,$users->avatar,);

    //         $data[$k]["name"]           = $users->name;
    //         $data[$k]["email"]          = $users->email;
    //         $data[$k]["currant_workspace"]=$users->currant_workspace;
    //         $data[$k]["type"]           = $users->type;
    //     }
    //     return $data;
    // }
    public function collection()
    {
        // $data = User::select(
        //     'users.id',
        //     'users.name',
        //     'users.email',
        //     'workspaces.name as workspace_name', // Include workspace name
        //     'users.type',
        //     'users.created_at',
        //     'users.updated_at'
        // )->leftJoin('workspaces', 'users.currant_workspace', '=', 'workspaces.id')->where('users.type','!=','admin')->get();

        $data = User::select(
            'users.id',
            'users.name',
            'users.email',
            'workspaces.name as workspace_name', // Include workspace name
            'users.type',
            'users.created_at',
            'users.updated_at'
        )->leftJoin('workspaces', 'users.currant_workspace', '=', 'workspaces.id')
            ->join('user_workspaces', 'user_workspaces.user_id', '=', 'users.id')
            ->where('user_workspaces.permission', '=', 'Owner')->distinct()->get();

        return $data;
    }
    public function headings(): array
    {
        return [
            "ID",
            "Name",
            "Email",
            "Current Workspace",
            "Type",
            "Created At",
            "Updated At",
        ];
    }

}
