export type NavItem = {
  label: string
  to: string
  legacyView: string
  priority: 'now' | 'later'
}

export const publicNav: NavItem[] = [
  { label: 'Home', to: '/', legacyView: 'resources/views/home.blade.php', priority: 'now' },
  { label: 'Search', to: '/cari', legacyView: 'resources/views/cari/results.blade.php', priority: 'now' },
  { label: 'Photo Detail', to: '/foto/123', legacyView: 'resources/views/photos/show.blade.php', priority: 'now' },
  { label: 'Public Profile', to: '/john_doe', legacyView: 'resources/views/user/profile.blade.php', priority: 'now' },
]

export const authNav: NavItem[] = [
  { label: 'Login', to: '/login', legacyView: 'resources/views/auth/login.blade.php', priority: 'now' },
  { label: 'Register', to: '/register', legacyView: 'resources/views/auth/register.blade.php', priority: 'now' },
  { label: 'Forgot Password', to: '/forgot-password', legacyView: 'resources/views/auth/forgot-password.blade.php', priority: 'now' },
  { label: 'Reset Password', to: '/reset-password', legacyView: 'resources/views/auth/reset-password.blade.php', priority: 'now' },
]

export const userNav: NavItem[] = [
  { label: 'Profil', to: '/user/profil', legacyView: 'resources/views/user/profile.blade.php', priority: 'now' },
  { label: 'Settings', to: '/user/settings', legacyView: 'resources/views/user/settings.blade.php', priority: 'now' },
  { label: 'Upload Foto', to: '/user/upload', legacyView: 'resources/views/photos/create.blade.php', priority: 'now' },
  { label: 'Notifikasi', to: '/user/notifications', legacyView: 'resources/views/user/notifications.blade.php', priority: 'now' },
  { label: 'Subscription', to: '/user/subscription', legacyView: 'resources/views/user/subscription.blade.php', priority: 'now' },
  { label: 'Penarikan', to: '/user/withdrawal', legacyView: 'resources/views/user/withdrawal.blade.php', priority: 'now' },
  { label: 'Riwayat Saldo', to: '/user/balance-history', legacyView: 'resources/views/user/balance_history.blade.php', priority: 'now' },
  { label: 'Manage Subscription', to: '/user/manage-subscription', legacyView: 'resources/views/user/manage_subscription.blade.php', priority: 'later' },
]

export const adminNav: NavItem[] = [
  { label: 'Dashboard', to: '/admin/dashboard', legacyView: 'resources/views/admin/dashboard.blade.php', priority: 'now' },
  { label: 'Manage Users', to: '/admin/users', legacyView: 'resources/views/admin/manageUsers.blade.php', priority: 'now' },
  { label: 'Manage Photos', to: '/admin/photos', legacyView: 'resources/views/admin/managePhotos.blade.php', priority: 'now' },
  { label: 'Manage Comments', to: '/admin/comments', legacyView: 'resources/views/admin/manageComments.blade.php', priority: 'now' },
  { label: 'Manage Reports', to: '/admin/reports', legacyView: 'resources/views/admin/manageReports.blade.php', priority: 'now' },
  { label: 'Subscriptions', to: '/admin/subscriptions', legacyView: 'resources/views/admin/subscriptions/subscriptions.blade.php', priority: 'now' },
  { label: 'Verification Requests', to: '/admin/verification-requests', legacyView: 'resources/views/admin/verifications/verificationRequests.blade.php', priority: 'now' },
  { label: 'Balance', to: '/admin/balance', legacyView: 'resources/views/admin/manageBalance.blade.php', priority: 'now' },
  { label: 'Preview User', to: '/admin/preview-user', legacyView: 'resources/views/admin/preview/profile.blade.php', priority: 'later' },
]
