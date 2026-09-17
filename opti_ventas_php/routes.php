<?php

declare(strict_types=1);

use App\Controllers\AuthController;
use App\Controllers\CategoryController;
use App\Controllers\CustomerController;
use App\Controllers\DashboardController;
use App\Controllers\PosController;
use App\Controllers\ProductController;
use App\Controllers\ReportController;
use App\Controllers\SaleController;
use App\Controllers\SettingController;
use App\Controllers\UserController;
use App\Core\Auth;

header('X-Content-Type-Options: nosniff');

$router = new App\Core\Router();

$router->get('/', function () {
    if (Auth::check()) {
        redirect('/dashboard');
    }
    redirect('/login');
});

$router->get('/login', [AuthController::class, 'create']);
$router->post('/login', [AuthController::class, 'store']);
$router->post('/logout', [AuthController::class, 'destroy']);
$router->get('/register', [AuthController::class, 'showRegister']);
$router->post('/register', [AuthController::class, 'register']);

$auth = ['auth'];

$router->get('/dashboard', [DashboardController::class, 'index'], $auth);

$router->get('/pos', [PosController::class, 'index'], $auth);
$router->post('/pos/sale', [PosController::class, 'store'], $auth);

$router->get('/categories', [CategoryController::class, 'index'], $auth);
$router->get('/categories/create', [CategoryController::class, 'create'], $auth);
$router->post('/categories', [CategoryController::class, 'store'], $auth);
$router->get('/categories/{id}/edit', [CategoryController::class, 'edit'], $auth);
$router->post('/categories/{id}', [CategoryController::class, 'update'], $auth);
$router->post('/categories/{id}/delete', [CategoryController::class, 'destroy'], $auth);

$router->get('/products', [ProductController::class, 'index'], $auth);
$router->get('/products/create', [ProductController::class, 'create'], $auth);
$router->post('/products', [ProductController::class, 'store'], $auth);
$router->get('/products/{id}/edit', [ProductController::class, 'edit'], $auth);
$router->post('/products/{id}', [ProductController::class, 'update'], $auth);
$router->post('/products/{id}/delete', [ProductController::class, 'destroy'], $auth);

$router->get('/customers', [CustomerController::class, 'index'], $auth);
$router->get('/customers/create', [CustomerController::class, 'create'], $auth);
$router->post('/customers', [CustomerController::class, 'store'], $auth);
$router->get('/customers/{id}/edit', [CustomerController::class, 'edit'], $auth);
$router->post('/customers/{id}', [CustomerController::class, 'update'], $auth);
$router->post('/customers/{id}/delete', [CustomerController::class, 'destroy'], $auth);

$router->get('/sales', [SaleController::class, 'index'], $auth);
$router->get('/sales/{id}', [SaleController::class, 'show'], $auth);
$router->post('/sales/{id}/void', [SaleController::class, 'void'], $auth + ['admin']);

$router->get('/reports', [ReportController::class, 'index'], $auth);

$admin = $auth + ['admin'];
$router->get('/settings', [SettingController::class, 'index'], $admin);
$router->post('/settings', [SettingController::class, 'update'], $admin);

$router->get('/users', [UserController::class, 'index'], $admin);
$router->get('/users/create', [UserController::class, 'create'], $admin);
$router->post('/users', [UserController::class, 'store'], $admin);
$router->get('/users/{id}/edit', [UserController::class, 'edit'], $admin);
$router->post('/users/{id}', [UserController::class, 'update'], $admin);
$router->post('/users/{id}/toggle-active', [UserController::class, 'toggleActive'], $admin);

$router->dispatch(request_path(), request_method());