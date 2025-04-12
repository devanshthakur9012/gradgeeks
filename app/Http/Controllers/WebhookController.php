<?php

namespace App\Http\Controllers;

use App\Models\Webhook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\Utility;



class WebhookController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($slug)
    {
        $method = WebHook::method();
        $module = WebHook::module();
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);


        return view('users.create_webhook', compact('method', 'module', 'slug'));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $webhook = Webhook::create(
            [
                'module' => $request->module,
                'url' => $request->url,
                'method' => $request->method,
                'created_by' => Auth::user()->id,
            ]
        );
        return redirect()->back()->with('success', __('webhook Created Successfully'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Webhook  $webhook
     * @return \Illuminate\Http\Response
     */
    public function show(Webhook $webhook)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Webhook  $webhook
     * @return \Illuminate\Http\Response
     */
    public function edit($slug, Webhook $webhook)
    {
        $method = WebHook::method();
        $module = WebHook::module();
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        return view('users.edit_webhook', compact('webhook', 'currentWorkspace', 'method', 'module'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Webhook  $webhook
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $slug, $id)
    {
        $webhook = Webhook::find($id);
        $webhook->module = $request->module;
        $webhook->url = $request->url;
        $webhook->method = $request->method;
        $webhook->save();

        return redirect()->back()->with('success', __('webhook Updated Successfully'));

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Webhook  $webhook
     * @return \Illuminate\Http\Response
     */
    public function destroy($slug, $id)
    {
        $webhook = Webhook::find($id);
        $webhook->delete();
        return redirect()->back()->with('success', __('webhook Deleted Successfully'));
    }
}
