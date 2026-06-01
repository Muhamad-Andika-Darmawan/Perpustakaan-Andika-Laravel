<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Cek apakah user sudah login
        if (!auth()->check()) {
            return redirect('login');
        }

        // Cek apakah role user ada di dalam daftar role yang diizinkan
        if (in_array(auth()->user()->role, $roles)) {
            return $next($request);
        }

        // Jika tidak punya akses, tendang balik ke dashboard mereka masing-masing
        return redirect()->back()->with('error', 'Anda tidak memiliki akses ke halaman ini.');
    }
}