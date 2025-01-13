<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\HomeController;

// Route untuk homepage


// Routes untuk login, register, dan logout
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/guest', [AuthController::class, 'guest'])->name('guest');

//Route::middleware(['auth', 'role:'])->prefix('admin')->group(function () {
    //Route::get('/', [HomeController::class, 'index'])->name('home');
   // Route::get('/foto/{id}', [UserController::class, 'showPhoto'])->name('photos.show');
    //Route::post('/foto/{id}/download', [UserController::class, 'downloadPhoto'])->name('photos.download');
//});
Route::middleware(['auth', 'redirect.if.admin'])->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/foto/{id}', [UserController::class, 'showPhoto'])->name('photos.show');
    Route::post('/foto/{id}/download', [UserController::class, 'downloadPhoto'])->name('photos.download');
});
// Grup untuk Admin
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('/users', [AdminController::class, 'manageUsers'])->name('admin.users');
    Route::post('/users/create', [AdminController::class, 'createUser'])->name('admin.users.create');
    Route::put('/users/{id}', [AdminController::class, 'updateUser'])->name('admin.users.update');
    Route::delete('/users/{id}', [AdminController::class, 'deleteUser'])->name('admin.users.delete');
    Route::get('/photos', [AdminController::class, 'managePhotos'])->name('admin.photos');
    Route::put('/photos/{id}', [AdminController::class, 'editPhoto'])->name('admin.photos.edit');
    Route::delete('/photos/{id}', [AdminController::class, 'deletePhoto'])->name('admin.photos.delete');
});

// Grup untuk User
Route::middleware(['auth', 'role:user'])->prefix('user')->group(function () {
    Route::get('/foto/{id}', [UserController::class, 'showPhoto'])->name('photos.show');
    Route::post('/foto/{id}/download', [UserController::class, 'downloadPhoto'])->name('photos.download');
    Route::get('/profil', [UserController::class, 'profile'])->name('user.profile');
    Route::get('/foto', [UserController::class, 'photos'])->name('user.photos');
    Route::get('/foto/upload', [UserController::class, 'createphotos'])->name('photos.create');
    Route::post('/foto/upload', [UserController::class, 'storePhoto'])->name('photos.store');
});

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// use App\Http\Controllers\AuthController;
// use App\Http\Controllers\AdminController;
// use App\Http\Controllers\UserController;
// use App\Http\Controllers\HomeController;
// use App\Http\Controllers\GuestController;

// // Route untuk homepage
// Route::get('/', [HomeController::class, 'index'])->name('home');
// Route::get('/foto/{id}', [UserController::class, 'showPhoto'])->name('photos.show');


// // Routes untuk login, register, dan logout
// Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
// Route::post('/login', [AuthController::class, 'login'])->name('login.post');
// Route::post('/register', [AuthController::class, 'register'])->name('register.post');
// Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

// // Route untuk guest
// //Route::get('/guest', [HomeController::class, 'index'])->name('guest');

// // Grup untuk Admin
// Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
//     Route::get('/', [AdminController::class, 'index'])->name('admin.dashboard');
//     Route::get('/users', [AdminController::class, 'manageUsers'])->name('admin.users');
//     Route::post('/users/create', [AdminController::class, 'createUser'])->name('admin.users.create');
//     Route::put('/users/{id}', [AdminController::class, 'updateUser'])->name('admin.users.update');
//     Route::delete('/users/{id}', [AdminController::class, 'deleteUser'])->name('admin.users.delete');
//     Route::get('/photos', [AdminController::class, 'managePhotos'])->name('admin.photos');
//     Route::put('/photos/{id}', [AdminController::class, 'editPhoto'])->name('admin.photos.edit');
//     Route::delete('/photos/{id}', [AdminController::class, 'deletePhoto'])->name('admin.photos.delete');
// });

// // Grup untuk User
// Route::middleware(['auth', 'role:user'])->prefix('user')->group(function () {
//     Route::get('/', [UserController::class, 'index'])->name('home');
//     Route::get('/profil', [UserController::class, 'profile'])->name('user.profile');
//     Route::get('/foto', [UserController::class, 'photos'])->name('user.photos');
//     Route::get('/foto/upload', [UserController::class, 'createphotos'])->name('photos.create');
//     Route::post('/foto/upload', [UserController::class, 'storePhoto'])->name('photos.store');
// });