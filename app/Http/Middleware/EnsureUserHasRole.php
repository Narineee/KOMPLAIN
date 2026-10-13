<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user || ! $user->is_aktif) {
            abort(403, 'Anda tidak memiliki hak akses untuk halaman ini atau akun Anda dinonaktifkan.');
        }

        $allowedRoles = [];
        foreach ($roles as $role) {
            $allowedRoles[] = $role;
            if ($role === 'pegawai') {
                $allowedRoles[] = 'pengadu';
            }
            if ($role === 'pengadu') {
                $allowedRoles[] = 'pegawai';
            }
        }

        if (! in_array($user->role, $allowedRoles, true)) {
            abort(403, 'Anda tidak memiliki hak akses untuk halaman ini atau akun Anda dinonaktifkan.');
        }

        return $next($request);
    }
}
