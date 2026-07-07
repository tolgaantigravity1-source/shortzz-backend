<?php

namespace App\Http\Controllers;
use App\Models\Admin;
use App\Models\GlobalFunction;
use App\Models\GlobalSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

use function Psy\debug;

class LoginController extends Controller
{
    public function forgotPasswordForm(Request $request)
    {
        $request->validate([
            'new_password' => 'required',
        ]);

        $databaseUsername = env('DB_USERNAME');
        $databasePassword = env('DB_PASSWORD');

        if ($request->database_username == $databaseUsername && $request->database_password == $databasePassword) {
            $encryptedPassword = Crypt::encrypt($request->new_password);

            $admin = Admin::where('admin_username', 'admin')->first();

            if (!$admin) {
                return GlobalFunction::sendSimpleResponse(false, 'Admin user not found.');
            }

            $admin->admin_password = $encryptedPassword;
            $admin->save();

            return GlobalFunction::sendSimpleResponse(true, 'Password updated successfully.');

        } else {
            return GlobalFunction::sendSimpleResponse(false, 'Wrong credentials.');
        }
    }

    function login()
    {
        $setting = GlobalSettings::first();
        if ($setting) {
            Session::put('app_name', $setting->app_name);
        }
        Artisan::call('storage:link');
        if (Session::get('username') && Session::get('userpassword') && Session::get('user_type')) {
            $adminUser = Admin::where('admin_username', Session::get('username'))->first();
            if (decrypt($adminUser->admin_password) == Session::get('userpassword')) {
                return redirect('dashboard');
            }
        }
        return view('login');
    }

    function checkLogin(Request $request)
    {
        $data = Admin::where('admin_username', $request->username)->first();

        if ($data && $request->username == $data['admin_username'] && $request->password == decrypt($data->admin_password)) {
            $request->session()->put('username', $data['admin_username']);
            $request->session()->put('userpassword', $request->password);
            $request->session()->put('user_type', $data['user_type']);

            return response()->json([
                'status' => true,
                'message' => 'Login Successfully',
                'data' => $data,
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Wrong credentials!',
            ]);
        }
    }

    function logout()
    {
        session()->pull('username');
        session()->pull('user_type');
        session()->pull('userpassword');
        return redirect(url('/'));
    }
}
