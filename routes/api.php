<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\StateController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\MediaApiController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\RecoveryController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\PaymentApiController;
use App\Http\Controllers\ReceiptMasterController;
use App\Http\Controllers\EmailTemplateController;

/*
    |--------------------------------------------------------------------------
    | API Routes
    |--------------------------------------------------------------------------
    |
    | Here is where you can register API routes for your application. These
    | routes are loaded by the RouteServiceProvider within a group which
    | is assigned the "api" middleware group. Enjoy building your API!
    |
*/
Route::group(
    [
        'middleware' => 'api',
        'namespace'  => 'App\Http\Controllers',
        'prefix'     => 'auth',
    ],
    function ($router) {
        Route::post('login', 'AuthController@login');
        Route::post('logout', 'AuthController@logout');
        Route::get('profile', 'AuthController@profile');
        Route::post('refresh', 'AuthController@refresh');
        Route::post('forgot_password', 'AuthController@forgotPassword');
        Route::post('pre-inspection', 'MediaApiController@preInspection');
        Route::post('accountSave','MediaApiController@accountSave');
		Route::post('contactSave','MediaApiController@contactSave');
		Route::post('quote-update','MediaApiController@quoteUpdate');
		Route::post('job-owner-change','MediaApiController@JobOwnerChange');
		Route::post('deal-name-change','MediaApiController@DealNameChange');
		Route::post('media-price','MediaApiController@getMediaPrice');
		Route::post('generate-invoice','MediaApiController@addAnalysisCharges');
    }
);


Route::group(['middleware' => ['api']], function() {
    /////User Start
    Route::post('user/add', [UserController::class, 'addUser']);
    Route::post('user/changePassword', [UserController::class, 'changePassword']);
    Route::post('user/userlist', [UserController::class, 'userList']);
    Route::get('user/delete/{id}', [UserController::class, 'deleteUser']);
    Route::get('user/getrole', [UserController::class, 'getRole']);
    Route::get('user/getTeam', [UserController::class, 'getTeam']);
    Route::get('user/DashBaordCount',[UserController::class,'DashBaordCount']);
    Route::get('user/getSupervisor/{roleId}/{branchid}',[UserController::class, 'getSupervisor']);
    ////User End
    
    ///Role Start
    Route::post('role/rolelist', [RoleController::class, 'roleList']);
    Route::post('role/add', [RoleController::class, 'addRole']);
    Route::post('role/update', [RoleController::class, 'updateRole']);
    Route::get('role/getrole/{id}', [RoleController::class, 'getRole']);
    Route::get('role/all', [RoleController::class, 'allRole']);
    ////Role End
    
    ///Job List Start
    Route::post('job/joblist', [JobController::class, 'joblist']);
    Route::post('job/jobconfirm', [JobController::class, 'jobconfirm']);
    Route::post('job/updateJobStatus', [JobController::class, 'updateJobStatus']);
    Route::get('job/getmedia/{id}', [JobController::class, 'getMediaJob']);
    Route::get('job/getObservation/{id}', [JobController::class, 'getMediaObservation']);
    Route::post('job/updateObservation', [JobController::class, 'updateObservation']);
    Route::get('job/getStatusHistory/{id}', [JobController::class, 'getStatusHistory']);
    Route::post('job/updateMediaStatus', [JobController::class, 'updateMediaStatus']);
    Route::get('job/obvertation-details/{id}', [JobController::class, 'getObvertationDetails']);
    ///Branch Start
    Route::post('branch/branchlist', [BranchController::class, 'branchList']);
    Route::post('branch/add', [BranchController::class, 'addBranch']);
    Route::post('branch/update', [BranchController::class, 'updateBranch']);
    Route::get('branch/all', [BranchController::class, 'allBranch']);
    ////Branch End
    Route::get('country/all', [CountryController::class, 'allCountry']);
    Route::get('state/all', [StateController::class, 'allState']);
    Route::get('module/all', [ModuleController::class, 'allModule']);

    //////new Controller For media Strat
    Route::post('media/medialist', [MediaController::class, 'medialist']);
    Route::post('media/mediaoutlist', [MediaController::class, 'mediaOutList']);
    Route::get('media/getmedia/{id}', [MediaController::class, 'getMedia']);
    Route::get('media/all-history/{id}', [MediaController::class, 'getAllHistory']);
    Route::get('media/transfer-history/{id}', [MediaController::class, 'getTransferHistory']);
    Route::get('media/dept-user/{id}/{mediaid}', [MediaController::class, 'getdeptUser']);
    Route::post('media/update-allot-job', [MediaController::class, 'updateAllotJob']);

    Route::get('media/mediaHistory/{id}/{type}/{module}', [MediaController::class, 'getMediaHistory']);
    Route::get('media/mediauserlist/{id}', [MediaController::class, 'getMediaUserList']);
    Route::post('media/changemediaAssign', [MediaController::class, 'changeMediaAssign']);
    Route::get('media/mediastatus/{type}', [MediaController::class, 'getMediaStatus']);
    Route::post('media/updatepreAnalysis', [MediaController::class, 'updateMediaAnalysis']); 
    Route::get('media/getAllBranch', [MediaController::class, 'getAllBranch']);
    Route::get('media/transfer-branch', [MediaController::class, 'transferBranch']);
    Route::post('media/sendMediatransfer/', [MediaController::class, 'sendMediatransfer']); 
    Route::post('media/updateMediaAssessment', [MediaController::class, 'updateMediaAssessment']);
    Route::get('media/generateMediaCode/{id}', [MediaController::class, 'generateMediaCode']);
    Route::post('media/updateGatePassRef/', [MediaController::class, 'updateGatePassRef']);
    Route::post('media/saveMediateam',[MediaController::class, 'updateMediaTeam']);
    Route::post('media/upload', [MediaController::class, 'upload']);
    Route::get('media/deleteFile/{id}', [MediaController::class,'deleteFile']);
    ///////End 
    /// GatePass List
    Route::post('job/gatepasslist', [JobController::class, 'gatepasslist']);
    Route::post('job/addgatepass', [JobController::class, 'addgatepass']);   
    Route::get('job/downloadpass/{id}',[JobController::class,'downloadPass']);
     /// Inventory
     Route::post('inventory/inventory-list', [InventoryController::class, 'inventoryList']);
     Route::post('inventory/update', [InventoryController::class, 'inventorySave']);
     Route::get('inventory/fatch/{id}', [InventoryController::class, 'getInventory']);
     
     //Recovery
     Route::get('media/send-attachment/{id}', [MediaController::class, 'SendAttahment']);
     Route::get('recovery/fatch-recovery/{id}', [RecoveryController::class, '_getRecovery']);
     Route::get('recovery/dept-user/{id}', [RecoveryController::class, 'getdeptUser']);
     Route::post('recovery/update', [RecoveryController::class, 'recoverySave']);
     Route::post('recovery/notify-tech', [RecoveryController::class, 'notifyTech']);
     Route::post('recovery/update-allot-job', [RecoveryController::class, 'updateAllotJob']);
     Route::post('recovery/update-branch-clone-user', [RecoveryController::class, 'updateBranchCloneUser']);
     Route::post('recovery/request-extension', [RecoveryController::class, 'requestEextension']);
     Route::get('recovery/fatch-directory/{id}', [RecoveryController::class, 'getDirectory']);
     Route::post('recovery/update-directory', [RecoveryController::class, 'saveDirectory']);
     Route::post('recovery/update-client-data', [RecoveryController::class, 'updateClentdata']);
     Route::post('recovery/rework-update', [RecoveryController::class, 'updateRework']);
     Route::post('recovery/update-media-dl', [RecoveryController::class, 'updateDl']);
     Route::get('media/original-media/{id}', [MediaController::class, 'originalMedia']);
     Route::post('media/data-out', [MediaController::class, 'mediaDataout']);
     Route::post('recovery/requsetmediaout', [RecoveryController::class, 'requsetmediaout']);
     Route::post('recovery/responcemediaout', [RecoveryController::class, 'responcemediaout']);
     Route::post('recovery/send-media-to-client', [RecoveryController::class, 'sendMediaToclient']);
     Route::post('job/wiping-list', [JobController::class, 'wipingList']);
     Route::get('job/request-wiping/{id}/{type}', [JobController::class, 'requestWiping']);
     Route::post('job/update-wipe-status', [JobController::class, 'updateWipingStatus']);
     Route::post('job/wiping-due-list', [JobController::class,'wipingDueList']);
     Route::post('media/media-status-update', [MediaController::class,'updateStatusMedia']);
     Route::post('recovery/update-extension', [RecoveryController::class, 'updateEextension']);
     Route::post('recovery/update-price', [RecoveryController::class, 'updatePrice']);
     Route::get('media/recovery-charges/{id}', [MediaController::class, 'getRecoveryCharges']);
     Route::post('recovery/add-quotation', [RecoveryController::class, 'addQuotation']);
     Route::post('payment/add-payment', [PaymentApiController::class, 'addPayment']);
     Route::post('payment/list', [PaymentApiController::class, 'paymentList']);
     Route::get('payment/generate-invoice/{id}', [PaymentApiController::class, 'generateInvoice']);
     Route::post('payment/update-po-number', [PaymentApiController::class, 'updatePoNumber']);
     Route::get('payment/generate-irn/{invoiceId}', [PaymentApiController::class, 'generateIrn']);
    /////////////Company

    Route::post('company/company-list', [CompanyController::class, 'companyList']);
    Route::post('company/update-company', [CompanyController::class, 'updateCompany']);
    Route::post('contact/update-contact', [ContactController::class, 'updateContact']);
    Route::post('contact/contact-list', [ContactController::class, 'contactList']);
    Route::get('contact/{id}', [ContactController::class, 'getContact']);
    Route::post('receipt/list', [ReceiptMasterController::class, 'receiptList']); 
    Route::post('receipt/update', [ReceiptMasterController::class, 'addReceipt']); 
    Route::get('receipt/getreceipt/{type}/{id}', [ReceiptMasterController::class, 'getReceiptDetails']);
    Route::post('template/list', [EmailTemplateController::class, 'templateList']);
     Route::post('template/add', [EmailTemplateController::class, 'templateAdd']);
     Route::get('template/detail/{id}', [EmailTemplateController::class, 'templateDetail']);
     Route::get('template/delete/{id}', [EmailTemplateController::class, 'deleteTemplate']);

    
});