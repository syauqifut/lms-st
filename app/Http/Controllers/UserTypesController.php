<?php

namespace App\Http\Controllers;

use App\Usertype;
use App\Menu;
use App\Menuprivilleges;
use Auth;
use Inertia\Inertia;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
// use Illuminate\Http\Request;

use DB;

class UserTypesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected $menuName = "User Type";
    
    

    public function index()
    {
        
        return Inertia::render('UserTypes/Index', [
            'filters'   => Request::all('search'),
            'usertypes'    => Usertype::filter(Request::only('search'))
            
                ->where('is_active', '1')
                ->paginate()                
                ->transform(function ($usertype) {
                    return [
                        'id' => $usertype->id,
                        'name' => $usertype->name
                    ];
                }),

            'menuName' =>  $this->menuName
        ]);

     
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        

        return Inertia::render('UserTypes/Create',[
            'menuName'  =>  $this->menuName,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        $arUserType = Request::validate([
            'name' => ['required', 'max:100']
        ]);
        
        $arUserType['is_active'] = 1;
        $arUserType['createdBy'] = Auth::id();
        $usertypes = Usertype::create($arUserType);

        //var_dump( $usertypes->id);exit();

        return Redirect::route('usertypes.edit', $usertypes->id)->with('success', $this->menuName.' Successfully added. Next, please select Accessible');

    }

    public function storeMenu(\Illuminate\Http\Request $request)
    {
       $checkMenu = Menuprivilleges::select('menuprivilleges.id','menus.parentId')
       ->where('menu_id', $request->menu_id)       
       ->where('usertype_id', $request->usertype_id)       
       ->join('menus', 'menus.id', '=', 'menuprivilleges.menu_id')
       ->first();
       

        if($checkMenu){

            
            $childMenus = Menu::select('id','name')
            ->where('parentId', $request->menu_id)       
            ->get();

            foreach( $childMenus as  $childMenu){
                Menuprivilleges::where('menu_id',$childMenu->id)
                ->where('usertype_id', $request->usertype_id)
                ->delete();

            }

            Menuprivilleges::where('id',$checkMenu->id)
            ->where('usertype_id', $request->usertype_id)
            ->delete();

            if( $checkMenu->parentId != '0'){

               // echo $checkMenu->parentId;
                $cekSubMenu     = Menuprivilleges::select('menuprivilleges.*')
                ->where('menus.isactive','1')       
                ->where('menus.parentId',  $checkMenu->parentId)       
                ->where('menuprivilleges.usertype_id', $request->usertype_id) 
                ->join('menus', 'menus.id', '=', 'menuprivilleges.menu_id')
                ->get();  

    
                if(count($cekSubMenu) < 1){
                    Menuprivilleges::where('menu_id',$checkMenu->parentId)
                    ->where('usertype_id', $request->usertype_id)
                    ->delete();

                   // echo "delete all";
                }
            }

        }
        else{           

            $arrMenu['menu_id']         = $request->menu_id;
            $arrMenu['usertype_id']     = $request->usertype_id;
            $arrMenu['created_by']      = Auth::id();
            Menuprivilleges::create($arrMenu);

            $childMenus = Menu::select('id','name')
            ->where('parentId', $request->menu_id)       
            ->get();

            foreach( $childMenus as  $childMenu){
                $arrMenu['menu_id']         = $childMenu->id;
                $arrMenu['usertype_id']     = $request->usertype_id;
                $arrMenu['created_by']      = Auth::id();
                Menuprivilleges::create($arrMenu);
            }
        }

        
          

       return Redirect::back();

    }

    public function edit( $id)
    {
       $rowData = Usertype::find($id);


        $menusQuery    =  Menu::addSelect(['menu_id' => 
        Menuprivilleges::select('id')
                ->whereColumn('menu_id', 'menus.id')
                ->where('usertype_id', $id)
            ->limit(1)
        ])->get();
        

        
        $array_with_elements = array();
    
        //while ($row = mysqli_fetch_array($sql ,MYSQLI_ASSOC)) { 
        foreach ($menusQuery as $menus) {
            $array_with_elements[$menus->parentId][] = $menus;
        }

        // starting with level 0
        $returnArray = $this->add_children($array_with_elements, 0);        

        //dd($returnArray->toArray());
        //exit();

        return Inertia::render('UserTypes/Edit', [            
            'usertype' => $rowData,
            'menuName' =>  $this->menuName,
            'listMenus'   => $returnArray
        ]);
    }

    function add_children($array_with_elements, $wp_level){

        $nested_array = array();
        foreach($array_with_elements[$wp_level] as $wp_post){
            $obj            = new \stdClass();
            $obj->name      = $wp_post['name'];
            $obj->id        = $wp_post['id'];
            $obj->menu_id   = $wp_post['menu_id'];
            // check if there are children for this item

            if(isset($array_with_elements[$wp_post->id])){
                $obj->children = $this->add_children($array_with_elements, $wp_post->id); 
                // and here we use this nested function again (and again)
            }
            $nested_array[] = $obj;
        }
        return $nested_array;
    }


    public function update(Usertype $usertypes)
    {
        
        $usertypes->update(
            array_merge(Request::validate([
                'name' => ['required', 'max:100']
            ]), 
            
            ['updatedBy' => Auth::id()])
        );
        //dd($usertypes);
        return Redirect::route('usertypes')->with('success', $this->menuName.' updated');

    }

    public function destroy(Usertype $usertypes)
    {
        $usertypes->is_active = 0;
        $usertypes->save();
        return Redirect::route('usertypes')->with('success', $this->menuName.' deleted');
    }

   
}
