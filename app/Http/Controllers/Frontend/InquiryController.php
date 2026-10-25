<?php
namespace App\Http\Controllers\Frontend;
use App\Http\Controllers\Controller;
use App\Models\{Inquiry,SiteSetting};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Log,Mail};
class InquiryController extends Controller {
 public function store(Request $request){
  $data=$request->validate([
   'full_name'=>'required|string|max:160','company_name'=>'required|string|max:180','email'=>'required|email:rfc|max:255',
   'phone'=>'nullable|string|max:50','country'=>'required|string|max:100','service_interest'=>'nullable|string|max:160',
   'product_category'=>'nullable|string|max:160','quantity'=>'nullable|string|max:100','message'=>'required|string|min:10|max:6000',
   'company_website'=>'nullable|string|max:0'
  ]);
  unset($data['company_website']);
  $inquiry=Inquiry::create($data);
  $destination=config('bdsourcinghut.inquiry_email');
  if($destination && filter_var($destination,FILTER_VALIDATE_EMAIL)) {
   try {Mail::raw('New inquiry #'.$inquiry->id.' from '.$inquiry->full_name.' ('.$inquiry->company_name.'). Sign in to the admin dashboard to review.',fn($message)=>$message->to($destination)->subject('New BD SOURCING HUT inquiry #'.$inquiry->id));}
   catch(\Throwable $e){Log::warning('Inquiry email failed',['inquiry_id'=>$inquiry->id,'exception'=>$e->getMessage()]);}
  }
  return redirect()->route('contact')->with('inquiry_success',SiteSetting::valueOf('inquiry_success_message','Your inquiry was received.'));
 }
}
