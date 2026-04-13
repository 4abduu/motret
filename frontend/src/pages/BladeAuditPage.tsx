import { adminNav, authNav, publicNav, userNav } from '../config/legacyRoutes'

const allRoutes = [...publicNav, ...authNav, ...userNav, ...adminNav]

export function BladeAuditPage() {
  const nowItems = allRoutes.filter((item) => item.priority === 'now')
  const laterItems = allRoutes.filter((item) => item.priority === 'later')

  return (
    <section className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900">
      <h2 className="text-xl font-semibold">Audit Blade ke SPA</h2>
      <p className="mt-2 text-sm text-slate-600 dark:text-slate-300">
        Ini ringkasan hasil intip Blade lama untuk nentuin prioritas migrasi inti frontend.
      </p>

      <div className="mt-5 grid gap-6 md:grid-cols-2">
        <div>
          <h3 className="text-sm font-semibold text-emerald-700 dark:text-emerald-300">Butuh Sekarang</h3>
          <ul className="mt-2 grid gap-2 text-sm">
            {nowItems.map((item) => (
              <li key={`now-${item.to}`} className="rounded-lg bg-emerald-50 px-3 py-2 dark:bg-emerald-900/20">
                <p className="font-medium">{item.label}</p>
                <p className="text-xs text-slate-600 dark:text-slate-300">{item.legacyView}</p>
              </li>
            ))}
          </ul>
        </div>
        <div>
          <h3 className="text-sm font-semibold text-amber-700 dark:text-amber-300">Bisa Menyusul</h3>
          <ul className="mt-2 grid gap-2 text-sm">
            {laterItems.map((item) => (
              <li key={`later-${item.to}`} className="rounded-lg bg-amber-50 px-3 py-2 dark:bg-amber-900/20">
                <p className="font-medium">{item.label}</p>
                <p className="text-xs text-slate-600 dark:text-slate-300">{item.legacyView}</p>
              </li>
            ))}
          </ul>
        </div>
      </div>
    </section>
  )
}
