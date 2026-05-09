import { Badge } from "@/components/ui/badge";
import { Card } from "@/components/ui/card";
import { IncidentForm } from "@/components/forms/incident-form";
import { PageLoader } from "@/components/shared/page-loader";
import { SectionHeading } from "@/components/shared/section-heading";
import { PageShell } from "@/components/layout/page-shell";
import { useApiData } from "@/hooks/use-api-data";
import { useDocumentTitle } from "@/hooks/use-document-title";
import { formatDateTime } from "@/lib/utils";

export default function IncidentsPage() {
  useDocumentTitle("Incidents");

  const { data, loading, error, reload } = useApiData("/api/incidents", []);

  if (loading) {
    return <PageLoader label="Chargement des incidents..." />;
  }

  return (
    <PageShell>
      <SectionHeading
        kicker="Surete"
        title="Incidents et signalements"
        description="Remontez un incident terrain et consultez les evenements deja ouverts."
      />

      {error ? <div className="rounded-2xl bg-danger/12 p-4 text-danger">{error}</div> : null}

      <div className="grid gap-6 xl:grid-cols-[0.7fr_1.3fr]">
        <Card>
          <h3 className="text-xl font-bold text-ink">Nouveau signalement</h3>
          <p className="mt-2 text-sm">Transmis au centre de supervision immediatement.</p>
          <div className="mt-6">
            <IncidentForm onSuccess={reload} />
          </div>
        </Card>

        <div className="space-y-4">
          {data.map((incident) => (
            <Card key={incident.id}>
              <div className="flex items-start justify-between gap-4">
                <div>
                  <h3 className="text-lg font-bold text-ink">{incident.title}</h3>
                  <p className="mt-2 text-sm">{incident.description}</p>
                </div>
                <Badge
                  variant={
                    incident.severity === "high"
                      ? "danger"
                      : incident.severity === "medium"
                        ? "warning"
                        : "muted"
                  }
                >
                  {incident.severity}
                </Badge>
              </div>
              <div className="mt-5 flex flex-wrap gap-3 text-xs text-muted-foreground">
                <span>{incident.booth}</span>
                <span>{incident.operator}</span>
                <span>{formatDateTime(incident.reported_at)}</span>
              </div>
            </Card>
          ))}
        </div>
      </div>
    </PageShell>
  );
}
