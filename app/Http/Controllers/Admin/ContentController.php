<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\{Collection,Media,Page,Service};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
class ContentController extends Controller {
 private const MODELS=['pages'=>Page::class,'services'=>Service::class,'collections'=>Collection::class];
 private function model(string $type):string {abort_unless(isset(self::MODELS[$type]),404);return self::MODELS[$type];}
 public function index(string $type,Request $request){
  $model=$this->model($type); $q=trim((string)$request->query('q',''));
  $items=$model::query()->when($q,fn($query)=>$query->where(fn($sub)=>$sub->where('title','like','%'.$q.'%')->orWhere('slug','like','%'.$q.'%')))->orderBy('sort_order')->latest('id')->paginate(15)->withQueryString();
  return view('admin.content.index',compact('type','items','q'));
 }
 public function create(string $type){$this->model($type);$item=null;$media=Media::latest()->get();return view('admin.content.form',compact('type','item','media'));}
 public function edit(string $type,int $id){$model=$this->model($type);$item=$model::findOrFail($id);$item->loadMissing($type==='collections'?['images']:[]);$media=Media::latest()->get();return view('admin.content.form',compact('type','item','media'));}
 public function store(string $type,Request $request){$model=$this->model($type);$data=$this->validated($type,$request,null);$record=DB::transaction(function()use($model,$type,$data,$request){
   $record=$model::create($data);if($type==='collections')$this->syncGallery($record,$request);return $record;
  });
  return redirect()->route('admin.content.edit',['type'=>$type,'id'=>$record->id])->with('status','Created successfully.');
 }
 public function update(string $type,int $id,Request $request){$model=$this->model($type);$item=$model::findOrFail($id);$data=$this->validated($type,$request,$item);
  if($type==='pages' && in_array($item->slug,['home','about','contact','services-overview','collections-overview'],true)) $data['slug']=$item->slug;
  DB::transaction(function()use($item,$type,$data,$request){$item->update($data);if($type==='collections')$this->syncGallery($item,$request);});
  return back()->with('status','Changes saved.');
 }
 public function destroy(string $type,int $id){$model=$this->model($type);$item=$model::findOrFail($id);
  if($type==='pages' && in_array($item->slug,['home','about','contact','services-overview','collections-overview'],true))return back()->withErrors(['general'=>'Core pages cannot be deleted. Unpublish them if necessary.']);
  $item->delete();return redirect()->route('admin.content.index',$type)->with('status','Item removed.');
 }
 private function validated(string $type,Request $request,$item):array {
  $rules=[
   'title'=>'required|string|max:180','slug'=>['required','string','alpha_dash','max:180',Rule::unique($type,'slug')->ignore($item?->id)],
   'is_published'=>'nullable|boolean','sort_order'=>'nullable|integer|min:0|max:99999',
   'seo_title'=>'nullable|string|max:180','seo_description'=>'nullable|string|max:500'
  ];
  if($type==='pages') $rules += ['eyebrow'=>'nullable|string|max:180','excerpt'=>'nullable|string|max:1500','body'=>'nullable|string|max:30000','hero_image_id'=>'nullable|exists:media,id'];
  if($type==='services')$rules += ['short_description'=>'nullable|string|max:1500','body'=>'nullable|string|max:30000','capabilities_text'=>'nullable|string|max:6000','steps_text'=>'nullable|string|max:6000','main_image_id'=>'nullable|exists:media,id','hero_image_id'=>'nullable|exists:media,id','is_featured'=>'nullable|boolean'];
  if($type==='collections')$rules += ['short_description'=>'nullable|string|max:1500','body'=>'nullable|string|max:30000','fabric_information'=>'nullable|string|max:4000','fit_information'=>'nullable|string|max:4000','production_information'=>'nullable|string|max:4000','main_image_id'=>'nullable|exists:media,id','is_featured'=>'nullable|boolean','gallery_ids'=>'nullable|array|max:40','gallery_ids.*'=>'integer|distinct|exists:media,id','gallery_order'=>'nullable|string|max:1000'];
  $data=$request->validate($rules);
  $data['slug']=Str::slug($data['slug']);$data['is_published']=$request->boolean('is_published');$data['sort_order']=(int)($data['sort_order']??0);
  if($type!=='pages')$data['is_featured']=$request->boolean('is_featured');
  if($type==='services'){
   $data['capabilities']=array_values(array_filter(array_map('trim',preg_split('/\r?\n/', $data['capabilities_text']??''))));
   $data['steps']=[];
   foreach(preg_split('/\r?\n/',$data['steps_text']??'') as $line){if(!trim($line))continue;$parts=explode('|',$line,2);$data['steps'][]=['title'=>trim($parts[0]),'description'=>trim($parts[1]??'')];}
   unset($data['capabilities_text'],$data['steps_text']);
  }
  unset($data['gallery_ids'],$data['gallery_order']);
  return $data;
 }
 private function syncGallery(Collection $collection,Request $request):void {
  $selected=array_map('intval',$request->input('gallery_ids',[]));
  $order=array_map('intval',array_filter(explode(',',(string)$request->input('gallery_order',''))));
  $ordered=array_values(array_unique(array_merge(array_values(array_intersect($order,$selected)),$selected)));
  $sync=[];foreach($ordered as $i=>$id)$sync[$id]=['sort_order'=>$i];
  $collection->images()->sync($sync);
 }
}
