<?php

namespace App\Http\Middleware;

use App\Models\Admin;
use Closure;
use Google\Service\CCAIPlatform\AdminUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class CheckLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {

      if (Session::has('username') && Session::has('userpassword') && Session::has('user_type')) {
            $adminUser = Admin::where('admin_username',Session::get('username'))->first();

            if(decrypt($adminUser->admin_password) == Session::get('userpassword')){
                 // Proceed only after successful auth
                $response = $next($request);

                // Set headers to prevent caching
                $response->headers->set('Cache-Control', 'nocache, no-store, max-age=0, must-revalidate');
                $response->headers->set('Pragma', 'no-cache');
                $response->headers->set('Expires', 'Sun, 02 Jan 2021 00:00:00 GMT');
                return $response;
            }else{
                session()->pull('username');
                session()->pull('user_type');
                session()->pull('userpassword');
                 return redirect(url('/'));
            }
        } else {
            session()->pull('username');
            session()->pull('user_type');
            session()->pull('userpassword');
            return redirect(url('/'));
        }
    }
}
