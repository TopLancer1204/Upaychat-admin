<?php

use App\Helper\Helper;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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

Route::get('/linkstorage', function () {
    Artisan::call('storage:link');
    return '<h1>Linked</h1>';
});

Route::get('/passport', function () {
    Artisan::call('passport:install --force');
    return '<h1>passport Cache install</h1>';
});

//Clear Cache facade value:
Route::get('/clear', function () {
    Artisan::call('optimize:clear');
    return '<h1>Optimize clear</h1>';
});

//Reoptimized class loader:
Route::get('/optimize', function () {
    Artisan::call('optimize');
    return '<h1>Optimized class loader</h1>';
});

Route::get('/sendEmail', function () {
    Helper::sendEmail('upmanager200@gmail.com', now() . "\n" . now(), "Test email");
    return now();
});

Route::get('/sendSms', function () {
    Helper::sendSMS('+12092370450', now());
    return now();
});

Auth::routes(['register' => false]);

Route::group(['middleware' => ['auth']], function () {
    Route::get('/', 'Backend\HomeGetController@index')->name("index");
    Route::get('/home', 'Backend\HomeGetController@index')->name("home");
    Route::get('/logout', 'Backend\HomeGetController@getLogout')->name("backend-logout");

    Route::get('/profile', 'Backend\ProfileController@getAdminEdit')->name("profile");
    Route::post('/saveprofile', 'Backend\ProfileController@postUsersEdit')->name("saveprofile");

    Route::get('/password', 'Backend\ProfileController@getAdminEditPass')->name("password");
    Route::post('/savepassword', 'Backend\ProfileController@savepassword')->name("savepassword");
    Route::resource('identity', 'Backend\IdentityController');

    Route::prefix("faq")->middleware('Admin')->group(function () {
        Route::get('/', 'Backend\FaqController@index')->name("faq");
        Route::get('/faq-add', 'Backend\FaqController@add')->name("faq-add");
        Route::post('/faq-add', 'Backend\FaqController@savefaq')->name("faq-add");
        Route::get('/faq-edit/{faqId}', 'Backend\FaqController@getFaqEdit')->name("faq-edit");
        Route::post('/faq-edit/{faqId}', 'Backend\FaqController@postFaqEdit')->name("faq-edit");
        Route::post('/', 'Backend\FaqController@postPages')->name("faq");
    });

    Route::prefix("pages")->middleware('Admin')->group(function () {
        Route::get('/', 'Backend\PageController@getPages')->name("pages");
        Route::get('/page-add', 'Backend\PageController@getPagesAdd')->name("page-add");
        Route::get('/page-edit/{pageId}', 'Backend\PageController@getPagesEdit')->name("page-edit");
        Route::post('/', 'Backend\PageController@postPages')->name("pages");
        Route::post('/page-add', 'Backend\PageController@postPagesAdd')->name("page-add");
        Route::post('/page-edit/{pageId}', 'Backend\PageController@postPagesEdit')->name("page-edit");
    });

    Route::prefix("sliders")->middleware('Admin')->group(function () {
        Route::get('/', 'Backend\SliderController@getSliders')->name("sliders");
        Route::get('/slider-add', 'Backend\SliderController@getSlidersAdd')->name("slider-add");
        Route::get('/slider-edit/{sliderId}', 'Backend\SliderController@getSlidersEdit')->name("slider-edit");
        Route::post('/', 'Backend\SliderController@postSliders')->name("sliders");
        Route::post('/slider-add', 'Backend\SliderController@postSlidersAdd')->name("slider-add");
        Route::post('/slider-edit/{sliderId}', 'Backend\SliderController@postSlidersEdit')->name("slider-edit");
    });

    Route::prefix("users")->middleware('Admin')->group(function () {
        Route::get('/', 'Backend\UserController@getUsers')->name("users");
        Route::get('/user-add', 'Backend\UserController@getUsersAdd')->name("user-add");
        Route::get('/user-edit/{userId}', 'Backend\UserController@getUsersEdit')->name("user-edit");
        Route::post('/', 'Backend\UserController@postUsers')->name("users");
        Route::post('/user-add', 'Backend\UserController@postUsersAdd')->name("user-add");
        Route::post('/user-edit/{userId}', 'Backend\UserController@postUsersEdit')->name("user-edit");
    });

    Route::prefix("posts")->middleware('Admin')->group(function () {
        Route::get('/', 'Backend\PostController@getPosts')->name("posts");
        Route::get('/getcomment/{postId}', 'Backend\PostController@postComments')->name("getcomment");
        Route::post('/deletecomment', 'Backend\PostController@deletecomment')->name("deletecomment");
        Route::post('/blockuser', 'Backend\PostController@blockuser')->name("blockuser");
    });

    Route::prefix("withdraws")->middleware('Admin')->group(function () {
        Route::get('/', 'Backend\WithdrawController@getWithdraws')->name("withdraws");
        Route::post('/', 'Backend\WithdrawController@postWithdraws')->name("Withdraws");
        //Route::post('/setting-edit/{settingId}', 'Backend\SettingController@postSettingsEdit')->name("withdraw-edit");
    });

    Route::prefix("transactions")->middleware('Admin')->group(function () {
        Route::get('/', 'Backend\TransactionController@getallUsers')->name("transactions");
        Route::get('/users-transactions/{userId}', 'Backend\TransactionController@getTransactions')->name("users-transactions");
        Route::post('/delete', 'Backend\TransactionController@delete')->name("delete");
    });

    Route::prefix("settings")->middleware('Admin')->group(function () {
        Route::get('/', 'Backend\SettingController@getSettings')->name("settings");
        Route::get('/setting-edit/{settingId}', 'Backend\SettingController@getSettingsEdit')->name("setting-edit");
        Route::post('/', 'Backend\SettingController@postSettings')->name("settings");
        Route::post('/setting-edit/{settingId}', 'Backend\SettingController@postSettingsEdit')->name("setting-edit");
    });

    Route::prefix("reports")->middleware('Admin')->group(function () {
        Route::get('/', 'Backend\ReportController@index')->name("reports");
        Route::post('/', 'Backend\ReportController@searchreport')->name("searchreport");
        Route::post('/getuser', 'Backend\ReportController@getuser')->name("getuser");
    });

    Route::prefix("blogs")->middleware('Blog')->group(function () {
        Route::get('/', 'Backend\BlogController@getBlogs')->name("blogs");
        Route::post('/', 'Backend\BlogController@postBlogs')->name("blogs");
        Route::get('/blog-add', 'Backend\BlogController@getBlogAdd')->name("blog-add");
        Route::post('/blog-add', 'Backend\BlogController@postBlogAdd')->name("blog-add");
        Route::get('/blog-edit/{settingId}', 'Backend\BlogController@getBlogEdit')->name("blog-edit");
        Route::post('/blog-edit/{settingId}', 'Backend\BlogController@postBlogEdit')->name("blog-edit");
        Route::get('/blog-category', 'Backend\BlogController@getBlogCategory')->name("blog-category");
        Route::post('/blog-category', 'Backend\BlogController@postBlogCategory')->name("blog-category");
        Route::get('/blog-category-add', 'Backend\BlogController@getBlogCategoryAdd')->name("blog-category-add");
        Route::post('/blog-category-add', 'Backend\BlogController@postBlogCategoryAdd')->name("blog-category-add");
        Route::get('/blog-category-edit/{settingId}', 'Backend\BlogController@getBlogCategoryEdit')->name("blog-category-edit");
        Route::post('/blog-category-edit/{settingId}', 'Backend\BlogController@postBlogCategoryEdit')->name("blog-category-edit");

        Route::post('/getuser', 'Backend\ReportController@getuser')->name("getuser");
    });


    Route::any('/ckfinder/connector', '\CKSource\CKFinderBridge\Controller\CKFinderController@requestAction')
        ->name('ckfinder_connector');

    Route::any('/ckfinder/browser', '\CKSource\CKFinderBridge\Controller\CKFinderController@browserAction')
        ->name('ckfinder_browser');
});
