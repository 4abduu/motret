import { NavLink, Outlet } from 'react-router-dom'
import type { NavItem } from '../config/legacyRoutes'

type PortalLayoutProps = {
  title: string
  subtitle: string
  navItems: NavItem[]
}

export function PortalLayout({ title, subtitle, navItems }: PortalLayoutProps) {
  return (
    <main className="min-h-screen bg-slate-50 text-slate-900 dark:bg-[#0B1220] dark:text-slate-200">
      <div className="mx-auto grid w-full max-w-7xl gap-4 p-6 md:grid-cols-[260px_1fr]">
        <aside className="h-fit rounded-2xl border border-slate-200 bg-white p-4 dark:border-slate-700 dark:bg-slate-900">
          <h1 className="text-lg font-bold tracking-tight">{title}</h1>
          <p className="mt-1 text-xs text-slate-500 dark:text-slate-400">{subtitle}</p>
          <nav className="mt-4 grid gap-1">
            {navItems.map((item) => (
              <NavLink
                key={item.to}
                to={item.to}
                className={({ isActive }) =>
                  [
                    'rounded-lg px-3 py-2 text-sm transition',
                    isActive
                      ? 'bg-blue-600 text-white'
                      : 'text-slate-700 hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-800',
                  ].join(' ')
                }
              >
                {item.label}
              </NavLink>
            ))}
          </nav>
        </aside>
        <section className="grid gap-4">
          <Outlet />
        </section>
      </div>
    </main>
  )
}
