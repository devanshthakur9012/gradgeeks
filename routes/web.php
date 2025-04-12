<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\ProjectReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\StripePaymentController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\WorkspaceController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\PaypalController;
use App\Http\Controllers\CalenderController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\PaystackPaymentController;
use App\Http\Controllers\ToyyibpayController;
use App\Http\Controllers\FlutterwavePaymentController;
use App\Http\Controllers\RazorpayPaymentController;
use App\Http\Controllers\MercadoPaymentController;
use App\Http\Controllers\CoingatePaymentController;
use App\Http\Controllers\ZoomMeetingController;
use App\Http\Controllers\PaymentWallPaymentController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\TimeTrackerController;
use App\Http\Controllers\EmailTemplateController;
use App\Http\Controllers\ContractsTypeController;
use App\Http\Controllers\PlanRequestController;
use App\Http\Controllers\PaytmPaymentController;
use App\Http\Controllers\MolliePaymentController;
use App\Http\Controllers\SkrillPaymentController;
use App\Http\Controllers\PayfastController;
use App\Http\Controllers\LoginDetailController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\BankTransferController;
use App\Http\Controllers\NotificationTemplatesController;
use App\Http\Controllers\AiTemplateController;
use App\Http\Controllers\IyziPayController;
use App\Http\Controllers\SspayController;
use App\Http\Controllers\LanguagesController;
use App\Http\Controllers\PaytabController;
use App\Http\Controllers\BenefitPaymentController;
use App\Http\Controllers\CashfreeController;
use App\Http\Controllers\AamarpayController;
use App\Http\Controllers\PaytrController;
use App\Http\Controllers\MidtransController;
use App\Http\Controllers\XenditPaymentController;
use App\Http\Controllers\YooKassaController;
use App\Http\Controllers\PaiementproController;
use App\Http\Controllers\NepalsteController;
use App\Http\Controllers\CinetPayController;
use App\Http\Controllers\FedapayPaymentController;
use App\Http\Controllers\GoogleAuthenticationController;
use App\Http\Controllers\PayHerePaymentController;
use App\Http\Controllers\PayUController;
use App\Http\Controllers\PowertranzPaymentController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth'])->name('dashboard');


require __DIR__ . '/auth.php';

Route::get('/verify-email/{lang?}', [AuthenticatedSessionController::class, 'showVerifcation'])->name('verification.notice')->middleware('auth', 'XSS');
Route::get('/verify-email/{id}/{hash}', [VerifyEmailController::class, '__invoke',])->name('verification.verify')->middleware('auth', 'XSS');
Route::get('/email/verification-notification', [EmailVerificationNotificationController::class, 'store',])->name('verification.send')->middleware('auth', 'XSS');

Route::get('/', [HomeController::class, 'landingPage'])->middleware(['XSS']);
Route::get('/{slug}/invoices/{id}/pay', [InvoiceController::class, 'payinvoice'])->name('pay.invoice');

// Route for Login as admin
Route::get('home/{id}/login-with-admin', [HomeController::class, 'LoginWithAdmin'])->name('login.with.admin')->middleware('auth', 'XSS');
Route::get('login-with-admin/exit', [HomeController::class, 'ExitAdmin'])->name('exit.admin')->middleware('auth', 'XSS');

Route::get('login/{lang?}', [AuthenticatedSessionController::class, 'showLoginForm'])->name('login')->middleware(['XSS']);
Route::get('register/{lang?}', [RegisteredUserController::class, 'showRegistrationForm'])->name('register')->middleware(['XSS']);

Route::get('password/resets/{lang?}', [AuthenticatedSessionController::class, 'showLinkRequestForm'])->name('password.request')->middleware(['XSS']);

Route::get('/{slug}/contract/pdf/{id}', [ContractController::class, 'pdffromcontract'])->name('contract.download.pdf');
Route::get('/{slug}/contract/{id}/get_contract', [ContractController::class, 'printContract'])->name('get.contract');


// Route::any('/{slug}/project/link/{id?}',[ProjectController::class,'projectPassCheck'])->name('project.passcheck');
Route::any('/{slug}/projects/link/{id}/{lang?}', [ProjectController::class, 'projectlink'])->name('projects.link');

Route::any('/{slug}/projects/copy/link/{id}', [ProjectController::class, 'copylinksetting'])->name('projects.copy.link');
Route::get('/{slug}/projects{id}/edit', [ProjectController::class, 'copylink_setting_create'])->name('projects.copylink.setting.create');

// Route::get('/workspace/change_lang_copylink/{lang}',[WorkspaceController::class, 'changeLangcopylink'])->name('change_lang_copylink')->middleware(['XSS']);
Route::get('/{slug}/projects/{id}/bug_report/{bid}/show', [ProjectController::class, 'bugReportShow'])->name('projects.bug.report.show')->middleware(['XSS']);
Route::get('/{slug}/timesheet-table-view', [ProjectController::class, 'filterTimesheetTableView'])->name('filter.timesheet.table.view')->middleware(['XSS']);




//================================= Invoice Payment Gateways for Copylink ====================================//

Route::post('/{slug}/invoice-pay-with-paystack/{invoice_id}', [PaystackPaymentController::class, 'invoicePayWithPaystack'])->name('invoice.pay.with.paystack')->middleware(['XSS']);
Route::get('/{slug}/invoice/paystack/{pay_id}/{invoice_id}', [PaystackPaymentController::class, 'getInvoicePaymentStatus'])->name('invoice.paystack');

Route::post('/{slug}/invoice-pay-with-flaterwave/{invoice_id}', [FlutterwavePaymentController::class, 'invoicePayWithFlutterwave'])->name('invoice.pay.with.flaterwave')->middleware(['XSS']);
Route::get('/{slug}/invoice/flaterwave/{txref}/{invoice_id}', [FlutterwavePaymentController::class, 'getInvoicePaymentStatus'])->name('invoice.flaterwave');

Route::post('/{slug}/invoice-pay-with-razorpay/{invoice_id}', [RazorpayPaymentController::class, 'invoicePayWithRazorpay'])->name('invoice.pay.with.razorpay')->middleware(['XSS']);
Route::get('/{slug}/invoice/razorpay/{txref}/{invoice_id}', [RazorpayPaymentController::class, 'getInvoicePaymentStatus'])->name('invoice.razorpay');

Route::post('/{slug}/invoice-pay-with-paytm/{invoice_id}', [PaytmPaymentController::class, 'invoicePayWithPaytm'])->name('invoice.pay.with.paytm')->middleware(['XSS']);
// Route::post('/{slug}/invoice/paytm/{invoice}', [PaytmPaymentController::class, 'getInvoicePaymentStatus'])->name('invoice.paytm');
Route::post('invoice/paytm', [PaytmPaymentController::class, 'getInvoicePaymentStatus'])->name('invoice.paytm');


Route::post('/{slug}/invoice-pay-with-mercado/{invoice_id}', [MercadoPaymentController::class, 'invoicePayWithMercado'])->name('invoice.pay.with.mercado')->middleware(['XSS']);
Route::get('/{slug}/invoice/mercado/{invoice}', [MercadoPaymentController::class, 'getInvoicePaymentStatus'])->name('invoice.mercado');

Route::post('/{slug}/invoice-pay-with-mollie/{invoice_id}', [MolliePaymentController::class, 'invoicePayWithMollie'])->name('invoice.pay.with.mollie')->middleware(['XSS']);
Route::get('/{slug}/invoice/mollie/{invoice}', [MolliePaymentController::class, 'getInvoicePaymentStatus'])->name('invoice.mollie');

Route::post('/{slug}/invoice-pay-with-skrill/{invoice_id}', [SkrillPaymentController::class, 'invoicePayWithSkrill'])->name('invoice.pay.with.skrill')->middleware(['XSS']);
Route::get('/{slug}/invoice/skrill/{invoice}', [SkrillPaymentController::class, 'getInvoicePaymentStatus'])->name('invoice.skrill');

Route::post('/{slug}/invoice-pay-with-coingate/{invoice_id}', [CoingatePaymentController::class, 'invoicePayWithCoingate'])->name('invoice.pay.with.coingate')->middleware(['XSS']);
Route::get('/{slug}/invoice/coingate/{invoice}', [CoingatePaymentController::class, 'getInvoicePaymentStatus'])->name('invoice.coingate');

Route::post('/{slug}/invoice-pay-with-toyyibpay/{invoice_id}', [ToyyibpayController::class, 'invoicepaywithtoyyibpay'])->name('invoice.pay.with.toyyibpay')->middleware(['XSS']);
Route::get('/{slug}/invoice/toyyibpay/{invoice}/{amt}', [ToyyibpayController::class, 'getInvoicePaymentStatus'])->name('invoice.toyyibpay');

Route::post('/{slug}/invoices/{id}/payment', [InvoiceController::class, 'addPayment'])->name('invoice.payment')->middleware(['XSS']);

Route::post('/{slug}/{id}/pay-with-paypal', [PaypalController::class, 'clientPayWithPaypal'])->name('pay.with.paypal')->middleware(['XSS']);
Route::get('/{slug}/{id}/get-payment-status', [PaypalController::class, 'clientGetPaymentStatus'])->name('get.payment.status')->middleware(['XSS']);


Route::post('/{slug}/invoice-pay-with-payfast/{invoice_id}', [PayfastController::class, 'invoicePayWithpayfast'])->name('invoice.pay.with.payfast')->middleware(['XSS']);
Route::get('/invoice/payfast/{status}', [PayfastController::class, 'getInvoicePaymentStatus'])->name('invoice.payfast');


/*==============================================Invoice Paymentwall===========================================================*/
Route::any('{slug}/paymentwall/invoice/{invoice_id}', [PaymentWallPaymentController::class, 'invoiceindex'])->name('paymentwall.index');
Route::post('{slug}/invoice-pay-with-paymentwall/{invoice_id}', [PaymentWallPaymentController::class, 'invoicePayWithPaymentwall'])->name('invoice.pay.with.paymentwall');
Route::any('{slug}/invoice/error/{flag}/{invoice_id}', [PaymentWallPaymentController::class, 'orderpaymenterror'])->name('invoice.callback.error');

Route::post('/{slug}/invoice-pay-with-iyzipay/{invoice_id}', [IyziPayController::class, 'invoicepaywithiyzipay'])->name('invoice.pay.with.iyzipay')->middleware(['XSS']);
Route::any('/{slug}/invoice/iyzipay/{invoice}/{amt}', [IyziPayController::class, 'getInvoicePaymentStatus'])->name('invoice.iyzipay')->middleware(['XSS']);

Route::post('/{slug}/invoice-pay-with-sspay/{invoice_id}', [SspayController::class, 'invoicepaywithsspay'])->name('invoice.pay.with.sspay')->middleware(['XSS']);
Route::any('/{slug}/invoice/sspay/{invoice}/{amt}', [SspayController::class, 'getInvoicePaymentStatus'])->name('invoice.sspay')->middleware(['XSS']);

Route::post('/{slug}/invoice-pay-with-paytab/{invoice_id}', [PaytabController::class, 'invoicePayWithpaytab'])->name('invoice.pay.with.paytab');
Route::any('/{slug}/invoice-paytab-success/', [PaytabController::class, 'getInvoicePaymentStatus'])->name('invoice.paytab.success');

Route::post('/{slug}/invoice-pay-with-benefit/{invoice_id}', [BenefitPaymentController::class, 'invoicePayWithbenefit'])->name('invoice.pay.with.benefit');
Route::any('/{slug}/invoice-benefit-success/', [BenefitPaymentController::class, 'getInvoicePaymentStatus'])->name('invoice.benefit.success');

Route::post('/{slug}/invoice-pay-with-cashfree/{invoice_id}', [CashfreeController::class, 'invoicePayWithcashfree'])->name('invoice.pay.with.cashfree');
Route::any('/{slug}/invoice-cashfree-success/', [CashfreeController::class, 'getInvoicePaymentStatus'])->name('invoice.cashfree.success');

///aamarpay route
Route::post('/{slug}/aamarpay/payment/{invoice_id}', [AamarpayController::class, 'invoicePayWithAamarpay'])->name('invoice.pay.with.aamarpay');
Route::any('/{slug}/aamarpay/success/{data}', [AamarpayController::class, 'getInvoicePaymentStatus'])->name('invoice.aamarpay.success');


//paytr route
Route::post('/{slug}/paytr/payment/{invoice_id}', [PaytrController::class, 'invoicePayWithPaytr'])->name('invoice.pay.with.paytr');
Route::get('/paytr/success/', [PaytrController::class, 'getInvoicePaymentStatus'])->name('invoice.paytr.success');


//midtrans route
Route::post('/{slug}/midtrans/payment/{invoice_id}', [MidtransController::class, 'invoicePayWithMidtrans'])->name('invoice.pay.with.midtrans');
Route::any('{slug}/midtrans/status/', [MidtransController::class, 'getInvoicePaymentStatus'])->name('invoice.midtrans.status');

// xendit routes
Route::any('/{slug}/xendit/payment/{invoice_id}', [XenditPaymentController::class, 'invoicePayWithXendit'])->name('invoice.pay.with.xendit');
Route::any('{slug}/xendit/status/', [XenditPaymentController::class, 'getInvoicePaymentStatus'])->name('invoice.xendit.status');

// yookassa routes
Route::any('/{slug}/yookassa/payment/{invoice_id}', [YooKassaController::class, 'invoicePayWithYookassa'])->name('invoice.pay.with.yookassa');
Route::any('{slug}/yookassa/status/', [YooKassaController::class, 'getInvoicePaymentStatus'])->name('invoice.yookassa.status');

// paiementpro invoice routes
Route::any('/{slug}/paiementpro/payment/{invoice_id}', [PaiementproController::class, 'invoicePayWithPaiementpro'])->name('invoice.pay.with.paiementpro');
Route::any('{slug}/paiementpro/status/', [PaiementproController::class, 'getInvoicePaymentStatus'])->name('invoice.paiementpro.status');

// Nepalste invoice routes
Route::any('/{slug}/nepalste/payment/{invoice_id}', [NepalsteController::class, 'invoicePayWithNepalste'])->name('invoice.pay.with.nepalste');
Route::any('{slug}/nepalste/status/', [NepalsteController::class, 'getInvoicePaymentStatus'])->name('invoice.nepalste.status');
Route::get('{slug}/invoice-nepalste-cancel/', [NepalsteController::class, 'invoiceGetNepalsteCancel'])->name('invoice.nepalste.cancel');

// cinetpay invoice routes
Route::any('/{slug}/cinetpay/payment/{invoice_id}', [CinetPayController::class, 'invoicePayWithCinetpay'])->name('invoice.pay.with.cinetpay');
Route::any('invoice-cinetpay-return/{id}/{amt?}/{slug}', [CinetPayController::class, 'getInvoicePaymentStatus'])->name('invoice.cinetpay.return');
Route::any('invoice-cinetpay-notify/{id?}', [CinetPayController::class, 'invoiceCinetPayNotify'])->name('invoice.cinetpay.notify');

// fedapay invoice routes
Route::any('/{slug}/fedapay/payment/{invoice_id}', [FedapayPaymentController::class, 'invoicePayWithFedapay'])->name('invoice.pay.with.fedapay');
Route::any('{slug}/fedapay/status/', [FedapayPaymentController::class, 'getInvoicePaymentStatus'])->name('invoice.fedapay.status');

// payhere invoice routes
Route::any('/{slug}/payhere/payment/{invoice_id}', [PayHerePaymentController::class, 'invoicePayWithPayhere'])->name('invoice.pay.with.payhere');
Route::any('invoice-payhere-status/{id}/{amt?}/{slug}', [PayHerePaymentController::class, 'invoiceGetPayHereStatus'])->name('invoice.payhere.status');

// powertranz routes
Route::any('/{slug}/powertranz/payment/{invoice_id}', [PowertranzPaymentController::class, 'invoicePayWithPowertranz'])->name('invoice.pay.powertranz.view');
Route::any('invoice-powertranz/response/{slug}', [PowertranzPaymentController::class, 'InvoiceResponse'])->name('powertranz.invoice.response');
Route::any('invoice-powertranz-status/{id}/{amt?}/{slug}', [PowertranzPaymentController::class, 'invoiceGetPowertranzStatus'])->name('invoice.powertranz.status');

//payu routes
Route::any('/payu/{pay}', [PayUController::class, 'pay'])->name('payu.pay');
Route::any('/{slug}/payu/payment/{invoice_id}', [PayUController::class, 'invoicePayWithPayu'])->name('invoice.pay.with.payu');
Route::any('invoice-payu-status/{id}', [PayUController::class, 'getPayuStatus'])->name('invoice.get.payu.status');

//================================= End Invoice Payment Gateways  ====================================//

// Chat GTP
Route::post('chatgptkey', [SettingsController::class, 'chatgptkey'])->name('settings.chatgptkey');
Route::get('generate/{template_name}/{formate?}', [AiTemplateController::class, 'create'])->name('generate');
Route::post('generate/keywords/{id}', [AiTemplateController::class, 'getKeywords'])->name('generate.keywords');
Route::post('generate/response', [AiTemplateController::class, 'AiGenerate'])->name('generate.response');

//Message AI
Route::get('grammar/{template}', [AiTemplateController::class, 'grammar'])->name('grammar')->middleware(['XSS']);
Route::post('grammar/response', [AiTemplateController::class, 'grammarProcess'])->name('grammar.response')->middleware(['XSS']);


Route::get('/config-cache', function () {
    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    Artisan::call('optimize:clear');
    return redirect()->back()->with('success', 'Clear Cache successfully.');
});


Route::post('/{slug}/invoice-pay-with-bank/{invoice_id}', [BankTransferController::class, 'invoicePayWithBank'])->name('invoice.pay.with.bank')->middleware(['XSS']);
Route::post('/InvoicePaymentApproval/{order}', [BankTransferController::class, 'invoicebankPaymentApproval'])->name('invoicebankPaymentApproval.response')->middleware(['auth', 'XSS']);
Route::get('/{slug}/invoice/{id}/status', [BankTransferController::class, 'invoice_status_show'])->name('invoice.status.show');
Route::delete('invoice/payments/{id}', [BankTransferController::class, 'invoice_payment_destroy'])->name('invoice.payments.destroy')->middleware(['auth', 'XSS']);
Route::get('/{slug}/contract/pdf/{id}', [ContractController::class, 'pdffromcontract'])->name('contract.download.pdf');


Route::prefix('client')->as('client.')->group(function () {
    Route::post('login', [AuthenticatedSessionController::class, 'clientLogin'])->middleware(['XSS']);
    Route::get('home/{slug?}', [HomeController::class, 'index'])->name('home')->middleware(['auth:client', 'XSS']);
    Route::get('login/{lang?}', [AuthenticatedSessionController::class, 'showClientLoginForm'])->name('login')->middleware(['XSS']);
    Route::post('logout', [ClientController::class, 'clientLogout'])->name('logout')->middleware(['auth:client', 'XSS']);
    Route::get('/{slug}/invoices/{id}/print', [InvoiceController::class, 'printInvoice'])->name('invoice.print')->middleware('XSS');


    Route::post('/{slug}/contract_status_edit/{id}', [ContractController::class, 'contract_status_edit'])->name('contract.status')->middleware(['auth:client', 'XSS']);


    Route::post('/{slug}/projects/{id}/comment/{tid}/file/{cid?}', [ProjectController::class, 'commentStoreFile'])->name('comment.store.file')->middleware(['auth:client', 'XSS']);
    Route::delete('/{slug}/projects/{id}/comment/{tid}/file/{fid}', [ProjectController::class, 'commentDestroyFile'])->name('comment.destroy.file')->middleware(['auth:client', 'XSS']);
    Route::post('/{slug}/projects/{id}/comment/{tid}/{cid?}', [ProjectController::class, 'commentStore'])->name('comment.store')->middleware(['auth:client', 'XSS']);
    Route::delete('/{slug}/projects/{id}/comment/{tid}/{cid}', [ProjectController::class, 'commentDestroy'])->name('comment.destroy')->middleware(['auth:client', 'XSS']);
    Route::post('/{slug}/projects/{id}/sub-task/update/{stid}', [ProjectController::class, 'subTaskUpdate'])->name('subtask.update')->middleware(['auth:client', 'XSS']);
    Route::post('/{slug}/projects/{id}/sub-task/{tid}/{cid?}', [ProjectController::class, 'subTaskStore'])->name('subtask.store')->middleware(['auth:client', 'XSS']);
    Route::delete('/{slug}/projects/{id}/sub-task/{stid}', [ProjectController::class, 'subTaskDestroy'])->name('subtask.destroy')->middleware(['auth:client', 'XSS']);


    Route::get('/{slug}/contract/pdf/{id}', [ContractController::class, 'pdffromcontract'])->name('contract.download.pdf');
    Route::get('/{slug}/contract/{id}/get_contract', [ContractController::class, 'printContract'])->name('get.contract');

    Route::post('/{slug}/invoice-pay-with-bank/{invoice_id}', [BankTransferController::class, 'invoicePayWithBank'])->name('invoice.pay.with.bank')->middleware(['auth:client', 'XSS']);


    /*==============================================Invoice Paymentwall===========================================================*/
    Route::any('{slug}/paymentwall/invoice/{invoice_id}', [PaymentWallPaymentController::class, 'invoiceindex'])->name('paymentwall.index')->middleware(['auth:client', 'XSS']);
    Route::post('{slug}/invoice-pay-with-paymentwall/{invoice_id}', [PaymentWallPaymentController::class, 'invoicePayWithPaymentwall'])->name('invoice.pay.with.paymentwall')->middleware(['auth:client', 'XSS']);
    Route::any('{slug}/invoice/error/{flag}/{invoice_id}', [PaymentWallPaymentController::class, 'orderpaymenterror'])->name('invoice.callback.error')->middleware(['auth:client', 'XSS']);


    // Route for add cooment in bug report
    Route::post('/{slug}/projects/{id}/bug_comment/{tid}/{cid?}', [ProjectController::class, 'bugCommentStore'])->name('bug.comment.store');
    // Route for add images in project bug report
    Route::post('/{slug}/projects/{id}/bug_comment_store_file/{tid}/file/{cid?}', [ProjectController::class, 'bugStoreFile'])->name('bug.comment.store.file');
    // Route for Remove image from project Bug
    Route::delete('/{slug}/projects/{id}/bug_comment/{tid}/file/{fid}', [ProjectController::class, 'bugDestroyFile'])->name('bug.comment.destroy.file');
});

Route::group(['middleware' => ['verified']], function () {

    Route::get('/check', [HomeController::class, 'check'])->middleware(['auth', 'XSS']);
    Route::get('/home', [HomeController::class, 'index'])->name('home')->middleware(['auth', 'XSS']);
    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard')->middleware(['auth', 'XSS']);


    Route::prefix('client')->as('client.')->group(function () {

        Route::get('/my-account', [UserController::class, 'account'])->name('users.my.account')->middleware(['auth:client', 'XSS']);
        Route::post('/{slug}/my-account/{id}/update', [ClientController::class, 'update'])->name('update.account')->middleware(['auth:client', 'XSS']);
        Route::post('/my-account/password', [UserController::class, 'updatePassword'])->name('update.password')->middleware(['auth:client', 'XSS']);
        Route::post('/my-account/billing', [ClientController::class, 'updateBilling'])->name('update.billing')->middleware(['auth:client', 'XSS']);
        Route::delete('/my-account', [UserController::class, 'deleteAvatar'])->name('delete.avatar')->middleware(['auth:client', 'XSS']);

        // project
        Route::get('/{slug}/projects', [ProjectController::class, 'index'])->name('projects.index')->middleware(['auth:client', 'XSS']);
        Route::get('/{slug}/projects/{id}', [ProjectController::class, 'show'])->name('projects.show')->middleware(['auth:client', 'XSS']);
        Route::get('/{slug}/projects/milestone/{id}', [ProjectController::class, 'milestone'])->name('projects.milestone')->middleware(['auth:client', 'XSS']);
        Route::post('/{slug}/projects/milestone/{id}/store', [ProjectController::class, 'milestoneStore'])->name('projects.milestone.store')->middleware(['auth:client', 'XSS']);
        Route::get('/{slug}/projects/milestone/{id}/show', [ProjectController::class, 'milestoneShow'])->name('projects.milestone.show')->middleware(['auth:client', 'XSS']);
        Route::get('/{slug}/projects/milestone/{id}/edit', [ProjectController::class, 'milestoneEdit'])->name('projects.milestone.edit')->middleware(['auth:client', 'XSS']);
        Route::post('/{slug}/projects/milestone/{id}/update', [ProjectController::class, 'milestoneUpdate'])->name('projects.milestone.update')->middleware(['auth:client', 'XSS']);
        Route::delete('/{slug}/projects/milestone/{id}', [ProjectController::class, 'milestoneDestroy'])->name('projects.milestone.destroy')->middleware(['auth:client', 'XSS']);
        Route::get('/{slug}/projects/{id}/file/{fid}', [ProjectController::class, 'fileDownload'])->name('projects.file.download')->middleware(['auth:client', 'XSS']);
        Route::delete('/{slug}/projects/{id}/file/delete/{fid}', [ProjectController::class, 'fileDelete'])->name('projects.file.delete')->middleware(['auth:client', 'XSS']);

        // Task Board
        Route::get('/{slug}/projects/{id}/task-board', [ProjectController::class, 'taskBoard'])->name('projects.task.board')->middleware(['auth:client', 'XSS']);
        Route::get('/{slug}/projects/{id}/task-board/create', [ProjectController::class, 'taskCreate'])->name('tasks.create')->middleware(['auth:client', 'XSS']);
        Route::post('/{slug}/projects/{id}/task-board', [ProjectController::class, 'taskStore'])->name('tasks.store')->middleware(['auth:client', 'XSS']);
        Route::post('/{slug}/projects/{id}/task-board/order-update', [ProjectController::class, 'taskOrderUpdate'])->name('tasks.update.order')->middleware(['auth:client', 'XSS']);
        Route::get('/{slug}/projects/{id}/task-board/edit/{tid}', [ProjectController::class, 'taskEdit'])->name('tasks.edit')->middleware(['auth:client', 'XSS']);
        Route::post('/{slug}/projects/{id}/task-board/{tid}/update', [ProjectController::class, 'taskUpdate'])->name('tasks.update')->middleware(['auth:client', 'XSS']);
        Route::delete('/{slug}/projects/{id}/task-board/{tid}', [ProjectController::class, 'taskDestroy'])->name('tasks.destroy')->middleware(['auth:client', 'XSS']);
        Route::get('/{slug}/projects/{id}/task-board/{tid}/{cid?}', [ProjectController::class, 'taskShow'])->name('tasks.show')->middleware(['auth:client', 'XSS']);

        Route::get('/{slug}/timesheet', [ProjectController::class, 'timesheet'])->name('timesheet.index')->middleware(['auth:client', 'XSS']);
        Route::get('/{slug}/timesheet-table-view', [ProjectController::class, 'filterTimesheetTableView'])->name('filter.timesheet.table.view')->middleware(['auth:client', 'XSS']);
        Route::get('/{slug}/timesheet/{id}', [ProjectController::class, 'projectsTimesheet'])->name('projects.timesheet.index')->middleware(['auth:client', 'XSS']);

        // Gantt Chart
        Route::get('/{slug}/projects/{id}/gantt/{duration?}', [ProjectController::class, 'gantt'])->name('projects.gantt')->middleware(['auth:client', 'XSS']);
        Route::post('/{slug}/projects/{id}/gantt', [ProjectController::class, 'ganttPost'])->name('projects.gantt.post')->middleware(['auth:client', 'XSS']);

        // bug report
        Route::get('/{slug}/projects/{id}/bug_report', [ProjectController::class, 'bugReport'])->name('projects.bug.report')->middleware(['auth:client', 'XSS']);
        Route::get('/{slug}/projects/{id}/bug_report/create', [ProjectController::class, 'bugReportCreate'])->name('projects.bug.report.create')->middleware(['auth:client', 'XSS']);
        Route::post('/{slug}/projects/{id}/bug_report', [ProjectController::class, 'bugReportStore'])->name('projects.bug.report.store')->middleware(['auth:client', 'XSS']);
        Route::post('/{slug}/projects/{id}/bug_report/order-update', [ProjectController::class, 'bugReportOrderUpdate'])->name('projects.bug.report.update.order')->middleware(['auth:client', 'XSS']);
        Route::get('/{slug}/projects/{id}/bug_report/{bid}/show', [ProjectController::class, 'bugReportShow'])->name('projects.bug.report.show')->middleware(['auth:client', 'XSS']);
        Route::get('/{slug}/projects/{id}/bug_report/{bid}/edit', [ProjectController::class, 'bugReportEdit'])->name('projects.bug.report.edit')->middleware(['auth:client', 'XSS']);
        Route::post('/{slug}/projects/{id}/bug_report/{bid}/update', [ProjectController::class, 'bugReportUpdate'])->name('projects.bug.report.update')->middleware(['auth:client', 'XSS']);
        Route::delete('/{slug}/projects/{id}/bug_report/{bid}', [ProjectController::class, 'bugReportDestroy'])->name('projects.bug.report.destroy')->middleware(['auth:client', 'XSS']);

        Route::get('/{slug}/searchJson/{search?}', [ProjectController::class, 'getSearchJson'])->name('search.json')->middleware(['auth:client', 'XSS']);
        Route::get('/userProjectJson/{id}', [UserController::class, 'getProjectUserJson'])->name('user.project.json')->middleware(['auth:client', 'XSS']);
        Route::get('/projectMilestoneJson/{id}', [UserController::class, 'getProjectMilestoneJson'])->name('project.milestone.json')->middleware(['auth:client', 'XSS']);

        Route::get('/{slug}/invoices', [InvoiceController::class, 'index'])->name('invoices.index')->middleware(['auth:client', 'XSS']);
        Route::get('/{slug}/invoices/{id}', [InvoiceController::class, 'show'])->name('invoices.show')->middleware(['auth:client', 'XSS']);
        Route::post('/{slug}/invoices/{id}/payment', [InvoiceController::class, 'addPayment'])->name('invoice.payment')->middleware(['auth:client', 'XSS']);
        Route::get('/workspace/{id}', [WorkspaceController::class, 'changeCurrentWorkspace'])->name('change-workspace')->middleware(['auth:client', 'XSS']);

        Route::get('/{slug}/calendar/{id?}', [CalenderController::class, 'index'])->name('calender.index')->middleware(['auth:client', 'XSS']);
        Route::any('/{slug}/calendar/{id?}', [CalenderController::class, 'calendar'])->name('calender.google.calendar')->middleware(['auth:client', 'XSS']);

        Route::any('{slug}/event/get_event_data', [ZoomMeetingController::class, 'get_event_data'])->name('event.get_event_data')->middleware(['auth:client', 'XSS']);



        Route::post('/{slug}/{id}/pay-with-paypal', [PaypalController::class, 'clientPayWithPaypal'])->name('pay.with.paypal')->middleware(['auth:client', 'XSS']);
        Route::get('/{slug}/{id}/get-payment-status', [PaypalController::class, 'clientGetPaymentStatus'])->name('get.payment.status')->middleware(['auth:client', 'XSS']);



        ////**===================================== Project Reports =======================================================////

        Route::resource('/{slug}/project_report', ProjectReportController::class)->except(['show'])->middleware(['auth:client', 'XSS']);
        Route::post('/{slug}/project_report_data', [ProjectReportController::class, 'ajax_data'])->name('projects.ajax')->middleware(['auth:client', 'XSS']);
        Route::get('/{slug}/project_report/{id}', [ProjectReportController::class, 'show'])->name('project_report.show')->middleware(['auth:client', 'XSS']);

        Route::post('/{slug}/project_report/tasks/{id}', [ProjectReportController::class, 'ajax_tasks_report'])->name('tasks.report.ajaxdata')->middleware(['auth:client', 'XSS']);
        Route::get('export/task_report/{id}', [ProjectReportController::class, 'export'])->name('project_report.export')->middleware(['auth:client', 'XSS']);



        ////**===================================== Client Contract Module =======================================================////
        Route::resource('/{slug}/contracts', ContractController::class)->middleware(['auth:client', 'XSS']);
        Route::get('/{slug}/signature/{id}', [ContractController::class, 'signature'])->name('signature')->middleware(['auth:client', 'XSS']);
        Route::post('/{slug}/signaturestore', [ContractController::class, 'signatureStore'])->name('signaturestore')->middleware(['auth:client', 'XSS']);

        Route::post('/{slug}/contract/{id}/file', [ContractController::class, 'fileUpload',])->name('contracts.file.upload')->middleware(['auth:client', 'XSS']);

        Route::get('/{slug}/contract/{id}/file/{fid}', [ContractController::class, 'fileDownload',])->name('contracts.file.download')->middleware(['auth:client', 'XSS']);

        Route::delete('/{slug}/contract/{id}/file/delete', [ContractController::class, 'fileDelete',])->name('contracts.file.delete')->middleware(['auth:client', 'XSS']);

        Route::post('/{slug}/contract/{id}/comment', [ContractController::class, 'commentStore',])->name('comment_store.store')->middleware(['auth:client', 'XSS']);

        Route::get('/{slug}/contract/{id}/comment', [ContractController::class, 'commentDestroy',])->name('comment_store.destroy')->middleware(['auth:client', 'XSS']);

        Route::post('/{slug}/contract/{id}/notes', [ContractController::class, 'noteStore',])->name('note_store.store')->middleware(['auth:client', 'XSS']);
        Route::get('/{slug}/contract/{id}/notes', [ContractController::class, 'noteDestroy',])->name('note_store.destroy')->middleware(['auth:client', 'XSS']);

        Route::post('/{slug}/contract/{id}/contract_description', [ContractController::class, 'contract_descriptionStore'])->name('contract.contract_description.store')->middleware(['auth:client']);

        //================================= Invoice Payment Gateways  ====================================//

        Route::post('/{slug}/invoice-pay-with-paystack/{invoice_id}', [PaystackPaymentController::class, 'invoicePayWithPaystack'])->name('invoice.pay.with.paystack')->middleware(['auth:client', 'XSS']);
        Route::get('/{slug}/invoice/paystack/{pay_id}/{invoice_id}', [PaystackPaymentController::class, 'getInvoicePaymentStatus'])->name('invoice.paystack')->middleware(['auth:client']);

        Route::post('/{slug}/invoice-pay-with-flaterwave/{invoice_id}', [FlutterwavePaymentController::class, 'invoicePayWithFlutterwave'])->name('invoice.pay.with.flaterwave')->middleware(['auth:client', 'XSS']);
        Route::get('/{slug}/invoice/flaterwave/{txref}/{invoice_id}', [FlutterwavePaymentController::class, 'getInvoicePaymentStatus'])->name('invoice.flaterwave')->middleware(['auth:client']);

        Route::post('/{slug}/invoice-pay-with-razorpay/{invoice_id}', [RazorpayPaymentController::class, 'invoicePayWithRazorpay'])->name('invoice.pay.with.razorpay')->middleware(['auth:client', 'XSS']);
        Route::get('/{slug}/invoice/razorpay/{txref}/{invoice_id}', [RazorpayPaymentController::class, 'getInvoicePaymentStatus'])->name('invoice.razorpay')->middleware(['auth:client']);

        Route::post('/{slug}/invoice-pay-with-paytm/{invoice_id}', [PaytmPaymentController::class, 'invoicePayWithPaytm'])->name('invoice.pay.with.paytm')->middleware(['auth:client', 'XSS']);
        // Route::post('/{slug}/invoice/paytm/{invoice}', [PaytmPaymentController::class, 'getInvoicePaymentStatus'])->name('invoice.paytm')->middleware(['auth:client']);

        Route::post('invoice/paytm', [PaytmPaymentController::class, 'getInvoicePaymentStatus'])->name('invoice.paytm')->middleware(['auth:client']);

        Route::post('/{slug}/invoice-pay-with-mercado/{invoice_id}', [MercadoPaymentController::class, 'invoicePayWithMercado'])->name('invoice.pay.with.mercado')->middleware(['auth:client', 'XSS']);
        Route::get('/{slug}/invoice/mercado/{invoice}', [MercadoPaymentController::class, 'getInvoicePaymentStatus'])->name('invoice.mercado')->middleware(['auth:client']);

        Route::post('/{slug}/invoice-pay-with-mollie/{invoice_id}', [MolliePaymentController::class, 'invoicePayWithMollie'])->name('invoice.pay.with.mollie')->middleware(['auth:client', 'XSS']);
        Route::get('/{slug}/invoice/mollie/{invoice}', [MolliePaymentController::class, 'getInvoicePaymentStatus'])->name('invoice.mollie')->middleware(['auth:client']);

        Route::post('/{slug}/invoice-pay-with-skrill/{invoice_id}', [SkrillPaymentController::class, 'invoicePayWithSkrill'])->name('invoice.pay.with.skrill')->middleware(['auth:client', 'XSS']);
        Route::get('/{slug}/invoice/skrill/{invoice}', [SkrillPaymentController::class, 'getInvoicePaymentStatus'])->name('invoice.skrill')->middleware(['auth:client']);

        Route::post('/{slug}/invoice-pay-with-coingate/{invoice_id}', [CoingatePaymentController::class, 'invoicePayWithCoingate'])->name('invoice.pay.with.coingate')->middleware(['auth:client', 'XSS']);
        Route::get('/{slug}/invoice/coingate/{invoice}', [CoingatePaymentController::class, 'getInvoicePaymentStatus'])->name('invoice.coingate')->middleware(['auth:client']);

        Route::post('/{slug}/invoice-pay-with-toyyibpay/{invoice_id}', [ToyyibpayController::class, 'invoicepaywithtoyyibpay'])->name('invoice.pay.with.toyyibpay')->middleware(['auth:client', 'XSS']);
        Route::get('/{slug}/invoice/toyyibpay/{invoice}/{amt}', [ToyyibpayController::class, 'getInvoicePaymentStatus'])->name('invoice.toyyibpay')->middleware(['auth:client']);

        Route::post('/{slug}/invoice-pay-with-payfast/{invoice_id}', [PayfastController::class, 'invoicePayWithpayfast'])->name('invoice.pay.with.payfast')->middleware(['auth:client']);
        Route::get('/invoice/payfast/{status}', [PayfastController::class, 'getInvoicePaymentStatus'])->name('invoice.payfast')->middleware(['auth:client']);

        Route::post('/{slug}/invoice-pay-with-iyzipay/{invoice_id}', [IyziPayController::class, 'invoicepaywithiyzipay'])->name('invoice.pay.with.iyzipay')->middleware(['auth:client', 'XSS']);
        Route::any('/{slug}/invoice/iyzipay/{invoice}/{amt}', [IyziPayController::class, 'getInvoicePaymentStatus'])->name('invoice.iyzipay')->middleware(['auth:client']);

        Route::post('/{slug}/invoice-pay-with-sspay/{invoice_id}', [SspayController::class, 'invoicepaywithsspay'])->name('invoice.pay.with.sspay')->middleware(['auth:client', 'XSS']);
        Route::any('/{slug}/invoice/sspay/{invoice}/{amt}', [SspayController::class, 'getInvoicePaymentStatus'])->name('invoice.sspay')->middleware(['auth:client', 'XSS']);

        Route::post('/{slug}/invoice-pay-with-paytab/{invoice_id}', [PaytabController::class, 'invoicePayWithpaytab'])->name('invoice.pay.with.paytab')->middleware(['auth:client', 'XSS']);
        Route::any('/{slug}/invoice-paytab-success/', [PaytabController::class, 'getInvoicePaymentStatus'])->name('invoice.paytab.success')->middleware(['auth:client', 'XSS']);

        Route::post('/{slug}/invoice-pay-with-benefit/{invoice_id}', [BenefitPaymentController::class, 'invoicePayWithbenefit'])->name('invoice.pay.with.benefit')->middleware(['auth:client', 'XSS']);
        Route::any('/{slug}/invoice-benefit-success/', [BenefitPaymentController::class, 'getInvoicePaymentStatus'])->name('invoice.benefit.success')->middleware(['auth:client', 'XSS']);

        Route::post('/{slug}/invoice-pay-with-cashfree/{invoice_id}', [CashfreeController::class, 'invoicePayWithcashfree'])->name('invoice.pay.with.cashfree')->middleware(['auth:client', 'XSS']);
        Route::any('/{slug}/invoice-cashfree-success/', [CashfreeController::class, 'getInvoicePaymentStatus'])->name('invoice.cashfree.success')->middleware(['auth:client', 'XSS']);

        //aamarpay route
        Route::post('/{slug}/aamarpay/payment/{invoice_id}', [AamarpayController::class, 'invoicePayWithAamarpay'])->name('invoice.pay.with.aamarpay')->middleware(['auth:client', 'XSS']);
        Route::any('/{slug}/aamarpay/success/{data}', [AamarpayController::class, 'getInvoicePaymentStatus'])->name('invoice.aamarpay.success')->middleware(['auth:client', 'XSS']);

        //paytr route
        Route::post('/{slug}/paytr/payment/{invoice_id}', [PaytrController::class, 'invoicePayWithPaytr'])->name('invoice.pay.with.paytr')->middleware(['auth:client']);
        Route::get('/{slug}/paytr/success/', [PaytrController::class, 'getInvoicePaymentStatus'])->name('invoice.paytr.success')->middleware(['auth:client']);

        //midtrans route
        Route::post('/{slug}/midtrans/payment/{invoice_id}', [MidtransController::class, 'invoicePayWithMidtrans'])->name('invoice.pay.with.midtrans')->middleware(['auth:client']);
        Route::any('/{slug}/midtrans/status/', [MidtransController::class, 'getInvoicePaymentStatus'])->name('invoice.midtrans.status')->middleware(['auth:client']);

        // xendit routes
        Route::any('/{slug}/xendit/payment/{invoice_id}', [XenditPaymentController::class, 'invoicePayWithXendit'])->name('invoice.pay.with.xendit')->middleware(['auth:client']);
        Route::any('/{slug}/xendit/status/response', [XenditPaymentController::class, 'getInvoicePaymentStatus'])->name('invoice.xendit.status')->middleware(['auth:client']);


        // yookassa routes
        Route::any('/{slug}/yookassa/payment/{invoice_id}', [YooKassaController::class, 'invoicePayWithYookassa'])->name('invoice.pay.with.yookassa')->middleware(['auth:client']);
        Route::any('/{slug}/yookassa/status/response', [YooKassaController::class, 'getInvoicePaymentStatus'])->name('invoice.yookassa.status')->middleware(['auth:client']);

        // paiementpro routes
        Route::any('/{slug}/paiementpro/payment/{invoice_id}', [PaiementproController::class, 'invoicePayWithPaiementpro'])->name('invoice.pay.with.paiementpro')->middleware(['auth:client']);
        Route::any('/{slug}/paiementpro/status/response', [PaiementproController::class, 'getInvoicePaymentStatus'])->name('invoice.paiementpro.status')->middleware(['auth:client']);

        // Nepalste routes
        Route::any('/{slug}/nepalste/payment/{invoice_id}', [NepalsteController::class, 'invoicePayWithNepalste'])->name('invoice.pay.with.nepalste')->middleware(['auth:client']);
        Route::any('{slug}/nepalste/status/', [NepalsteController::class, 'getInvoicePaymentStatus'])->name('invoice.nepalste.status')->middleware(['auth:client']);
        Route::get('{slug}/invoice-nepalste-cancel/', [NepalsteController::class, 'invoiceGetNepalsteCancel'])->name('invoice.nepalste.cancel')->middleware(['auth:client']);


        // cinetpay routes
        Route::any('/{slug}/cinetpay/payment/{invoice_id}', [CinetPayController::class, 'invoicePayWithCinetpay'])->name('invoice.pay.with.cinetpay')->middleware(['auth:client']);
        Route::any('invoice-cinetpay-return/{id}/{amt?}/{slug}', [CinetPayController::class, 'getInvoicePaymentStatus'])->name('invoice.cinetpay.return')->middleware(['auth:client']);
        Route::any('invoice-cinetpay-notify/{id?}', [CinetPayController::class, 'invoiceCinetPayNotify'])->name('invoice.cinetpay.notify')->middleware(['auth:client']);

        // fedapay routes
        Route::any('/{slug}/fedapay/payment/{invoice_id}', [FedapayPaymentController::class, 'invoicePayWithFedapay'])->name('invoice.pay.with.fedapay')->middleware(['auth:client']);
        Route::any('{slug}/fedapay/status/', [FedapayPaymentController::class, 'getInvoicePaymentStatus'])->name('invoice.fedapay.status')->middleware(['auth:client']);

        // payhere routes
        Route::any('/{slug}/payhere/payment/{invoice_id}', [PayHerePaymentController::class, 'invoicePayWithPayhere'])->name('invoice.pay.with.payhere')->middleware(['auth:client']);
        Route::any('invoice-payhere-status/{id}/{amt?}/{slug}', [PayHerePaymentController::class, 'invoiceGetPayHereStatus'])->name('invoice.payhere.status')->middleware(['auth:client']);

        // powertranz routes
        Route::any('/{slug}/powertranz/payment/{invoice_id}', [PowertranzPaymentController::class, 'invoicePayWithPowertranz'])->name('invoice.pay.powertranz.view')->middleware(['auth:client']);
        Route::any('invoice-powertranz/response/{slug}', [PowertranzPaymentController::class, 'InvoiceResponse'])->name('powertranz.invoice.response')->middleware(['auth:client']);
        Route::any('invoice-powertranz-status/{id}/{amt?}/{slug}', [PowertranzPaymentController::class, 'invoiceGetPowertranzStatus'])->name('invoice.powertranz.status')->middleware(['auth:client']);

        //payu routes
        Route::any('/{slug}/payu/payment/{invoice_id}', [PayUController::class, 'invoicePayWithPayu'])->name('invoice.pay.with.payu')->middleware(['auth:client']);
        Route::any('invoice-payu-status/{id}', [PayUController::class, 'getPayuStatus'])->name('invoice.get.payu.status')->middleware(['auth:client']);
        
        //================================= End Invoice Payment Gateways  ====================================//

        Route::get('/{slug}/zoom-meeting', [ZoomMeetingController::class, 'index'])->name('zoom-meeting.index')->middleware(['auth:client']);

        // Expenses for client.

        Route::get('/{slug}/projects/{id}/expenses', [ProjectController::class, 'expenseReport'])->name('projects.expense.report')->middleware(['auth:client', 'XSS']);

        Route::get('/{slug}/projects/{id}/expenses/create', [ProjectController::class, 'expensesReportCreate'])->name('projects.expenses.report.create')->middleware(['auth:client', 'XSS']);

        Route::post('/{slug}/projects/{id}/expenses_report', [ProjectController::class, 'expenseReportStore'])->name('projects.expense.report.store')->middleware(['auth:client', 'XSS']);
        Route::get('/{slug}/projects/{id}/expense_report/{bid}/edit', [ProjectController::class, 'expenseReportEdit'])->name('projects.expense.report.edit')->middleware(['auth:client', 'XSS']);
        Route::get('/{slug}/projects/{id}/expense_report/{bid}/view', [ProjectController::class, 'expenseReportView'])->name('projects.expense.report.view')->middleware(['auth:client', 'XSS']);
        Route::post('/{slug}/projects/{id}/expense_report/{bid}/update', [ProjectController::class, 'expenseReportUpdate'])->name('projects.expense.report.update')->middleware(['auth:client', 'XSS']);

        Route::delete('/{slug}/projects/{id}/expense_report/{bid}', [ProjectController::class, 'expenseReportDestroy'])->name('projects.expense.report.destroy')->middleware(['auth:client', 'XSS']);
    });



    // Route::any('/plan/error/{flag}', [PaymentWallPaymentController::class, 'paymenterror'])->name('callback.error')->middleware(['auth','XSS']);
    // Route::post('paymentwall', [PaymentWallPaymentController::class, 'index'])->name('paymentwall')->middleware(['auth','XSS']);
    // Route::post('plan-pay-with-paymentwall/{plan}', [PaymentWallPaymentController::class, 'planPayWithPaymentwall'])->name('plan.pay.with.paymentwall')->middleware(['auth','XSS']);


    // Calender
    Route::get('/{slug}/calendar/{id?}', [CalenderController::class, 'index'])->name('calender.index')->middleware(['auth', 'XSS']);
    Route::any('/{slug}/calendarr/{id?}', [CalenderController::class, 'calendar'])->name('calender.google.calendar')->middleware(['auth', 'XSS']);


    // Chats

    Route::get('/{slug}/notification/seen', [UserController::class, 'notificationSeen'])->name('notification.seen');
    Route::get('/{slug}/message/seen', [UserController::class, 'messageSeen'])->name('message.seen');

    // End Chats

    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index')->middleware(['auth', 'XSS']);
    Route::post('/settings', [SettingsController::class, 'store'])->name('settings.store')->middleware(['XSS']);

    Route::post('cookie-setting', [SettingsController::class, 'saveCookieSettings'])->name('cookie.setting');
    Route::any('/cookie-consent', [SettingsController::class, 'CookieConsent'])->name('cookie-consent');

    Route::post('/email-settings', [SettingsController::class, 'emailSettingStore'])->name('email.settings.store')->middleware(['auth', 'XSS']);
    Route::post('/payment-settings', [SettingsController::class, 'paymentSettingStore'])->name('payment.settings.store')->middleware(['auth', 'XSS']);
    Route::post('/pusher-settings', [SettingsController::class, 'pusherSettingStore'])->name('pusher.settings.store')->middleware(['auth', 'XSS']);
    Route::post('/test', [SettingsController::class, 'testEmail'])->name('test.email')->middleware(['auth', 'XSS']);
    Route::post('/test/send', [SettingsController::class, 'testEmailSend'])->name('test.email.send')->middleware(['auth', 'XSS']);

    Route::get('/{slug}/clients', [ClientController::class, 'index'])->name('clients.index')->middleware(['auth', 'XSS']);
    Route::post('/{slug}/clients', [ClientController::class, 'store'])->name('clients.store')->middleware(['auth', 'XSS']);
    Route::get('/{slug}/clients/create', [ClientController::class, 'create'])->name('clients.create')->middleware(['auth', 'XSS']);
    Route::get('/{slug}/clients/{id}/edit', [ClientController::class, 'edit'])->name('clients.edit')->middleware(['auth', 'XSS']);
    Route::post('/{slug}/clients/{id}/update', [ClientController::class, 'update'])->name('clients.update')->middleware(['auth', 'XSS']);
    Route::delete('/{slug}/clients/{id}', [ClientController::class, 'destroy'])->name('clients.destroy')->middleware(['auth', 'XSS']);
    // User
    Route::get('/usersJson/{id}', [UserController::class, 'getUserJson'])->name('user.email.json')->middleware(['auth', 'XSS']);
    Route::get('/{slug}/searchJson/{search?}', [ProjectController::class, 'getSearchJson'])->name('search.json')->middleware(['auth', 'XSS']);
    Route::get('/userProjectJson/{id}', [UserController::class, 'getProjectUserJson'])->name('user.project.json')->middleware(['auth', 'XSS']);
    Route::get('/projectMilestoneJson/{id}', [UserController::class, 'getProjectMilestoneJson'])->name('project.milestone.json')->middleware(['auth', 'XSS']);
    Route::get('/users', [UserController::class, 'index'])->name('admin.users.index')->middleware(['auth', 'XSS']);
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create')->middleware(['auth', 'XSS']);
    Route::post('/users/store', [UserController::class, 'store'])->name('users.store')->middleware(['auth', 'XSS']);
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.delete')->middleware(['auth', 'XSS']);
    Route::get('/users/{id}', [UserController::class, 'changePlan'])->name('users.change.plan')->middleware(['auth', 'XSS']);
    Route::get('/resetpassword/{id}', [UserController::class, 'resetPassword'])->name('users.reset.password')->middleware(['auth', 'XSS']);
    Route::post('/changepassword/{id}', [UserController::class, 'changePassword'])->name('users.change.password')->middleware(['auth', 'XSS']);
    Route::get('/login-manage/{userId}', [UserController::class, 'managelogin'])->name('login.manage')->middleware(['auth', 'XSS']);
    Route::get('/company-info/{id}', [UserController::class, 'companyInfo'])->name('company.info')->middleware(['auth', 'XSS']);
    Route::post('user-unable', [UserController::class, 'UserUnable'])->name('user.unable')->middleware(['auth', 'XSS']);

    //user logs
    Route::get('/{slug}/userlogs/', [LoginDetailController::class, 'index'])->name('users_logs.index')->middleware(['auth', 'XSS']);
    Route::delete('/{slug}/userlogs/{id}', [LoginDetailController::class, 'destroy'])->name('users_logs.destroy')->middleware(['auth', 'XSS']);
    Route::get('/{slug}/userlogs/{id}', [LoginDetailController::class, 'show'])->name('users_logs.show')->middleware(['auth', 'XSS']);
    Route::any('/{slug}/userlogs-filter', [LoginDetailController::class, 'index'])->name('users_logs.filter')->middleware(['auth', 'XSS']);



    Route::get('/{slug}/users', [UserController::class, 'index'])->name('users.index')->middleware(['auth', 'XSS']);
    Route::get('/{slug}/users/invite', [UserController::class, 'invite'])->name('users.invite')->middleware(['auth', 'XSS']);
    Route::post('/{slug}/users/invite', [UserController::class, 'inviteUser'])->name('users.invite.update')->middleware(['auth', 'XSS']);
    Route::get('/{slug}/users/edit/{id}', [UserController::class, 'edit'])->name('users.edit')->middleware(['auth', 'XSS']);
    Route::post('/{slug}/users/update/{id}', [UserController::class, 'update'])->name('users.update')->middleware(['auth', 'XSS']);
    Route::delete('/{slug}/users/{id}', [UserController::class, 'removeUser'])->name('users.remove')->middleware(['auth', 'XSS']);



    Route::get('/my-account', [UserController::class, 'account'])->name('users.my.account')->middleware(['auth', 'XSS']);
    Route::post('/{slug}/my-account/{id}/update', [UserController::class, 'update'])->name('update.account')->middleware(['auth', 'XSS']);
    Route::post('/my-account/password', [UserController::class, 'updatePassword'])->name('update.password')->middleware(['auth', 'XSS']);
    Route::delete('/my-account', [UserController::class, 'deleteAvatar'])->name('delete.avatar')->middleware(['auth', 'XSS']);
    Route::delete('/delete-my-account', [UserController::class, 'deleteMyAccount'])->name('delete.my.account')->middleware(['auth', 'XSS']);

    // 2FA Google Authenticated
    Route::group(['middleware' => ['web', 'auth', 'verified']], function () {
        Route::post('/generateSecret', [GoogleAuthenticationController::class, 'generate2faSecret'])->name('generate2faSecret');
        Route::post('/enable2fa', [GoogleAuthenticationController::class, 'enable2fa'])->name('enable2fa');
        Route::post('/disable2fa', [GoogleAuthenticationController::class, 'disable2fa'])->name('disable2fa');
    });

    Route::middleware(['web'])->group(function () {
        Route::post('/2faVerify', function () {
            return redirect(request()->get('2fa_referrer'));
        })->name('2faVerify')->middleware('2fa');
    });

    Route::get('/plans', [PlanController::class, 'index'])->name('plans.index')->middleware(['auth', 'XSS']);
    Route::get('/plans/create', [PlanController::class, 'create'])->name('plans.create')->middleware(['auth', 'XSS']);
    Route::post('/plans', [PlanController::class, 'store'])->name('plans.store')->middleware(['auth', 'XSS']);
    Route::get('/plans/{id}/edit', [PlanController::class, 'edit'])->name('plans.edit')->middleware(['auth', 'XSS']);
    Route::post('/plans/{id}/update', [PlanController::class, 'update'])->name('plans.update')->middleware(['auth', 'XSS']);
    Route::post('/user-plans/', [PlanController::class, 'userPlan'])->name('update.user.plan')->middleware(['auth', 'XSS']);
    Route::get('/payment/{frequency}/{code}', [PlanController::class, 'payment'])->name('payment')->middleware(['auth', 'XSS']);

    Route::get('/orders', [StripePaymentController::class, 'index'])->name('order.index')->middleware(['auth', 'XSS']);
    Route::post('/stripe', [StripePaymentController::class, 'stripePost'])->name('stripe.post')->middleware(['auth', 'XSS']);

    Route::get('/apply-coupon', [CouponController::class, 'applyCoupon'])->name('apply.coupon')->middleware(['auth', 'XSS']);
    Route::resource('coupons', CouponController::class)->middleware(['auth', 'XSS',]);

    // Lang
    Route::get('/admin/change_lang/{lang}', [WorkspaceController::class, 'changeLangAdmin'])->name('change_lang_admin')->middleware(['auth', 'XSS']);
    Route::get('/workspace/{slug}/change_lang/{lang}', [WorkspaceController::class, 'changeLangWorkspace'])->name('change_lang_workspace')->middleware(['auth', 'XSS']);
    Route::get('/workspace/{slug}/change_lang1/{lang}', [WorkspaceController::class, 'changeLangWorkspace1'])->name('change_lang_workspace1')->middleware(['auth:client', 'XSS']);
    Route::get('/workspace/change_lang_copylink/{lang}', [WorkspaceController::class, 'changeLangcopylink'])->name('change_lang_copylink')->middleware(['XSS']);

    Route::post('{slug}/company-email-settings', [WorkspaceController::class, 'conpanyEmailSettingStore'])->name('company.email.settings.store')->middleware(['auth', 'XSS']);

    Route::post('{slug}/zoom-metting-settings', [WorkspaceController::class, 'ZoomMeetingStore'])->name('zoom.mettings.settings.store')->middleware(['auth', 'XSS']);


    Route::get('/workspace/lang/create', [WorkspaceController::class, 'createLangWorkspace'])->name('create_lang_workspace')->middleware(['auth', 'XSS']);
    Route::get('/workspace/lang/{lang?}', [WorkspaceController::class, 'langWorkspace'])->name('lang_workspace')->middleware(['auth', 'XSS']);
    Route::post('/workspace/lang/{lang}', [WorkspaceController::class, 'storeLangDataWorkspace'])->name('store_lang_data_workspace')->middleware(['auth', 'XSS']);
    Route::post('/workspace/lang', [WorkspaceController::class, 'storeLangWorkspace'])->name('store_lang_workspace')->middleware(['auth', 'XSS']);
    Route::post('disable-language', [LanguagesController::class, 'disableLang'])->name('disablelanguage')->middleware(['auth', 'XSS']);

    // Workspace
    Route::get('/workspace/{slug}/settings', [WorkspaceController::class, 'settings'])->name('workspace.settings')->middleware(['auth', 'XSS']);
    Route::post('/workspace/{slug}/settings', [WorkspaceController::class, 'settingsStore'])->name('workspace.settings.store')->middleware(['auth', 'XSS']);

    Route::post('/workspace/{slug}', [WorkspaceController::class, 'store'])->name('add-workspace')->middleware(['auth', 'XSS']);
    Route::delete('/workspace/{id}', [WorkspaceController::class, 'destroy'])->name('delete-workspace')->middleware(['auth', 'XSS']);
    Route::delete('/workspace/leave/{id}', [WorkspaceController::class, 'leave'])->name('leave-workspace')->middleware(['auth', 'XSS']);
    Route::get('/workspace/{id}', [WorkspaceController::class, 'changeCurrentWorkspace'])->name('change-workspace')->middleware(['auth', 'XSS']);
    Route::post('/workspace/settings/seo', [SettingsController::class, 'seosetting'])->name('settings.seo.store')->middleware(['auth', 'XSS']);


    // project
    Route::get('/{slug}/projects', [ProjectController::class, 'index'])->name('projects.index')->middleware(['auth', 'XSS']);
    Route::get('/{slug}/projects/create', [ProjectController::class, 'create'])->name('projects.create')->middleware(['auth', 'XSS']);
    Route::get('/{slug}/projects/{id}', [ProjectController::class, 'show'])->name('projects.show')->middleware(['auth', 'XSS']);
    Route::post('/{slug}/projects', [ProjectController::class, 'store'])->name('projects.store')->middleware(['auth', 'XSS']);
    Route::get('/{slug}/projects/{id}/edit', [ProjectController::class, 'edit'])->name('projects.edit')->middleware(['auth', 'XSS']);
    Route::post('/{slug}/projects/{id}/update', [ProjectController::class, 'update'])->name('projects.update')->middleware(['auth', 'XSS']);
    Route::delete('/{slug}/projects/{id}', [ProjectController::class, 'destroy'])->name('projects.destroy')->middleware(['auth', 'XSS']);

    Route::get('/{slug}/projects/copy/{id}', [ProjectController::class, 'copyproject'])->name('project.copy')->middleware(['auth', 'XSS']);
    Route::post('/{slug}/projects/copy/store/{id}', [ProjectController::class, 'copyprojectstore'])->name('project.copy.store')->middleware(['auth', 'XSS']);

    Route::delete('/{slug}/projects/leave/{id}', [ProjectController::class, 'leave'])->name('projects.leave')->middleware(['auth', 'XSS']);
    Route::get('/{slug}/projects/invite/{id}', [ProjectController::class, 'popup'])->name('projects.invite.popup')->middleware(['auth', 'XSS']);
    Route::get('/{slug}/projects/{id}/user/{uid}/permission', [ProjectController::class, 'userPermission'])->name('projects.user.permission')->middleware(['auth', 'XSS']);
    Route::post('/{slug}/projects/{id}/user/{uid}/permission', [ProjectController::class, 'userPermissionStore'])->name('projects.user.permission.store')->middleware(['auth', 'XSS']);
    Route::delete('/{slug}/projects/{id}/user/{uid}', [ProjectController::class, 'userDelete'])->name('projects.user.delete')->middleware(['auth', 'XSS']);
    Route::get('/{slug}/projects/share/{id}', [ProjectController::class, 'sharePopup'])->name('projects.share.popup')->middleware(['auth', 'XSS']);
    Route::get('/{slug}/projects/{id}/client/{uid}/permission', [ProjectController::class, 'clientPermission'])->name('projects.client.permission')->middleware(['auth', 'XSS']);
    Route::post('/{slug}/projects/{id}/client/{uid}/permission', [ProjectController::class, 'clientPermissionStore'])->name('projects.client.permission.store')->middleware(['auth', 'XSS']);
    Route::delete('/{slug}/projects/{id}/client/{uid}', [ProjectController::class, 'clientDelete'])->name('projects.client.delete')->middleware(['auth', 'XSS']);
    Route::post('/{slug}/projects/share/{id}', [ProjectController::class, 'share'])->name('projects.share')->middleware(['auth', 'XSS']);
    Route::post('/{slug}/projects/invite/{id}/update', [ProjectController::class, 'invite'])->name('projects.invite.update')->middleware(['auth', 'XSS']);
    Route::get('/{slug}/projects/milestone/{id}', [ProjectController::class, 'milestone'])->name('projects.milestone')->middleware(['auth', 'XSS']);
    Route::post('/{slug}/projects/milestone/{id}/store', [ProjectController::class, 'milestoneStore'])->name('projects.milestone.store')->middleware(['auth', 'XSS']);
    Route::get('/{slug}/projects/milestone/{id}/show', [ProjectController::class, 'milestoneShow'])->name('projects.milestone.show')->middleware(['auth', 'XSS']);
    Route::get('/{slug}/projects/milestone/{id}/edit', [ProjectController::class, 'milestoneEdit'])->name('projects.milestone.edit')->middleware(['auth', 'XSS']);
    Route::post('/{slug}/projects/milestone/{id}/update', [ProjectController::class, 'milestoneUpdate'])->name('projects.milestone.update')->middleware(['auth', 'XSS']);
    Route::delete('/{slug}/projects/milestone/{id}', [ProjectController::class, 'milestoneDestroy'])->name('projects.milestone.destroy')->middleware(['auth', 'XSS']);
    Route::post('/{slug}/projects/{id}/file', [ProjectController::class, 'fileUpload'])->name('projects.file.upload')->middleware(['auth', 'XSS']);
    Route::get('/{slug}/projects/{id}/file/{fid}', [ProjectController::class, 'fileDownload'])->name('projects.file.download')->middleware(['auth', 'XSS']);
    Route::delete('/{slug}/projects/{id}/file/delete/{fid}', [ProjectController::class, 'fileDelete'])->name('projects.file.delete')->middleware(['auth', 'XSS']);

    // Task Board
    Route::get('/{slug}/projects/client/task-board/{code}', [ProjectController::class, 'taskBoard'])->name('projects.client.task.board');
    Route::get('/{slug}/projects/{id}/task-board', [ProjectController::class, 'taskBoard'])->name('projects.task.board')->middleware(['auth', 'XSS']);
    Route::get('/{slug}/projects/{id}/task-board/create', [ProjectController::class, 'taskCreate'])->name('tasks.create')->middleware(['auth', 'XSS']);
    Route::post('/{slug}/projects/{id}/task-board', [ProjectController::class, 'taskStore'])->name('tasks.store')->middleware(['auth', 'XSS']);
    Route::post('/{slug}/projects/{id}/task-board/order-update', [ProjectController::class, 'taskOrderUpdate'])->name('tasks.update.order');
    Route::get('/{slug}/projects/{id}/task-board/edit/{tid}', [ProjectController::class, 'taskEdit'])->name('tasks.edit')->middleware(['auth', 'XSS']);
    Route::post('/{slug}/projects/{id}/task-board/{tid}/update', [ProjectController::class, 'taskUpdate'])->name('tasks.update')->middleware(['auth', 'XSS']);
    Route::delete('/{slug}/projects/{id}/task-board/{tid}', [ProjectController::class, 'taskDestroy'])->name('tasks.destroy')->middleware(['auth', 'XSS']);
    Route::post('/{slug}/projects/{id}/task-board/{tid}/drag', [ProjectController::class, 'taskDrag'])->name('tasks.drag.event');

    // Gantt Chart
    Route::get('/{slug}/projects/{id}/gantt/{duration?}', [ProjectController::class, 'gantt'])->name('projects.gantt')->middleware(['auth', 'XSS']);
    Route::post('/{slug}/projects/{id}/gantt', [ProjectController::class, 'ganttPost'])->name('projects.gantt.post')->middleware(['auth', 'XSS']);

    Route::get('/{slug}/tasks', [ProjectController::class, 'allTasks'])->name('tasks.index')->middleware(['auth', 'XSS']);
    Route::post('/{slug}/tasks', [ProjectController::class, 'ajax_tasks'])->name('tasks.ajax')->middleware(['auth', 'XSS']);

    // Timesheet
    Route::get('/{slug}/tasks/{id?}', [ProjectController::class, 'getTask'])->name('get.task.ajax')->middleware(['auth', 'XSS']);
    Route::get('/{slug}/timesheet', [ProjectController::class, 'timesheet'])->name('timesheet.index')->middleware(['auth', 'XSS']);
    Route::get('/{slug}/timesheet/create', [ProjectController::class, 'timesheetCreate'])->name('timesheet.create')->middleware(['auth', 'XSS']);
    Route::post('/{slug}/timesheet/store', [ProjectController::class, 'timesheetStore'])->name('timesheet.store')->middleware(['auth', 'XSS']);
    Route::get('/{slug}/timesheet/{id}/edit', [ProjectController::class, 'timesheetEdit'])->name('timesheet.edit')->middleware(['auth', 'XSS']);
    Route::post('/{slug}/timesheet/{id}/update', [ProjectController::class, 'timesheetUpdate'])->name('timesheet.update')->middleware(['auth', 'XSS']);
    Route::delete('/{slug}/timesheet/{id}', [ProjectController::class, 'timesheetDestroy'])->name('timesheet.destroy')->middleware(['auth', 'XSS']);

    Route::post('/{slug}/projects/{id}/comment/{tid}/file/{cid?}', [ProjectController::class, 'commentStoreFile'])->name('comment.store.file');
    Route::delete('/{slug}/projects/{id}/comment/{tid}/file/{fid}', [ProjectController::class, 'commentDestroyFile'])->name('comment.destroy.file');
    Route::post('/{slug}/projects/{id}/comment/{tid}/{cid?}', [ProjectController::class, 'commentStore'])->name('comment.store');
    Route::delete('/{slug}/projects/{id}/comment/{tid}/{cid}', [ProjectController::class, 'commentDestroy'])->name('comment.destroy');
    Route::post('/{slug}/projects/{id}/sub-task/update/{stid}', [ProjectController::class, 'subTaskUpdate'])->name('subtask.update');
    Route::post('/{slug}/projects/{id}/sub-task/{tid}/{cid?}', [ProjectController::class, 'subTaskStore'])->name('subtask.store');
    Route::delete('/{slug}/projects/{id}/sub-task/{stid}', [ProjectController::class, 'subTaskDestroy'])->name('subtask.destroy');

    // todo
    //Route::get('/{slug}/todo',['as' => 'todos.index','uses' =>'TodoController@index'])->middleware(['auth','XSS']);
    //Route::post('/{slug}/todo',['as' => 'todos.store','uses' =>'TodoController@store'])->middleware(['auth','XSS']);
    //Route::post('/{slug}/todo',['as' => 'todos.update','uses' =>'TodoController@update'])->middleware(['auth','XSS']);
    //Route::delete('/{slug}/todo',['as' => 'todos.destroy','uses' =>'TodoController@destroy'])->middleware(['auth','XSS']);

    // note
    Route::get('/{slug}/notes', [NoteController::class, 'index'])->name('notes.index')->middleware(['auth', 'XSS']);
    Route::get('/{slug}/notes/create', [NoteController::class, 'create'])->name('note.create')->middleware(['auth', 'XSS']);
    Route::post('/{slug}/notes', [NoteController::class, 'store'])->name('notes.store')->middleware(['auth', 'XSS']);
    Route::get('/{slug}/notes/{id}/edit', [NoteController::class, 'edit'])->name('notes.edit')->middleware(['auth', 'XSS']);
    Route::post('/{slug}/notes/{id}/update', [NoteController::class, 'update'])->name('notes.update')->middleware(['auth', 'XSS']);
    Route::delete('/{slug}/notes/{id}', [NoteController::class, 'destroy'])->name('notes.destroy')->middleware(['auth', 'XSS']);
    // bug report
    Route::get('/{slug}/projects/{id}/bug_report', [ProjectController::class, 'bugReport'])->name('projects.bug.report')->middleware(['auth', 'XSS']);
    Route::get('/{slug}/projects/{id}/bug_report/create', [ProjectController::class, 'bugReportCreate'])->name('projects.bug.report.create')->middleware(['auth', 'XSS']);
    Route::post('/{slug}/projects/{id}/bug_report', [ProjectController::class, 'bugReportStore'])->name('projects.bug.report.store')->middleware(['auth', 'XSS']);
    Route::post('/{slug}/projects/{id}/bug_report/order-update', [ProjectController::class, 'bugReportOrderUpdate'])->name('projects.bug.report.update.order')->middleware(['auth', 'XSS']);
    Route::get('/{slug}/projects/{id}/bug_report/{bid}/edit', [ProjectController::class, 'bugReportEdit'])->name('projects.bug.report.edit')->middleware(['auth', 'XSS']);
    Route::post('/{slug}/projects/{id}/bug_report/{bid}/update', [ProjectController::class, 'bugReportUpdate'])->name('projects.bug.report.update')->middleware(['auth', 'XSS']);
    Route::delete('/{slug}/projects/{id}/bug_report/{bid}', [ProjectController::class, 'bugReportDestroy'])->name('projects.bug.report.destroy')->middleware(['auth', 'XSS']);

    // Expenses for company and user
    Route::get('/{slug}/projects/{id}/expenses', [ProjectController::class, 'expenseReport'])->name('projects.expense.report')->middleware(['auth', 'XSS']);

    Route::get('/{slug}/projects/{id}/expenses/create', [ProjectController::class, 'expensesReportCreate'])->name('projects.expenses.report.create')->middleware(['auth', 'XSS']);

    Route::post('/{slug}/projects/{id}/expenses_report', [ProjectController::class, 'expenseReportStore'])->name('projects.expense.report.store')->middleware(['auth', 'XSS']);

    Route::get('/{slug}/projects/{id}/expense_report/{bid}/edit', [ProjectController::class, 'expenseReportEdit'])->name('projects.expense.report.edit')->middleware(['auth', 'XSS']);

    Route::get('/{slug}/projects/{id}/expense_report/{bid}/view', [ProjectController::class, 'expenseReportView'])->name('projects.expense.report.view')->middleware(['auth', 'XSS']);

    Route::post('/{slug}/projects/{id}/expense_report/{bid}/update', [ProjectController::class, 'expenseReportUpdate'])->name('projects.expense.report.update')->middleware(['auth', 'XSS']);

    Route::delete('/{slug}/projects/{id}/expense_report/{bid}', [ProjectController::class, 'expenseReportDestroy'])->name('projects.expense.report.destroy')->middleware(['auth', 'XSS']);

    Route::post('/{slug}/projects/{id}/bug_comment/{tid}/{cid?}', [ProjectController::class, 'bugCommentStore'])->name('bug.comment.store');
    Route::post('/{slug}/projects/{id}/bug_comment_store_file/{tid}/file/{cid?}', [ProjectController::class, 'bugStoreFile'])->name('bug.comment.store.file');
    Route::delete('/{slug}/projects/{id}/bug_comment/{tid}/file/{fid}', [ProjectController::class, 'bugDestroyFile'])->name('bug.comment.destroy.file');
    Route::delete('/{slug}/projects/{id}/bug_comment/{tid}/{cid}', [ProjectController::class, 'bugCommentDestroy'])->name('bug.comment.destroy');

    Route::get('/{slug}/invoices/preview/{template}/{color}', [InvoiceController::class, 'previewInvoice'])->name('invoice.preview');
    Route::resource('/{slug}/invoices', InvoiceController::class);
    Route::get('/{slug}/invoices/{id}/item', [InvoiceController::class, 'create_item'])->name('invoice.item.create');
    Route::post('/{slug}/invoices/{id}/item', [InvoiceController::class, 'store_item'])->name('invoice.item.store');
    Route::delete('/{slug}/invoices/{id}/item/{iid}', [InvoiceController::class, 'destroy_item'])->name('invoice.item.destroy');
    Route::get('/{slug}/invoices/{id}/print', [InvoiceController::class, 'printInvoice'])->name('invoice.print');

    Route::get('/{slug}/taxes', [WorkspaceController::class, 'create_tax'])->name('tax.create')->middleware(['auth', 'XSS']);
    Route::post('/{slug}/taxes', [WorkspaceController::class, 'store_tax'])->name('tax.store')->middleware(['auth', 'XSS']);
    Route::get('/{slug}/taxes/{id}/edit', [WorkspaceController::class, 'edit_tax'])->name('tax.edit')->middleware(['auth', 'XSS']);
    Route::post('/{slug}/taxes/{id}/update', [WorkspaceController::class, 'update_tax'])->name('tax.update')->middleware(['auth', 'XSS']);
    Route::delete('/{slug}/taxes/{id}', [WorkspaceController::class, 'destroy_tax'])->name('tax.destroy')->middleware(['auth', 'XSS']);

    Route::post('/{slug}/stages', [WorkspaceController::class, 'store_stages'])->name('stages.store')->middleware(['auth', 'XSS']);
    Route::post('/{slug}/bug/stages', [WorkspaceController::class, 'store_bug_stages'])->name('bug.stages.store')->middleware(['auth', 'XSS']);


    Route::post('/{slug}/manual-invoice-payment/{invoice_id}', [InvoiceController::class, 'addManualPayment'])->name('manual.invoice.payment')->middleware(['auth', 'XSS']);

    Route::post('/plan-pay-with-paypal', [PaypalController::class, 'planPayWithPaypal'])->name('plan.pay.with.paypal')->middleware(['auth', 'XSS']);
    Route::get('/{id}/plan-get-payment-status', [PaypalController::class, 'planGetPaymentStatus'])->name('plan.get.payment.status')->middleware(['auth', 'XSS']);



    Route::get('plan_request', [PlanRequestController::class, 'index'])->name('plan_request.index')->middleware(['auth', 'XSS',]);
    Route::get('request_frequency/{id}', [PlanRequestController::class, 'requestView'])->name('request.view')->middleware(['auth', 'XSS',]);
    Route::get('request_send/{id}', [PlanRequestController::class, 'userRequest'])->name('send.request')->middleware(['auth', 'XSS',]);
    Route::get('request_response/{id}/{response}', [PlanRequestController::class, 'acceptRequest'])->name('response.request')->middleware(['auth', 'XSS',]);
    Route::get('request_cancel/{id}', [PlanRequestController::class, 'cancelRequest'])->name('request.cancel')->middleware(['auth', 'XSS',]);





    Route::get('/{slug}/timesheet/{id}', [ProjectController::class, 'projectsTimesheet'])->name('projects.timesheet.index')->middleware(['auth', 'XSS']);


    Route::get('/{slug}/append-timesheet-task-html', [ProjectController::class, 'appendTimesheetTaskHTML'])->name('append.timesheet.task.html')->middleware(['auth', 'XSS']);

    Route::get('/{slug}/timesheet/create/{project_id}', [ProjectController::class, 'projectTimesheetCreate'])->name('project.timesheet.create')->middleware(['auth', 'XSS']);

    Route::post('/{slug}/timesheet/store/{project_id}', [ProjectController::class, 'projectTimesheetStore'])->name('project.timesheet.store')->middleware(['auth', 'XSS']);

    Route::get('/{slug}/timesheet/{timesheet_id}/edit/{project_id}', [ProjectController::class, 'projectTimesheetEdit'])->name('project.timesheet.edit')->middleware(['auth', 'XSS']);

    Route::post('/{slug}/timesheet/{timesheet_id}/update/{project_id}', [ProjectController::class, 'projectTimesheetUpdate'])->name('project.timesheet.update')->middleware(['auth', 'XSS']);

    Route::get('/{slug}/checkuserexists', [UserController::class, 'checkUserExists'])->name('user.exists')->middleware(['auth', 'XSS']);

    Route::delete('/lang/{lang}', [WorkspaceController::class, 'destroyLang'])->name('lang.destroy')->middleware(['auth', 'XSS']);

    Route::get('/stripe-payment-status', [StripePaymentController::class, 'planGetStripePaymentStatus'])->name('stripe.payment.status');
    Route::post('/webhook-stripe', [StripePaymentController::class, 'webhookStripe'])->name('webhook.stripe');

    Route::get('/take-a-plan-trial/{plan_id}', [PlanController::class, 'takeAPlanTrial'])->name('take.a.plan.trial')->middleware(['auth', 'XSS']);
    Route::get('/change-user-plan/{plan_id}', [PlanController::class, 'changeUserPlan'])->name('change.user.plan')->middleware(['auth', 'XSS']);

    Route::get('user/{id}/plan/{pid}/{duration}', [UserController::class, 'manuallyActivatePlan'])->name('manually.activate.plan')->middleware(['auth', 'XSS',]);



    //================================= Plan Payment Gateways Route ====================================//

    Route::post('/plan-pay-with-paystack', [PaystackPaymentController::class, 'planPayWithPaystack'])->name('plan.pay.with.paystack')->middleware(['auth', 'XSS']);
    Route::get('/plan/paystack/{pay_id}/{plan_id}', [PaystackPaymentController::class, 'getPaymentStatus'])->name('plan.paystack');

    Route::post('/plan-pay-with-flaterwave', [FlutterwavePaymentController::class, 'planPayWithFlutterwave'])->name('plan.pay.with.flaterwave')->middleware(['auth', 'XSS']);
    Route::get('/plan/flaterwave/{txref}/{plan_id}', [FlutterwavePaymentController::class, 'getPaymentStatus'])->name('plan.flaterwave');

    Route::post('/plan-pay-with-razorpay', [RazorpayPaymentController::class, 'planPayWithRazorpay'])->name('plan.pay.with.razorpay')->middleware(['auth', 'XSS']);
    Route::get('/plan/razorpay/{txref}/{plan_id}', [RazorpayPaymentController::class, 'getPaymentStatus'])->name('plan.razorpay');

    Route::post('/plan-pay-with-paytm', [PaytmPaymentController::class, 'planPayWithPaytm'])->name('plan.pay.with.paytm')->middleware(['auth', 'XSS']);
    Route::post('/plan/paytm/{plan}', [PaytmPaymentController::class, 'getPaymentStatus'])->name('plan.paytm');

    Route::post('/plan-pay-with-mercado', [MercadoPaymentController::class, 'planPayWithMercado'])->name('plan.pay.with.mercado')->middleware(['auth', 'XSS']);
    Route::get('/plan/mercado/{plan}', [MercadoPaymentController::class, 'getPaymentStatus'])->name('plan.mercado');

    Route::post('/plan-pay-with-mollie', [MolliePaymentController::class, 'planPayWithMollie'])->name('plan.pay.with.mollie')->middleware(['auth', 'XSS']);
    Route::get('/plan/mollie/{plan}', [MolliePaymentController::class, 'getPaymentStatus'])->name('plan.mollie');

    Route::post('/plan-pay-with-skrill', [SkrillPaymentController::class, 'planPayWithSkrill'])->name('plan.pay.with.skrill')->middleware(['auth', 'XSS']);
    Route::get('/plan/skrill/{plan}', [SkrillPaymentController::class, 'getPaymentStatus'])->name('plan.skrill');

    Route::post('/plan-pay-with-coingate', [CoingatePaymentController::class, 'planPayWithCoingate'])->name('plan.pay.with.coingate')->middleware(['auth', 'XSS']);
    Route::get('/plan/coingate/{plan}', [CoingatePaymentController::class, 'getPaymentStatus'])->name('plan.coingate');

    Route::post('plan/toyyibpay/payment', [ToyyibpayController::class, 'charge'])->name('plan.toyyibpaypayment')->middleware(['auth', 'XSS']);
    Route::get('plan/status/{planId}/{getAmount?}/{couponCode?}', [ToyyibpayController::class, 'status'])->name('plan.status');



    //================================= End Plan Payment Gateways Route ====================================//


    //================================= Custom Landing Page ====================================//

    //--------------------------------------------------------Import/Export Data Route-----------------------------------------------------------------


    Route::get('import/user/file', [UserController::class, 'importFile'])->name('user.file.import');
    Route::post('/import/user', [UserController::class, 'import'])->name('user.import');
    Route::get('export/user', [UserController::class, 'export'])->name('user.export');



    Route::get('/{slug}/import/client/file', [ClientController::class, 'importFile'])->name('client.file.import');
    Route::post('import/client', [ClientController::class, 'import'])->name('client.import');
    Route::get('export/client', [ClientController::class, 'export'])->name('client.export');

    Route::get('/{slug}/import/project/file', [ProjectController::class, 'importFile'])->name('project.file.import');
    Route::post('import/project', [ProjectController::class, 'import'])->name('project.import');
    Route::get('export/project', [ProjectController::class, 'export'])->name('project.export');

    Route::get('export/invoice', [InvoiceController::class, 'export'])->name('invoice.export');


    ////------------------------tracker-----------------------------------------////

    Route::get('/{slug}/projects/time-tracker/{id}', [ProjectController::class, 'tracker'])->name('projecttime.tracker')->middleware(['auth', 'XSS']);
    Route::get('/{slug}/projectsss/time-tracker/{id}', [ProjectController::class, 'tracker'])->name('client.projecttime.tracker')->middleware(['auth:client', 'XSS']);
    Route::delete('tracker/{tid}/destroy', [TimeTrackerController::class, 'Destroy'])->name('tracker.destroy');
    Route::get('/{slug}/time-tracker', [TimeTrackerController::class, 'index'])->name('time.tracker')->middleware(['auth', 'XSS']);
    Route::post('/{slug}/tracker/image-view', [TimeTrackerController::class, 'getTrackerImages'])->name('tracker.image.view');
    Route::any('tracker/image-remove', [TimeTrackerController::class, 'removeTrackerImages'])->name('tracker.image.remove');


    // ================================= Zoom Meeting ======================================//


    Route::get('/{slug}/zoom-meeting', [ZoomMeetingController::class, 'index'])->name('zoom-meeting.index')->middleware(['auth', 'XSS']);
    Route::get('/{slug}/zoom-meeting/create', [ZoomMeetingController::class, 'create'])->name('zoom-meeting.create')->middleware(['auth', 'XSS']);
    Route::get('/{slug}/zoom-meeting/calendar', [ZoomMeetingController::class, 'calender'])->name('zoommeeting.Calender')->middleware(['auth', 'XSS']);
    Route::get('/{slug}/Zoom-Meeting/calendar', [ZoomMeetingController::class, 'calender'])->name('zoommeetings.Calender')->middleware(['auth:client', 'XSS']);

    Route::post('/{slug}/zoom-meeting/store', [ZoomMeetingController::class, 'store'])->name('zoom-meeting.store')->middleware(['auth', 'XSS']);
    Route::get('/{slug}/zoom-meeting/{id}/show', [ZoomMeetingController::class, 'show'])->name('zoom_meeting.show')->middleware(['auth', 'XSS']);

    Route::get('/{slug}/zoom-meetings/{id}/show', [ZoomMeetingController::class, 'show'])->name('zoom_meetings.show')->middleware(['auth:client', 'XSS']);

    Route::get('/{slug}/zoom-meeting/{id}/edit', [ZoomMeetingController::class, 'edit'])->name('zoom-meeting.edit')->middleware(['auth', 'XSS']);
    Route::post('/{slug}/zoom-meeting/{id}/update', [ZoomMeetingController::class, 'update'])->name('zoom-meeting.update')->middleware(['auth', 'XSS']);
    Route::delete('/{slug}/zoom-meeting/{id}', [ZoomMeetingController::class, 'destroy'])->name('zoom-meeting.destroy')->middleware(['auth', 'XSS']);
    Route::get('/{slug}/projects/{id}/members', [ProjectController::class, 'members'])->name('projects.members')->middleware(['auth', 'XSS']);


    //=================================== slack=============================================================//

    Route::post('/workspace/{slug}/settingsss', [WorkspaceController::class, 'settingsSlack'])->name('workspace.settings.Slack')->middleware(['auth', 'XSS']);


    //=================================== Google Calender===================================================//

    Route::post('/workspace/{slug}/google-calender', [WorkspaceController::class, 'saveGoogleCalenderSettings'])->name('google.calender.settings');
    Route::any('{slug}/event/get_event_data', [ZoomMeetingController::class, 'get_event_data'])->name('event.get_event_data')->middleware(['auth', 'XSS']);
    Route::any('/workspace/{slug}/event/export-event', [ZoomMeetingController::class, 'export_event'])->name('event.export-event')->middleware(['auth', 'XSS']);

    // ====================================telegram===============================================================//

    Route::post('/workspace/{slug}/telegram', [WorkspaceController::class, 'settingstelegram'])->name('workspace.settings.telegram')->middleware(['auth', 'XSS']);

    /*==================================Recaptcha====================================================*/

    Route::post('/recaptcha-settings', [SettingsController::class, 'recaptchaSettingStore'])->name('recaptcha.settings.store')->middleware(['auth', 'XSS']);

    // /*==============================================Invoice Paymentwall===========================================================*/
    // Route::any('{slug}/paymentwall/invoice/{invoice_id}', [PaymentWallPaymentController::class, 'invoiceindex'])->name('paymentwall.index');
    // Route::post('{slug}/invoice-pay-with-paymentwall/{invoice_id}', [PaymentWallPaymentController::class, 'invoicePayWithPaymentwall'])->name('invoice.pay.with.paymentwall');
    // Route::any('{slug}/invoice/error/{flag}/{invoice_id}', [PaymentWallPaymentController::class, 'orderpaymenterror'])->name('invoice.callback.error');

    /*================================================client password reset======================================================*/
    Route::get('{slug}/client/resetpassword/{id}', [ClientController::class, 'resetPassword'])->name('client.reset.password')->middleware(['auth', 'XSS']);
    Route::post('{slug}/client/changepassword/{id}', [ClientController::class, 'changePassword'])->name('client.change.password')->middleware(['auth', 'XSS']);

    /*================================================client account enable/disable======================================================*/
    Route::get('client/login-manage/{clientId}', [ClientController::class, 'clientManageLogin'])->name('client.login.manage')->middleware(['auth', 'XSS']);

    /*================================================Email Templates======================================================*/

    Route::get('email_template_lang/{id}/{lang?}', [EmailTemplateController::class, 'manageEmailLang'])->name('manage.email.language')->middleware(['auth']);
    Route::post('email_template_store/{pid}', [EmailTemplateController::class, 'storeEmailLang'])->name('store.email.language')->middleware(['auth']);
    Route::any('{slug}/email_template_status/{id?}', [EmailTemplateController::class, 'updateStatus'])->name('status.email.language')->middleware(['auth']);

    Route::resource('email_template', EmailTemplateController::class)->middleware(['auth']);
    // Route::resource('email_template_lang', EmailTemplateLangController::class)->middleware(['auth', 'XSS']);
    // End Email Templates



    //=================================== notifications module =============================================================//


    Route::put('{slug}/notification-templates/{id?}/{lang?}/', [NotificationTemplatesController::class, 'update'])->name('notification-templates.update')->middleware(['auth', 'XSS']);
    Route::get('{slug}/notification-templates/{id}/{lang}/', [NotificationTemplatesController::class, 'show'])->name('notification-templates.show')->middleware(['auth']);

    Route::get('{slug}/notification-templates', [NotificationTemplatesController::class, 'index'])->name('notification-templates.index')->middleware(['auth']);
    // End notifications module



    ////**===================================== Project Reports =======================================================////

    Route::resource('/{slug}/project_report', ProjectReportController::class)->except(['show'])->middleware(['auth', 'XSS']);
    Route::post('/{slug}/project_report_data', [ProjectReportController::class, 'ajax_data'])->name('projects.ajax')->middleware(['auth', 'XSS']);
    Route::get('/{slug}/project_report/{id}', [ProjectReportController::class, 'show'])->name('project_report.show')->middleware(['auth', 'XSS']);

    Route::post('/{slug}/project_report/tasks/{id}', [ProjectReportController::class, 'ajax_tasks_report'])->name('tasks.report.ajaxdata')->middleware(['auth', 'XSS']);

    Route::get('export/task_report/{id}', [ProjectReportController::class, 'export'])->name('project_report.export');


    // End Project Reports




    //////****================================== Contract Module ===========================================******///////
    Route::resource('/{slug}/contract_type', ContractsTypeController::class)->middleware(['auth', 'XSS']);

    Route::resource('/{slug}/contracts', ContractController::class)->middleware(['auth', 'XSS']);

    Route::get('get-projects/{client_id}', [ContractController::class, 'clientByProject'])->name('project.by.user.id')->middleware(['auth', 'XSS']);


    Route::get('/{slug}/contract/{id}', [ContractController::class, 'copycontract'])->name('contracts.copy')->middleware(['auth', 'XSS']);
    Route::post('/{slug}/contract/copy/store/{id}', [ContractController::class, 'copycontractstore'])->name('contracts.copy.store')->middleware(['auth', 'XSS']);


    Route::post('/{slug}/contract/{id}/contract_description', [ContractController::class, 'contract_descriptionStore'])->name('contract.contract_description.store')->middleware(['auth']);
    Route::post('/{slug}/contract/{id}/file', [ContractController::class, 'fileUpload',])->name('contracts.file.upload')->middleware(['auth', 'XSS']);

    Route::get('/{slug}/contract/{id}/file/{fid}', [ContractController::class, 'fileDownload',])->name('contracts.file.download')->middleware(['auth', 'XSS']);
    Route::delete('/{slug}/contract/{id}/file/delete', [ContractController::class, 'fileDelete',])->name('contracts.file.delete')->middleware(['auth', 'XSS']);

    Route::post('/{slug}/contract/{id}/comment', [ContractController::class, 'commentStore',])->name('comment_store.store')->middleware(['auth']);
    Route::get('/{slug}/contract/{id}/comment', [ContractController::class, 'commentDestroy',])->name('comment_store.destroy');
    Route::post('/{slug}/contract/{id}/notes', [ContractController::class, 'noteStore',])->name('note_store.store')->middleware(['auth']);
    Route::get('/{slug}/contract/{id}/notes', [ContractController::class, 'noteDestroy',])->name('note_store.destroy')->middleware(['auth']);

    Route::get('/{slug}/contract/{id}/mail', [ContractController::class, 'sendmailContract',])->name('send.mail.contract');



    Route::get('/{slug}/signature/{id}', [ContractController::class, 'signature'])->name('signature')->middleware(['auth', 'XSS']);
    Route::post('/{slug}/signaturestore', [ContractController::class, 'signatureStore'])->name('signaturestore')->middleware(['auth', 'XSS']);

    Route::post('/{slug}/contract_status_edit/{id}', [ContractController::class, 'contract_status_edit'])->name('contract.status')->middleware(['auth', 'XSS']);


    ///////*************End Contract Module ==================================================================================////


    ///////////////////////////////-------------------Storage-Setting--------------------------------------------------\\\\\\\\\
    Route::post('storage-settings', [SettingsController::class, 'storageSettingStore'])->name('storage.setting.store')->middleware(['auth', 'XSS']);


    Route::post(
        '/{slug}/Notification/Delete',
        [UserController::class, 'delete_all_notification',]
    )->name('delete_all.notifications');


    //=============================================Webhook===================================================
    Route::resource('/{slug}/webhook', WebhookController::class)->middleware(['auth', 'XSS']);
    Route::post('webhooks/response/get', [WebhookController::class, 'WebhookResponse'])->name('webhooks.response.get');
});

Route::get('/{slug}/projects/{id}/task-board/{tid}/{cid?}', [ProjectController::class, 'taskShow'])->name('tasks.show');
