<?php

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
use Intervention\Image\ImageManagerStatic as InterventionImage;
use Illuminate\Support\Facades\Storage;

Route::get('mail/basic','MailController@basic_email');
Route::get('mail/html','MailController@html_email');
Route::get('mail/attachment','MailController@attachment_email');

Auth::routes();
Route::get('logout', 'Auth\LoginController@logout')->name('logout');

// Baintree
Route::get('/plans', 'PlanController@index');
Route::post('braintree/webhook', '\Laravel\Cashier\Http\Controllers\WebhookController@handleWebhook');
Route::get('/payment/process', 'PaymentController@process')->name('payment.process');
Route::group(['middleware' => 'auth'], function () {
    Route::get('/plan/{plan}', 'PlanController@show');
    Route::get('/braintree/token', 'BraintreeController@token');
    Route::post('/subscribe', 'SubscriptionController@store');
});

Route::middleware('guest')->group(function(){
    Route::get('verify-user/{code}', 'Auth\RegisterController@activateUser')->name('activate.user');
    Route::get('resend-code/{user}', 'Auth\RegisterController@resendActivation')->name('resend_code');
});

Route::get('storage/{album}/{filename}', function ($album,$filename)
{
    $path = storage_path('app/'.$album.'/'.$filename);
    if (!File::exists($path)) {
        abort(404);
    }
    $file = File::get($path);
    $type = File::mimeType($path);
    $response = Response::make($file, 200);
    $response->header("Content-Type", $type);
    return $response;
});

Route::get('storage/thumbnail/{album}/{filename}', function ($album,$filename)
{
    $path = storage_path('app/'.$album.'/'.$filename);
    if (!File::exists($path)) {
        abort(404);
    }
    $thumbnail = storage_path('app/'.$album.'/thumb_'.$filename);
    if (!File::exists($thumbnail)) {
        InterventionImage::make($path)->resize(320,240)->save($thumbnail);
    }
    $file = File::get($thumbnail);
    $type = File::mimeType($thumbnail);
    $response = Response::make($file, 200);
    $response->header("Content-Type", $type);
    return $response;
});

// Registration 
Route::get('register/{role}', 'Auth\RegisterController@index')->name('register')->middleware('guest');
Route::post('register/{role}', 'Auth\RegisterController@register')->name('register')->middleware('guest');

Route::get('localization/{locale}', 'LocalizationController@index')->name('localization');
Route::get('search', 'SearchController@index')->name('search');

// Static pages
Route::get('/', 'IndexController@index')->name('home');
Route::get('services', 'IndexController@services')->name('services');
Route::get('terms', 'IndexController@terms')->name('terms');
Route::get('help', 'IndexController@help')->name('help');
Route::get('publicities', 'IndexController@publicities')->name('publicities');
Route::get('confidentialities', 'IndexController@confidentialities')->name('confidentialities');


Route::get('shop/{category?}', 'ShopController@index')->name('shop.index');// List product by Category OR no
Route::post('product/{product}', 'ShopController@add')->name('shop.add')->middleware('auth');// Add product in cart
Route::get('cart', 'ShopController@cart')->name('shop.cart')->middleware('auth');// Show cart

Route::get('product/{product}', 'ProductController@index')->name('product.index');// View Product

Route::get('shop/reduce/{product}', 'ShopController@reduceByOne')->name('shop.product.reduce');// Delete one unity or all the selected product in the cart
Route::get('shop/delete/{product}', 'ShopController@deleteAll')->name('shop.product.delete');

Route::get('checkout', 'ShopController@getCheckout')->name('shop.product.checkout');
Route::post('checkout', 'ShopController@getCheckout')->name('shop.product.postCheckout');


Route::get('blogs/{filter?}', 'BlogController@all')->name('blog.all');
Route::get('blog/{blog}', 'BlogController@index')->name('blog.index');
Route::get('blog/{blog}/comments', 'CommentController@index')->name('comment.list');

Route::get('page/{page}', 'PageController@index')->name('page.index');

Route::middleware(["auth"])->group(function () {
    Route::get('profile', 'UserController@profile')->name('profile');

    Route::get('product/{product}/{type}', 'LabelController@storeOrUpdate')->name('label.store');// Save OR Star Product
    Route::get('products/{type}', 'LabelController@all')->name('label.list');// List saved products OR starred Product

    Route::post('blog/{blog}/comment', 'CommentController@store')->name('comment.store');
    Route::get('blog/{blog}/comment/{comment}', 'CommentController@edit')->name('comment.edit');
    Route::post('blog/{blog}/comment/{comment}', 'CommentController@update')->name('comment.update');

    // Send a message by Javascript.
    Route::get('/chat', 'ChatController@index');
    Route::get('/chat/messages', 'ChatController@fetchMessages');
    Route::post('/chat/messages', 'ChatController@sendMessage');

});

Route::prefix('admin')->middleware(["auth","role:admin"])->group(function () {
    Route::get('/', 'AdminController@dashboard')->name('admin.dashboard');

    Route::get('card', 'AdminController@card')->name('admin.card');
    Route::get('carts', 'CartController@allAdmin')->name('admin.cart.list');
    Route::get('cart/{cart}', 'CartController@index')->name('admin.cart.show');

    // Blog Controller Groups
    Route::get('blogs/{filter?}', 'BlogController@allAdmin')->name('admin.blog.list');
    Route::prefix('blog')->group(function(){
        Route::get('/', 'BlogController@create')->name('admin.blog.create');
        Route::post('/', 'BlogController@store')->name('admin.blog.store');
        Route::get('update/{blog}', 'BlogController@edit')->name('admin.blog.edit');
        Route::post('update/{blog}', 'BlogController@update')->name('admin.blog.update');
        Route::get('publish/{blog}', 'BlogController@publish')->name('admin.blog.publish');
        Route::get('archive/{blog}', 'BlogController@archive')->name('admin.blog.archive');
        Route::get('trash/{blog}', 'BlogController@trash')->name('admin.blog.trash');
        Route::get('restore/{blog}', 'BlogController@restore')->name('admin.blog.restore');
        Route::get('star/{blog}', 'BlogController@star')->name('admin.blog.star');
        Route::get('delete/{blog}', 'BlogController@delete')->name('admin.blog.delete');
    });

    // Product Controller Groups
    Route::get('products/{filter?}', 'ProductController@all')->name('admin.product.list');
    Route::prefix('product')->group(function(){
        Route::get('/', 'ProductController@create')->name('admin.product.create');
        Route::post('/', 'ProductController@store')->name('admin.product.store');
        Route::get('show/{product}', 'ProductController@show')->name('admin.product.show');
        Route::get('update/{product}', 'ProductController@edit')->name('admin.product.edit');
        Route::post('update/{product}', 'ProductController@update')->name('admin.product.update');
        Route::get('publish/{product}', 'ProductController@publish')->name('admin.product.publish');
        Route::get('archive/{product}', 'ProductController@archive')->name('admin.product.archive');
        Route::get('trash/{product}', 'ProductController@trash')->name('admin.product.trash');
        Route::get('restore/{product}', 'ProductController@restore')->name('admin.product.restore');
        Route::get('delete/{product}', 'ProductController@delete')->name('admin.product.delete');
    });

    // Category Controller Groups
    Route::get('categories/{filter?}', 'CategoryController@allAdmin')->name('admin.category.list');
    Route::prefix('category')->group(function(){
        Route::get('/', 'CategoryController@create')->name('admin.category.create');
        Route::post('/', 'CategoryController@store')->name('admin.category.store');
        Route::get('show/{category}', 'CategoryController@show')->name('admin.category.show');
        Route::get('update/{category}', 'CategoryController@edit')->name('admin.category.edit');
        Route::post('update/{category}', 'CategoryController@update')->name('admin.category.update');
        Route::get('delete/{category}', 'CategoryController@delete')->name('admin.category.delete');
    });

    // User Controller Groups
    Route::get('users/{filter?}', 'UserController@all')->name('admin.user.list');
    Route::prefix('user')->group(function(){
        Route::get('/', 'UserController@create')->name('admin.user.create');
        Route::post('/', 'UserController@store')->name('admin.user.store');
        Route::get('show/{user}', 'UserController@show')->name('admin.user.show');
        Route::post('show/{user}', 'ObservationController@store')->name('admin.user.observe');
        Route::get('update/{user}', 'UserController@edit')->name('admin.user.edit');
        Route::post('update/{user}', 'UserController@update')->name('admin.user.update');
        Route::get('active/{user}', 'UserController@active')->name('admin.user.active');
        Route::get('block/{user}', 'UserController@block')->name('admin.user.block');
        Route::get('disable/{user}', 'UserController@disable')->name('admin.user.disable');
        Route::get('delete/{user}', 'UserController@delete')->name('admin.user.delete');
    });

    // Page Controller Groups
    Route::get('pages/{filter?}', 'PageController@allAdmin')->name('admin.page.list');
    Route::prefix('page')->group(function(){
        Route::get('/', 'PageController@create')->name('admin.page.create');
        Route::post('/', 'PageController@store')->name('admin.page.store');
        Route::get('show/{page}', 'PageController@show')->name('admin.page.show');
        Route::get('update/{page}', 'PageController@edit')->name('admin.page.edit');
        Route::post('update/{page}', 'PageController@update')->name('admin.page.update');
        Route::get('delete/{page}', 'PageController@delete')->name('admin.page.delete');
    });

    // Pub Controller Groups
    Route::get('pubs/{filter?}', 'PubController@allAdmin')->name('admin.pub.list');
    Route::prefix('pub')->group(function(){
        Route::get('/', 'PubController@create')->name('admin.pub.create');
        Route::post('/', 'PubController@store')->name('admin.pub.store');
        Route::get('show/{pub}', 'PubController@show')->name('admin.pub.show');
        Route::get('update/{pub}', 'PubController@edit')->name('admin.pub.edit');
        Route::post('update/{pub}', 'PubController@update')->name('admin.pub.update');
        Route::get('delete/{pub}', 'PubController@delete')->name('admin.pub.delete');
    });

    // Observation Controller Groups
    Route::get('observations/{filter?}', 'ObservationController@allAdmin')->name('admin.observation.list');
    Route::prefix('observation')->group(function(){
        Route::get('update/{observation}', 'ObservationController@edit')->name('admin.observation.edit');
        Route::post('update/{observation}', 'ObservationController@update')->name('admin.observation.update');
        Route::get('delete/{observation}', 'ObservationController@delete')->name('admin.observation.delete');
        Route::get('restore/{observation}', 'ObservationController@restore')->name('admin.observation.restore');
    });

    // Config Controller
    Route::prefix('config')->group(function () {
        Route::get('site', 'ConfigController@site')->name('config.site');
        Route::post('site', 'ConfigController@site')->name('config.site.update');
        Route::get('social', 'ConfigController@social')->name('config.social');
        Route::post('social', 'ConfigController@social')->name('config.social.update');
        Route::get('payment', 'ConfigController@payment')->name('config.payment');
        Route::post('payment', 'ConfigController@payment')->name('config.payment.update');
        Route::get('fontawesome', 'ConfigController@fontawesome')->name('config.fontawesome');
    });

    // Order Controller Groups
    Route::get('orders/{filter?}', 'OrderController@allAdmin')->name('admin.order.list');
    Route::prefix('order')->group(function(){
        Route::get('/{order}', 'OrderController@index')->name('admin.order.index');
    });

});

