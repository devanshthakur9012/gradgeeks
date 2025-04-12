<?php

namespace App\Http\Controllers;

use App\Models\EmailTemplate;
use App\Models\EmailTemplateLang;
use App\Models\UserEmailTemplate;
use App\Models\Utility;
use Illuminate\Http\Request;

class EmailTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $usr = \Auth::user();
        if ($usr->type == 'admin') {
            $EmailTemplates = EmailTemplate::all();
            return view('email_templates.index', compact('EmailTemplates'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


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
     * @param  \App\Models\EmailTemplate  $emailTemplate
     * @return \Illuminate\Http\Response
     */
    public function show(EmailTemplate $emailTemplate)
    {
        $lang = 'en';
        $languages = Utility::languages();
        $emailTemplate = EmailTemplate::where('id', '=', $emailTemplate->id)->first();

        $currEmailTempLang = EmailTemplateLang::where('parent_id', '=', $emailTemplate->id)->where('lang', 'en')->first();
        if (!isset($currEmailTempLang) || empty($currEmailTempLang)) {
            $currEmailTempLang = EmailTemplateLang::where('parent_id', '=', $emailTemplate->id)->where('lang', 'en')->first();
            $currEmailTempLang->lang = $lang;
        }

        return view('email_templates.show', compact('emailTemplate', 'languages', 'currEmailTempLang'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\EmailTemplate  $emailTemplate
     * @return \Illuminate\Http\Response
     */
    public function edit(EmailTemplate $emailTemplate)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\EmailTemplate  $emailTemplate
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = \Validator::make(
            $request->all(),
            [
                'from' => 'required',
                // 'subject' => 'required',
                // 'content' => 'required',
            ]
        );

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();
            return redirect()->back()->with('error', $messages->first());
        }

        $emailTemplate = EmailTemplate::where('id', $id)->first();
        $emailTemplate->from = $request->from;
        $emailTemplate->save();

        // $emailLangTemplate = EmailTemplateLang::where('parent_id', '=', $id)->where('lang', '=', $request->lang)->first();

        // if record not found then create new record else update it.
        // if (empty($emailLangTemplate)) {
        //     $emailLangTemplate = new EmailTemplateLang();
        //     $emailLangTemplate->parent_id = $id;
        //     $emailLangTemplate->lang = $request['lang'];
        //     $emailLangTemplate->subject = $request['subject'];
        //     $emailLangTemplate->content = $request['content'];
        //     $emailLangTemplate->from = $request['from'];
        //     $emailLangTemplate->save();
        // } else {
        //     $emailLangTemplate->subject = $request['subject'];
        //     $emailLangTemplate->content = $request['content'];
        //     $emailLangTemplate->from = $request['from'];

        //     $emailLangTemplate->save();
        // }

        return redirect()->route(
            'manage.email.language',
            [
                $id,
                $request->lang,
            ]
        )->with('success', __('Email Template Detail successfully updated.'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\EmailTemplate  $emailTemplate
     * @return \Illuminate\Http\Response
     */
    public function destroy(EmailTemplate $emailTemplate)
    {
        //
    }

    public function manageEmailLang($id, $lang = 'en')
    {
        $languages = Utility::languages();
        $emailTemplate = EmailTemplate::where('id', '=', $id)->first();
        $currEmailTempLang = EmailTemplateLang::where('parent_id', '=', $id)->where('lang', $lang)->first();

        if (!isset($currEmailTempLang) || empty($currEmailTempLang)) {
            $currEmailTempLang = EmailTemplateLang::where('parent_id', '=', $id)->where('lang', 'en')->first();
            $currEmailTempLang->lang = $lang;
        }

        return view('email_templates.show', compact('emailTemplate', 'languages', 'currEmailTempLang'));
    }

    // Used For Store Email Template Language Wise
    public function storeEmailLang(Request $request, $id)
    {
        $validator = \Validator::make(
            $request->all(),
            [
                'subject' => 'required',
                'content' => 'required',
            ]
        );

        if ($validator->fails()) {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }

        $emailLangTemplate = EmailTemplateLang::where('parent_id', '=', $id)->where('lang', '=', $request->lang)->first();
        // if record not found then create new record else update it.
        if (empty($emailLangTemplate)) {
            $emailLangTemplate = new EmailTemplateLang();
            $emailLangTemplate->parent_id = $id;
            $emailLangTemplate->lang = $request['lang'];
            $emailLangTemplate->subject = $request['subject'];
            $emailLangTemplate->content = $request['content'];
            $emailLangTemplate->save();
        } else {
            $emailLangTemplate->subject = $request['subject'];
            $emailLangTemplate->content = $request['content'];
            $emailLangTemplate->save();
        }

        return redirect()->route(
            'manage.email.language',
            [
                $id,
                $request->lang,
            ]
        )->with('success', __('Email Template Detail successfully updated.'));

    }

    // Used For Update Status Company Wise.
    public function updateStatus(Request $request, $slug)
    {
        $usr = \Auth::user();
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $user_emails = UserEmailTemplate::where('workspace_id', '=', $currentWorkspace->id)->where('user_id', '=', $usr->id)->get();
        if (!$user_emails) {
            $user_emails = UserEmailTemplate::create(
                [
                    'template_id' => 1,
                    'user_id' => $usr->id,
                    'workspace_id' => $currentWorkspace->id,
                    'is_active' => 0,
                ]
            );

            $user_emails = UserEmailTemplate::create(
                [
                    'template_id' => 2,
                    'user_id' => $usr->id,
                    'workspace_id' => $currentWorkspace->id,
                    'is_active' => 0,
                ]
            );

            $user_emails = UserEmailTemplate::create(
                [
                    'template_id' => 3,
                    'user_id' => $usr->id,
                    'workspace_id' => $currentWorkspace->id,
                    'is_active' => 0,
                ]
            );

            $user_emails = UserEmailTemplate::create(
                [
                    'template_id' => 4,
                    'user_id' => $usr->id,
                    'workspace_id' => $currentWorkspace->id,
                    'is_active' => 0,
                ]
            );

        }
        foreach ($user_emails as $user_email) {
            if (isset($request->New_Client) && $user_email->id == $request->New_Client) {
                $user_email->is_active = 1;
            } elseif (isset($request->User_Invited) && $user_email->id == $request->User_Invited) {
                $user_email->is_active = 1;
            } elseif (isset($request->Project_Assigned) && $user_email->id == $request->Project_Assigned) {
                $user_email->is_active = 1;
            } elseif (isset($request->Contract_Shared) && $user_email->id == $request->Contract_Shared) {
                $user_email->is_active = 1;
            } else {
                $user_email->is_active = 0;

            }
            $user_email->save();
        }
        return redirect()->back()->with('success', __('Status successfully updated!'));
    }
}
