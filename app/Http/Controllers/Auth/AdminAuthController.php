<?php
namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth,Hash,Password,RateLimiter};
use Illuminate\Validation\ValidationException;
class AdminAuthController extends Controller {
 public function form(){return view('admin.auth.login');}
 public function login(Request $request){
  $data=$request->validate(['email'=>['required','email'],'password'=>['required','string']]);
  $key=strtolower($data['email']).'|'.$request->ip();
  if(RateLimiter::tooManyAttempts($key,5)) throw ValidationException::withMessages(['email'=>'Too many attempts. Please try again later.']);
  if(!Auth::attempt($data,$request->boolean('remember')) || !Auth::user()->is_admin){
   Auth::logout();RateLimiter::hit($key,60);throw ValidationException::withMessages(['email'=>'These credentials cannot access the dashboard.']);
  }
  RateLimiter::clear($key);$request->session()->regenerate();return redirect()->intended(route('admin.dashboard'));
 }
 public function logout(Request $request){Auth::logout();$request->session()->invalidate();$request->session()->regenerateToken();return redirect()->route('admin.login');}
 public function forgot(){return view('admin.auth.forgot');}
 public function sendReset(Request $request){
  $request->validate(['email'=>'required|email']);
  $email=$request->string('email')->toString();
  $user=User::where('email',$email)->where('is_admin',true)->first();
  if($user) Password::broker()->sendResetLink(['email'=>$email]);
  return back()->with('status','If an eligible administrator account exists, reset instructions have been sent.');
 }
 public function resetForm(string $token,Request $request){return view('admin.auth.reset',['token'=>$token,'email'=>$request->query('email','')]);}
 public function reset(Request $request){
  $request->validate(['token'=>'required','email'=>'required|email','password'=>'required|string|min:12|confirmed']);
  $user=User::where('email',$request->email)->where('is_admin',true)->first();
  if(!$user) return back()->withErrors(['email'=>'Unable to reset these credentials.']);
  $status=Password::reset($request->only('email','password','password_confirmation','token'),function(User $user,string $password){
   $user->forceFill(['password'=>Hash::make($password),'remember_token'=>\Illuminate\Support\Str::random(60)])->save();
  });
  return $status===Password::PASSWORD_RESET ? redirect()->route('admin.login')->with('status',__($status)) : back()->withErrors(['email'=>__($status)]);
 }
}
