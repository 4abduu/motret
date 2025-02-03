<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\NotifController;
use App\Http\Controllers\AlbumController; // Pastikan AlbumController diimport

// Route untuk homepage
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::middleware(['auth'])->group(function () {
    Route::get('/foto/upload', [UserController::class, 'createphotos'])->name('photos.create');
    Route::post('/foto/upload', [UserController::class, 'storePhoto'])->name('photos.store');
});

// Rute untuk guest melihat dan mendownload foto
Route::get('/foto/{id}', [UserController::class, 'showPhoto'])->name('photos.show');
Route::post('/foto/{id}/download', [UserController::class, 'downloadPhoto'])->name('photos.download');

// Routes untuk login, register, dan logout
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login')->middleware('logout_if_authenticated');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/guest', [AuthController::class, 'guest'])->name('guest');
Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('oauth/google/callback', [AuthController::class, 'handleGoogleCallback']);
Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
Route::get('/reset-password', [AuthController::class, 'showResetPasswordForm'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
// Grup untuk Admin
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('/admin/comments', [AdminController::class, 'manageComments'])->name('admin.manageComments');
    Route::get('/admin/comments/comments', [AdminController::class, 'comments'])->name('admin.comments');
    Route::get('/admin/comments/replies', [AdminController::class, 'replies'])->name('admin.replies');
    Route::get('/admin/reports', [AdminController::class, 'manageReports'])->name('admin.manageReports');
    Route::get('/admin/reports/users', [AdminController::class, 'reportUsers'])->name('admin.reports.users');
    Route::get('/admin/reports/comments', [AdminController::class, 'reportComments'])->name('admin.reports.comments');
    Route::get('/admin/reports/photos', [AdminController::class, 'reportPhotos'])->name('admin.reports.photos');
    Route::get('/users', [AdminController::class, 'manageUsers'])->name('admin.users');
    Route::post('/users/create', [AdminController::class, 'createUser'])->name('admin.users.create');
    Route::put('/users/{id}', [AdminController::class, 'updateUser'])->name('admin.users.update');
    Route::delete('/users/{id}', [AdminController::class, 'deleteUser'])->name('admin.users.delete');
    Route::delete('/users/{id}/foto', [AdminController::class, 'deleteProfilePhoto'])->name('admin.users.deleteProfilePhoto');
    Route::get('/photos', [AdminController::class, 'managePhotos'])->name('admin.photos');
    Route::put('/photos/{id}', [AdminController::class, 'editPhoto'])->name('admin.photos.edit');
    Route::delete('/photos/{id}', [AdminController::class, 'deletePhoto'])->name('admin.photos.delete');
    Route::delete('/reports/{id}', [AdminController::class, 'deleteReport'])->name('admin.reports.delete');
    Route::put('/admin/reports/comments/{id}/ban', [AdminController::class, 'banComment'])->name('admin.comments.ban');
    Route::put('/admin/reports/photos/{id}/ban', [AdminController::class, 'banPhoto'])->name('admin.photos.ban');
    Route::put('/admin/reports/users/{id}/ban', [AdminController::class, 'banPhoto'])->name('admin.users.ban');
    Route::get('/subscriptions', [AdminController::class, 'managePhotos'])->name('admin.subscriptions');
    Route::delete('admin/comments/{id}', [AdminController::class, 'deleteComment'])->name('admin.comments.delete');
    Route::delete('/replies/{id}', [AdminController::class, 'deleteReply'])->name('admin.replies.delete');
});

// Grup untuk User
Route::middleware(['auth', 'role:user'])->group(function () {
    Route::get('/profil', [UserController::class, 'profile'])->name('user.profile');
    Route::put('/profil', [UserController::class, 'updateProfile'])->name('user.updateProfile');
    Route::delete('/profil/foto', [UserController::class, 'deleteProfilePhoto'])->name('user.deleteProfilePhoto');
    Route::post('/check-username', [UserController::class, 'checkUsername'])->name('user.checkUsername');
    Route::post('/check-email', [UserController::class, 'checkEmail'])->name('user.checkEmail');
    Route::get('/foto', [UserController::class, 'photos'])->name('user.photos');
    Route::post('/photo/{id}/report', [UserController::class, 'reportPhoto'])->name('photo.report');
    Route::post('/comment/{id}/report', [UserController::class, 'reportComment'])->name('comment.report');
    Route::post('/user/{id}/report', [UserController::class, 'reportUser'])->name('user.report');    Route::post('/photos/{photo}/like', [LikeController::class, 'like'])->name('photos.like');
    Route::post('/photos/{photo}/unlike', [LikeController::class, 'unlike'])->name('photos.unlike');
    Route::delete('/comments/{id}', [CommentController::class, 'destroy'])->name('comments.destroy');
    Route::post('/comments/{comment}/reply', [CommentController::class, 'storeReply'])->name('comments.reply'); 
    Route::post('/photos/{photo}/comments', [CommentController::class, 'store'])->name('photos.comments.store');
    Route::post('/users/{user}/follow', [FollowController::class, 'follow'])->name('users.follow');
    Route::post('/users/{user}/unfollow', [FollowController::class, 'unfollow'])->name('users.unfollow');
    Route::get('/notifications', [NotifController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [NotifController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::post('/albums', [AlbumController::class, 'store'])->name('albums.store');
    Route::put('/albums/{id}', [AlbumController::class, 'update'])->name('albums.update');
    Route::delete('/albums/{id}', [AlbumController::class, 'destroy'])->name('albums.destroy');
    Route::post('/albums/{albumId}/photos/{photoId}/add', [AlbumController::class, 'addPhoto'])->name('albums.addPhoto');
    Route::post('/albums/{albumId}/photos/{photoId}/remove', [AlbumController::class, 'removePhoto'])->name('albums.removePhoto');
    Route::put('/albums/{id}/updateTitle', [AlbumController::class, 'updateTitle'])->name('albums.updateTitle');
    Route::put('/albums/{id}/updateDescription', [AlbumController::class, 'updateDescription'])->name('albums.updateDescription'); 
});

// Rute untuk guest melihat album
Route::get('/albums/{id}', [AlbumController::class, 'show'])->name('albums.show');
Route::get('/cari', [SearchController::class, 'search'])->name('search');
Route::get('/{username}', [UserController::class, 'showProfile'])->name('user.showProfile');