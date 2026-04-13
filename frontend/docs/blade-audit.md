# Blade Audit for SPA Core

Dokumen ini merangkum hasil audit awal dari Blade lama untuk migrasi SPA React + TS.

## Prinsip
- Backend Laravel tetap jadi source of truth endpoint.
- Frontend SPA fokus dulu ke flow utama user dan admin.
- Halaman detail turunan atau preview khusus dipindah setelah fondasi stabil.

## Butuh Sekarang (Core)
- Public
- resources/views/home.blade.php
- resources/views/cari/results.blade.php
- resources/views/photos/show.blade.php
- resources/views/auth/login.blade.php
- resources/views/auth/register.blade.php
- resources/views/auth/forgot-password.blade.php
- resources/views/auth/reset-password.blade.php
- User
- resources/views/user/profile.blade.php
- resources/views/user/settings.blade.php
- resources/views/photos/create.blade.php
- resources/views/user/notifications.blade.php
- resources/views/user/subscription.blade.php
- resources/views/user/withdrawal.blade.php
- resources/views/user/balance_history.blade.php
- Admin
- resources/views/admin/dashboard.blade.php
- resources/views/admin/manageUsers.blade.php
- resources/views/admin/managePhotos.blade.php
- resources/views/admin/manageComments.blade.php
- resources/views/admin/manageReports.blade.php
- resources/views/admin/subscriptions/subscriptions.blade.php
- resources/views/admin/verifications/verificationRequests.blade.php
- resources/views/admin/manageBalance.blade.php

## Bisa Menyusul
- resources/views/photos/more.blade.php
- resources/views/photos/showterbaru.blade.php
- resources/views/admin/preview/profile.blade.php
- resources/views/admin/preview/photos.blade.php
- resources/views/admin/preview/comments.blade.php
- resources/views/admin/preview/albums.blade.php
- resources/views/admin/subscriptions/transactions.blade.php
- resources/views/admin/subscriptions/priceSubsSystem.blade.php
- resources/views/admin/subscriptions/priceSubsUser.blade.php
- resources/views/admin/subscriptions/subsSystem.blade.php
- resources/views/admin/subscriptions/subsUser.blade.php
- resources/views/admin/subscriptions/subsCombo.blade.php
- resources/views/admin/verifications/verificationDocuments.blade.php
- resources/views/template/email-change.blade.php
- resources/views/template/password-reset.blade.php

## Catatan Integrasi
- Route Laravel sekarang tetap dipakai sebagai referensi API/action.
- Di SPA, penamaan path internal sementara disederhanakan per portal: /user/* dan /admin/*.
- Pada tahap binding API, path internal bisa diselaraskan kembali ke URL publik final agar transisi mulus.
