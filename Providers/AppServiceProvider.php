<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\view;

use Illuminate\Support\Facades\DB;
use App\User;
use App\UserDts;
use Auth;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
                Schema::defaultStringLength(200); 



//view::share('user',$User);

view::composer('*',function($view){




if ($view->getName() != 'layouts.Nav') {
        
    }else{

$ref=Auth::user()->Reference;

$User = DB::table('user_dts')
            ->join('users', 'users.Reference', '=', 'user_dts.user_Ref')
            ->select('photo','user_dts.user_Ref')
            ->where('user_dts.user_Ref',$ref)
            ->get();


$view->with('user',$User);

    }


    
});







    }
}
