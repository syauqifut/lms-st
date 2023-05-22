<?php

namespace App\Providers;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\UrlWindow;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;
use League\Glide\Server;

use Route;


use App\Menuprivilleges;
use App\Menu;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */

    public function listMenus( $usertype_id )
    {
        $listRoleMenus     = Menuprivilleges::select('menus.id','menus.name', 'menus.icon', 'menus.route')
        ->where('menus.isactive','1')       
        ->where('menus.parentId','0')       
        ->where('menuprivilleges.usertype_id', $usertype_id) 
        ->join('menus', 'menus.id', '=', 'menuprivilleges.menu_id')
        ->orderBy('menus.order_menu')
        ->get();

        $dataMenu = array();
        foreach($listRoleMenus as $listRoleMenu){

            $firstRoleSubMenu     = Menuprivilleges::select('menus.route')
            ->where('menus.isactive','1')       
            ->where('menus.parentId',  $listRoleMenu->id)       
            ->where('menuprivilleges.usertype_id', $usertype_id) 
            ->join('menus', 'menus.id', '=', 'menuprivilleges.menu_id')
            ->orderBy('menus.order_menu')
            ->first();            

            $data['id']         = $listRoleMenu->id;
            $data['name']       = $listRoleMenu->name;
            $data['icon']       = $listRoleMenu->icon;

            if($firstRoleSubMenu){
                $data['route']      = $firstRoleSubMenu->route;
            }
            else{
                $data['route']      = $listRoleMenu->route;
            }
            

            

            array_push($dataMenu, $data);
        }
  
        
        return  $dataMenu;
    }

    public function subMenu( $route , $usertype_id)
    {

        $dataMenuByRoute     = Menu::select('menus.id','menus.parentId' ,'menus.name', 'menus.icon', 'menus.route')
        ->where('menus.isactive','1')       
        ->where('menus.route', $route)       
        ->first();

        //return  $dataMenuByRoute->parentId;

        if($dataMenuByRoute){

            if($dataMenuByRoute->parentId == 0){
                $listRoleSubMenu     = Menuprivilleges::addSelect('menus.id')
                
                //->addSelect(REPLACE('menus.route','.index',''))
                ->addSelect('menus.name')
                ->addSelect('menus.route')
                ->addSelect('menus.icon')

                ->where('menus.isactive','1')       
                ->where('menus.parentId', $dataMenuByRoute->id)       
                ->where('menuprivilleges.usertype_id', $usertype_id) 
                ->join('menus', 'menus.id', '=', 'menuprivilleges.menu_id')
                ->orderBy('menus.order_menu')
                
                ->get();


                if(count($listRoleSubMenu) > 1){
                    return  $listRoleSubMenu;
                }
                else{
                    return  null;
                }
               
            }
            else{
                $listRoleSubMenu     = Menuprivilleges::addSelect('menus.id')
               
                ->addSelect('menus.name')
                ->addSelect('menus.route')
                ->addSelect('menus.icon')
                ->where('menus.isactive','1')       
                ->where('menus.parentId', $dataMenuByRoute->parentId)       
                ->where('menuprivilleges.usertype_id', $usertype_id) 
                ->join('menus', 'menus.id', '=', 'menuprivilleges.menu_id')
                ->orderBy('menus.order_menu')
                ->get();

                if(count($listRoleSubMenu) > 1){
                    return  $listRoleSubMenu;
                }
                else{
                    return  null;
                }
            }
            

        }
        else{
            return null;
            
        }
       
    }

    public function register()
    {
        $this->registerInertia();
        $this->registerGlide();
        $this->registerLengthAwarePaginator();
    }

    public function registerInertia()
    {
        Inertia::version(function () {
            return md5_file(public_path('mix-manifest.json'));
        });


       
        //Auth::user() ?  $usertype_id = Auth::user()->usertype_,id : $usertype_id =null;

        Inertia::share([
            'auth' => function () {
                return [
                    'user' => Auth::user() ? [
                        'id' => Auth::user()->id,
                        'usertype_id' => Auth::user()->usertype_id,
                        'first_name' => Auth::user()->first_name,
                        'last_name' => Auth::user()->last_name,
                        'email' => Auth::user()->email,
                        'email' => Auth::user()->email,
                        'role' => Auth::user()->role,
                        'account' => [
                            'id' => Auth::user()->account->id,
                            'name' => Auth::user()->account->name,
                        ],
                        
                    ] : null,
                    'listMenuByRole' => $this->listMenus(  
                        Auth::user() ?  $usertype_id = Auth::user()->usertype_id : $usertype_id = null ),
                    'listSubMenuByRole' => $this->subMenu(Route::current()->getName(), Auth::user() ?  $usertype_id = Auth::user()->usertype_id : $usertype_id = null),
                ];
            },
            'flash' => function () {
                return [
                    'success' => Session::get('success'),
                    'error' => Session::get('error'),
                ];
            },
            'errors' => function () {
                return Session::get('errors')
                    ? Session::get('errors')->getBag('default')->getMessages()
                    : (object) [];
            },
        ]);
    }

    protected function registerGlide()
    {
        $this->app->bind(Server::class, function ($app) {
            return Server::create([
                'source' => Storage::getDriver(),
                'cache' => Storage::getDriver(),
                'cache_folder' => '.glide-cache',
                'base_url' => 'img',
            ]);
        });
    }

    protected function registerLengthAwarePaginator()
    {
        $this->app->bind(LengthAwarePaginator::class, function ($app, $values) {
            return new class(...array_values($values)) extends LengthAwarePaginator {
                public function only(...$attributes)
                {
                    return $this->transform(function ($item) use ($attributes) {
                        return $item->only($attributes);
                    });
                }

                public function transform($callback)
                {
                    $this->items->transform($callback);

                    return $this;
                }

                public function toArray()
                {
                    return [
                        'data' => $this->items->toArray(),
                        'links' => $this->links(),
                    ];
                }

                public function links($view = null, $data = [])
                {
                    $this->appends(Request::all());

                    $window = UrlWindow::make($this);

                    $elements = array_filter([
                        $window['first'],
                        is_array($window['slider']) ? '...' : null,
                        $window['slider'],
                        is_array($window['last']) ? '...' : null,
                        $window['last'],
                    ]);

                    return Collection::make($elements)->flatMap(function ($item) {
                        if (is_array($item)) {
                            return Collection::make($item)->map(function ($url, $page) {
                                return [
                                    'url' => $url,
                                    'label' => $page,
                                    'active' => $this->currentPage() === $page,
                                ];
                            });
                        } else {
                            return [
                                [
                                    'url' => null,
                                    'label' => '...',
                                    'active' => false,
                                ],
                            ];
                        }
                    })->prepend([
                        'url' => $this->previousPageUrl(),
                        'label' => 'Previous',
                        'active' => false,
                    ])->push([
                        'url' => $this->nextPageUrl(),
                        'label' => 'Next',
                        'active' => false,
                    ]);
                }
            };
        });
    }
}
