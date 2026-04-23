<?php
// bootstrap/app.php
// Tambahkan kode ini ke dalam withMiddleware() di file bootstrap/app.php Laravel 11+
// Atau daftarkan di app/Http/Kernel.php untuk Laravel 10 ke bawah

// =====================================================================
// UNTUK LARAVEL 11+ (bootstrap/app.php)
// =====================================================================
// use Illuminate\Foundation\Application;
// use Illuminate\Foundation\Configuration\Exceptions;
// use Illuminate\Foundation\Configuration\Middleware;
// use App\Http\Middleware\RoleMiddleware;
//
// return Application::configure(basePath: dirname(__DIR__))
//     ->withRouting(
//         web: __DIR__.'/../routes/web.php',
//         commands: __DIR__.'/../routes/console.php',
//         health: '/up',
//     )
//     ->withMiddleware(function (Middleware $middleware) {
//         $middleware->alias([
//             'role' => RoleMiddleware::class,
//         ]);
//     })
//     ->withExceptions(function (Exceptions $exceptions) {
//         //
//     })->create();


// =====================================================================
// UNTUK LARAVEL 10 (app/Http/Kernel.php)
// =====================================================================
// Di dalam $routeMiddleware atau $middlewareAliases, tambahkan:
//
// protected $middlewareAliases = [
//     // ... existing ...
//     'role' => \App\Http\Middleware\RoleMiddleware::class,
// ];

/*
 * CARA REGISTRASI MIDDLEWARE 'role':
 *
 * Laravel 11+:
 *   Edit file bootstrap/app.php, tambahkan:
 *   ->withMiddleware(function (Middleware $middleware) {
 *       $middleware->alias(['role' => \App\Http\Middleware\RoleMiddleware::class]);
 *   })
 *
 * Laravel 10:
 *   Edit app/Http/Kernel.php, pada $middlewareAliases tambahkan:
 *   'role' => \App\Http\Middleware\RoleMiddleware::class,
 */
