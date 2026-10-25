<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\{Media,Page,PageSection};
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
class SectionController extends Controller {
 public function homepage(){return redirect()->route('admin.sections.index','home');}
 public function index(string $slug){
  $page=Page::where('slug',$slug)->with('sections')->firstOrFail();$media=Media::latest()->get();
  return view('admin.sections.index',compact('page','media'));
 }
 public function create(string $slug){$page=Page::where('slug',$slug)->firstOrFail();$section=null;$media=Media::latest()->get();return view('admin.sections.form',compact('page','section','media'));}
 public function edit(PageSection $section){$page=$section->page;$media=Media::latest()->get();return view('admin.sections.form',compact('page','section','media'));}
 public function store(string $slug,Request $request){$page=Page::where('slug',$slug)->firstOrFail();$data=$this->check($request,$page);$page->sections()->create($data);return redirect()->route('admin.sections.index',$slug)->with('status','Section added.');}
 public function update(PageSection $section,Request $request){$section->update($this->check($request,$section->page,$section));return redirect()->route('admin.sections.index',$section->page->slug)->with('status','Section updated.');}
 public function destroy(PageSection $section){$slug=$section->page->slug;$section->delete();return redirect()->route('admin.sections.index',$slug)->with('status','Section deleted.');}
 public function preview(PageSection $section){
  $page=$section->page;$services=\App\Models\Service::where('is_published',true)->with('mainImage')->orderBy('sort_order')->get();
  $collections=\App\Models\Collection::where('is_published',true)->with('mainImage')->orderBy('sort_order')->get();
  $steps=\App\Models\ProcessStep::where('is_visible',true)->orderBy('sort_order')->get();
  $featured=$collections->first();$section->load('image','secondaryImage','mobileImage');
  return view('frontend.pages.preview',compact('page','section','services','collections','steps','featured'));
 }
 private function check(Request $request,Page $page,?PageSection $section=null):array {
  $data=$request->validate([
   'section_key'=>['required','string','alpha_dash','max:80',Rule::unique('page_sections','section_key')->where('page_id',$page->id)->ignore($section?->id)],
   'layout'=>'required|in:hero,statement,about,services,collections,production,featured,cta,editorial,editorial-reverse',
   'eyebrow'=>'nullable|string|max:180','heading'=>'nullable|string|max:1000','body'=>'nullable|string|max:10000',
   'cta_label'=>'nullable|string|max:120','cta_url'=>['nullable','string','max:300',function($attribute,$value,$fail){if(!str_starts_with($value,'/')&&!filter_var($value,FILTER_VALIDATE_URL))$fail('Use a relative path or a valid URL.');}],
   'background_color'=>'nullable|regex:/^#[0-9a-fA-F]{6}$/','image_id'=>'nullable|exists:media,id',
   'secondary_image_id'=>'nullable|exists:media,id','mobile_image_id'=>'nullable|exists:media,id', 'image_position'=>'required|in:center,left,right,top,bottom', 'overlay_opacity'=>'required|numeric|between:0,1','sort_order'=>'required|integer|min:0|max:9999', 'is_visible'=>'nullable|boolean'
  ]);
  $data['is_visible']=$request->boolean('is_visible');return $data;
 }
}
