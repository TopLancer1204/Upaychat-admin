<?php

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

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
Route::post('/login', 'API\UserController@login');
Route::post('/logout', 'API\UserController@logout');
Route::post('/register', 'API\UserController@register');
Route::post('/checkemail', 'API\UserController@checkemail');
Route::post('/checkmobile', 'API\UserController@checkmobile');
//Route::post('/changepassword/{id}', 'API\UserController@changePassword');
Route::post('/forgotpassword', 'API\UserController@forgotpassword');

Route::post('/slider-list', 'API\SliderController@sliderList');

Route::middleware('auth:api')->post('/user', function (Request $request) {
    $user = $request->user();
    if($user->user_status == "off") {
        $response['status'] = "false";
        $response['message'] = "Your account suspended.";
        $response['data'] = [];
    } else {
        $response['status'] = "true";
        $response['message'] = "";
        $response['data'] = $user;
    }
    return $response;
});

Route::post('/routelist', function () {
    $routes = Artisan::call('route:list');
    return $routes;
});

Route::post('/pendingsms', function () {
    $response['status'] = "true";
    $response['data'] = \App\Models\PendingSms::select('mobile', 'message')->orderBy('created_at')->get();
    \App\Models\PendingSms::query()->delete();
    return response()->json($response);
});


Route::group(['middleware' => 'auth:api'], function () {
    Route::post('/changepassword', 'API\UserController@changepassword');
    Route::post('/updateprofile', 'API\UserController@updateprofile');
    Route::post('/details', 'API\UserController@details');
    Route::post('/removephoto', 'API\UserController@deletepic');
    Route::post('/usersearch', 'API\UserController@usersearch'); // list of all users
    // **********************************************************************************************************
    Route::post('/addbank', 'API\BankDetailController@addbank'); // add a bank of user
    Route::post('/bankdetails', 'API\BankDetailController@bankdetails'); // list all banks of user
    Route::post('/getbank', 'API\BankDetailController@getbank'); // get details of a particual bank
    Route::post('/updatebank', 'API\BankDetailController@updateaddbank'); // get details of a particual bank
    Route::post('/deletebank', 'API\BankDetailController@deletebank'); // get details of a particual bank
    // **********************************************************************************************************
    Route::post('/addcard', 'API\CardDetailController@addcard'); // add a card of user
    Route::post('/carddetails', 'API\CardDetailController@carddetails'); // list all cards of user
    Route::post('/getcard', 'API\CardDetailController@getcard'); // get details of a particual card
    Route::post('/updatecard', 'API\CardDetailController@updateaddcard'); // get details of a particual card
    Route::post('/deletecard', 'API\CardDetailController@deletecard'); // get details of a particual card

    // **********************************************************************************************************

    Route::post('/wallet', 'API\WalletController@mywallet');
    Route::post('/stripepay', 'API\WalletController@stripepay');
    Route::post('/addmoneytowallet', 'API\WalletController@addmoneytowallet');

    // **********************************************************************************************************
    Route::post('/mynotification', 'API\NotificationController@mynotification');
    Route::post('/closenotification', 'API\NotificationController@closenotification');

    Route::post('/mytransactionshistory', 'API\TransactionController@mytransactionshistory');
    Route::post('/transactionshistory', 'API\TransactionController@transactionshistory');

    Route::post('/pendingrequest', 'API\TransactionController@pendingrequest');
    Route::post('/addtransaction', 'API\TransactionController@addtransaction');
    Route::post('/acceptrequest', 'API\TransactionController@acceptrequest');
    Route::post('/cancelrequest', 'API\TransactionController@cancelrequest');
    Route::post('/take-back', 'API\TransactionController@takeBack');

    Route::post('/addcomment', 'API\TransactionController@addcomment');
    Route::post('/withdrawrequest', 'API\TransactionController@addwithdrawrequest');
    Route::post('/addlike', 'API\TransactionController@addlike');
    // **********************************************************************************************************
    Route::post('/faq', 'API\PageController@faq');

    Route::post('/currenttime', function () {
        $response['status'] = "true";
        $response['message'] = Carbon::now()->toDateTimeString();
        $response['data'] = [];
        return response()->json($response);
    });
    Route::get('/id_verify', 'API\IDVerificationController@index');
    Route::post('/id_verify', 'API\IDVerificationController@verify');
});
