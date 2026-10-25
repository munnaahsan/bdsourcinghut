<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
class UserController extends Controller {
 public function index(){return view('admin.users.index',['users'=>User::orderBy('id')->paginate(20)]);}
 public function store(Request $request){$data=$request->validate(['name'=>'required|string|max:120','email'=>'required|email|max:255|unique:users,email','password'=>'required|string|min:12|confirmed']);User::create($data+['is_admin'=>true]);return back()->with('status','Administrator added.');}
 public function update(Request $request,User $user){$data=$request->validate(['name'=>'required|string|max:120','email'=>['required','email','max:255',Rule::unique('users')->ignore($user->id)],'password'=>'nullable|string|min:12|confirmed']);if(empty($data['password']))unset($data['password']);$user->update($data);return back()->with('status','Administrator updated.');}
 public function destroy(User $user){
  if($user->id===auth()->id())throw ValidationException::withMessages(['general'=>'You cannot delete your own administrator account.']);
  if($user->is_admin&&User::where('is_admin',true)->count()<=1)throw ValidationException::withMessages(['general'=>'Cannot delete the final administrator.']);
  $user->delete();return back()->with('status','User deleted.');
 }
}
