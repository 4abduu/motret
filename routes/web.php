<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\CommentController as AdminCommentController;
use App\Http\Controllers\Admin\PhotoController as AdminPhotoController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\SubscriptionController as AdminSubscriptionController;
use App\Http\Controllers\Admin\VerificationController as AdminVerificationController;
use App\Http\Controllers\User\SubscriptionController as UserSubscriptionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\FollowController;
use App\Http\Controllers\NotifController;
use App\Http\Controllers\AlbumController; // Pastikan AlbumController diimport

// Route untuk homepage

Route::middleware(['logout_if_authenticated'])->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
});

Route::middleware(['auth', 'logout_if_authenticated'])->group(function () {
    Route::get('/foto/upload', [UserController::class, 'createphotos'])->name('photos.create');
    Route::post('/foto/upload', [UserController::class, 'storePhoto'])->name('photos.store');
    Route::post('/settings/email/send-code', [AuthController::class, 'sendEmailVerificationCode'])->name('user.sendEmailVerificationCode');
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
Route::middleware(['auth', 'role:admin', 'logout_if_authenticated'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    
    // Manage Users
    Route::get('/admin/users', [AdminUserController::class, 'index'])->name('admin.users');
    Route::post('/admin/users/create', [AdminUserController::class, 'createUser'])->name('admin.users.create');
    Route::put('/admin/users/{id}', [AdminUserController::class, 'updateUser'])->name('admin.users.update');
    Route::delete('/admin/users/{id}', [AdminUserController::class, 'deleteUser'])->name('admin.users.delete');
    Route::delete('/admin/users/{id}/foto', [AdminUserController::class, 'deleteProfilePhoto'])->name('admin.users.deleteProfilePhoto');
    Route::put('/admin/users/{id}/ban', [AdminUserController::class, 'banUser'])->name('admin.users.ban');
    
    // Manage Photos
    Route::get('/admin/photos', [AdminPhotoController::class, 'index'])->name('admin.photos');
    Route::put('/admin/photos/{id}', [AdminPhotoController::class, 'editPhoto'])->name('admin.photos.edit');
    Route::delete('/admin/photos/{id}', [AdminPhotoController::class, 'deletePhoto'])->name('admin.photos.delete');
    Route::put('/admin/photos/{id}/ban', [AdminPhotoController::class, 'banPhoto'])->name('admin.photos.ban');
    
    // Manage Comments
    Route::get('/admin/comments', [AdminCommentController::class, 'index'])->name('admin.manageComments');
    Route::get('/admin/comments/comments', [AdminCommentController::class, 'comments'])->name('admin.comments');
    Route::get('/admin/comments/replies', [AdminCommentController::class, 'replies'])->name('admin.replies');
    Route::delete('/admin/comments/{id}', [AdminCommentController::class, 'deleteComment'])->name('admin.comments.delete');
    Route::delete('/admin/replies/{id}', [AdminCommentController::class, 'deleteReply'])->name('admin.replies.delete');
    Route::put('/admin/comments/{id}/ban', [AdminCommentController::class, 'banComment'])->name('admin.comments.ban');
    
    // Manage Reports
    Route::get('/admin/reports', [AdminReportController::class, 'index'])->name('admin.manageReports');
    Route::get('/admin/reports/users', [AdminReportController::class, 'reportUsers'])->name('admin.reports.users');
    Route::get('/admin/reports/comments', [AdminReportController::class, 'reportComments'])->name('admin.reports.comments');
    Route::get('/admin/reports/photos', [AdminReportController::class, 'reportPhotos'])->name('admin.reports.photos');
    Route::delete('/admin/reports/{id}', [AdminReportController::class, 'deleteReport'])->name('admin.reports.delete');
    
    // Manage Subscriptions
    Route::get('/admin/subscriptions', [AdminSubscriptionController::class, 'index'])->name('admin.subscriptions');
    Route::get('/admin/subscriptions/transactions', [AdminSubscriptionController::class, 'transactions'])->name('admin.subscriptions.transactions');
    Route::get('/admin/subscriptions/system-prices', [AdminSubscriptionController::class, 'systemPrices'])->name('admin.subscriptions.systemPrices');
    Route::get('/admin/subscriptions/user-prices', [AdminSubscriptionController::class, 'userPrices'])->name('admin.subscriptions.userPrices');
    Route::get('/admin/subscriptions/user-subscriptions', [AdminSubscriptionController::class, 'userSubscriptions'])->name('admin.subscriptions.userSubscriptions');
    Route::get('/admin/subscriptions/system-subscriptions', [AdminSubscriptionController::class, 'systemSubscriptions'])->name('admin.subscriptions.systemSubscriptions');
    Route::get('/admin/subscriptions/combo-subscriptions', [AdminSubscriptionController::class, 'comboSubscriptions'])->name('admin.subscriptions.comboSubscriptions');
    Route::put('/subscriptions/system-prices/{id}', [AdminSubscriptionController::class, 'updatePriceSystem'])->name('admin.subscriptions.updatePriceSystem');
    // Manage Verification Requests
    Route::get('/admin/verification-requests', [AdminVerificationController::class, 'index'])->name('admin.verificationRequests');
    Route::get('/admin/verification-requests/{id}/documents', [AdminVerificationController::class, 'showVerificationDocuments'])->name('admin.verificationDocuments');
    Route::put('/admin/verification-requests/{id}/approve', [AdminVerificationController::class, 'approveVerificationRequest'])->name('admin.verificationRequests.approve');
    Route::put('/admin/verification-requests/{id}/reject', [AdminVerificationController::class, 'rejectVerificationRequest'])->name('admin.verificationRequests.reject');
});

// Grup untuk User dan Pro
Route::middleware(['auth', 'role:user,pro', 'logout_if_authenticated'])->group(function () {
    Route::get('/profil', [UserController::class, 'profile'])->name('user.profile');
    Route::put('/profile', [UserController::class, 'updateProfile'])->name('user.updateProfile');
    Route::get('/subscription', [UserSubscriptionController::class, 'index'])->name('subscription');
    Route::delete('/profil/foto', [UserController::class, 'deleteProfilePhoto'])->name('user.deleteProfilePhoto');
    Route::post('/check-username', [UserController::class, 'checkUsername'])->name('user.checkUsername');
    Route::post('/check-email', [UserController::class, 'checkEmail'])->name('user.checkEmail');
    Route::get('/foto', [UserController::class, 'photos'])->name('user.photos');
    Route::post('/photo/{id}/report', [UserController::class, 'reportPhoto'])->name('photo.report');
    Route::post('/comment/{id}/report', [UserController::class, 'reportComment'])->name('comment.report');
    Route::post('/user/{id}/report', [UserController::class, 'reportUser'])->name('user.report');    
    Route::post('/photos/{photo}/like', [LikeController::class, 'like'])->name('photos.like');
    Route::post('/photos/{photo}/unlike', [LikeController::class, 'unlike'])->name('photos.unlike');
    Route::delete('/comments/{id}', [CommentController::class, 'destroy'])->name('comments.destroy');
    Route::post('/comments/{comment}/reply', [CommentController::class, 'storeReply'])->name('comments.reply'); 
    Route::delete('/replies/{id}', [CommentController::class, 'destroyReply'])->name('reply.destroy');
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
    Route::get('/settings', [UserController::class, 'settings'])->name('user.settings');
    Route::put('/settings/username', [UserController::class, 'updateUsername'])->name('user.updateUsername');
    Route::put('/settings/password', [UserController::class, 'updatePassword'])->name('user.updatePassword');
    Route::post('/settings/send-email-verification', [AuthController::class, 'sendEmailVerification'])->name('user.sendEmailVerification');
    Route::post('/settings/verify-email-code', [AuthController::class, 'verifyEmailCode'])->name('user.verifyEmailCode');
    Route::put('/settings/update-email', [UserController::class, 'updateEmail'])->name('user.updateEmail');
    Route::post('/check-verification-username', [UserController::class, 'checkVerificationUsername'])->name('user.checkVerificationUsername');
    Route::put('/settings/update-email', [UserController::class, 'updateEmail'])->name('user.updateEmail');
    Route::post('/settings/submit-verification', [UserController::class, 'submitVerification'])->name('user.submitVerification');
    Route::get('/photos/{id}/edit', [UserController::class, 'editPhoto'])->name('photos.edit');
    Route::put('/photos/{id}', [UserController::class, 'updatePhoto'])->name('photos.update');
    Route::delete('/photos/{id}', [UserController::class, 'destroyPhoto'])->name('photos.destroy');
    Route::post('/transaction/create', [UserSubscriptionController::class, 'createTransaction'])->name('transaction.create');
    Route::post('/transaction/check-status', [UserSubscriptionController::class, 'checkTransactionStatus'])->name('transaction.checkStatus');
});

// Rute untuk guest melihat album
Route::get('/albums/{id}', [AlbumController::class, 'show'])->name('albums.show');
Route::get('/cari', [SearchController::class, 'search'])->name('search');
Route::get('/{username}', [UserController::class, 'showProfile'])->name('user.showProfile');