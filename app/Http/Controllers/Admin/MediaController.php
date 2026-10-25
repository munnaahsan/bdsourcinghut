<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\{Collection,Media,Page,PageSection,Service,SiteSetting};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\File;
class MediaController extends Controller {
 public function index(Request $request){
  $q=trim((string)$request->query('q',''));$category=$request->query('category');
  $media=Media::when($q,fn($query)=>$query->where(fn($sub)=>$sub->where('file_name','like','%'.$q.'%')->orWhere('alt_text','like','%'.$q.'%')))
   ->when($category,fn($query)=>$query->where('category',$category))->latest()->paginate(24)->withQueryString();
  $categories=Media::distinct()->orderBy('category')->pluck('category');
  return view('admin.media.index',compact('media','categories','category','q'));
 }
 public function store(Request $request){
  $data=$request->validate(['images'=>'required|array|min:1|max:15','images.*'=>['required',File::types(['jpg','jpeg','png','webp'])->max('10mb')], 'category'=>'required|string|max:64','alt_text'=>'nullable|string|max:180']);
  foreach($request->file('images') as $image)$this->saveImage($image,$data['category'],$data['alt_text']??null);
  return back()->with('status','Images uploaded.');
 }
 public function update(Request $request,Media $medium){
  $data=$request->validate(['alt_text'=>'nullable|string|max:180','category'=>'required|string|max:64','replacement'=>['nullable',File::types(['jpg','jpeg','png','webp'])->max('10mb')]]);
  $oldDisk=$medium->disk;$oldPath=$medium->path;
  if($request->hasFile('replacement')){
   $new=$this->saveImage($request->file('replacement'),$data['category'],$data['alt_text']??null);
   $medium->update(['file_name'=>$new->file_name,'disk'=>$new->disk,'path'=>$new->path,'mime'=>$new->mime,'width'=>$new->width,'height'=>$new->height,'bytes'=>$new->bytes]);$new->delete();
   if($oldDisk!=='assets')Storage::disk($oldDisk)->delete($oldPath);
  }
  $medium->update(['alt_text'=>$data['alt_text']??null,'category'=>$data['category']]);
  return back()->with('status','Media updated.');
 }
 public function destroy(Media $medium){
  $id=$medium->id;
  $inUse=Page::where('hero_image_id',$id)->exists() || PageSection::where('image_id',$id)->orWhere('secondary_image_id',$id)->orWhere('mobile_image_id',$id)->exists()
   || Service::where('hero_image_id',$id)->orWhere('main_image_id',$id)->exists()||Collection::where('main_image_id',$id)->exists()
   || \Illuminate\Support\Facades\DB::table('collection_images')->where('media_id',$id)->exists()
   || SiteSetting::whereIn('key',['logo_media_id','favicon_media_id'])->where('value',(string)$id)->exists();
  if($inUse)return back()->withErrors(['general'=>'This image is used in published or editable content. Remove its references first.']);
  $disk=$medium->disk;$path=$medium->path;$medium->delete();if($disk!=='assets')Storage::disk($disk)->delete($path);
  return back()->with('status','Unused media removed.');
 }
 private function saveImage($upload,string $category,?string $alt):Media {
  $path=$upload->store('media/'.date('Y/m'),'public');
  $size=@getimagesize($upload->getRealPath());
  return Media::create(['file_name'=>basename($path),'disk'=>'public','path'=>$path,'category'=>$category,
   'alt_text'=>$alt ?: pathinfo($upload->getClientOriginalName(),PATHINFO_FILENAME),
   'mime'=>$upload->getMimeType(),'width'=>$size[0]??null,'height'=>$size[1]??null,'bytes'=>$upload->getSize()]);
 }
}
