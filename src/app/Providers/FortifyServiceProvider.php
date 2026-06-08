<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Contracts\LogoutResponse;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);
        //会員登録画面表示(一般ユーザー）
        Fortify::registerView(function(){
            return view('auth.register');
        });
        //ログイン画面の表示(一般ユーザーと管理者の出し分け)
        Fortify::loginView(function(){
            if(request()->is('admin/*') || request()->routeIs('admin.*')){
                return view('admin.auth.login');
            }
            return view('auth.login');
        });
        //メール認証誘導画面
        Fortify::verifyEmailView(function(){
            return view('auth.verify-email');
        });

        app()->instance(LogoutResponse::class,new class implements LogoutResponse{
            public function toResponse($request)
            {
                return  $request->is('admin/*') || $request->is('admin')
                    ? redirect('/admin/login')
                    : redirect('/login');
            }
        });
        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())).'|'.$request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });
    }
}
