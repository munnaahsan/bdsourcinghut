<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\{SiteSetting,ThemeSetting};
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
class SettingController extends Controller {
 public function footer(){return redirect()->route('admin.settings.index',['group'=>'footer']);}
 public function seo(){return redirect()->route('admin.settings.index',['group'=>'seo']);}
 public function index(Request $request){$groups=SiteSetting::select('group_name')->distinct()->orderBy('group_name')->pluck('group_name');$group=$request->query('group','general');abort_unless($groups->contains($group),404);$settings=SiteSetting::where('group_name',$group)->orderBy('key')->get();return view('admin.settings',compact('group','groups','settings'));}
 public function update(Request $request){
  $data=$request->validate(['group'=>'required|string|max:40','settings'=>'required|array','settings.*'=>'nullable|string|max:1500']);
  $rows=SiteSetting::where('group_name',$data['group'])->get()->keyBy('key');abort_if($rows->isEmpty(),404);
  foreach($request->input('settings',[]) as $key=>$value){
   abort_unless($rows->has($key),422);
   if(in_array($key,['contact_email'],true)&&$value&&!filter_var($value,FILTER_VALIDATE_EMAIL))throw ValidationException::withMessages(['settings.'.$key=>'Invalid email.']);
   if(str_starts_with($key,'social_')&&$value&&(!filter_var($value,FILTER_VALIDATE_URL)||!str_starts_with($value,'https://')))throw ValidationException::withMessages(['settings.'.$key=>'Use an HTTPS URL.']);
   if(in_array($key,['header_cta_url'],true)&&$value&&!str_starts_with($value,'/')&&!filter_var($value,FILTER_VALIDATE_URL))throw ValidationException::withMessages(['settings.'.$key=>'Invalid URL.']);
   if(in_array($key,['logo_media_id','favicon_media_id'],true)&&$value&&!\App\Models\Media::whereKey($value)->exists())throw ValidationException::withMessages(['settings.'.$key=>'Choose an existing media ID.']);
   $rows[$key]->update(['value'=>$value??'']);
  }
  return back()->with('status','Site settings updated.');
 }
 public function theme(){return view('admin.theme',['settings'=>ThemeSetting::orderBy('key')->get()->keyBy('key')]);}
 public function saveTheme(Request $request){
  $colorFields=['primary','secondary','accent','background','surface','light','text','muted','button'];$rules=[];
  foreach($colorFields as $key)$rules[$key]='required|regex:/^#[a-fA-F0-9]{6}$/';
  $rules['heading_font']='required|in:Cormorant Garamond,Playfair Display,DM Serif Display';
  $rules['body_font']='required|in:Manrope,Inter,DM Sans';
  $rules['radius']='required|integer|between:0,40';$rules['shadow_intensity']='required|integer|between:0,100';$rules['animation_intensity']='required|integer|between:0,100';
  $data=$request->validate($rules);
  if($this->contrast($data['text'],$data['background'])<4.5||$this->contrast($data['text'],$data['surface'])<4.5)throw ValidationException::withMessages(['text'=>'Insufficient text contrast on one of the dark surfaces (minimum 4.5:1).']);
  if($this->contrast($data['secondary'],$data['light'])<4.5)throw ValidationException::withMessages(['light'=>'Dark headings need stronger contrast on ivory surfaces.']);
  if($this->contrast($data['secondary'],$data['button'])<4.5)throw ValidationException::withMessages(['button'=>'The button must contrast with the dark button label by at least 4.5:1.']);
  foreach($data as $key=>$value)ThemeSetting::updateOrCreate(['key'=>$key],['value'=>(string)$value]);
  return back()->with('status','Theme saved and applied to the website.');
 }
 private function contrast(string $a,string $b):float{return (max($this->luminance($a),$this->luminance($b))+.05)/(min($this->luminance($a),$this->luminance($b))+.05);}
 private function luminance(string $hex):float{
  $channels=array_map('hexdec',str_split(substr($hex,1),2));$linear=[];
  foreach($channels as $ch){$x=$ch/255;$linear[]=$x<=.04045?$x/12.92:(($x+.055)/1.055)**2.4;}
  return $linear[0]*.2126+$linear[1]*.7152+$linear[2]*.0722;
 }
}
