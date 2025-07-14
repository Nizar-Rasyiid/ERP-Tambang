<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class DetectTenantByToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header('X-Tenant-Token');

        if (!$token) {
            return response()->json(['error' => 'Tenant token missing'], 401);
        }

        // Pastikan koneksi ke DB utama
        $tenant = DB::connection('mysql')->table('tenants')->where('token', $token)->first();

        if (!$tenant) {
            return response()->json(['error' => 'Invalid tenant token'], 403);
        }

        // Set koneksi dinamis
        Config::set('database.connections.tenant', [
            'driver' => 'mysql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => $tenant->database,
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
        ]);

        // Jadikan default
        Config::set('database.default', 'tenant');

        return $next($request);
    }
}
