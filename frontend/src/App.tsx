import { useEffect, useState } from 'react'

function App() {
  const [dark, setDark] = useState(false)

  useEffect(() => {
    const savedTheme = window.localStorage.getItem('motret-theme')
    const isDark = savedTheme === 'dark'
    setDark(isDark)
    document.documentElement.classList.toggle('dark', isDark)
  }, [])

  const toggleTheme = () => {
    const next = !dark
    setDark(next)
    document.documentElement.classList.toggle('dark', next)
    window.localStorage.setItem('motret-theme', next ? 'dark' : 'light')
  }

  return (
    <main className="min-h-screen bg-slate-50 text-slate-900 transition-colors dark:bg-[#0B1220] dark:text-slate-200">
      <div className="mx-auto grid w-full max-w-6xl gap-4 p-6">
        <section className="rounded-2xl border border-slate-200 bg-gradient-to-br from-blue-50 to-blue-100 p-6 dark:border-slate-700 dark:from-slate-800 dark:to-blue-900">
          <div className="flex flex-col items-start justify-between gap-4 md:flex-row md:items-center">
            <div>
              <div className="flex items-center gap-3">
                <svg width="40" height="40" viewBox="0 0 64 64" fill="none" aria-label="Motret logo">
                  <rect x="8" y="16" width="48" height="34" rx="10" fill="url(#g1)" />
                  <rect x="18" y="10" width="14" height="8" rx="3" fill="#93c5fd" />
                  <circle cx="32" cy="33" r="12" fill="#0f172a" />
                  <circle cx="32" cy="33" r="8" fill="#3b82f6" />
                  <circle cx="32" cy="33" r="3" fill="#e5e7eb" />
                  <defs>
                    <linearGradient id="g1" x1="8" y1="16" x2="56" y2="50" gradientUnits="userSpaceOnUse">
                      <stop stopColor="#60a5fa" />
                      <stop offset="1" stopColor="#1e3a8a" />
                    </linearGradient>
                  </defs>
                </svg>
                <h1 className="text-2xl font-bold tracking-tight">Motret</h1>
              </div>
              <p className="mt-1 text-sm text-slate-600 dark:text-slate-300">Blue palette preview with light and dark mode</p>
            </div>

            <div className="flex items-center gap-2">
              <button
                onClick={toggleTheme}
                className="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 dark:hover:bg-slate-700"
              >
                {dark ? 'Switch to Light' : 'Switch to Dark'}
              </button>
              <button className="rounded-xl bg-blue-500 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-600">Primary CTA</button>
            </div>
          </div>
        </section>

        <section className="grid gap-4 md:grid-cols-3">
          <article className="rounded-2xl border border-slate-200 bg-white p-4 dark:border-slate-700 dark:bg-[#121A2B]">
            <h2 className="text-sm font-semibold">Brand Blue</h2>
            <div className="mt-3 flex gap-2">
              <div className="h-8 w-8 rounded-lg bg-blue-300"></div>
              <div className="h-8 w-8 rounded-lg bg-blue-400"></div>
              <div className="h-8 w-8 rounded-lg bg-blue-500"></div>
              <div className="h-8 w-8 rounded-lg bg-blue-600"></div>
              <div className="h-8 w-8 rounded-lg bg-blue-900"></div>
            </div>
            <p className="mt-3 text-sm text-slate-500 dark:text-slate-400">Dipakai untuk CTA, active state, dan link.</p>
          </article>

          <article className="rounded-2xl border border-slate-200 bg-white p-4 dark:border-slate-700 dark:bg-[#121A2B]">
            <h2 className="text-sm font-semibold">Semantic Colors</h2>
            <div className="mt-3 flex gap-2">
              <span className="rounded-full bg-emerald-500 px-2.5 py-1 text-xs font-bold text-white">Success</span>
              <span className="rounded-full bg-amber-500 px-2.5 py-1 text-xs font-bold text-white">Warning</span>
              <span className="rounded-full bg-red-500 px-2.5 py-1 text-xs font-bold text-white">Danger</span>
            </div>
            <p className="mt-3 text-sm text-slate-500 dark:text-slate-400">Khusus status dan feedback, jangan overuse.</p>
          </article>

          <article className="rounded-2xl border border-slate-200 bg-white p-4 dark:border-slate-700 dark:bg-[#121A2B]">
            <h2 className="text-sm font-semibold">Readability</h2>
            <p className="mt-2 text-sm text-slate-700 dark:text-slate-200">Primary text untuk konten utama.</p>
            <p className="mt-1 text-sm text-slate-500 dark:text-slate-400">Muted text untuk metadata dan subtitle.</p>
          </article>
        </section>
      </div>
    </main>
  )
}

export default App
