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
        Route::post('mediaAnalysis', 'MediaApiController@mediaAnalysis');
        Route::post('mediaAssessment', 'MediaApiController@mediaAssessment');

    }
);


Route::group(['middleware' => ['api']], function() {
    /////User Start
    Route::post('user/add', [UserController::class, 'addUser']);
    Route::post('user/userlist', [UserController::class, 'userList']);
    Route::get('user/delete/{id}', [UserController::class, 'deleteUser']);
    Route::get('user/getrole', [UserController::class, 'getRole']);
    Route::get('user/getTeam', [UserController::class, 'getTeam']);
    Route::get('user/countPre',[UserController::class,'getPreAnalysis']);
    Route::get('user/countPreDone',[UserController::class,'getPreAnalysDone']);
    Route::get('user/countMediaIn',[UserController::class,'getMediaIn']);
    Route::get('user/countAssessmentPro',[UserController::class,'getAssessmentInProcess']);
    Route::get('user/countAssessmentDone',[UserController::class,'getAssessmentDone']);
    Route::get('user/countCasePossible',[UserController::class,'getCasePossible']);
    Route::get('user/countCaseNotPossible',[UserController::class,'getCaseNotPossible']);
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
    Route::post('job/updateJobStatus', [JobController::class, 'updateJobStatus']);
    Route::get('job/getmedia/{id}', [JobController::class, 'getMediaJob']);
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
    Route::get('media/getmedia/{id}', [MediaController::class, 'getMedia']);
    Route::get('media/mediaHistory/{id}/{type}/{module}', [MediaController::class, 'getMediaHistory']);
    Route::get('media/mediauserlist/{id}', [MediaController::class, 'getMediaUserList']);
    Route::post('media/changemediaAssign', [MediaController::class, 'changeMediaAssign']);
    Route::get('media/mediastatus/{type}', [MediaController::class, 'getMediaStatus']);
    Route::post('media/updatepreAnalysis', [MediaController::class, 'updateMediaAnalysis']); 
    Route::get('media/getAllBranch', [MediaController::class, 'getAllBranch']);
    Route::post('media/sendMediatransfer/', [MediaController::class, 'sendMediatransfer']); 
    Route::post('media/updateMediaAssessment', [MediaController::class, 'updateMediaAssessment']);
    Route::get('media/generateMediaCode/{id}', [MediaController::class, 'generateMediaCode']);
    Route::post('media/saveMediateam',[MediaController::class, 'updateMediaTeam']);
    ///////End 
});
