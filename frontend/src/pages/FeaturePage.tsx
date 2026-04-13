type FeaturePageProps = {
  title: string
  description: string
  legacyView: string
  priority: 'now' | 'later'
}

export function FeaturePage({ title, description, legacyView, priority }: FeaturePageProps) {
  return (
    <section className="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900">
      <h2 className="text-xl font-semibold text-slate-900 dark:text-slate-100">{title}</h2>
      <p className="mt-2 text-sm text-slate-600 dark:text-slate-300">{description}</p>
      <div className="mt-4 grid gap-2 text-sm text-slate-700 dark:text-slate-200">
        <p>
          Legacy Blade: <span className="font-mono">{legacyView}</span>
        </p>
        <p>
          Migrasi Prioritas:{' '}
          <span
            className={
              priority === 'now'
                ? 'rounded-full bg-emerald-100 px-2 py-0.5 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300'
                : 'rounded-full bg-amber-100 px-2 py-0.5 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300'
            }
          >
            {priority === 'now' ? 'Butuh Sekarang' : 'Bisa Menyusul'}
          </span>
        </p>
      </div>
    </section>
  )
}
