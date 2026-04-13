import { Navigate, createBrowserRouter } from 'react-router-dom'
import { adminNav, authNav, publicNav, userNav } from '../config/legacyRoutes'
import { PortalLayout } from '../layouts/PortalLayout'
import { BladeAuditPage } from '../pages/BladeAuditPage'
import { FeaturePage } from '../pages/FeaturePage'
import { NotFoundPage } from '../pages/NotFoundPage'

export const router = createBrowserRouter([
  {
    path: '/',
    element: (
      <PortalLayout
        title="Public"
        subtitle="Landing, pencarian, dan foto publik"
        navItems={[...publicNav, ...authNav, { label: 'Audit Blade', to: '/_migration-audit', legacyView: 'internal', priority: 'now' }]}
      />
    ),
    children: [
      {
        index: true,
        element: (
          <FeaturePage
            title="Home Feed"
            description="Pengganti awal untuk homepage feed publik."
            legacyView="resources/views/home.blade.php"
            priority="now"
          />
        ),
      },
      {
        path: 'cari',
        element: (
          <FeaturePage
            title="Search"
            description="Halaman hasil pencarian foto dan user."
            legacyView="resources/views/cari/results.blade.php"
            priority="now"
          />
        ),
      },
      {
        path: 'foto/:id',
        element: (
          <FeaturePage
            title="Photo Detail"
            description="Detail foto, komentar, like, download, dan report."
            legacyView="resources/views/photos/show.blade.php"
            priority="now"
          />
        ),
      },
      {
        path: 'u/:username',
        element: (
          <FeaturePage
            title="Public Profile"
            description="Profil creator yang bisa dilihat publik."
            legacyView="resources/views/user/profile.blade.php"
            priority="now"
          />
        ),
      },
      {
        path: 'login',
        element: (
          <FeaturePage
            title="Login"
            description="Form autentikasi user."
            legacyView="resources/views/auth/login.blade.php"
            priority="now"
          />
        ),
      },
      {
        path: 'register',
        element: (
          <FeaturePage
            title="Register"
            description="Form pendaftaran akun baru."
            legacyView="resources/views/auth/register.blade.php"
            priority="now"
          />
        ),
      },
      {
        path: 'forgot-password',
        element: (
          <FeaturePage
            title="Forgot Password"
            description="Kirim link reset password."
            legacyView="resources/views/auth/forgot-password.blade.php"
            priority="now"
          />
        ),
      },
      {
        path: 'reset-password',
        element: (
          <FeaturePage
            title="Reset Password"
            description="Setel password baru setelah verifikasi email."
            legacyView="resources/views/auth/reset-password.blade.php"
            priority="now"
          />
        ),
      },
      { path: '_migration-audit', element: <BladeAuditPage /> },
    ],
  },
  {
    path: '/user',
    element: (
      <PortalLayout title="User Portal" subtitle="Fitur user/pro yang butuh login" navItems={userNav} />
    ),
    children: [
      { index: true, element: <Navigate to="/user/profil" replace /> },
      {
        path: 'profil',
        element: (
          <FeaturePage
            title="Profil"
            description="Kelola profil user, album, dan foto milik sendiri."
            legacyView="resources/views/user/profile.blade.php"
            priority="now"
          />
        ),
      },
      {
        path: 'settings',
        element: (
          <FeaturePage
            title="Settings"
            description="Pengaturan username, password, email, dan verifikasi akun."
            legacyView="resources/views/user/settings.blade.php"
            priority="now"
          />
        ),
      },
      {
        path: 'upload',
        element: (
          <FeaturePage
            title="Upload Foto"
            description="Form upload foto dan metadata awal."
            legacyView="resources/views/photos/create.blade.php"
            priority="now"
          />
        ),
      },
      {
        path: 'notifications',
        element: (
          <FeaturePage
            title="Notifikasi"
            description="Notifikasi like, comment, reply, follow, dan subscription."
            legacyView="resources/views/user/notifications.blade.php"
            priority="now"
          />
        ),
      },
      {
        path: 'subscription',
        element: (
          <FeaturePage
            title="Subscription"
            description="Paket subscription, status transaksi, dan histori."
            legacyView="resources/views/user/subscription.blade.php"
            priority="now"
          />
        ),
      },
      {
        path: 'withdrawal',
        element: (
          <FeaturePage
            title="Penarikan Saldo"
            description="Ajukan penarikan saldo user/pro."
            legacyView="resources/views/user/withdrawal.blade.php"
            priority="now"
          />
        ),
      },
      {
        path: 'balance-history',
        element: (
          <FeaturePage
            title="Riwayat Saldo"
            description="Lihat histori transaksi saldo."
            legacyView="resources/views/user/balance_history.blade.php"
            priority="now"
          />
        ),
      },
      {
        path: 'manage-subscription',
        element: (
          <FeaturePage
            title="Manage Subscription"
            description="Kelola harga subscription creator."
            legacyView="resources/views/user/manage_subscription.blade.php"
            priority="later"
          />
        ),
      },
    ],
  },
  {
    path: '/admin',
    element: (
      <PortalLayout title="Admin Portal" subtitle="Moderasi, manajemen, dan monitoring" navItems={adminNav} />
    ),
    children: [
      { index: true, element: <Navigate to="/admin/dashboard" replace /> },
      {
        path: 'dashboard',
        element: (
          <FeaturePage
            title="Admin Dashboard"
            description="Ringkasan metrik user, foto, report, verifikasi, dan transaksi."
            legacyView="resources/views/admin/dashboard.blade.php"
            priority="now"
          />
        ),
      },
      {
        path: 'users',
        element: (
          <FeaturePage
            title="Manage Users"
            description="CRUD user, ban user, dan preview akun user."
            legacyView="resources/views/admin/manageUsers.blade.php"
            priority="now"
          />
        ),
      },
      {
        path: 'photos',
        element: (
          <FeaturePage
            title="Manage Photos"
            description="Moderasi foto, ban foto, dan edit metadata."
            legacyView="resources/views/admin/managePhotos.blade.php"
            priority="now"
          />
        ),
      },
      {
        path: 'comments',
        element: (
          <FeaturePage
            title="Manage Comments"
            description="Moderasi komentar dan balasan user."
            legacyView="resources/views/admin/manageComments.blade.php"
            priority="now"
          />
        ),
      },
      {
        path: 'reports',
        element: (
          <FeaturePage
            title="Manage Reports"
            description="Penanganan report user, comment, dan photo."
            legacyView="resources/views/admin/manageReports.blade.php"
            priority="now"
          />
        ),
      },
      {
        path: 'subscriptions',
        element: (
          <FeaturePage
            title="Manage Subscriptions"
            description="Kelola sistem langganan dan transaksi subscription."
            legacyView="resources/views/admin/subscriptions/subscriptions.blade.php"
            priority="now"
          />
        ),
      },
      {
        path: 'verification-requests',
        element: (
          <FeaturePage
            title="Verification Requests"
            description="Approval atau reject request verifikasi creator."
            legacyView="resources/views/admin/verifications/verificationRequests.blade.php"
            priority="now"
          />
        ),
      },
      {
        path: 'balance',
        element: (
          <FeaturePage
            title="Manage Balance"
            description="Withdrawals, daftar saldo user, dan riwayat saldo."
            legacyView="resources/views/admin/manageBalance.blade.php"
            priority="now"
          />
        ),
      },
      {
        path: 'preview-user',
        element: (
          <FeaturePage
            title="Preview User"
            description="Halaman preview profil/albums/comments user dari sisi admin."
            legacyView="resources/views/admin/preview/profile.blade.php"
            priority="later"
          />
        ),
      },
    ],
  },
  {
    path: '*',
    element: <NotFoundPage />,
  },
])
