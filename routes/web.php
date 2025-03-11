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
use App\Http\Controllers\User\AlbumController as UserAlbumController;
use App\Http\Controllers\User\CommentController as UserCommentController;
use App\Http\Controllers\User\FollowController as UserFollowController;
use App\Http\Controllers\User\LikeController as UserLikeController;
use App\Http\Controllers\User\NotifController as UserNotifController;
use App\Http\Controllers\User\PhotoController as UserPhotoController;
use App\Http\Controllers\User\ProfileController as UserProfileController;
use App\Http\Controllers\User\ReportController as UserReportController;
use App\Http\Controllers\User\SettingController as UserSettingController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SearchController;

// Route untuk homepage
Route::middleware(['logout_if_authenticated'])->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
});

Route::middleware(['auth', 'logout_if_authenticated'])->group(function () {
    Route::get('/foto/upload', [UserPhotoController::class, 'createphotos'])->name('photos.create');
    Route::post('/foto/upload', [UserPhotoController::class, 'storePhoto'])->name('photos.store');
    Route::post('/settings/email/send-code', [AuthController::class, 'sendEmailVerificationCode'])->name('user.sendEmailVerificationCode');
});

// Rute untuk guest melihat dan mendownload foto
Route::get('/foto/{id}', [UserPhotoController::class, 'showPhoto'])->name('photos.show');
Route::post('/foto/{id}/download', [UserPhotoController::class, 'downloadPhoto'])->name('photos.download');

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
    Route::get('/profil', [UserProfileController::class, 'index'])->name('user.profile');
    Route::put('/profile', [UserProfileController::class, 'updateProfile'])->name('user.updateProfile');
    Route::get('/subscription', [UserSubscriptionController::class, 'index'])->name('subscription');
    Route::delete('/profil/foto', [UserProfileController::class, 'deleteProfilePhoto'])->name('user.deleteProfilePhoto');
    Route::post('/check-username', [UserProfileController::class, 'checkUsername'])->name('user.checkUsername');
    Route::post('/check-email', [UserProfileController::class, 'checkEmail'])->name('user.checkEmail');
    Route::get('/foto', [UserPhotoController::class, 'index'])->name('user.photos');
    Route::post('/photo/{id}/report', [UserReportController::class, 'reportPhoto'])->name('photo.report');
    Route::post('/comment/{id}/report', [UserReportController::class, 'reportComment'])->name('comment.report');
    Route::post('/user/{id}/report', [UserReportController::class, 'reportUser'])->name('user.report');    
    Route::post('/photos/{photo}/like', [UserLikeController::class, 'like'])->name('photos.like');
    Route::post('/photos/{photo}/unlike', [UserLikeController::class, 'unlike'])->name('photos.unlike');
    Route::delete('/comments/{id}', [UserCommentController::class, 'destroy'])->name('comments.destroy');
    Route::post('/comments/{comment}/reply', [UserCommentController::class, 'storeReply'])->name('comments.reply'); 
    Route::delete('/replies/{id}', [UserCommentController::class, 'destroyReply'])->name('reply.destroy');
    Route::post('/photos/{photo}/comments', [UserCommentController::class, 'store'])->name('photos.comments.store');
    Route::post('/users/{user}/follow', [UserFollowController::class, 'follow'])->name('users.follow');
    Route::post('/users/{user}/unfollow', [UserFollowController::class, 'unfollow'])->name('users.unfollow');
    Route::get('/notifications', [UserNotifController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [UserNotifController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::post('/albums', [UserAlbumController::class, 'store'])->name('albums.store');
    Route::put('/albums/{id}', [UserAlbumController::class, 'update'])->name('albums.update');
    Route::delete('/albums/{id}', [UserAlbumController::class, 'destroy'])->name('albums.destroy');
    Route::post('/albums/{albumId}/photos/{photoId}/add', [UserAlbumController::class, 'addPhoto'])->name('albums.addPhoto');
    Route::post('/albums/{albumId}/photos/{photoId}/remove', [UserAlbumController::class, 'removePhoto'])->name('albums.removePhoto');
    Route::put('/albums/{id}/updateTitle', [UserAlbumController::class, 'updateTitle'])->name('albums.updateTitle');
    Route::put('/albums/{id}/updateDescription', [UserAlbumController::class, 'updateDescription'])->name('albums.updateDescription'); 
    Route::get('/settings', [UserSettingController::class, 'index'])->name('user.settings');
    Route::put('/settings/username', [UserSettingController::class, 'updateUsername'])->name('user.updateUsername');
    Route::put('/settings/password', [UserSettingController::class, 'updatePassword'])->name('user.updatePassword');
    Route::post('/settings/send-email-verification', [AuthController::class, 'sendEmailVerification'])->name('user.sendEmailVerification');
    Route::post('/settings/verify-email-code', [AuthController::class, 'verifyEmailCode'])->name('user.verifyEmailCode');
    Route::put('/settings/update-email', [UserSettingController::class, 'updateEmail'])->name('user.updateEmail');
    Route::post('/check-verification-username', [UserSettingController::class, 'checkVerificationUsername'])->name('user.checkVerificationUsername');
    Route::post('/settings/submit-verification', [UserSettingController::class, 'submitVerification'])->name('user.submitVerification');
    Route::get('/photos/{id}/edit', [UserPhotoController::class, 'editPhoto'])->name('photos.edit');
    Route::put('/photos/{id}', [UserPhotoController::class, 'updatePhoto'])->name('photos.update');
    Route::delete('/photos/{id}', [UserPhotoController::class, 'destroyPhoto'])->name('photos.destroy');
    Route::post('/transaction/create', [UserSubscriptionController::class, 'createTransaction'])->name('transaction.create');
    Route::post('/transaction/check-status', [UserSubscriptionController::class, 'checkTransactionStatus'])->name('transaction.checkStatus');
    Route::get('/subscription/manage', [UserSubscriptionController::class, 'manage'])->name('subscription.manage');
    Route::post('/subscription/save', [UserSubscriptionController::class, 'saveSubsUser'])->name('subscription.save');
    Route::get('/subscription/history', [UserSubscriptionController::class, 'history'])->name('subscription.history');
    Route::get('/subscribe/{username}', [UserSubscriptionController::class, 'showSubscriptionOptions'])->name('subscription.options');
    Route::post('/subscribe/{username}', [UserSubscriptionController::class, 'subscribeOn'])->name('subscription.subscribe');
    Route::post('/transaction/check-status-user', [UserSubscriptionController::class, 'checkTransactionStatusUser'])->name('transaction.checkStatusUser');
});

// Rute untuk guest melihat album
Route::get('/albums/{id}', [UserAlbumController::class, 'show'])->name('albums.show');
Route::get('/cari', [SearchController::class, 'search'])->name('search');
Route::get('/{username}', [UserProfileController::class, 'showProfile'])->name('user.showProfile');