<?php

namespace App\Http\Middleware;

use Closure;

use Illuminate\Support\Facades\Redirect;
use App\Menuprivilleges;
use App\Menu;
use Auth;
use Illuminate\Support\Facades\Route;

class Privilleges
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $name = (Route::currentRouteName());
        $prod = false;
        if($name != 'dashboard' && $prod)
        {
            $arname = explode('.', $name);

            $menu = Menu::where("route", $name)->orWhere('route', $arname[0])->first();
            if(!$menu)
                return redirect('/')->with('error', 'Menu yang anda akses tidak ada');

            $priv = Menuprivilleges::where('menu_id', $menu->id)->where('usertype_id', Auth::user()->usertype_id)->first();
            if(!$priv)
                return redirect('/')->with('error', "Anda tidak memiliki hak akses untuk menu ini");
        }

        return $next($request);
    }
}
