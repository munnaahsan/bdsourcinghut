<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\{Collection,Inquiry,Media,Page,Service};
class DashboardController extends Controller {
 public function __invoke(){
  $stats=['Published pages'=>Page::where('is_published',true)->count(),'Services'=>Service::count(),'Collections'=>Collection::count(),'Media assets'=>Media::count(),'Total inquiries'=>Inquiry::count(),'New inquiries'=>Inquiry::where('status','new')->count()];
  $inquiries=Inquiry::latest()->limit(7)->get();
  return view('admin.dashboard',compact('stats','inquiries'));
 }
}
