import { CheckCircle2, Clock3, Radar, Timer } from "lucide-react";
import { Card } from "@/components/ui/card";
import { PageLoader } from "@/components/shared/page-loader";
import { SectionHeading } from "@/components/shared/section-heading";
import { StatCard } from "@/components/shared/stat-card";
import { PageShell } from "@/components/layout/page-shell";
import { useApiData } from "@/hooks/use-api-data";
import { useDocumentTitle } from "@/hooks/use-document-title";
import { formatDateTime } from "@/lib/utils";

export default function ShiftPage() {
  useDocumentTitle("Prise de service");

  const { data, loading, error } = useApiData("/api/dashboard/operator", {
    booth: {},
    shift: {},
    incidents: [],
  });

  if (loading) {
    return <PageLoader label="Chargement du service..." />;
  }

  return (
    <PageShell>
      <SectionHeading
        kicker="Service"
        title="Suivi de prise de service"
        description="Etat du poste, checklist d'ouverture et progression de la vacation."
      />

      {error ? <div className="rounded-2xl bg-danger/12 p-4 text-danger">{error}</div> : null}

      <div className="grid gap-4 xl:grid-cols-4">
        <StatCard icon={Clock3} label="Debut" value={formatDateTime(data.shift.start_at)} tone="accent" />
        <StatCard icon={Timer} label="Objectif du service" value={data.shift.target_formatted} tone="secondary" />
        <StatCard icon={Radar} label="Voie" value={data.booth.lane_code} tone="primary" />
        <StatCard icon={CheckCircle2} label="Etat" value={data.booth.status} tone="success" />
      </div>

      <div className="grid gap-6 xl:grid-cols-2">
        <Card className="space-y-4">
          <h3 className="text-xl font-bold text-ink">Checklist d'ouverture</h3>
          {[
            "Capteur de voie verifie",
            "Camera frontale operationnelle",
            "TPE et lecteur badge disponibles",
            "Barriere testee avant ouverture",
          ].map((item) => (
            <div key={item} className="flex items-center gap-3 rounded-2xl bg-muted px-4 py-3">
              <CheckCircle2 className="h-5 w-5 text-success" />
              <span className="text-sm font-medium text-ink">{item}</span>
            </div>
          ))}
        </Card>

        <Card className="space-y-4">
          <h3 className="text-xl font-bold text-ink">Consignes de service</h3>
          {[
            "Prioriser la fluidite sur les voies Nord en heure de pointe.",
            "Remonter tout ralentissement superieur a 3 minutes.",
            "Verifier manuellement les abonnements au moindre doute sur la plaque.",
            "Conserver une trace des incidents techniques dans le module dedie.",
          ].map((item) => (
            <div key={item} className="rounded-2xl border border-border bg-white/70 p-4">
              <p className="text-sm font-medium text-ink">{item}</p>
            </div>
          ))}
        </Card>
      </div>
    </PageShell>
  );
}
