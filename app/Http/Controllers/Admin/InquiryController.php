<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use Illuminate\Http\Request;
class InquiryController extends Controller {
 public function index(Request $request){
  $q=trim((string)$request->query('q',''));$status=$request->query('status');
  if($status)abort_unless(in_array($status,['new','in_progress','completed'],true),422);
  $inquiries=Inquiry::when($q,fn($query)=>$query->where(fn($sub)=>$sub->where('full_name','like','%'.$q.'%')->orWhere('company_name','like','%'.$q.'%')->orWhere('email','like','%'.$q.'%')))
   ->when($status,fn($query)=>$query->where('status',$status))->latest()->paginate(20)->withQueryString();
  return view('admin.inquiries.index',compact('inquiries','q','status'));
 }
 public function show(Inquiry $inquiry){return view('admin.inquiries.show',compact('inquiry'));}
 public function update(Request $request,Inquiry $inquiry){$data=$request->validate(['status'=>'required|in:new,in_progress,completed','internal_notes'=>'nullable|string|max:20000']);$inquiry->update($data);return back()->with('status','Inquiry updated.');}
 public function export(Request $request){
  $status=$request->query('status');if($status)abort_unless(in_array($status,['new','in_progress','completed'],true),422);
  $filename='inquiries-'.date('Y-m-d').'.csv';
  return response()->streamDownload(function()use($status){
   $out=fopen('php://output','w');fputcsv($out,['ID','Date','Name','Company','Email','Phone','Country','Service','Category','Quantity','Status','Message']);
   Inquiry::when($status,fn($q)=>$q->where('status',$status))->orderBy('id')->chunk(200,function($items)use($out){foreach($items as $item){
    $values=[$item->id,$item->created_at?->toDateTimeString(),$item->full_name,$item->company_name,$item->email,$item->phone,$item->country,$item->service_interest,$item->product_category,$item->quantity,$item->status,$item->message];
    fputcsv($out,array_map(function($value){$text=(string)$value;return preg_match('/^[\s\t\r\n]*[=+@\-]/',$text)?"'".$text:$text;},$values));
   }});fclose($out);
  },$filename,['Content-Type'=>'text/csv; charset=UTF-8','X-Content-Type-Options'=>'nosniff']);
 }
}
