import { ArrowRight, LockKeyhole, ShieldCheck } from "lucide-react";
import { motion } from "motion/react";
import { useState } from "react";
import { useLocation, useNavigate } from "react-router-dom";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { useDocumentTitle } from "@/hooks/use-document-title";
import { useAuth } from "@/providers/auth-provider";

const DEMO_ACCOUNTS = [
  { role: "Admin", email: "admin@pontpeage.local", password: "admin123" },
  { role: "Operateur", email: "agent@pontpeage.local", password: "operator123" },
];

export default function LoginPage() {
  useDocumentTitle("Connexion");

  const navigate = useNavigate();
  const location = useLocation();
  const { login } = useAuth();
  const [form, setForm] = useState({
    email: "admin@pontpeage.local",
    password: "admin123",
  });
  const [error, setError] = useState("");
  const [submitting, setSubmitting] = useState(false);

  const handleSubmit = async (event) => {
    event.preventDefault();
    setSubmitting(true);
    setError("");

    try {
      const user = await login(form);
      const fallback = user.role === "admin" ? "/app/admin" : "/app/operator";
      navigate(location.state?.from || fallback, { replace: true });
    } catch (err) {
      setError(err.message);
    } finally {
      setSubmitting(false);
    }
  };

  return (
    <main className="flex min-h-screen flex-col lg:flex-row">
      <section className="relative flex w-full flex-col overflow-hidden bg-background px-8 py-10 lg:w-1/2 lg:justify-center lg:px-12 xl:px-24">
        <div className="absolute inset-y-0 right-0 hidden w-px bg-[#e7e2da] lg:block" />

        <motion.div
          initial={{ opacity: 0, y: 18 }}
          animate={{ opacity: 1, y: 0 }}
          className="relative z-10 mx-auto w-full max-w-md"
        >
          <div className="mb-10">
            <div className="mb-6 flex items-center gap-3 lg:hidden">
              <img className="h-11 w-11 rounded-xl" src="/icons/peage_bridge_logo_africain.svg" alt="Pont Peage" />
              <div>
                <p className="font-headline text-2xl font-black text-primary">Peage Bridge</p>
                <p className="text-xs text-muted-foreground">Votre passage simplifie</p>
              </div>
            </div>

            <h1 className="font-headline text-[32px] font-bold leading-tight text-primary">Bon retour</h1>
            <p className="mt-2 text-base text-muted-foreground">Connectez-vous a votre espace.</p>
          </div>

          <form className="space-y-6" onSubmit={handleSubmit}>
            <div className="space-y-2">
              <label className="font-label text-sm font-medium text-ink">Adresse e-mail</label>
              <Input
                type="email"
                placeholder="exemple@email.com"
                value={form.email}
                onChange={(event) => setForm((current) => ({ ...current, email: event.target.value }))}
                className={error ? "ring-2 ring-danger/50" : ""}
              />
            </div>

            <div className="space-y-2">
              <label className="font-label text-sm font-medium text-ink">Mot de passe</label>
              <Input
                type="password"
                placeholder="Votre mot de passe"
                value={form.password}
                onChange={(event) => setForm((current) => ({ ...current, password: event.target.value }))}
                className={error ? "ring-2 ring-danger/50" : ""}
              />
            </div>

            {error ? <div className="rounded-lg bg-danger/10 px-4 py-3 text-sm text-danger">{error}</div> : null}

            <Button className="w-full uppercase tracking-[0.24em]" size="lg" disabled={submitting}>
              {submitting ? "Connexion..." : "Se connecter"}
            </Button>
          </form>

          <div className="mt-8 flex justify-center">
            <p className="text-sm text-muted-foreground">
              Environnements de demo precharges pour l&apos;administration et l&apos;operateur.
            </p>
          </div>

          <div className="mt-10 flex items-center gap-4">
            <span className="h-px grow bg-[#e7e2da]" />
            <span className="font-mono text-[10px] uppercase tracking-widest text-muted-foreground">
              Precision architecturale
            </span>
            <span className="h-px grow bg-[#e7e2da]" />
          </div>
        </motion.div>
      </section>

      <section className="hero-mesh road-silhouette relative hidden w-1/2 overflow-hidden lg:flex lg:flex-col lg:items-center lg:justify-center lg:p-24">
        <div className="absolute inset-0 bg-gradient-to-br from-primary via-primary to-transparent" />

        <motion.div
          initial={{ opacity: 0, scale: 0.98 }}
          animate={{ opacity: 1, scale: 1 }}
          className="relative z-10 max-w-lg text-center"
        >
          <div className="mb-8 flex justify-center">
            <div className="gold-glow flex h-16 w-16 items-center justify-center rounded-xl bg-secondary/20">
              <img className="h-10 w-10 rounded-lg" src="/icons/peage_bridge_logo_africain.svg" alt="Pont Peage" />
            </div>
          </div>

          <h2 className="font-headline text-4xl font-light italic leading-tight text-white">
            Chaque passage compte.
            <br />
            <span className="font-bold text-secondary">Chaque trajet simplifie.</span>
          </h2>

          <div className="mt-12 grid grid-cols-3 gap-8 border-t border-white/10 pt-12">
            {[
              ["1.2M", "passages"],
              ["3", "voies"],
              ["24h/24", "disponible"],
            ].map(([value, label]) => (
              <div key={label} className="flex flex-col items-center">
                <span className="font-mono text-2xl text-secondary">{value}</span>
                <span className="mt-1 font-label text-[10px] uppercase tracking-widest text-white/60">{label}</span>
              </div>
            ))}
          </div>

          <div className="mt-12 grid gap-4 rounded-2xl border border-white/10 bg-white/5 p-5 backdrop-blur">
            {DEMO_ACCOUNTS.map((account) => (
              <div
                key={account.role}
                className="flex items-center justify-between rounded-xl border border-white/10 bg-black/10 px-4 py-3 text-left"
              >
                <div>
                  <p className="font-label text-[11px] uppercase tracking-[0.2em] text-secondary">{account.role}</p>
                  <p className="mt-2 text-sm text-white">{account.email}</p>
                </div>
                <div className="text-right">
                  <p className="font-mono text-xs text-white/70">{account.password}</p>
                  <ArrowRight className="ml-auto mt-2 h-4 w-4 text-white/45" />
                </div>
              </div>
            ))}
          </div>

          <div className="mt-10 rounded-2xl border border-white/10 bg-white/5 p-5 text-left backdrop-blur">
            <div className="flex items-center gap-3">
              <div className="rounded-lg bg-white/10 p-3 text-secondary">
                <LockKeyhole className="h-5 w-5" />
              </div>
              <div>
                <p className="font-label text-sm font-semibold text-white">Connexion securisee</p>
                <p className="mt-1 text-sm text-white/65">Acces unifie au back-office et au poste operateur.</p>
              </div>
            </div>
          </div>

          <div className="mt-6 flex items-center justify-center gap-3 text-white/70">
            <ShieldCheck className="h-5 w-5 text-secondary" />
            <p className="text-sm text-white/70">Backend PHP et React relies a la meme base metier.</p>
          </div>
        </motion.div>

        <div className="pointer-events-none absolute bottom-0 left-0 h-32 w-full opacity-10">
          <svg fill="none" height="100%" preserveAspectRatio="none" viewBox="0 0 800 200" width="100%">
            <path d="M0 150L200 120L400 150L600 110L800 140" stroke="white" strokeDasharray="20 20" strokeWidth="4" />
            <path d="M0 180L200 150L400 180L600 140L800 170" stroke="white" strokeDasharray="20 20" strokeWidth="4" />
          </svg>
        </div>
      </section>
    </main>
  );
}
