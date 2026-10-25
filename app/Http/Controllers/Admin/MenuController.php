<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\{Menu,MenuItem};
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
class MenuController extends Controller {
 public function index(Request $request){
  $menus=Menu::orderBy('id')->get();$slug=$request->query('menu','primary');$menu=Menu::where('slug',$slug)->firstOrFail();
  $items=$menu->allItems()->with('parent')->get();return view('admin.menus.index',compact('menus','menu','items'));
 }
 public function store(Request $request){$data=$this->validateItem($request);$menu=Menu::findOrFail($data['menu_id']);
  if($menu->slug==='primary' && empty($data['parent_id']) && $menu->items()->count()>=5)throw ValidationException::withMessages(['parent_id'=>'The five-item primary navigation is full. Add a dropdown item instead.']);
  MenuItem::create($data);return back()->with('status','Menu item added.');}
 public function update(Request $request,MenuItem $item){$data=$this->validateItem($request);$menu=Menu::findOrFail($data['menu_id']);
  if($data['parent_id']??null){
   $ancestor=MenuItem::find($data['parent_id']);
   while($ancestor){if($ancestor->id===$item->id)throw ValidationException::withMessages(['parent_id'=>'A menu cannot be its own ancestor.']);$ancestor=$ancestor->parent;}
  }
  if($menu->slug==='primary' && empty($data['parent_id']) && ($item->parent_id!==null||$item->menu_id!==$menu->id) && $menu->items()->count()>=5)
   throw ValidationException::withMessages(['parent_id'=>'The primary menu supports only five top-level entries.']);
  $item->update($data);return back()->with('status','Menu item updated.');}
 public function destroy(MenuItem $item){$item->delete();return back()->with('status','Menu item removed.');}
 private function validateItem(Request $request):array{
  $data=$request->validate(['menu_id'=>'required|exists:menus,id','parent_id'=>'nullable|exists:menu_items,id','label'=>'required|string|max:90',
   'url'=>['required','string','max:500',function($attribute,$value,$fail){if(!str_starts_with($value,'/')&&!preg_match('/^https?:\/\/[^\s]+$/i',$value))$fail('Use a safe relative or HTTP(S) URL.');}],
   'target'=>'required|in:_self,_blank','sort_order'=>'required|integer|min:0|max:9999','is_visible'=>'nullable|boolean']);
  if(isset($data['parent_id'])){$parent=MenuItem::findOrFail($data['parent_id']);if($parent->menu_id!=(int)$data['menu_id'])throw ValidationException::withMessages(['parent_id'=>'Parent must belong to the selected menu.']);}
  $data['is_visible']=$request->boolean('is_visible');return $data;
 }
}
