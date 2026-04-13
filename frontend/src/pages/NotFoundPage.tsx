import { Link } from 'react-router-dom'

export function NotFoundPage() {
  return (
    <main className="mx-auto flex min-h-screen w-full max-w-xl items-center justify-center p-6">
      <div className="w-full rounded-2xl border border-slate-200 bg-white p-6 text-center dark:border-slate-700 dark:bg-slate-900">
        <h1 className="text-2xl font-bold">404</h1>
        <p className="mt-2 text-sm text-slate-600 dark:text-slate-300">Halaman tidak ditemukan.</p>
        <Link
          to="/"
          className="mt-4 inline-flex rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700"
        >
          Kembali ke Home
        </Link>
      </div>
    </main>
  )
}
