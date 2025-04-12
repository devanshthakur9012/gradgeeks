<?php

namespace App\Http\Controllers;

use App\Models\BugReport;
use App\Models\BugStage;
use App\Models\Project;
use App\Models\Stage;
use App\Models\Task;
use App\Models\Tax;
// use Artisan;
use Illuminate\Support\Facades\Artisan;
use App\Models\UserProject;
use App\Models\UserWorkspace;
use App\Models\Utility;
use App\Models\Workspace;
use App\Models\EmailTemplate;
use App\Models\Languages;
use App\Models\Mail\EmailTest;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\UserEmailTemplate;

class WorkspaceController extends Controller
{

    public function store(Request $request, $slug)
    {
        $objUser = Auth::user();

        $validator = \Validator::make(
            $request->all(),
            [
                'name' => 'required|unique:workspaces',
            ]
        );
        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->first());
        }
        $parentWorksace = Utility::getWorkspaceBySlug($slug);

        $objWorkspace = Workspace::create(
            [
                'created_by' => $objUser->id,
                'name' => $request->name,
                'theme_color' => $parentWorksace['theme_color'],
                'color_flag' => $parentWorksace['color_flag'],
                'currency_code' => 'USD',
                'paypal_mode' => 'sandbox',
            ]
        );

        UserWorkspace::create(
            [
                'user_id' => $objUser->id,
                'workspace_id' => $objWorkspace->id,
                'permission' => 'Owner',
            ]
        );

        $emailTemplate = EmailTemplate::all();
        foreach ($emailTemplate as $emailtemp) {
            UserEmailTemplate::create(
                [
                    'template_id' => $emailtemp->id,
                    'user_id' => $objUser->id,
                    'workspace_id' => $objWorkspace->id,
                    'is_active' => 0,
                ]
            );
        }

        $objUser->currant_workspace = $objWorkspace->id;
        $objUser->save();

        return redirect()->route('home', $objWorkspace->slug)->with('success', __('Workspace Created Successfully!'));
    }


    //    public function destroy($workspaceID)
    // {
    //     $objUser   = Auth::user();
    //     $workspace = Workspace::find($workspaceID);
    //     $all_workspaces = Workspace::get();


    //     if($workspace->created_by == $objUser->id)
    //     {

    //       if(count($all_workspaces) > 1)
    //        {
    //         UserWorkspace::where('workspace_id', '=', $workspaceID)->delete();
    //         Stage::where('workspace_id', '=', $workspaceID)->delete();
    //         $workspace->delete();
    //         $work_space = Workspace::first();

    //          UserWorkspace::create(
    //                 [
    //                     'user_id' => $objUser->id,
    //                     'workspace_id' => $work_space->id,
    //                     'permission' => 'Owner',
    //                 ]
    //             );
    //          $objUser->currant_workspace = $work_space->id;
    //          $objUser->save();

    //        }
    //     else
    //     {
    //       return redirect()->back()->with('error', __("You can't delete Workspace!"));
    //     }
    //         return redirect()->route('home')->with('success', __('Workspace Deleted Successfully!'));
    //     }
    //     else
    //     {
    //         return redirect()->route('home')->with('error', __("You can't delete Workspace!"));
    //     }
    // }

    // Old
    // public function destroy($workspaceID)
    // {
    //     $objUser   = Auth::user();
    //     $workspace = Workspace::find($workspaceID);
    //     $all_workspaces = Workspace::get();


    //     if ($workspace->created_by == $objUser->id) {
    //         if (count($all_workspaces) > 1) {

    //             UserWorkspace::where('workspace_id', '=', $workspaceID)->delete();
    //             Stage::where('workspace_id', '=', $workspaceID)->delete();
    //             $workspace->delete();
    //             $work_space = Workspace::first();

    //             $objUser->currant_workspace = $work_space->id;
    //             $objUser->save();
    //         } else {
    //             return redirect()->back()->with('error', __("You can't delete Workspace!"));
    //         }
    //         return redirect()->route('home')->with('success', __('Workspace Deleted Successfully!'));
    //     } else {
    //         return redirect()->route('home')->with('error', __("You can't delete Workspace!"));
    //     }
    // }

    // public function destroy($workspaceID)
    // {
    //     $objUser   = Auth::user();
    //     $workspace = Workspace::find($workspaceID);
    //     $all_workspaces = Workspace::where('created_by','=',Auth::user()->id)->get();
    //     if ($workspace->created_by == $objUser->id) {

    //         if (count($all_workspaces) > 1) {

    //             UserWorkspace::where('workspace_id', '=', $workspaceID)->delete();
    //             Stage::where('workspace_id', '=', $workspaceID)->delete();
    //             $workspace->delete();
    //             $work_space = Workspace::where('created_by','=',Auth::user()->id)->first();

    //             $objUser->currant_workspace = $work_space->id;
    //             $objUser->save();
    //         } else {
    //             return redirect()->back()->with('error', __("You can't delete Workspace!"));
    //         }
    //         return redirect()->route('home')->with('success', __('Workspace Deleted Successfully!'));
    //     } else {
    //         return redirect()->route('home')->with('error', __("You can't delete Workspace!"));
    //     }
    // }

    public function destroy($workspaceID)
    {
        $objUser = Auth::user();
        $workspace = Workspace::find($workspaceID);
        $all_workspaces = Workspace::where('created_by', '=', Auth::user()->id)->get();
        if ($workspace->created_by == $objUser->id) {

            if (count($all_workspaces) > 1) {

                $currentWorkspaceUsers = UserWorkspace::where([
                    ['workspace_id', '=', $workspaceID],
                    ['permission', '=', 'Member'],
                ])
                    ->get();

                if (count($currentWorkspaceUsers) >= 1) {
                    return redirect()->back()->with('error', __('Please Remove the Users Before Delete the Workspace !!!'));
                } else {
                    UserWorkspace::where('workspace_id', '=', $workspaceID)->delete();
                    Stage::where('workspace_id', '=', $workspaceID)->delete();
                    $workspace->delete();
                    $work_space = Workspace::where('created_by', '=', Auth::user()->id)->first();

                    $objUser->currant_workspace = $work_space->id;
                    $objUser->save();
                }
            } else {
                return redirect()->route('home')->with('error', __('You Can Not Delete the Workspace!!'));
                //return redirect()->back()->with('error', __("You can't delete Workspace!"));
            }
            return redirect()->route('home')->with('success', __('Workspace Deleted Successfully!'));
        } else {
            return redirect()->route('home')->with('error', __("You can't delete Workspace!"));
        }
    }


    //  Old
    // public function leave($workspaceID)
    // {

    //     $objUser = Auth::user();
    //     $all_workspaces = Workspace::get();
    //     $userProjects = Project::where('workspace', '=', $workspaceID)->get();


    //     if (count($all_workspaces) > 1) {

    //         $work_space = Workspace::first();

    //         $user_Project = Project::where('workspace', '=', $work_space->id)->get();

    //         foreach ($userProjects as $userProject) {
    //             UserProject::where('project_id', '=', $userProject->id)->where('user_id', '=', $objUser->id)->delete();
    //         }

    //         UserWorkspace::where('workspace_id', '=', $workspaceID)->where('user_id', '=', $objUser->id)->delete();


    //         foreach ($user_Project as $userProject_s) {
    //             UserProject::create(
    //                 [
    //                     'user_id' => $objUser->id,
    //                     'project_id' => $userProject_s->id,

    //                 ]
    //             );
    //         }

    //         UserWorkspace::create(
    //             [
    //                 'user_id' => $objUser->id,
    //                 'workspace_id' => $work_space->id,
    //                 'permission' => 'Owner',
    //             ]
    //         );
    //         $objUser->currant_workspace = $work_space->id;
    //         $objUser->save();
    //     } else {
    //         return redirect()->back()->with('error', __("You can't delete Workspace!"));
    //     }

    //     return redirect()->route('home')->with('success', __('Workspace Leave Successfully!'));
    // }

    public function leave($workspaceID)
    {
        $objUser = Auth::user();
        // $all_workspaces = Workspace::get();
        $all_workspaces = UserWorkspace::where('user_id', '=', Auth::user()->id)->get();
        $userProjects = Project::where('workspace', '=', $workspaceID)->get();

        if (count($all_workspaces) > 1) {
            // $work_space = Workspace::first();
            // $user_Project = Project::where('workspace', '=', $work_space->id)->get();

            foreach ($userProjects as $userProject) {
                UserProject::where('project_id', '=', $userProject->id)->where('user_id', '=', $objUser->id)->delete();
            }
            UserWorkspace::where('workspace_id', '=', $workspaceID)->where('user_id', '=', $objUser->id)->delete();

            $work_space = UserWorkspace::where('user_id', '=', Auth::user()->id)->first();
            $objUser->currant_workspace = $work_space->workspace_id;
            $objUser->save();
        } else {
            return redirect()->route('home')->with('error', __('You Can Not Leave the Workspace. Please Contact Your Company!!!'));
            foreach ($userProjects as $userProject) {
                UserProject::where('project_id', '=', $userProject->id)->where('user_id', '=', $objUser->id)->delete();
            }
            UserWorkspace::where('workspace_id', '=', $workspaceID)->where('user_id', '=', $objUser->id)->delete();
            $user = User::find($objUser->id);
            $user->delete();

            return redirect()->back()->with('Sussess', __("Workspace Leave Successfully!"));
        }

        return redirect()->route('home')->with('success', __('Workspace Leave Successfully!'));
    }

    public function changeCurrentWorkspace($workspaceID)
    {
        $objWorkspace = Workspace::find($workspaceID);
        if ($objWorkspace && $objWorkspace->is_active) {
            $currentWorkspace = Utility::getWorkspaceBySlug($objWorkspace->slug);
            $objUser = Auth::user();
            $objUser->currant_workspace = $workspaceID;
            $objUser->save();

            if (Auth::user()->getGuard() == 'client') {

                return redirect()->route('client.home')->with('success', __('Workspace Change Successfully!'));
            } else {

                return redirect()->route('home')->with('success', __('Workspace Change Successfully!'));
            }
        } else {
            return redirect()->back()->with('error', __('Workspace is locked'));
        }
    }

    public function changeLangAdmin($lang)
    {
        // if (Auth::user()->type == 'admin' && app('App\Http\Controllers\SettingsController')->setEnvironmentValue(['DEFAULT_ADMIN_LANG' => $lang])) {

        //     Artisan::call('config:cache');
        //     Artisan::call('config:clear');

        //     return redirect()->back()->with('success', __('Language Change Successfully!'));
        // } else {
        //     return redirect()->back()->with('error', __('Something is wrong'));
        // }
        $user = \Auth::user();
        $user->lang = $lang;
        $user->save();
        return redirect()->back()->with('success', __('Language Change Successfully!'));
    }

    public function changeLangcopylink($lang)
    {
        \Cookie::queue('LANGUAGE', $lang, 120);

        return redirect()->back()->with('success', __('Language Change Successfully!'));
    }

    public function changeLangWorkspace1($workspaceID, $lang)
    {

        $user = \Auth::user();
        $user->lang = $lang;
        $user->save();
        return redirect()->back()->with('success', __('Language Change Successfully!'));
    }
    public function changeLangWorkspace($workspaceID, $lang)
    {

        $user = \Auth::user();
        $user->lang = $lang;
        $user->save();

        return redirect()->back()->with('success', __('Language Change Successfully!'));
    }

    public function langWorkspace($currantLang = '')
    {
        $objUser = Auth::user();
        if ($objUser->type == 'admin') {

            $languages = Languages::pluck('lang_fullname', 'lang_code');
            $settings = Utility::getAdminPaymentSettings();
            if (!empty($settings['disable_lang'])) {
                $disabledLang = explode(',', $settings['disable_lang']);
            } else {
                $disabledLang = [];
            }
            $dir = base_path() . '/resources/lang/' . $currantLang;
            if (!empty($currantLang)) {
                $dir = base_path() . '/resources/lang/' . $currantLang;
                if (!is_dir($dir)) {
                    $dir = base_path() . '/resources/lang/en';
                }
            } else {
                $currantLang = env('DEFAULT_LANG') ?? 'en';
                $dir = base_path() . '/resources/lang/' . $currantLang;
            }

            $arrLabel = json_decode(file_get_contents($dir . '.json'));

            $arrFiles = array_diff(
                scandir($dir),
                array(
                    '..',
                    '.',
                )
            );
            $arrMessage = [];
            foreach ($arrFiles as $file) {
                $fileName = basename($file, ".php");
                $fileData = $myArray = include $dir . "/" . $file;
                if (is_array($fileData)) {
                    $arrMessage[$fileName] = $fileData;
                }
            }
            $workspace = new Workspace();

            return view('lang.index', compact('workspace', 'currantLang', 'arrLabel', 'arrMessage', 'disabledLang', 'languages'));
        } else {
            redirect()->route('home');
        }
    }

    public function storeLangDataWorkspace($currantLang, Request $request)
    {

        $objUser = Auth::user();
        if ($objUser->type == 'admin') {
            $dir = base_path() . '/resources/lang';
            $jsonFile = $dir . "/" . $currantLang . ".json";

            file_put_contents($jsonFile, json_encode($request->label));

            $langFolder = $dir . "/" . $currantLang;

            if ($request->message) {
                foreach ($request->message as $fileName => $fileData) {
                    $content = "<?php return [";
                    $content .= $this->buildArray($fileData);
                    $content .= "];";
                    file_put_contents($langFolder . "/" . $fileName . '.php', $content);
                }
            }

            return redirect()->route('lang_workspace', [$currantLang])->with('success', __('Language Save Successfully!'));
        } else {
            redirect()->route('home');
        }
    }

    public function buildArray($fileData)
    {
        $content = "";
        foreach ($fileData as $label => $data) {
            if (is_array($data)) {
                $content .= "'$label'=>[" . $this->buildArray($data) . "],";
            } else {
                $content .= "'$label'=>'" . addslashes($data) . "',";
            }
        }

        return $content;
    }

    public function createLangWorkspace()
    {
        $objUser = Auth::user();
        if ($objUser->type == 'admin') {
            return view('lang.create');
        } else {
            redirect()->route('home');
        }
    }

    public function storeLangWorkspace(Request $request)
    {
        $validator = \Validator::make(
            $request->all(),
            [
                'lang_code' => 'required|unique:languages',
                'lang_fullname' => 'required|unique:languages',
            ]
        );
        if ($validator->fails()) {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }

        $newlang = new Languages();
        $newlang->lang_code = $request->lang_code;
        $newlang->lang_fullname = $request->lang_fullname;
        $newlang->save();

        $objUser = Auth::user();
        if ($objUser->type == 'admin') {

            $Filesystem = new Filesystem();
            $langCode = strtolower($request->lang_code);

            $langDir = base_path() . '/resources/lang/';
            $dir = $langDir;

            $dir = $dir . $langCode;
            $jsonFile = $dir . ".json";
            \File::copy($langDir . 'en.json', $jsonFile);

            if (!is_dir($dir)) {
                mkdir($dir);
                chmod($dir, 0777);
            }
            $Filesystem->copyDirectory($langDir . "en", $dir . "/");

            return redirect()->route('lang_workspace', [$langCode])->with('success', __('Language Created Successfully!'));
        } else {
            redirect()->route('home');
        }
    }

    public function destroyLang($lang)
    {
        $default_lang = env('DEFAULT_LANG') ?? 'en';

        $langDir = base_path() . '/resources/lang/';
        if (is_dir($langDir)) {
            // remove directory and file
            Utility::delete_directory($langDir . $lang);
            unlink($langDir . $lang . '.json');
            // update user that has assign deleted language.
            Workspace::where('lang', 'LIKE', $lang)->update(['lang' => $default_lang]);
        }

        return redirect()->route('lang_workspace', $default_lang)->with('success', __('Language Deleted Successfully!'));
    }

    public function rename($workspaceID)
    {
        $objUser = Auth::user();
        $workspace = Workspace::find($workspaceID);
        $currentWorkspace = Utility::getWorkspaceBySlug($workspace->slug);
        if ($currentWorkspace && $workspace->created_by == $objUser->id) {
            return view('users.rename_workspace', compact('workspace'));
        } else {
            return redirect()->route('home')->with('error', __("You can't rename Workspace!"));
        }
    }

    public function update(Request $request, $id)
    {
        $objUser = Auth::user();
        $workspace = Workspace::find($id);

        if ($workspace->created_by == $objUser->id) {
            $workspace->name = $request->name;
            $workspace->save();

            return redirect()->route('home')->with('success', __('Rename Successfully.!'));
        } else {
            return redirect()->route('home')->with('error', __('You can\'t rename Workspace!'));
        }
    }

    public function settings($slug)
    {
        $objUser = Auth::user();
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);

        if ($currentWorkspace && $currentWorkspace->created_by == $objUser->id) {
            $taxes = Tax::where('workspace_id', '=', $currentWorkspace->id)->get();
            $stages = Stage::where('workspace_id', '=', $currentWorkspace->id)->orderBy('order')->get();
            $bugStages = BugStage::where('workspace_id', '=', $currentWorkspace->id)->orderBy('order')->get();

            $colors = [
                '003580',
                '666666',
                '6677ef',
                'f50102',
                'f9b034',
                'fbdd03',
                'c1d82f',
                '37a4e4',
                '8a7966',
                '6a737b',
                '050f2c',
                '0e3666',
                '3baeff',
                '3368e6',
                'b84592',
                'f64f81',
                'f66c5f',
                'fac168',
                '46de98',
                '40c7d0',
                'be0028',
                '2f9f45',
                '371676',
                '52325d',
                '511378',
                '0f3866',
                '48c0b6',
                '297cc0',
                'ffffff',
                '000000',
            ];
            // $EmailTemplates = EmailTemplate::with('template')->get();
            // $userEmailTemplates = UserEmailTemplate::with('templateName')->where('workspace_id', $currentWorkspace->id)->where('user_id', Auth::user()->id)->get();

            $payment_detail = Utility::getPaymentSetting($currentWorkspace->id);
            $EmailTemplates = EmailTemplate::all();

            return view('users.setting', compact('currentWorkspace', 'EmailTemplates', 'taxes', 'stages', 'bugStages', 'colors', 'payment_detail'));
        } else {
            return redirect()->route('home')->with('error', __("You can't access workspace settings!"));
        }
    }

    public function settingsStore($slug, Request $request)
    {
        $objUser = Auth::user();
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);

        if ($currentWorkspace->created_by == $objUser->id) {
            $validate = [];
            $stripe_status = $request->is_stripe_enabled == 'on' ? 1 : 0;
            $paypal_status = $request->is_paypal_enabled == 'on' ? 1 : 0;

            if (isset($request->is_bank_enabled) && $request->is_bank_enabled == 'on') {
                $validate['bank_details'] = 'required|string';
            }

            if ($stripe_status == 1) {
                $validate['stripe_key'] = 'required|string|max:255';
                $validate['stripe_secret'] = 'required|string|max:255';
            }

            if ($paypal_status == 1) {
                $validate['paypal_client_id'] = 'required|string|max:255';
                $validate['paypal_secret_key'] = 'required|string|max:255';
            }

            if (isset($request->is_paystack_enabled) && $request->is_paystack_enabled == 'on') {
                $validate['paystack_public_key'] = 'required|string';
                $validate['paystack_secret_key'] = 'required|string';
            }

            if (isset($request->is_flutterwave_enabled) && $request->is_flutterwave_enabled == 'on') {
                $validate['flutterwave_public_key'] = 'required|string';
                $validate['flutterwave_secret_key'] = 'required|string';
            }

            if (isset($request->is_razorpay_enabled) && $request->is_razorpay_enabled == 'on') {
                $validate['razorpay_public_key'] = 'required|string';
                $validate['razorpay_secret_key'] = 'required|string';
            }

            if (isset($request->is_mercado_enabled) && $request->is_mercado_enabled == 'on') {
                $validate['mercado_access_token'] = 'required|string';
                $validate['mercado_mode'] = 'required|string';
            }

            if (isset($request->is_paytm_enabled) && $request->is_paytm_enabled == 'on') {
                $validate['paytm_mode'] = 'required|string';
                $validate['paytm_merchant_id'] = 'required|string';
                $validate['paytm_merchant_key'] = 'required|string';
                $validate['paytm_industry_type'] = 'required|string';
            }

            if (isset($request->is_mollie_enabled) && $request->is_mollie_enabled == 'on') {
                $validate['mollie_api_key'] = 'required|string';
                $validate['mollie_profile_id'] = 'required|string';
                $validate['mollie_partner_id'] = 'required|string';
            }

            if (isset($request->is_skrill_enabled) && $request->is_skrill_enabled == 'on') {
                $validate['skrill_email'] = 'required|email';
            }

            if (isset($request->is_coingate_enabled) && $request->is_coingate_enabled == 'on') {
                $validate['coingate_mode'] = 'required|string';
                $validate['coingate_auth_token'] = 'required|string';
            }

            if (isset($request->is_toyyibpay_enabled) && $request->is_toyyibpay_enabled == 'on') {
                $validate['toyyibpay_secret_key'] = 'required|string';
                $validate['toyyibpay_category_code'] = 'required|string';
            }

            if (isset($request->is_payfast_enabled) && $request->is_payfast_enabled == 'on') {
                $validate['payfast_merchant_key'] = 'required|string';
                $validate['payfast_merchant_id'] = 'required|string';
                $validate['payfast_signature'] = 'required|string';
                $validate['payfast_mode'] = 'required|string';
            }

            if (isset($request->is_iyzipay_enabled) && $request->is_iyzipay_enabled == 'on') {
                $validate['iyzipay_api_key'] = 'required|string';
                $validate['iyzipay_secret_key'] = 'required|string';
                $validate['iyzipay_mode'] = 'required|string';
            }

            if (isset($request->is_sspay_enabled) && $request->is_sspay_enabled == 'on') {
                $validate['sspay_secret_key'] = 'required|string';
                $validate['sspay_category_code'] = 'required|string';
            }

            if (isset($request->is_paytab_enabled) && $request->is_paytab_enabled == 'on') {
                $validate['paytabs_region'] = 'required|string';
                $validate['paytab_server_key'] = 'required|string';
                $validate['paytabs_profile_id'] = 'required|string';
            }

            if (isset($request->is_benefit_enabled) && $request->is_benefit_enabled == 'on') {
                $validate['benefit_secret_key'] = 'required|string';
                $validate['benefit_publishable_key'] = 'required|string';
            }

            if (isset($request->is_cashfree_enabled) && $request->is_cashfree_enabled == 'on') {
                $validate['cashfree_api_key'] = 'required|string';
                $validate['cashfree_secret_key'] = 'required|string';
            }

            if (isset($request->is_aamarpay_enabled) && $request->is_aamarpay_enabled == 'on') {
                $validate['aamarpay_store_id'] = 'required|string';
                $validate['aamarpay_signature_key'] = 'required|string';
                $validate['aamarpay_description'] = 'required|string';
            }

            if (isset($request->is_paytr_enabled) && $request->is_paytr_enabled == 'on') {
                $validate['paytr_merchant_id'] = 'required|string';
                $validate['paytr_merchant_key'] = 'required|string';
                $validate['paytr_merchant_salt'] = 'required|string';
            }

            if (isset($request->is_midtrans_enabled) && $request->is_midtrans_enabled == 'on') {
                $validate['is_midtrans_enabled'] = 'required|string';
                $validate['midtrans_server_key'] = 'required|string';
            }

            if (isset($request->is_xendit_enabled) && $request->is_xendit_enabled == 'on') {
                $validate['is_xendit_enabled'] = 'required|string';
                $validate['xendit_api_key'] = 'required|string';
                $validate['xendit_token'] = 'required|string';
            }

            if (isset($request->is_yookassa_enabled) && $request->is_yookassa_enabled == 'on') {
                $validate['is_yookassa_enabled'] = 'required|string';
                $validate['yookassa_shopid'] = 'required|string';
                $validate['yookassa_secret_key'] = 'required|string';
            }

            if (isset($request->is_paiementpro_enabled) && $request->is_paiementpro_enabled == 'on') {
                $validate['is_paiementpro_enabled'] = 'required|string';
                $validate['paiementpro_merchant_id'] = 'required|string';
            }

            if (isset($request->is_nepalste_enabled) && $request->is_nepalste_enabled == 'on') {
                $validate['is_nepalste_enabled'] = 'required';
                $validate['nepalste_mode'] = 'required';
                $validate['nepalste_public_key'] = 'required';
            }

            if (isset($request->is_cinetpay_enabled) && $request->is_cinetpay_enabled == 'on') {
                $validate['is_cinetpay_enabled'] = 'required';
                $validate['cinetpay_api_key'] = 'required';
                $validate['cinetpay_site_id'] = 'required';
            }

            if (isset($request->is_fedapay_enabled) && $request->is_fedapay_enabled == 'on') {
                $validate['is_fedapay_enabled'] = 'required';
                $validate['fedapay_mode'] = 'required';
                $validate['fedapay_public_key'] = 'required';
                $validate['fedapay_secret_key'] = 'required';
            }

            if (isset($request->is_payhere_enabled) && $request->is_payhere_enabled == 'on') {
                $validate['is_payhere_enabled'] = 'required';
                $validate['payhere_mode'] = 'required';
                $validate['payhere_merchant_id'] = 'required';
                $validate['payhere_merchant_secret'] = 'required';
                $validate['payhere_app_id'] = 'required';
                $validate['payhere_app_secret'] = 'required';
            }

            if (isset($request->is_powertranz_enabled) && $request->is_powertranz_enabled == 'on') {
                $validate['is_powertranz_enabled'] = 'required';
                $validate['powertranz_mode'] = 'required';
                $validate['powertranz_merchant_id'] = 'required';
                $validate['powertranz_processing_password'] = 'required';
            }

            if (isset($request->is_payu_enabled) && $request->is_payu_enabled == 'on') {
                $validate['is_payu_enabled'] = 'required';
                $validate['payu_mode'] = 'required';
                $validate['payu_merchant_key'] = 'required';
                $validate['payu_salt_key'] = 'required';
            }

            if ($request->has('interval_time')) {
                $validate['interval_time'] = 'required';
            }

            if ($request->has('zoom_account_id')) {
                $validate['zoom_account_id'] = 'required';
            }

            if ($request->has('zoom_client_id')) {
                $validate['zoom_client_id'] = 'required';
            }

            if ($request->has('zoom_client_secret')) {
                $validate['zoom_client_secret'] = 'required';
            }

            $validator = Validator::make(
                $request->all(),
                $validate
            );

            if ($validator->fails()) {
                return redirect()->back()->with('error', $validator->errors()->first());
            }

            $dir = 'logo/';

            if ($request->name) {

                if ($request->logo) {
                    $request->validate(['logo' => 'required']);
                    $logo_dark_Name = $currentWorkspace->id . '_' . 'logo-light.png';

                    $path = Utility::upload_file($request, 'logo', $logo_dark_Name, $dir, []);
                    if ($path['flag'] == 1) {
                        $logo = $path['url'];
                    } else {
                        return redirect()->back()->with('error', __($path['msg']));
                    }
                    $currentWorkspace->logo = $logo_dark_Name;
                }

                if ($request->logo_white) {
                    $request->validate(['logo_white' => 'required']);
                    $logo_white_Name = $currentWorkspace->id . '_' . 'logo-dark.png';

                    $path = Utility::upload_file($request, 'logo_white', $logo_white_Name, $dir, []);
                    if ($path['flag'] == 1) {
                        $logo_white = $path['url'];
                    } else {
                        return redirect()->back()->with('error', __($path['msg']));
                    }
                    $currentWorkspace->logo_white = $logo_white_Name;
                }

                if ($request->favicon) {
                    $request->validate(['favicon' => 'required']);
                    $logo_favicon_Name = $currentWorkspace->id . '_' . 'favicon.png';
                    $path = Utility::upload_file($request, 'favicon', $logo_favicon_Name, $dir, []);
                    if ($path['flag'] == 1) {
                        $favicon = $path['url'];
                    } else {
                        return redirect()->back()->with('error', __($path['msg']));
                    }
                    $currentWorkspace->favicon = $logo_favicon_Name;
                }

                if (isset($request->color) && $request->color_flag == 'false') {
                    $currentWorkspace->theme_color = (!empty($request->color)) ? $request->color : 'theme-3';
                    $currentWorkspace->color_flag = $request->color_flag;
                } else {
                    $currentWorkspace->theme_color = $request->custom_color;
                    $currentWorkspace->color_flag = $request->color_flag;
                }

                $currentWorkspace->site_rtl = !empty($request->site_rtl) ? $request->site_rtl : 'off';

                $currentWorkspace->cust_darklayout = !empty($request->cust_darklayout) ? 'on' : 'off';
                $currentWorkspace->cust_theme_bg = (!empty($request->cust_theme_bg)) ? 'on' : 'off';

                $currentWorkspace->name = $request->name;
                $currentWorkspace->lang = $request->default_language;
            } elseif ($request->has('interval_time')) {

                $currentWorkspace->interval_time = $request->interval_time;
            } elseif ($request->has('zoom_account_id')) {

                $currentWorkspace->zoom_account_id = $request->zoom_account_id;
                $currentWorkspace->zoom_client_id = $request->zoom_client_id;
                $currentWorkspace->zoom_client_secret = $request->zoom_client_secret;
            } elseif ($request->invoice_template) {

                $currentWorkspace->invoice_template = $request->invoice_template;
                $currentWorkspace->qr_display = $request->qr_display;
                $currentWorkspace->invoice_color = $request->invoice_color;
            } elseif ($request->has('invoice_footer_title')) {

                $currentWorkspace->invoice_footer_title = $request->invoice_footer_title;
                $currentWorkspace->invoice_footer_notes = $request->invoice_footer_notes;
            } elseif ($request->currency) {

                $currentWorkspace->currency = $request->currency;
                $currentWorkspace->currency_code = $request->currency_code;
                $currentWorkspace->is_stripe_enabled = $stripe_status;
                $currentWorkspace->stripe_key = $request->stripe_key;
                $currentWorkspace->stripe_secret = $request->stripe_secret;
                $currentWorkspace->is_paypal_enabled = $paypal_status;
                $currentWorkspace->paypal_mode = $request->paypal_mode;
                $currentWorkspace->paypal_client_id = $request->paypal_client_id;
                $currentWorkspace->paypal_secret_key = $request->paypal_secret_key;

                $post = [];

                // Save bank Detail
                if (isset($request->is_bank_enabled) && $request->is_bank_enabled == 'on') {
                    $post['is_bank_enabled'] = $request->is_bank_enabled;
                    $post['bank_details'] = $request->bank_details;
                } else {
                    $post['is_bank_enabled'] = 'off';
                }

                // Save Paystack Detail
                if (isset($request->is_paystack_enabled) && $request->is_paystack_enabled == 'on') {
                    $post['is_paystack_enabled'] = $request->is_paystack_enabled;
                    $post['paystack_public_key'] = $request->paystack_public_key;
                    $post['paystack_secret_key'] = $request->paystack_secret_key;
                } else {
                    $post['is_paystack_enabled'] = 'off';
                }

                // Save Fluuterwave Detail
                if (isset($request->is_flutterwave_enabled) && $request->is_flutterwave_enabled == 'on') {
                    $post['is_flutterwave_enabled'] = $request->is_flutterwave_enabled;
                    $post['flutterwave_public_key'] = $request->flutterwave_public_key;
                    $post['flutterwave_secret_key'] = $request->flutterwave_secret_key;
                } else {
                    $post['is_flutterwave_enabled'] = 'off';
                }

                // Save Razorpay Detail
                if (isset($request->is_razorpay_enabled) && $request->is_razorpay_enabled == 'on') {
                    $post['is_razorpay_enabled'] = $request->is_razorpay_enabled;
                    $post['razorpay_public_key'] = $request->razorpay_public_key;
                    $post['razorpay_secret_key'] = $request->razorpay_secret_key;
                } else {
                    $post['is_razorpay_enabled'] = 'off';
                }

                // Save Marcado Detail

                if (isset($request->is_mercado_enabled) && $request->is_mercado_enabled == 'on') {
                    $request->validate(
                        [
                            'mercado_access_token' => 'required|string',
                        ]
                    );
                    $post['is_mercado_enabled'] = $request->is_mercado_enabled;
                    $post['mercado_access_token'] = $request->mercado_access_token;
                    $post['mercado_mode'] = $request->mercado_mode;
                } else {
                    $post['is_mercado_enabled'] = 'off';
                }

                // Save Paytm Detail
                if (isset($request->is_paytm_enabled) && $request->is_paytm_enabled == 'on') {
                    $post['is_paytm_enabled'] = $request->is_paytm_enabled;
                    $post['paytm_mode'] = $request->paytm_mode;
                    $post['paytm_merchant_id'] = $request->paytm_merchant_id;
                    $post['paytm_merchant_key'] = $request->paytm_merchant_key;
                    $post['paytm_industry_type'] = $request->paytm_industry_type;
                } else {
                    $post['is_paytm_enabled'] = 'off';
                }

                // Save Mollie Detail
                if (isset($request->is_mollie_enabled) && $request->is_mollie_enabled == 'on') {
                    $post['is_mollie_enabled'] = $request->is_mollie_enabled;
                    $post['mollie_api_key'] = $request->mollie_api_key;
                    $post['mollie_profile_id'] = $request->mollie_profile_id;
                    $post['mollie_partner_id'] = $request->mollie_partner_id;
                } else {
                    $post['is_mollie_enabled'] = 'off';
                }

                // Save Skrill Detail
                if (isset($request->is_skrill_enabled) && $request->is_skrill_enabled == 'on') {
                    $post['is_skrill_enabled'] = $request->is_skrill_enabled;
                    $post['skrill_email'] = $request->skrill_email;
                } else {
                    $post['is_skrill_enabled'] = 'off';
                }

                // Save Coingate Detail
                if (isset($request->is_coingate_enabled) && $request->is_coingate_enabled == 'on') {
                    $post['is_coingate_enabled'] = $request->is_coingate_enabled;
                    $post['coingate_mode'] = $request->coingate_mode;
                    $post['coingate_auth_token'] = $request->coingate_auth_token;
                } else {
                    $post['is_coingate_enabled'] = 'off';
                }
                if (isset($request->is_paymentwall_enabled) && $request->is_paymentwall_enabled == 'on') {
                    $post['is_paymentwall_enabled'] = $request->is_paymentwall_enabled;
                    $post['paymentwall_public_key'] = $request->paymentwall_public_key;
                    $post['paymentwall_private_key'] = $request->paymentwall_private_key;
                } else {
                    $post['is_paymentwall_enabled'] = 'off';
                }

                // Save Toyyibpay Detail
                if (isset($request->is_toyyibpay_enabled) && $request->is_toyyibpay_enabled == 'on') {

                    $post['is_toyyibpay_enabled'] = $request->is_toyyibpay_enabled;
                    $post['toyyibpay_secret_key'] = $request->toyyibpay_secret_key;
                    $post['toyyibpay_category_code'] = $request->toyyibpay_category_code;
                } else {
                    $post['is_toyyibpay_enabled'] = 'off';
                }

                // Save payfast Detail
                if (isset($request->is_payfast_enabled) && $request->is_payfast_enabled == 'on') {
                    $post['is_payfast_enabled'] = $request->is_payfast_enabled;
                    $post['payfast_merchant_key'] = $request->payfast_merchant_key;
                    $post['payfast_merchant_id'] = $request->payfast_merchant_id;
                    $post['payfast_signature'] = $request->payfast_signature;
                    $post['payfast_mode'] = $request->payfast_mode;
                } else {
                    $post['is_payfast_enabled'] = 'off';
                }

                // Save iyzipay Detail
                if (isset($request->is_iyzipay_enabled) && $request->is_iyzipay_enabled == 'on') {
                    $post['is_iyzipay_enabled'] = $request->is_iyzipay_enabled;
                    $post['iyzipay_api_key'] = $request->iyzipay_api_key;
                    $post['iyzipay_secret_key'] = $request->iyzipay_secret_key;
                    $post['iyzipay_mode'] = $request->iyzipay_mode;
                } else {
                    $post['is_iyzipay_enabled'] = 'off';
                }

                // Save sspay Detail
                if (isset($request->is_sspay_enabled) && $request->is_sspay_enabled == 'on') {
                    $post['is_sspay_enabled'] = $request->is_sspay_enabled;
                    $post['sspay_secret_key'] = $request->sspay_secret_key;
                    $post['sspay_category_code'] = $request->sspay_category_code;
                } else {
                    $post['is_sspay_enabled'] = 'off';
                }

                // Save paytab Detail
                if (isset($request->is_paytab_enabled) && $request->is_paytab_enabled == 'on') {
                    $post['is_paytab_enabled'] = $request->is_paytab_enabled;
                    $post['paytabs_profile_id'] = $request->paytabs_profile_id;
                    $post['paytab_server_key'] = $request->paytab_server_key;
                    $post['paytabs_region'] = $request->paytabs_region;
                } else {
                    $post['is_paytab_enabled'] = 'off';
                }

                // Save benefit Detail
                if (isset($request->is_benefit_enabled) && $request->is_benefit_enabled == 'on') {
                    $post['is_benefit_enabled'] = $request->is_benefit_enabled;
                    $post['benefit_secret_key'] = $request->benefit_secret_key;
                    $post['benefit_publishable_key'] = $request->benefit_publishable_key;
                } else {
                    $post['is_benefit_enabled'] = 'off';
                }

                // Save cashfree Detail
                if (isset($request->is_cashfree_enabled) && $request->is_cashfree_enabled == 'on') {
                    $post['is_cashfree_enabled'] = $request->is_cashfree_enabled;
                    $post['cashfree_api_key'] = $request->cashfree_api_key;
                    $post['cashfree_secret_key'] = $request->cashfree_secret_key;
                } else {
                    $post['is_cashfree_enabled'] = 'off';
                }

                // Save aamarpay Detail
                if (isset($request->is_aamarpay_enabled) && $request->is_aamarpay_enabled == 'on') {
                    $post['is_aamarpay_enabled'] = $request->is_aamarpay_enabled;
                    $post['aamarpay_store_id'] = $request->aamarpay_store_id;
                    $post['aamarpay_signature_key'] = $request->aamarpay_signature_key;
                    $post['aamarpay_description'] = $request->aamarpay_description;
                } else {
                    $post['is_aamarpay_enabled'] = 'off';
                }

                // Save PAYTr Detail
                if (isset($request->is_paytr_enabled) && $request->is_paytr_enabled == 'on') {
                    $post['is_paytr_enabled'] = $request->is_paytr_enabled;
                    $post['paytr_merchant_key'] = $request->paytr_merchant_key;
                    $post['paytr_merchant_id'] = $request->paytr_merchant_id;
                    $post['paytr_merchant_salt'] = $request->paytr_merchant_salt;
                } else {
                    $post['is_paytr_enabled'] = 'off';
                }

                // Save Midtrans Detail
                if (isset($request->is_midtrans_enabled) && $request->is_midtrans_enabled == 'on') {
                    $post['is_midtrans_enabled'] = $request->is_midtrans_enabled;
                    $post['midtrans_mode'] = $request->midtrans_mode;
                    $post['midtrans_server_key'] = $request->midtrans_server_key;
                } else {
                    $post['is_midtrans_enabled'] = 'off';
                }

                // save Xendit Details
                if (isset($request->is_xendit_enabled) && $request->is_xendit_enabled == 'on') {
                    $post['is_xendit_enabled'] = $request->is_xendit_enabled;
                    $post['xendit_api_key'] = $request->xendit_api_key;
                    $post['xendit_token'] = $request->xendit_token;
                } else {
                    $post['is_xendit_enabled'] = 'off';
                }


                // save yookassa Details
                if (isset($request->is_yookassa_enabled) && $request->is_yookassa_enabled == 'on') {
                    $post['is_yookassa_enabled'] = $request->is_yookassa_enabled;
                    $post['yookassa_shopid'] = $request->yookassa_shopid;
                    $post['yookassa_secret_key'] = $request->yookassa_secret_key;
                } else {
                    $post['is_yookassa_enabled'] = 'off';
                }

                // save paiementpro payment details
                if (isset($request->is_paiementpro_enabled) && $request->is_paiementpro_enabled == 'on') {
                    $post['is_paiementpro_enabled'] = $request->is_paiementpro_enabled;
                    $post['paiementpro_merchant_id'] = $request->paiementpro_merchant_id;
                } else {
                    $post['is_paiementpro_enabled'] = 'off';
                }

                // save Nepalste payment details
                if (isset($request->is_nepalste_enabled) && $request->is_nepalste_enabled == 'on') {
                    $post['is_nepalste_enabled'] = $request->is_nepalste_enabled;
                    $post['nepalste_mode'] = $request->nepalste_mode;
                    $post['nepalste_public_key'] = $request->nepalste_public_key;
                } else {
                    $post['is_nepalste_enabled'] = 'off';
                }

                // save Cinetpay payment details
                if (isset($request->is_cinetpay_enabled) && $request->is_cinetpay_enabled == 'on') {
                    $post['is_cinetpay_enabled'] = $request->is_cinetpay_enabled;
                    $post['cinetpay_api_key'] = $request->cinetpay_api_key;
                    $post['cinetpay_site_id'] = $request->cinetpay_site_id;
                } else {
                    $post['is_cinetpay_enabled'] = 'off';
                }

                // save Fedapay payment details
                if (isset($request->is_fedapay_enabled) && $request->is_fedapay_enabled == 'on') {
                    $post['is_fedapay_enabled'] = $request->is_fedapay_enabled;
                    $post['fedapay_mode'] = $request->fedapay_mode;
                    $post['fedapay_public_key'] = $request->fedapay_public_key;
                    $post['fedapay_secret_key'] = $request->fedapay_secret_key;
                } else {
                    $post['is_fedapay_enabled'] = 'off';
                }

                // save PayHere payment details
                if (isset($request->is_payhere_enabled) && $request->is_payhere_enabled == 'on') {
                    $post['is_payhere_enabled'] = $request->is_payhere_enabled;
                    $post['payhere_mode'] = $request->payhere_mode;
                    $post['payhere_merchant_id'] = $request->payhere_merchant_id;
                    $post['payhere_merchant_secret'] = $request->payhere_merchant_secret;
                    $post['payhere_app_id'] = $request->payhere_app_id;
                    $post['payhere_app_secret'] = $request->payhere_app_secret;
                } else {
                    $post['is_payhere_enabled'] = 'off';
                }

                // save PowerTranz Payment details
                if (isset($request->is_powertranz_enabled) && $request->is_powertranz_enabled == 'on') {
                    $post['is_powertranz_enabled'] = $request->is_powertranz_enabled;
                    $post['powertranz_mode'] = $request->powertranz_mode;
                    $post['powertranz_merchant_id'] = $request->powertranz_merchant_id;
                    $post['powertranz_processing_password'] = $request->powertranz_processing_password;
                } else {
                    $post['is_powertranz_enabled'] = 'off';
                }

                // save PayU Payment details
                if (isset($request->is_payu_enabled) && $request->is_payu_enabled == 'on') {
                    $post['is_payu_enabled'] = $request->is_payu_enabled;
                    $post['payu_mode'] = $request->payu_mode;
                    $post['payu_merchant_key'] = $request->payu_merchant_key;
                    $post['payu_salt_key'] = $request->payu_salt_key;
                } else {
                    $post['is_payu_enabled'] = 'off';
                }

                $created_at = date('Y-m-d H:i:s');
                $updated_at = date('Y-m-d H:i:s');

                foreach ($post as $key => $data) {
                    \DB::insert(
                        'insert into payment_settings (`value`, `name`,`created_by`,`created_at`,`updated_at`) values (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`), `updated_at` = VALUES(`updated_at`)',
                        [
                            $data,
                            $key,
                            $currentWorkspace->id,
                            $created_at,
                            $updated_at,
                        ]
                    );
                }
            } else {

                $currentWorkspace->company = $request->company;
                $currentWorkspace->address = $request->address;
                $currentWorkspace->city = $request->city;
                $currentWorkspace->state = $request->state;
                $currentWorkspace->zipcode = $request->zipcode;
                $currentWorkspace->country = $request->country;
                $currentWorkspace->telephone = $request->telephone;
            }

            $currentWorkspace->save();

            return redirect()->back()->with('success', __('Settings Save Successfully.!'));
        } else {
            return redirect()->route('home')->with('error', __("You can't access workspace settings!"));
        }
    }

    public function ZoomMeetingStore($slug, Request $request)
    {

        if ($request->has('zoom_account_id')) {
            $validate['zoom_account_id'] = 'required';
        }
        if ($request->has('zoom_client_id')) {
            $validate['zoom_client_id'] = 'required';
        }
        if ($request->has('zoom_client_secret')) {
            $validate['zoom_client_secret'] = 'required';
        }

        $validator = Validator::make(
            $request->all(),
            $validate
        );

        if ($validator->fails()) {
            return redirect()->back()->with('error', $validator->errors()->first());
        }

        // Save PAYTr Detail
        $post['zoom_account_id'] = $request->zoom_account_id;
        $post['zoom_client_id'] = $request->zoom_client_id;
        $post['zoom_client_secret'] = $request->zoom_client_secret;


        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $created_at = date('Y-m-d H:i:s');
        $updated_at = date('Y-m-d H:i:s');


        foreach ($post as $key => $data) {
            \DB::insert(
                'INSERT INTO payment_settings (`value`, `name`,`created_by`,`created_at`,`updated_at`) values (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`), `updated_at` = VALUES(`updated_at`) ',
                [
                    $data,
                    $key,
                    $currentWorkspace->id,
                    $created_at,
                    $updated_at,
                ]
            );
        }

        return redirect()->back()->with('success', __('Zoom Metting Saved Successfully.'));
    }

    public function create_tax($slug)
    {
        $objUser = Auth::user();
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        if ($currentWorkspace->created_by == $objUser->id) {
            return view('users.create_tax', compact('currentWorkspace'));
        }
    }

    public function edit_tax($slug, $id)
    {
        $objUser = Auth::user();
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        if ($currentWorkspace->created_by == $objUser->id) {
            $tax = Tax::find($id);

            return view('users.edit_tax', compact('currentWorkspace', 'tax'));
        }
    }

    public function store_tax($slug, Request $request)
    {
        $objUser = Auth::user();
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        if ($currentWorkspace->created_by == $objUser->id) {
            $request->validate(
                [
                    'name' => [
                        'required',
                        'string',
                        'max:255',
                    ],
                    'rate' => ['required'],
                ]
            );
            $tax = new Tax();
            $tax->name = $request->name;
            $tax->rate = $request->rate;
            $tax->workspace_id = $currentWorkspace->id;
            $tax->save();

            return redirect()->back()->with('success', __('Tax Save Successfully.!'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function update_tax($slug, Request $request, $id)
    {
        $objUser = Auth::user();
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        if ($currentWorkspace->created_by == $objUser->id) {
            $request->validate(
                [
                    'name' => [
                        'required',
                        'string',
                        'max:255',
                    ],
                    'rate' => ['required'],
                ]
            );
            $tax = Tax::find($id);
            $tax->name = $request->name;
            $tax->rate = $request->rate;
            $tax->save();

            return redirect()->back()->with('success', __('Tax Save Successfully.!'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function destroy_tax($slug, $id)
    {
        $objUser = Auth::user();
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        if ($currentWorkspace->created_by == $objUser->id) {
            $tax = Tax::find($id);
            $tax->delete();

            return redirect()->back()->with('success', __('Tax Delete Successfully.!'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function store_stages($slug, Request $request)
    {
        $objUser = Auth::user();
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        if ($currentWorkspace->created_by == $objUser->id) {

            $rules = [
                'stages' => 'required|present|array',
            ];
            $attributes = [];
            if ($request->stages) {

                foreach ($request->stages as $key => $val) {
                    $rules['stages.' . $key . '.name'] = 'required|max:255';
                    $attributes['stages.' . $key . '.name'] = __('Stage Name');
                }
            }
            $validator = \Validator::make($request->all(), $rules, [], $attributes);
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $arrStages = Stage::where('workspace_id', '=', $currentWorkspace->id)->orderBy('order')->pluck('name', 'id')->all();
            $order = 0;
            foreach ($request->stages as $key => $stage) {

                $obj = null;
                if ($stage['id']) {
                    $obj = Stage::find($stage['id']);
                    unset($arrStages[$obj->id]);
                } else {
                    $obj = new Stage();
                    $obj->workspace_id = $currentWorkspace->id;
                }
                $obj->name = $stage['name'];
                $obj->color = $stage['color'];
                $obj->order = $order++;
                $obj->complete = 0;
                $obj->save();
            }

            $taskExist = [];
            if ($arrStages) {
                foreach ($arrStages as $id => $name) {
                    $count = Task::where('status', '=', $id)->count();
                    if ($count != 0) {
                        $taskExist[] = $name;
                    } else {
                        Stage::find($id)->delete();
                    }
                }
            }

            $lastStage = Stage::where('workspace_id', '=', $currentWorkspace->id)->orderBy('order', 'desc')->first();
            if ($lastStage) {
                $lastStage->complete = 1;
                $lastStage->save();
            }

            if (empty($taskExist)) {
                return redirect()->back()->with('success', __('Stage Save Successfully.!'));
            } else {
                return redirect()->back()->with('error', __('Please remove tasks from stage: ' . implode(', ', $taskExist)));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function store_bug_stages($slug, Request $request)
    {
        $objUser = Auth::user();
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        if ($currentWorkspace->created_by == $objUser->id) {
            $rules = [
                'stages' => 'required|present|array',
            ];
            $attributes = [];
            if ($request->stages) {

                foreach ($request->stages as $key => $val) {
                    $rules['stages.' . $key . '.name'] = 'required|max:255';
                    $attributes['stages.' . $key . '.name'] = __('Stage Name');
                }
            }
            $validator = \Validator::make($request->all(), $rules, [], $attributes);
            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $arrStages = BugStage::where('workspace_id', '=', $currentWorkspace->id)->orderBy('order')->pluck('name', 'id')->all();
            $order = 0;
            foreach ($request->stages as $key => $stage) {

                $obj = null;
                if ($stage['id']) {
                    $obj = BugStage::find($stage['id']);
                    unset($arrStages[$obj->id]);
                } else {
                    $obj = new BugStage();
                    $obj->workspace_id = $currentWorkspace->id;
                }
                $obj->name = $stage['name'];
                $obj->color = $stage['color'];
                $obj->order = $order++;
                $obj->complete = 0;
                $obj->save();
            }

            $taskExist = [];
            if ($arrStages) {
                foreach ($arrStages as $id => $name) {
                    $count = BugReport::where('status', '=', $id)->count();
                    if ($count != 0) {
                        $taskExist[] = $name;
                    } else {
                        BugStage::find($id)->delete();
                    }
                }
            }

            $lastStage = BugStage::where('workspace_id', '=', $currentWorkspace->id)->orderBy('order', 'desc')->first();
            if ($lastStage) {
                $lastStage->complete = 1;
                $lastStage->save();
            }

            if (empty($taskExist)) {
                return redirect()->back()->with('success', __('Stage Save Successfully.!'));
            } else {
                return redirect()->back()->with('error', __('Please remove bugs from stage: ' . implode(', ', $taskExist)));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function settingsSlack($slug, Request $request)
    {

        $objUser = Auth::user();

        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $created_by = $currentWorkspace->id;
        if ($currentWorkspace->created_by == $objUser->id) {
            $post = [];
            $post['slack_webhook'] = $request->slack_webhook;
            if (isset($request->project_notificaation) && $request->project_notificaation == '1') {
                $post['project_notificaation'] = $request->project_notificaation;
            } else {
                $post['project_notificaation'] = '0';
            }

            $post['task_notificaation'] = $request->has('task_notificaation') ? $request->input('task_notificaation') : 0;
            $post['taskmove_notificaation'] = $request->has('taskmove_notificaation') ? $request->input('taskmove_notificaation') : 0;
            $post['taskcom_notificaation'] = $request->has('taskcom_notificaation') ? $request->input('taskcom_notificaation') : 0;
            $post['milestone_notificaation'] = $request->has('milestone_notificaation') ? $request->input('milestone_notificaation') : 0;
            $post['milestonest_notificaation'] = $request->has('milestonest_notificaation') ? $request->input('milestonest_notificaation') : 0;
            $post['invoice_notificaation'] = $request->has('invoice_notificaation') ? $request->input('invoice_notificaation') : 0;
            $post['invoicest_notificaation'] = $request->has('invoicest_notificaation') ? $request->input('invoicest_notificaation') : 0;


            if (isset($post) && !empty($post) && count($post) > 0) {
                $created_at = $updated_at = date('Y-m-d H:i:s');

                foreach ($post as $key => $data) {
                    \DB::insert(
                        'INSERT INTO payment_settings (`value`, `name`,`created_by`,`created_at`,`updated_at`) values (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`), `updated_at` = VALUES(`updated_at`) ',
                        [
                            $data,
                            $key,
                            $created_by,
                            $created_at,
                            $updated_at,
                        ]
                    );
                }
            }

            return redirect()->back()->with('success', __('Settings updated successfully.'));
        }
    }


    public function settingstelegram($slug, Request $request)
    {

        $objUser = Auth::user();

        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $created_by = $currentWorkspace->id;
        if ($currentWorkspace->created_by == $objUser->id) {
            $post = [];
            $post['telegram_token'] = $request->telegram_token;
            $post['telegram_chatid'] = $request->telegram_chatid;
            if (isset($request->telegram_project_notificaation) && $request->telegram_project_notificaation == '1') {
                $post['telegram_project_notificaation'] = $request->telegram_project_notificaation;
            } else {
                $post['telegram_project_notificaation'] = '0';
            }

            $post['telegram_task_notificaation'] = $request->has('telegram_task_notificaation') ? $request->input('telegram_task_notificaation') : 0;


            $post['telegram_taskmove_notificaation'] = $request->has('telegram_taskmove_notificaation') ? $request->input('telegram_taskmove_notificaation') : 0;


            $post['telegram_taskcom_notificaation'] = $request->has('telegram_taskcom_notificaation') ? $request->input('telegram_taskcom_notificaation') : 0;


            $post['telegram_milestone_notificaation'] = $request->has('telegram_milestone_notificaation') ? $request->input('telegram_milestone_notificaation') : 0;


            $post['telegram_milestonest_notificaation'] = $request->has('telegram_milestonest_notificaation') ? $request->input('telegram_milestonest_notificaation') : 0;


            $post['telegram_invoice_notificaation'] = $request->has('telegram_invoice_notificaation') ? $request->input('telegram_invoice_notificaation') : 0;



            $post['telegram_invoicest_notificaation'] = $request->has('telegram_invoicest_notificaation') ? $request->input('telegram_invoicest_notificaation') : 0;


            if (isset($post) && !empty($post) && count($post) > 0) {
                $created_at = $updated_at = date('Y-m-d H:i:s');

                foreach ($post as $key => $data) {
                    \DB::insert(
                        'INSERT INTO payment_settings (`value`, `name`,`created_by`,`created_at`,`updated_at`) values (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`), `updated_at` = VALUES(`updated_at`) ',
                        [
                            $data,
                            $key,
                            $created_by,
                            $created_at,
                            $updated_at,
                        ]
                    );
                }
            }

            return redirect()->back()->with('success', __('Settings updated successfully.'));
        }
    }


    public function saveGoogleCalenderSettings($slug, Request $request)
    {
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $currentWorkspace->is_googlecalendar_enabled = $request->is_googlecalendar_enabled ? 'on' : 'off';

        if ($request->google_calender_json_file) {

            $dir = storage_path() . "/" . md5(time());
            if (!is_dir($dir)) {
                \File::makeDirectory($dir, $mode = 0777, true, true);
            }
            $file_name = $request->google_calender_json_file->getClientOriginalName();
            $file_path = md5(time()) . "/" . md5(time()) . "." . $request->google_calender_json_file->getClientOriginalExtension();
            $file = $request->file('google_calender_json_file');
            $file->move($dir, $file_path);
            $currentWorkspace->google_calender_json_file = $file_path;
        }

        if ($request->google_calender_id) {

            $currentWorkspace->google_calender_id = $request->google_calender_id;
        }

        $currentWorkspace->save();

        return redirect()->back()->with('success', 'Google Calendar Settings updated successfully.');
    }

    public function conpanyEmailSettingStore(Request $request, $slug)
    {
        $user = Auth::user();
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $created_by = $currentWorkspace->id;

        if ($user->type == 'user') {
            $rules = [
                'mail_driver' => 'required|string|max:50',
                'mail_host' => 'required|string|max:50',
                'mail_port' => 'required|string|max:50',
                'mail_username' => 'required|string|max:50',
                'mail_password' => 'required|string|max:255',
                'mail_encryption' => 'required|string|max:50',
                'mail_from_address' => 'required|string|max:50',
                'mail_from_name' => 'required|string|max:50',
            ];

            $request->validate($rules);

            $post['mail_driver'] = $request->input('mail_driver');
            $post['mail_host'] = $request->input('mail_host');
            $post['mail_port'] = $request->input('mail_port');
            $post['mail_username'] = $request->input('mail_username');
            $post['mail_password'] = $request->input('mail_password');
            $post['mail_encryption'] = $request->input('mail_encryption');
            $post['mail_from_address'] = $request->input('mail_from_address');
            $post['mail_from_name'] = $request->input('mail_from_name');

            $created_at = date('Y-m-d H:i:s');
            $updated_at = date('Y-m-d H:i:s');

            foreach ($post as $key => $data) {
                \DB::insert(
                    'INSERT INTO payment_settings (`value`, `name`,`created_by`,`created_at`,`updated_at`)
                    values (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`), `updated_at` = VALUES(`updated_at`) ',
                    [
                        $data,
                        $key,
                        $created_by,
                        $created_at,
                        $updated_at,
                    ]
                );
            }

            return redirect()->back()->with('success', __('Comapny Setting updated successfully'));
        } else {
            return redirect()->back()->with('error', __('Something is wrong'));
        }
    }
}
