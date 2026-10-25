<?php

use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

Artisan::command('app:create-admin', function () {
    $name = $this->ask('Administrator name');
    $email = $this->ask('Administrator email');
    $password = $this->secret('Strong password (12+ characters)');
    $data = compact('name','email','password');
    $validator = Validator::make($data, [
        'name' => 'required|string|max:120',
        'email' => 'required|email|max:255|unique:users,email',
        'password' => 'required|string|min:12',
    ]);
    if ($validator->fails()) { $this->error($validator->errors()->first()); return 1; }
    User::create(['name'=>$name,'email'=>$email,'password'=>Hash::make($password),'is_admin'=>true]);
    $this->info('Administrator created. Visit /admin/login.');
    return 0;
})->purpose('Interactively create an administrator without a hardcoded password');
