<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\Webhook;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\InstallerController;
use App\Http\Controllers\Seller\MediaController as SellerMediaController;
use App\Http\Controllers\Seller\AreaController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\StoreController;
use App\Http\Controllers\Seller\CategoryController;
use App\Http\Controllers\Admin\AffiliateController;
use App\Http\Controllers\DeepLinkRedirectController;

use App\Jobs\SendOrderNotificationJob;
use Imagine\Filter\Basic\Rotate;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

// ---------------------------------------------------------------------------------------------------------------------------
Route::get('/sitemap', function () {
    Artisan::call('sitemap:generate');
    return redirect()->back()->with('message', 'Sitemap generated successfully!');
});
Route::get('/clear-cache', function () {
    Artisan::call('optimize:clear');
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    return redirect()->back()->with('message', 'Cache cleared successfully.');
});
Route::get('/clear-logs', function () {
    $logPath = storage_path('logs/laravel.log');
    if (file_exists($logPath)) {
        file_put_contents($logPath, '');
    }
    return redirect()->back()->with('message', 'Log file cleared successfully.');
});
Route::get('/version', function () {
    return app()->version();
});

Route::get('storage-link', function () {
    try {
        $target = storage_path('app/public');
        $link = public_path('storage');

        // Always copy the directory, even if the link/folder exists
        if (file_exists($link) && is_link($link)) {
            // Remove the existing symlink if it exists
            unlink($link);
            print_r('Symlink removed, now copying files to public/storage.<br>');
        } elseif (file_exists($link) && is_dir($link)) {
            // Remove the existing directory if it's not a symlink
            File::deleteDirectory($link);
            print_r('Directory removed, now copying files to public/storage.<br>');
        }

        // Attempt to create symbolic link first
        if (function_exists('symlink')) {
            symlink($target, $link);
            return 'Symlink created successfully.';
        } else {
            // If symlink creation fails, fallback to copying files
            File::copyDirectory($target, $link);
            return 'Files copied to public/storage (symlink not available).';
        }
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ], 500);
    }
});

Route::get('/install', [InstallerController::class, 'index'])->middleware('guest');

Route::post('/installer/config-db', [InstallerController::class, 'config_db'])->middleware('guest');

Route::post('/installer/install', [InstallerController::class, 'install'])->middleware('guest');

Route::get('admin/web_product_card_style', [StoreController::class, 'webProductCardStyle']);
Route::get('admin/web_categories_style', [StoreController::class, 'webCategoriesStyle']);
Route::get('admin/web_brands_style', [StoreController::class, 'webBrandsStyle']);
Route::get('admin/web_wishlist_style', [StoreController::class, 'webWishlistStyle']);
Route::get('admin/web_home_page_theme', [StoreController::class, 'webHomePageTheme']);
Route::get('/media/list', [MediaController::class, 'list'])->name('admin.media.list');
Route::get('/manifest', function () {
    return response()->json(config('manifest'));
})->name('manifest');



Route::get('/publish-livewire-assets', function () {
    Artisan::call('vendor:publish', ['--tag' => 'livewire:assets']);
    return 'Livewire assets published successfully.';
});
Route::middleware([])->group(function () {
    // Check if the frontend product details component physically exists
    // This is safer than class_exists because it doesn't trigger the autoloader
    $frontEndExists = file_exists(app_path('Livewire/Products/Details.php'));

    Route::get('/', function () {
        return redirect()->route('admin.home');
    });
    Route::get('admin/register', [UserController::class, 'create']);

    Route::post('admin/users', [UserController::class, 'store']);

    Route::get('admin/logout', [UserController::class, 'logout'])->name('admin.logout');

    Route::post('/admin/users/authenticate', [UserController::class, 'authenticate'])->name('admin.authenticate');
    Route::get('admin/home', [HomeController::class, 'index'])->name('admin.home');
    Route::get('admin/login', function () {
        if (Auth::check()) {
            return redirect()->route('admin.home');
        }
        return app(UserController::class)->login();
    })->name('admin.login');



    // Routs for forgot password and reset password

    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password-mail', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [ForgotPasswordController::class, 'showResetPasswordForm'])->name('password.reset');
    Route::post('/reset-password', [ForgotPasswordController::class, 'ResetPassword'])->name('admin.password.update');

    Route::get('/admin', function () {
        if (Auth::check()) {
            //dd('here');
            // User is logged in, redirect to the admin home page
            return redirect()->route('admin.home');
            // return redirect()->route('admin.login');
        } else {
            // User is not logged in, redirect to the admin login page
            return redirect()->route('admin.login');
        }
    });

    // Deep Link Catch-all for App-Only Redirect
    // The 'where' constraint ensures reserved/list-type slugs (e.g. "list", "search", "all", etc.)
    // are NOT captured by the deep link handler.
    if (!$frontEndExists) {
        $excludedSlugs = 'list|search|all|index|page|filter|category|tag';
        Route::get('/combo-products/{slug}', [DeepLinkRedirectController::class, 'handle'])
            ->defaults('type', 'combo-products')
            ->where('slug', '^(?!(' . $excludedSlugs . ')$).+');
        Route::get('/products/{slug}', [DeepLinkRedirectController::class, 'handle'])
            ->defaults('type', 'products')
            ->where('slug', '^(?!(' . $excludedSlugs . ')$).+');
        Route::get('/sellers/{slug}', [DeepLinkRedirectController::class, 'handle'])
            ->defaults('type', 'sellers')
            ->where('slug', '^(?!(' . $excludedSlugs . ')$).+');
        Route::get('/blogs/{slug}', [DeepLinkRedirectController::class, 'handle'])
            ->defaults('type', 'blogs')
            ->where('slug', '^(?!(' . $excludedSlugs . ')$).+');
    }

    // seller routes
    Route::get('/seller', function () {
        return redirect()->route('seller.login');
    });

    Route::get('seller/login', [UserController::class, 'seller_login'])->name('seller.login');
    Route::get('seller/register', [UserController::class, 'seller_register'])->name('seller.register');
    Route::get('seller/get_zones', [AreaController::class, 'get_zones'])->name('seller.get_zones');
    Route::get('seller/logout', [UserController::class, 'seller_logout'])->name('seller.logout');
    Route::get('seller/categories/get_category_details', [CategoryController::class, 'getCategoryDetails']);
    Route::post('seller/store', [UserController::class, 'sellerStore'])->name('seller.register.store')->middleware(['demo_restriction']);

    // delivery boy routes
    Route::get('/delivery_boy', function () {
        return redirect()->route('delivery_boy.login');
    });

    Route::get('delivery_boy/login', [UserController::class, 'delivery_boy_login'])->name('delivery_boy.login');
    Route::get('delivery_boy/logout', [UserController::class, 'delivery_boy_logout'])->name('delivery_boy.logout');

    // affiliate routes
    Route::get('affiliate/login', [UserController::class, 'affiliate_login'])->name('affiliate.login');
    Route::get('affiliate/logout', [UserController::class, 'affiliate_logout'])->name('affiliate.logout');
    Route::get('affiliate/register', [UserController::class, 'affiliate_register'])->name('affiliate.register');

    // system policies pages
    Route::get("settings/seller_privacy_policy", [SettingController::class, 'sellerPrivacyPolicy'])->name('seller_privacy_policy.view');

    Route::get("admin/privacy_policy/privacy_policy_page", [SettingController::class, 'privacy_policy'])->name('privacy_policy.view');

    Route::get("admin/terms_and_conditions/terms_and_condition_page", [SettingController::class, 'terms_and_conditions'])->name('terms_and_conditions.view');

    Route::get("admin/shipping_policy/shipping_policy_page", [SettingController::class, 'shipping_policy'])->name('shipping_policy.view');

    Route::get("admin/return_policy/return_policy_page", [SettingController::class, 'return_policy'])->name('return_policy.view');

    //admin & seller policies page

    Route::get("admin/privacy_policy/seller_privacy_policy_page", [SettingController::class, 'sellerPrivacyPolicy']);









    Route::get("admin/terms_and_condition/seller_terms_and_condition_page", [SettingController::class, 'seller_terms_and_condition'])->name('seller_terms_and_conditions.view');

    // delivery boy policies page

    Route::get("admin/privacy_policy/delivery_boy_privacy_page", [SettingController::class, 'delivery_boy_privacy_policy'])->name('delivery_boy_privacy_policy.view');

    Route::get("admin/terms_and_conditions/delivery_boy_terms_and_condition_page", [SettingController::class, 'delivery_boy_terms_and_conditions'])->name('delivery_boy_terms_and_conditions.view');

    // admin routes file

    Route::group(['middleware' => ['auth']], function () {
        // Routes that only admins can access
        include_once("admin_routes.php");
        include_once("seller_routes.php");
        include_once("delivery_boy_routes.php");
        include_once("affiliate_routes.php");
    });

    Route::get('admin/media/image', [MediaController::class, 'dynamic_image'])->name('admin.dynamic_image');
    Route::get('/media/image', [MediaController::class, 'dynamic_image'])->name('front_end.dynamic_image');

    // media route

    Route::get('/admin/media/list', [MediaController::class, 'list'])->name('admin.media.list');

    Route::get('/seller/media/list', [SellerMediaController::class, 'list'])->name('seller.media.list');

    if ($frontEndExists) {
        include_once("front_end_routes.php");
    }

    //webhook route

    Route::post('admin/webhook/razorpay_webhook', [Webhook::class, 'razorpay_webhook'])->name('admin.razorpay_webhook');
    Route::post('admin/webhook/paystack_webhook', [Webhook::class, 'paystack_webhook'])->name('admin.paystack_webhook');
    Route::post('admin/webhook/stripe_webhook', [Webhook::class, 'stripe_webhook'])->name('admin.stripe_webhook');
    Route::post('/admin/webhook/phonepe_webhook', [Webhook::class, 'phonepe_webhook']);

    Route::get('admin/webhook/spr_webhook', [Webhook::class, 'spr_webhook'])->name('admin.spr_webhook');
});
Route::get('admin/orders/generat_invoice_PDF/{id}/{user_id}', [OrderController::class, 'generatInvoicePDF'])
    ->name('admin.orders.generatInvoicePDF');
Route::get('admin/orders/generat_app_invoice_PDF/{id}/{user_id}/{path}', [OrderController::class, 'generatInvoicePDF'])
    ->name('admin.orders.generatAPPInvoicePDF');
Route::get('/admin/stores', [StoreController::class, 'index'])->name('admin.stores.index');
Route::post('admin/store', [StoreController::class, 'store'])->middleware(['demo_restriction'])->middleware('permissions:create store')->name('admin.stores.store');
Route::get("settings/registration", [SettingController::class, 'registration'])->name('admin.system_registration');
Route::post("settings/system_registration", [SettingController::class, 'systemRegister'])->name('admin.system_register')->middleware(['demo_restriction']);
Route::post("settings/web_system_registration", [SettingController::class, 'WebsystemRegister'])->name('admin.web_system_register')->middleware(['demo_restriction']);
Route::post('/affiliate_users/store', [AffiliateController::class, 'store'])->name('admin.affiliate_users.register');

Route::post('add_to_favorites', [ProductController::class, 'add_to_favorites'])
    ->name('add_to_favorites');

Route::post('remove_from_favorite', [ProductController::class, 'remove_from_favorite'])
    ->name('remove_from_favorite');
Route::get('/test-mail', function () {
    try {
        \Mail::raw('SMTP test successful.', function ($message) {
            $message->to('infinitie.raj@gmail.com')
                ->subject('SMTP Test');
        });
        return 'Mail sent successfully';
    } catch (\Exception $e) {
        return 'Mail failed: ' . $e->getMessage();
    }
});

// Route::get('sellers', [HomeController::class, 'getSellersDebug'])->name('get.sellers');
Route::get('/run-queue', function () {
    Artisan::call('queue:work', ['--once' => true]);

});
Route::get('/test-job', function () {
    SendOrderNotificationJob::dispatch(1); // pass test order ID
    return 'Job dispatched successfully';
});

Route::get('/queue-work', function () {
    Artisan::call('queue:work', ['--stop-when-empty' => true]);
});

// Deep Link Testing Page
Route::get('/test-deep-links', function () {
    $products = \App\Models\Product::limit(5)->get(['id', 'slug', 'name', 'store_id']);
    $sellers = \App\Models\Store::limit(5)->get(['id', 'slug', 'name']);
    $blogs = \App\Models\Blog::where('status', 1)->limit(5)->get(['id', 'slug', 'title']);

    return view('test-deep', compact('products', 'sellers', 'blogs'));
})->name('test.deep.links');
