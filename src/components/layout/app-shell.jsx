import { LogOut, Menu, Search, X } from "lucide-react";
import { useEffect, useMemo, useState } from "react";
import { NavLink, useLocation, useNavigate } from "react-router-dom";
import { Button } from "@/components/ui/button";
import { adminNavigation, operatorNavigation } from "@/data/navigation";
import { cn } from "@/lib/utils";
import { useAuth } from "@/providers/auth-provider";

function getInitials(name) {
  return (name || "PP")
    .split(" ")
    .filter(Boolean)
    .slice(0, 2)
    .map((part) => part[0])
    .join("")
    .toUpperCase();
}

function AdminSidebar({ navigation, open, onClose }) {
  const sections = navigation.reduce((accumulator, item) => {
    const key = item.section || "Navigation";
    accumulator[key] = [...(accumulator[key] || []), item];
    return accumulator;
  }, {});

  return (
    <>
      <div
        className={cn(
          "fixed inset-0 z-40 bg-black/20 backdrop-blur-sm transition-opacity duration-300 md:hidden",
          open ? "opacity-100" : "pointer-events-none opacity-0",
        )}
        onClick={onClose}
      />
      <aside
        className={cn(
          "fixed left-0 top-0 z-50 flex h-screen w-60 flex-col bg-primary px-5 py-6 text-white shadow-[0_0_20px_rgba(201,144,26,0.15)] transition-transform duration-300 ease-in-out",
          open ? "translate-x-0" : "-translate-x-full md:translate-x-0",
        )}
      >
        <div className="mb-8 flex items-center justify-between md:justify-start">
          <div className="flex items-center gap-3">
            <img className="h-11 w-11 rounded-xl" src="/icons/peage_bridge_logo_africain.svg" alt="Pont Peage" />
            <div>
              <h2 className="font-headline text-xl font-black text-white">Peage Bridge</h2>
              <p className="text-xs text-white/60">Centre de supervision</p>
            </div>
          </div>
          <button className="rounded-lg p-2 text-white/80 md:hidden" onClick={onClose} aria-label="Fermer">
            <X className="h-5 w-5" />
          </button>
        </div>

        <nav className="space-y-6">
          {Object.entries(sections).map(([section, items]) => (
            <div key={section} className="space-y-2">
              <p className="px-3 text-[11px] font-bold uppercase tracking-[0.3em] text-white/35">{section}</p>
              <div className="space-y-1">
                {items.map((item) => (
                  <NavLink
                    key={item.to}
                    to={item.to}
                    end={item.end}
                    onClick={onClose}
                    className={({ isActive }) =>
                      cn(
                        "flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-medium transition-all duration-200",
                        isActive
                          ? "bg-white text-primary shadow-[0_8px_24px_rgba(255,255,255,0.12)]"
                          : "text-white/72 hover:bg-white/10 hover:text-white",
                      )
                    }
                  >
                    <item.icon className="h-4 w-4" />
                    <span>{item.label}</span>
                  </NavLink>
                ))}
              </div>
            </div>
          ))}
        </nav>
      </aside>
    </>
  );
}

function AdminLayout({ children, navigation, user, onLogout }) {
  const location = useLocation();
  const [open, setOpen] = useState(false);
  const currentItem = navigation.find((item) => item.to === location.pathname) || navigation[0];

  useEffect(() => {
    setOpen(false);
  }, [location.pathname]);

  return (
    <div className="min-h-screen bg-background">
      <AdminSidebar navigation={navigation} open={open} onClose={() => setOpen(false)} />

      <header className="fixed left-0 right-0 top-0 z-30 flex h-16 items-center justify-between bg-[#fef9f1]/95 px-4 backdrop-blur md:left-60 md:px-8">
        <div className="flex min-w-0 flex-1 items-center gap-4 md:gap-8">
          <button
            className="rounded-md p-2 text-primary transition hover:bg-muted md:hidden"
            onClick={() => setOpen(true)}
            aria-label="Ouvrir le menu"
          >
            <Menu className="h-5 w-5" />
          </button>

          <div className="min-w-0">
            <h1 className="truncate font-headline text-2xl font-black text-primary">{currentItem?.label}</h1>
          </div>

          <div className="relative hidden w-full max-w-md md:block">
            <Search className="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-primary/40" />
            <input
              className="w-full rounded-md border-0 bg-[#f2ede5] py-2 pl-10 pr-4 text-sm text-ink outline-none ring-1 ring-transparent transition-all focus:ring-2 focus:ring-secondary/40"
              placeholder="Recherche un paiement (Matricule)"
              type="search"
            />
          </div>
        </div>

        <div className="ml-4 flex items-center gap-4 md:gap-12">
          <div className="hidden items-center gap-2 rounded-full bg-[#e7e2da] px-3 py-1.5 text-xs font-bold text-primary md:flex">
            <span className="h-2 w-2 rounded-full bg-emerald-500" />
            Etat du systeme
          </div>

          <div className="relative inline-flex">
            <div className="flex h-10 w-10 items-center justify-center overflow-hidden rounded-full border-2 border-[#e7e2da] bg-white shadow-sm">
              <span className="font-mono text-lg font-bold text-primary">{getInitials(user?.name)}</span>
            </div>
            <span className="absolute bottom-0 right-0 h-2.5 w-2.5 rounded-full border-2 border-white bg-emerald-500" />
          </div>
        </div>
      </header>

      <main className="px-4 pb-8 pt-24 md:ml-60 md:px-8">
        <div className="mx-auto max-w-[1500px] space-y-8">{children}</div>
      </main>

      <div className="fixed bottom-4 right-4 md:right-8">
        <Button variant="danger" size="sm" className="rounded-full px-4 shadow-lg" onClick={onLogout}>
          <LogOut className="h-4 w-4" />
          Deconnexion
        </Button>
      </div>
    </div>
  );
}

function OperatorLayout({ children, navigation, user, onLogout }) {
  const location = useLocation();
  const laneLabel = user?.booth?.lane_code || user?.booth?.name || "Voie active";

  return (
    <div className="min-h-screen bg-background">
      <header className="fixed top-0 z-40 flex h-16 w-full items-center justify-between bg-white/95 px-4 backdrop-blur md:px-6">
        <div className="flex items-center gap-4 md:gap-8">
          <a href="/app/operator" className="flex items-center gap-3">
            <img className="h-10 w-10 rounded-lg" src="/icons/peage_bridge_logo_africain.svg" alt="Pont Peage" />
            <div>
              <h1 className="font-headline text-lg font-black text-primary md:text-xl">Peage Bridge</h1>
              <p className="hidden text-xs text-primary/60 md:block">Votre passage simplifie</p>
            </div>
          </a>

          <nav className="hidden items-center gap-6 pt-1 md:flex">
            {navigation.map((item) => (
              <NavLink
                key={item.to}
                to={item.to}
                end={item.end}
                className={({ isActive }) =>
                  cn(
                    "flex h-16 items-center px-2 text-sm font-semibold transition-all duration-200",
                    isActive ? "border-b-2 border-secondary text-secondary-foreground" : "text-slate-500 hover:text-primary",
                  )
                }
              >
                {item.label}
              </NavLink>
            ))}
          </nav>
        </div>

        <div className="flex items-center gap-4 md:gap-10">
          <div className="hidden items-center gap-2 rounded-lg bg-[#f8f3eb] px-4 py-1.5 md:flex">
            <span className="status-dot bg-secondary" />
            <span className="font-semibold text-primary">{laneLabel}</span>
          </div>

          <div className="flex items-center gap-4">
            <div className="relative inline-flex">
              <div className="flex h-10 w-10 items-center justify-center overflow-hidden rounded-full border-2 border-[#e7e2da] bg-white shadow-sm">
                <span className="font-mono text-lg font-bold text-primary">{getInitials(user?.name)}</span>
              </div>
              <span className="absolute bottom-0 right-0 h-2.5 w-2.5 rounded-full border-2 border-white bg-emerald-500" />
            </div>

            <Button variant="danger" size="sm" className="rounded-full px-4" onClick={onLogout}>
              <LogOut className="h-4 w-4" />
              <span className="hidden sm:inline">Se deconnecter</span>
            </Button>
          </div>
        </div>
      </header>

      <main className="mx-auto max-w-[1500px] px-4 pb-28 pt-24 md:px-6 md:pb-10">{children}</main>

      <nav className="fixed bottom-0 left-0 z-40 flex h-20 w-full items-center justify-around border-t border-primary/5 bg-[#fef9f1] px-4 md:hidden">
        {navigation.map((item) => (
          <NavLink
            key={item.to}
            to={item.to}
            end={item.end}
            className={({ isActive }) =>
              cn(
                "flex flex-col items-center justify-center gap-1 font-bold",
                isActive ? "text-secondary-foreground" : "text-slate-500",
              )
            }
          >
            <item.icon className="h-5 w-5" />
            <span className="font-label text-[11px] font-semibold">{item.label}</span>
          </NavLink>
        ))}
      </nav>
    </div>
  );
}

export default function AppShell({ children }) {
  const navigate = useNavigate();
  const { user, logout } = useAuth();

  const navigation = useMemo(
    () => (user?.role === "admin" ? adminNavigation : operatorNavigation),
    [user?.role],
  );

  const handleLogout = async () => {
    await logout();
    navigate("/login");
  };

  if (user?.role === "admin") {
    return (
      <AdminLayout navigation={navigation} user={user} onLogout={handleLogout}>
        {children}
      </AdminLayout>
    );
  }

  return (
    <OperatorLayout navigation={navigation} user={user} onLogout={handleLogout}>
      {children}
    </OperatorLayout>
  );
}
