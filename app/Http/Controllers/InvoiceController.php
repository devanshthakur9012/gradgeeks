<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\InvoicePayment;
use App\Models\Project;
use App\Models\Task;
use App\Models\Tax;
use App\Models\User;
use App\Exports\invoiceExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Stripe;

class InvoiceController extends Controller
{

    public function __construct()
    {
        if (\Auth::check()) {
            $this->middleware(['auth:client', 'XSS']);
        } else {
            $this->middleware('XSS');
        }
    }

    public function export()
    {
        $name = 'invoice_' . date('Y-m-d i:h:s');
        $data = Excel::download(new invoiceExport(), $name . '.xlsx');
        return $data;
    }

    public function index($slug)
    {
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $objUser = Auth::user();

        if ($currentWorkspace && $objUser) {
            if ($currentWorkspace->creater->id == \Auth::user()->id || $objUser->getGuard() == 'client') {
                $objUser = Auth::user();
                // $currentWorkspace = Utility::getWorkspaceBySlug($slug);
                $invoices = $objUser->getInvoices($currentWorkspace->id);
                $invoices->load('project', 'tax');

                return view('invoices.index', compact('currentWorkspace', 'invoices'));
            } else {
                return redirect()->route('home');
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create($slug)
    {
        $objUser = Auth::user();
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        if ($currentWorkspace->created_by == $objUser->id) {
            $projects = Project::select('projects.*')->join('user_projects', 'projects.id', '=', 'user_projects.project_id')->where('user_projects.user_id', '=', $objUser->id)->where('projects.workspace', '=', $currentWorkspace->id)->get();
            $taxes = Tax::where('workspace_id', '=', $currentWorkspace->id)->get();
            $clients = Client::select('clients.*')->join('client_workspaces', 'client_workspaces.client_id', '=', 'clients.id')->where('client_workspaces.is_active', '=', 1)->where('client_workspaces.workspace_id', '=', $currentWorkspace->id)->get();
            return view('invoices.create', compact('currentWorkspace', 'taxes', 'clients', 'projects'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function store($slug, Request $request)
    {
        $objUser = Auth::user();
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $project_name = Project::where('id', $request->project_id)->first();
        $user1 = $currentWorkspace->id;
        $setting = Utility::getAdminPaymentSettings();

        if ($currentWorkspace->created_by == $objUser->id) {

            $rules = [
                'project_id' => 'required',
                'issue_date' => 'required',
                'due_date' => 'required',
                'discount' => 'required',
                'client_id' => 'required',
            ];
            $validator = \Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $invoice = new Invoice();
            $invoice->invoice_id = $this->invoiceNumber($currentWorkspace->id);
            $invoice->project_id = $request->project_id;
            $invoice->issue_date = $request->issue_date;
            $invoice->due_date = $request->due_date;
            $invoice->discount = $request->discount;
            $invoice->tax_id = $request->tax_id;
            $invoice->client_id = $request->client_id;
            $invoice->status = 'sent';
            $invoice->workspace_id = $currentWorkspace->id;
            $invoice->save();

            $settings = Utility::getPaymentSetting($user1);
            $client = Client::find($request->client_id);

            $uArr = [
                // 'user_name' => $user->name,
                'project_name' => $project_name->name,
                'company_name' => \Auth::user()->name,
                'invoice_id' => Utility::invoiceNumberFormat($invoice->id),
                'client_name' => $client->name,
                'app_url' => env('APP_URL'),
                'app_name' => $setting['app_name'],
            ];

            if (isset($settings['invoice_notificaation']) && $settings['invoice_notificaation'] == 1) {
                Utility::send_slack_msg('New Invoice', $user1, $uArr);
            }

            if (isset($settings['telegram_invoice_notificaation']) && $settings['telegram_invoice_notificaation'] == 1) {
                Utility::send_telegram_msg('New Invoice', $uArr, $user1);
            }

            //webhook
            $module = 'New Invoice';
            // $webhook=  Utility::webhookSetting($module);
            $webhook = Utility::webhookSetting($module, $user1);

            if ($webhook) {
                $parameter = json_encode($invoice);
                // 1 parameter is  URL , 2 parameter is data , 3 parameter is method
                $status = Utility::WebhookCall($webhook['url'], $parameter, $webhook['method']);
                // if($status == true)
                // {
                //     return redirect()->back()->with('success', __('Invoice Save Successfully!'));
                // }
                // else
                // {
                //     return redirect()->back()->with('error', __('Webhook call failed.'));
                // }
            }

            return redirect()->back()->with('success', __('Invoice Save Successfully.!'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    function invoiceNumber($workspace_id)
    {
        $latest = Invoice::where('workspace_id', '=', $workspace_id)->latest()->first();

        return $latest ? $latest->invoice_id + 1 : 1;
    }


    public function show($slug, $id)
    {
        $invoice = Invoice::with('project', 'items.task.invoiceproject')->find($id);
        if (isset($invoice) && $invoice != null) {
            $client = Client::find($invoice->client_id);
            $objUser = Auth::user();
            $currentWorkspace = Utility::getWorkspaceBySlug($slug);
            $paymentSetting = Utility::getPaymentSetting($currentWorkspace->id);
            return view('invoices.show', compact('currentWorkspace', 'invoice', 'paymentSetting', 'client'));
        } else {
            return redirect()->back()->with('error', __('Invoice Not Found.'));
        }
    }

    public function payinvoice($slug, $invoice_id)
    {
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $id = Crypt::decrypt($invoice_id);

        $objUser = Auth::user();
        $invoice = Invoice::find($id);
        $client = Client::find($invoice->client_id);
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $paymentSetting = Utility::getPaymentSetting($currentWorkspace->id);

        return view('invoices.invoicepay', compact('currentWorkspace', 'invoice', 'paymentSetting', 'client'));
    }

    public function edit($slug, Invoice $invoice)
    {
        $objUser = Auth::user();
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        if ($currentWorkspace->created_by == $objUser->id) {
            $projects = Project::select('projects.*')->join('user_projects', 'projects.id', '=', 'user_projects.project_id')->where('user_projects.user_id', '=', $objUser->id)->where('projects.workspace', '=', $currentWorkspace->id)->get();
            $taxes = Tax::where('workspace_id', '=', $currentWorkspace->id)->get();
            $clients = Client::select('clients.*')->join('client_workspaces', 'client_workspaces.client_id', '=', 'clients.id')->where('client_workspaces.is_active', '=', 1)->where('client_workspaces.workspace_id', '=', $currentWorkspace->id)->get();

            return view('invoices.edit', compact('currentWorkspace', 'projects', 'taxes', 'invoice', 'clients'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function update($slug, Request $request, Invoice $invoice)
    {
        $objUser = Auth::user();
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        if ($currentWorkspace->created_by == $objUser->id) {

            $rules = [
                'issue_date' => 'required',
                'due_date' => 'required',
                'discount' => 'required',
                'status' => 'required',
            ];
            $validator = \Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }

            $invoice->issue_date = $request->issue_date;
            $invoice->due_date = $request->due_date;
            $invoice->discount = $request->discount;
            $invoice->tax_id = $request->tax_id;
            $invoice->status = $request->status;
            $invoice->client_id = $request->client_id;
            $invoice->save();

            return redirect()->back()->with('success', __('Invoice Save Successfully.!'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }


    public function destroy($slug, Invoice $invoice)
    {
        $objUser = Auth::user();
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        if ($currentWorkspace->created_by == $objUser->id) {
            $invoice->delete();

            return redirect()->back()->with('success', __('Invoice Deleted Successfully.!'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function create_item($slug, $id)
    {
        $objUser = Auth::user();
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        if ($currentWorkspace->created_by == $objUser->id) {
            $invoice = Invoice::find($id);

            return view('invoices.create_item', compact('currentWorkspace', 'invoice'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function store_item($slug, $id, Request $request)
    {
        $objUser = Auth::user();
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        if ($currentWorkspace->created_by == $objUser->id) {
            $invoice = Invoice::find($id);

            $rules = [
                'task' => 'required',
                'price' => 'required',
            ];
            $validator = \Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $messages = $validator->getMessageBag();

                return redirect()->back()->with('error', $messages->first());
            }
            $task = Task::find($request->task);
            $item = new InvoiceItem();
            $item->item_type = get_class($task);
            $item->item_id = $task->id;
            $item->price = $request->price;
            $item->qty = 1;
            $item->invoice_id = $invoice->id;
            $item->save();

            return redirect()->back()->with('success', __('Item Added Successfully.!'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function destroy_item($slug, $id, $item_id)
    {
        $objUser = Auth::user();
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        if ($currentWorkspace->created_by == $objUser->id) {
            $invoice_item = InvoiceItem::find($item_id);
            $invoice_item->delete();

            return redirect()->back()->with('success', __('Item Deleted Successfully.!'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function printInvoice($slug, $id)
    {

        $objUser = Auth::user();
        // $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $id = Crypt::decryptString($id);
        $invoice = Invoice::find($id);
        $currentWorkspace = $objUser ? Utility::getWorkspaceBySlug($slug) : Utility::getWorkspaceBySlug_copylink('invoice', $invoice->id);
        if ($invoice) {
            $color = '#' . (($currentWorkspace->invoice_color) ? $currentWorkspace->invoice_color : 'ffffff');
            $template = ($currentWorkspace->invoice_template) ? $currentWorkspace->invoice_template : 'template1';

            $font_color = Utility::getFontColor($color);


            return view('invoices.' . $template, compact('currentWorkspace', 'invoice', 'color', 'font_color'));

        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }


    }

    public function previewInvoice($slug, $template, $color)
    {
        $objUser = Auth::user();
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $invoice = new Invoice();

        $project = new \stdClass();
        $project->name = 'UI Design';

        $client = new \stdClass();
        $client->name = '<Client Name>';
        $client->address = '<Address>';
        $client->city = '<City>';
        $client->state = '<State>';
        $client->country = '<Country>';
        $client->zipcode = '<Zipcode>';
        $client->email = '<Client Email>';
        $client->telephone = '<Client Phone Number>';

        $items = [];
        for ($i = 1; $i <= 3; $i++) {
            $task = new \stdClass();
            $task->title = 'Task ' . $i;

            $item = new \stdClass();
            $item->task = $task;
            $item->price = 100;
            $item->qty = 1;
            $items[] = $item;
        }

        $invoice->invoice_id = 1;
        $invoice->issue_date = date('Y-m-d H:i:s');
        $invoice->due_date = date('Y-m-d H:i:s');
        $invoice->project = $project;
        $invoice->client = $client;
        $invoice->discount = 50;
        $invoice->items = $items;

        $preview = 1;

        $color = '#' . ($color != 'undefined' ? $color : 'ffffff');

        $font_color = Utility::getFontColor($color);



        return view('invoices.' . $template, compact('currentWorkspace', 'invoice', 'preview', 'color', 'font_color'));

    }

    public function addPayment($slug, $id, Request $request)
    {
        $objUser = Auth::user();
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $user1 = $currentWorkspace->id;
        $invoice = Invoice::find($id);
        $project_name = Project::where('id', $invoice->project_id)->first();
        $client = Client::find($invoice->client_id);

        if ($invoice) {
            if ($request->amount > $invoice->getDueAmounts($invoice->id)) {
                return redirect()->back()->with('error', __('Invalid amount.'));
            } else {
                try {
                    $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
                    $price = $request->amount;
                    Stripe\Stripe::setApiKey($currentWorkspace->stripe_secret);
                    $data = Stripe\Charge::create(
                        [
                            "amount" => 100 * $price,
                            "currency" => $currentWorkspace->currency_code,
                            "source" => $request->stripeToken,
                            "description" => $currentWorkspace->name . " - " . Utility::invoiceNumberFormat($invoice->invoice_id),
                            "metadata" => ["order_id" => $orderID],
                            "shipping" => [
                                "name" => $request->name,
                                'address' => [
                                    "line1" => "123 Default Street",
                                    "city" => "aaaa",
                                    "state" => "bbbbbb",
                                    "postal_code" => "111111",
                                    "country" => "IN",
                                ]
                            ],
                        ]
                    );

                    if ($data['amount_refunded'] == 0 && empty($data['failure_code']) && $data['paid'] == 1 && $data['captured'] == 1) {
                        InvoicePayment::create(
                            [
                                'order_id' => $orderID,
                                'invoice_id' => $invoice->id,
                                'currency' => $data['currency'],
                                'amount' => $price,
                                'txn_id' => $data['balance_transaction'],
                                'payment_type' => 'STRIPE',
                                'payment_status' => $data['status'],
                                'receipt' => $data['receipt_url'],
                                'client_id' => $invoice->client_id,
                            ]
                        );

                        if (($invoice->getDueAmounts($invoice->id) - $request->amount) == 0) {
                            $invoice->status = 'paid';
                            $invoice->save();
                        } else {
                            $invoice->status = 'partialy paid';
                            $invoice->save();
                        }

                        $settings = Utility::getPaymentSetting($user1);
                        $total_amount = $invoice->getDueAmounts($invoice->id);

                        $uArr = [
                            // 'user_name' => $user->name,
                            'project_name' => $project_name->name,
                            'company_name' => User::find($project_name->created_by)->name,
                            'invoice_id' => Utility::invoiceNumberFormat($invoice->id),
                            'client_name' => $client->name,
                            'total_amount' => "$total_amount",
                            'paid_amount' => $request->amount,
                        ];

                        if (isset($settings['invoicest_notificaation']) && $settings['invoicest_notificaation'] == 1) {
                            Utility::send_slack_msg('Invoice Status Updated', $user1, $uArr);
                        }

                        if (isset($settings['telegram_invoicest_notificaation']) && $settings['telegram_invoicest_notificaation'] == 1) {
                            Utility::send_telegram_msg('Invoice Status Updated', $uArr, $user1);
                        }

                        //webhook
                        $module = 'Invoice Status Updated';
                        $webhook = Utility::webhookSetting($module, $user1);

                        // $webhook=  Utility::webhookSetting($module);
                        if ($webhook) {
                            $parameter = json_encode($invoice);
                            // 1 parameter is  URL , 2 parameter is data , 3 parameter is method
                            $status = Utility::WebhookCall($webhook['url'], $parameter, $webhook['method']);
                            // if($status == true)
                            // {
                            //     return redirect()->back()->with('success', __('Payment added Successfully!'));
                            // }
                            // else
                            // {
                            //     return redirect()->back()->with('error', __('Webhook call failed.'));
                            // }
                        }

                        return redirect()->back()->with('success', __(' Payment added Successfully'));
                    } else {
                        return redirect()->back()->with('error', __('Transaction has been failed!'));
                    }

                } catch (\Exception $e) {
                    if (\Auth::check()) {
                        return redirect()->route(
                            'client.invoices.show',
                            [
                                $currentWorkspace->slug,
                                $invoice->id,
                            ]
                        )->with('error', __($e->getMessage()));
                    } else {
                        return redirect()->route('pay.invoice', [$slug, Crypt::encrypt($invoice->id)])->with('error', __($e->getMessage()));
                    }
                }
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
    public function addManualPayment($slug, $id, Request $request)
    {
        $objUser = Auth::user();
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $invoice = Invoice::find($id);
        $project_name = Project::where('id', $invoice->project_id)->first();
        $user1 = $currentWorkspace->id;
        if ($invoice && Auth::guard('web')->check()) {
            if ($request->amount > $invoice->getDueAmounts($invoice->id)) {
                return redirect()->back()->with('error', __('Invalid amount.'));
            } else {

                $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
                $price = $request->amount;

                InvoicePayment::create(
                    [
                        'order_id' => $orderID,
                        'invoice_id' => $invoice->id,
                        'currency' => $currentWorkspace->currency_code,
                        'amount' => $price,
                        'txn_id' => '',
                        'payment_type' => 'Manual',
                        'payment_status' => 'succeeded',
                        'receipt' => '',
                        'client_id' => $objUser->id,
                    ]
                );

                if (($invoice->getDueAmounts($invoice->id) - $request->amount) == 0) {
                    $invoice->status = 'paid';
                    $invoice->save();
                } else {
                    $invoice->status = 'partialy paid';
                    $invoice->save();
                }

                $client = Client::find($invoice->client_id);
                $total_amount = $invoice->getDueAmounts($invoice->id);
                $settings = Utility::getPaymentSetting($user1);

                $uArr = [
                    // 'user_name' => $user->name,
                    'project_name' => $project_name->name,
                    'company_name' => \Auth::user()->name,
                    'invoice_id' => Utility::invoiceNumberFormat($invoice->id),
                    'client_name' => \Auth::user()->name,
                    'total_amount' => "$total_amount",
                    'paid_amount' => $request->amount,
                ];

                if (isset($settings['invoicest_notificaation']) && $settings['invoicest_notificaation'] == 1) {

                    Utility::send_slack_msg('Invoice Status Updated', $user1, $uArr);
                }

                if (isset($settings['telegram_invoicest_notificaation']) && $settings['telegram_invoicest_notificaation'] == 1) {
                    Utility::send_telegram_msg('Invoice Status Updated', $uArr, $user1);
                }

                //webhook
                $module = 'Invoice Status Updated';
                $webhook = Utility::webhookSetting($module, $user1);

                // $webhook=  Utility::webhookSetting($module);
                if ($webhook) {
                    $parameter = json_encode($invoice);
                    // 1 parameter is  URL , 2 parameter is data , 3 parameter is method
                    $status = Utility::WebhookCall($webhook['url'], $parameter, $webhook['method']);
                    // if($status == true)
                    // {
                    //     return redirect()->back()->with('success', __('Payment added Successfully!'));
                    // }
                    // else
                    // {
                    //     return redirect()->back()->with('error', __('Webhook call failed.'));
                    // }
                }

                return redirect()->back()->with('success', __(' Payment added Successfully'));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }
}
