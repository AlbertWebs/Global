<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TeacherAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->session()->has('teacher_logged_in') || !$request->session()->has('teacher_id')) {
            return redirect()->route('teacher.login')->with('error', 'Please login to access the teacher portal.');
        }

        return $next($request);
    }
}
