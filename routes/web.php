<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\CommentController as AdminCommentController;
use App\Http\Controllers\Admin\PhotoController as AdminPhotoController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\SubscriptionController as AdminSubscriptionController;
use App\Http\Controllers\Admin\VerificationController as AdminVerificationController;
use App\Http\Controllers\Admin\BalanceController as AdminBalanceController;
use App\Http\Controllers\User\BalanceController as UserBalanceController;
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
Route::middleware(['logout_if_authenticated', 'redirect.if.admin'])->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
});

Route::middleware(['auth', 'logout_if_authenticated', 'redirect.if.admin'])->group(function () {
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
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register')->middleware('logout_if_authenticated');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/check-username-register', [AuthController::class, 'checkUsername'])->name('check.usernameRegister');
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
    Route::put('/admin/replies/{id}/ban', [AdminCommentController::class, 'banReplies'])->name('admin.replies.ban');
    
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
    Route::delete('/admin/verification-requests/{id}', [AdminVerificationController::class, 'deleteVerificationRequest'])->name('admin.verificationRequests.delete');
    Route::get('/admin/verification-requests/{id}/documents', [AdminVerificationController::class, 'showVerificationDocuments'])->name('admin.verificationDocuments');
    Route::put('/admin/verification-requests/{id}/approve', [AdminVerificationController::class, 'approveVerificationRequest'])->name('admin.verificationRequests.approve');
    Route::put('/admin/verification-requests/{id}/reject', [AdminVerificationController::class, 'rejectVerificationRequest'])->name('admin.verificationRequests.reject');

    // Preview 
    Route::get('/admin/users/{id}/preview', [AdminUserController::class, 'previewProfile'])->name('admin.users.previewProfile');
    Route::get('/admin/users/{id}/photos', [AdminUserController::class, 'previewPhotos'])->name('admin.users.previewPhotos');
    Route::get('/admin/users/{id}/{type}', [AdminUserController::class, 'previewCommentReplies'])->name('admin.previewCommentReplies');
    Route::get('/admin/albums/{album}/preview', [AdminUserController::class, 'previewAlbum'])->name('admin.albums.preview');

    //Saldo
    Route::get('/admin/saldo', [AdminBalanceController::class, 'index'])->name('admin.saldo');
    Route::get('/admin/penarikan-saldo', [AdminBalanceController::class, 'withdrawals'])->name('admin.saldo.penarikan');
    Route::post('/admin/penarikan-saldo/{id}/acc', [AdminBalanceController::class, 'accPenarikan'])->name('admin.saldo.acc');
    Route::post('/admin/penarikan-saldo/{id}/reject', [AdminBalanceController::class, 'rejectPenarikan'])->name('admin.saldo.reject');
    Route::delete('/admin/penarikan-saldo/{id}/delete', [AdminBalanceController::class, 'deletePenarikan'])->name('admin.saldo.delete');    
    Route::get('/admin/daftar-saldo', [AdminBalanceController::class, 'balanceUser'])->name('admin.saldo.daftar');
    Route::get('/admin/riwayat-saldo/{userId}', [AdminBalanceController::class, 'historyBalance'])->name('admin.saldo.detail');
});

 // Grup untuk User dan Pro
Route::middleware(['auth', 'role:user,pro', 'prevent.admin.access', 'logout_if_authenticated'])->group(function () {
    // Profil
    Route::get('/profil', [UserProfileController::class, 'index'])->name('user.profile');
    Route::put('/profile', [UserProfileController::class, 'updateProfile'])->name('user.updateProfile');
    Route::delete('/profil/foto', [UserProfileController::class, 'deleteProfilePhoto'])->name('user.deleteProfilePhoto');
    
    // Foto
    Route::get('/foto', [UserPhotoController::class, 'index'])->name('user.photos');
    Route::get('/photos/{id}/edit', [UserPhotoController::class, 'editPhoto'])->name('photos.edit');
    Route::put('/photos/{id}', [UserPhotoController::class, 'updatePhoto'])->name('photos.update');
    Route::delete('/photos/{id}', [UserPhotoController::class, 'destroyPhoto'])->name('photos.destroy');
    Route::post('/photos/{photo}/like', [UserLikeController::class, 'like'])->name('photos.like');
    Route::post('/photos/{photo}/unlike', [UserLikeController::class, 'unlike'])->name('photos.unlike');
    Route::post('/photos/{photo}/comments', [UserCommentController::class, 'store'])->name('photos.comments.store');
    
    // Komentar & Balasan
    Route::delete('/comments/{id}', [UserCommentController::class, 'destroy'])->name('comments.destroy');
    Route::post('/comments/{comment}/reply', [UserCommentController::class, 'storeReply'])->name('comments.reply');
    Route::delete('/replies/{id}', [UserCommentController::class, 'destroyReply'])->name('reply.destroy');
    
    // Pelaporan
    Route::post('/photo/{id}/report', [UserReportController::class, 'reportPhoto'])->name('photo.report');
    Route::post('/comment/{id}/report', [UserReportController::class, 'reportComment'])->name('comment.report');
    Route::post('/reply/{id}/report', [UserReportController::class, 'reportReply'])->name('reply.report');
    Route::post('/user/{id}/report', [UserReportController::class, 'reportUser'])->name('user.report');
    
    // Pengikut
    Route::post('/users/{id}/follow', [UserFollowController::class, 'follow'])->name('user.follow');
    Route::post('/users/{id}/unfollow', [UserFollowController::class, 'unfollow'])->name('user.unfollow');
    
    // Album
    Route::post('/albums', [UserAlbumController::class, 'store'])->name('albums.store');
    Route::put('/albums/{id}', [UserAlbumController::class, 'update'])->name('albums.update');
    Route::delete('/albums/{id}', [UserAlbumController::class, 'destroy'])->name('albums.destroy');
    Route::post('/albums/{albumId}/photos/{photoId}/add', [UserAlbumController::class, 'addPhoto'])->name('albums.addPhoto');
    Route::put('/albums/{id}/updateTitle', [UserAlbumController::class, 'updateTitle']);
    Route::put('/albums/{id}/updateDescription', [UserAlbumController::class, 'updateDescription']);
    Route::put('/albums/{id}/updateVisibility', [UserAlbumController::class, 'updateVisibility']);    
    Route::post('/albums/{albumId}/removePhoto/{photoId}', [UserAlbumController::class, 'removePhoto']);

    // Notifikasi
    Route::get('/notifications', [UserNotifController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [UserNotifController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::get('/fetch-notifications', [UserNotifController::class, 'fetchNotifications'])->name('notifications.fetch');

    // Pengaturan
    Route::get('/settings', [UserSettingController::class, 'index'])->name('user.settings');
    Route::put('/settings/username', [UserSettingController::class, 'updateUsername'])->name('user.updateUsername');
    Route::put('/settings/password', [UserSettingController::class, 'updatePassword'])->name('user.updatePassword');
    Route::post('/settings/send-email-verification', [AuthController::class, 'sendEmailVerification'])->name('user.sendEmailVerification');
    Route::post('/settings/verify-email-code', [AuthController::class, 'verifyEmailCode'])->name('user.verifyEmailCode');
    Route::put('/settings/update-email', [UserSettingController::class, 'updateEmail'])->name('user.updateEmail');
    Route::post('/check-verification-username', [UserSettingController::class, 'checkVerificationUsername'])->name('user.checkVerificationUsername');
    Route::post('/settings/submit-verification', [UserSettingController::class, 'submitVerification'])->name('user.submitVerification');
    Route::post('/check-username', [UserSettingController::class, 'checkUsername'])->name('user.checkUsername');
    Route::post('/check-email', [UserSettingController::class, 'checkEmail'])->name('user.checkEmail');
    
    // Subscription & Transaksi
    Route::get('/subscription', [UserSubscriptionController::class, 'index'])->name('subscription');
    Route::get('/subscription/manage', [UserSubscriptionController::class, 'manage'])->name('subscription.manage');
    Route::get('/subscription/history', [UserSubscriptionController::class, 'history'])->name('subscription.history');
    Route::get('/subscribe/{username}', [UserSubscriptionController::class, 'showSubscriptionOptions'])->name('subscription.options');
    Route::post('/subscribe/{username}', [UserSubscriptionController::class, 'subscribeOn'])->name('subscription.subscribe');
    Route::post('/subscribe/combo/{username}', [UserSubscriptionController::class, 'subscribeCombo'])->name('subscription.subscribeCombo');
    Route::post('/subscription/save', [UserSubscriptionController::class, 'saveSubsUser'])->name('subscription.save');
    Route::post('/transaction/create', [UserSubscriptionController::class, 'createTransaction'])->name('transaction.create');
    Route::post('/transaction/check-status', [UserSubscriptionController::class, 'checkTransactionStatus'])->name('transaction.checkStatus');
    Route::post('/transaction/check-status-user', [UserSubscriptionController::class, 'checkTransactionStatusUser'])->name('transaction.checkStatusUser');
    Route::post('/transaction/check-status-combo', [UserSubscriptionController::class, 'checkTransactionStatusCombo'])->name('transaction.checkStatusCombo');

    // Saldo
    Route::get('/penarikan', [UserBalanceController::class, 'index'])->name('withdrawal.balance');
    Route::post('/penarikan', [UserBalanceController::class, 'storeWithdrawal'])->name('withdrawal.store');
    Route::get('/riwayat', [UserBalanceController::class, 'historyBalance'])->name('balance.history');
    Route::get('/riwayat/{id}', [UserBalanceController::class, 'detailRiwayatSaldo'])->name('user.saldo.detail');
});

// Rute untuk guest melihat album
Route::get('/albums/{id}', [UserAlbumController::class, 'index'])->name('albums.show');
Route::get('/cari', [SearchController::class, 'search'])->name('search');
Route::get('/{username}', [UserProfileController::class, 'showProfile'])->name('user.showProfile');