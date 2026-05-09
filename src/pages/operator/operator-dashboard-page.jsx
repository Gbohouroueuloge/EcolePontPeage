import { CircleDollarSign, Clock3, ShieldAlert, TrafficCone } from "lucide-react";
import { Badge } from "@/components/ui/badge";
import { Card } from "@/components/ui/card";
import { PassageForm } from "@/components/forms/passage-form";
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table";
import { PageLoader } from "@/components/shared/page-loader";
import { SectionHeading } from "@/components/shared/section-heading";
import { StatCard } from "@/components/shared/stat-card";
import { PageShell } from "@/components/layout/page-shell";
import { useApiData } from "@/hooks/use-api-data";
import { useDocumentTitle } from "@/hooks/use-document-title";
import { formatCurrency, formatDateTime } from "@/lib/utils";

export default function OperatorDashboardPage() {
  useDocumentTitle("Poste Operateur");

  const { data, loading, error, reload } = useApiData("/api/dashboard/operator", {
    booth: {},
    shift: {},
    kpis: [],
    tariffs: [],
    recentPassages: [],
    incidents: [],
  });

  if (loading) {
    return <PageLoader label="Chargement du poste operateur..." />;
  }

  return (
    <PageShell>
      <SectionHeading
        kicker="Cabine"
        title={`Poste ${data.booth?.lane_code || ""} - ${data.booth?.name || "non assigne"}`}
        description="Enregistrez les passages, suivez votre service et gardez un oeil sur les incidents locaux."
      />

      {error ? <div className="rounded-2xl bg-danger/12 p-4 text-danger">{error}</div> : null}

      <div className="grid gap-4 xl:grid-cols-4">
        <StatCard icon={TrafficCone} label="Passages du service" value={data.shift.passages} tone="primary" />
        <StatCard icon={CircleDollarSign} label="Encaisse du service" value={data.shift.collected_formatted} tone="secondary" />
        <StatCard icon={Clock3} label="Debut" value={formatDateTime(data.shift.start_at)} tone="accent" />
        <StatCard icon={ShieldAlert} label="Incidents ouverts" value={data.incidents.length} tone="danger" />
      </div>

      <div className="grid gap-6 xl:grid-cols-[0.7fr_1.3fr]">
        <Card>
          <div className="mb-5 flex items-center justify-between">
            <div>
              <h3 className="text-xl font-bold text-ink">Nouveau passage</h3>
              <p className="mt-2 text-sm">Validation manuelle d'un vehicule en cabine.</p>
            </div>
            <Badge variant={data.booth.status === "Ouverte" ? "success" : "warning"}>{data.booth.status}</Badge>
          </div>
          <PassageForm tariffs={data.tariffs} onSuccess={reload} />
        </Card>

        <Card className="overflow-x-auto">
          <div className="mb-5">
            <h3 className="text-xl font-bold text-ink">Derniers passages du poste</h3>
            <p className="mt-2 text-sm">Historique recent sur votre voie.</p>
          </div>
          <Table>
            <TableHeader>
              <TableRow>
                <TableHead>Plaque</TableHead>
                <TableHead>Categorie</TableHead>
                <TableHead>Mode</TableHead>
                <TableHead>Montant</TableHead>
                <TableHead>Heure</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              {data.recentPassages.map((item) => (
                <TableRow key={item.id}>
                  <TableCell className="font-semibold text-ink">{item.plate}</TableCell>
                  <TableCell>{item.tariff_label}</TableCell>
                  <TableCell>{item.payment_mode}</TableCell>
                  <TableCell>{formatCurrency(item.amount)}</TableCell>
                  <TableCell>{formatDateTime(item.created_at)}</TableCell>
                </TableRow>
              ))}
            </TableBody>
          </Table>
        </Card>
      </div>
    </PageShell>
  );
}
