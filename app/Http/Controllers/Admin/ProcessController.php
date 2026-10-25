<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\ProcessStep;
use Illuminate\Http\Request;
class ProcessController extends Controller {
 public function index(){return view('admin.process', ['steps'=>ProcessStep::orderBy('sort_order')->get()]);}
 public function store(Request $request){$data=$request->validate(['title'=>'required|string|max:120','description'=>'nullable|string|max:1000','sort_order'=>'required|integer|min:0','is_visible'=>'nullable|boolean']);$data['is_visible']=$request->boolean('is_visible');ProcessStep::create($data);return back()->with('status','Step added.');}
 public function update(Request $request,ProcessStep $step){$data=$request->validate(['title'=>'required|string|max:120','description'=>'nullable|string|max:1000','sort_order'=>'required|integer|min:0','is_visible'=>'nullable|boolean']);$data['is_visible']=$request->boolean('is_visible');$step->update($data);return back()->with('status','Step saved.');}
 public function destroy(ProcessStep $step){$step->delete();return back()->with('status','Step removed.');}
}
