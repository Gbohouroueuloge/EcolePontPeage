import { Activity, Clock3, UserCog } from "lucide-react";
import { useMemo, useState } from "react";
import { Badge } from "@/components/ui/badge";
import { Card } from "@/components/ui/card";
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table";
import { PageLoader } from "@/components/shared/page-loader";
import { SectionHeading } from "@/components/shared/section-heading";
import { PageShell } from "@/components/layout/page-shell";
import { useApiData } from "@/hooks/use-api-data";
import { useDocumentTitle } from "@/hooks/use-document-title";
import { formatCurrency, formatDateTime } from "@/lib/utils";

export default function OperatorsPage() {
  useDocumentTitle("Operateurs");

  const { data, loading, error } = useApiData("/api/operators", []);
  const [selectedId, setSelectedId] = useState(null);

  const selectedOperator = useMemo(
    () => data.find((item) => item.id === selectedId) || data[0],
    [data, selectedId],
  );

  if (loading) {
    return <PageLoader label="Chargement des operateurs..." />;
  }

  return (
    <PageShell>
      <SectionHeading
        kicker="Effectifs"
        title="Operateurs et prises de service"
        description="Vue detaillee des agents en cabine avec productivite, statut et activite recente."
      />

      {error ? <div className="rounded-2xl bg-danger/12 p-4 text-danger">{error}</div> : null}

      <div className="grid gap-6 xl:grid-cols-[0.8fr_1.2fr]">
        <Card className="space-y-3">
          {data.map((operator) => (
            <button
              key={operator.id}
              onClick={() => setSelectedId(operator.id)}
              className={`w-full rounded-[22px] border p-4 text-left transition ${
                selectedOperator?.id === operator.id
                  ? "border-primary bg-primary/6"
                  : "border-border bg-white/65 hover:bg-muted"
              }`}
            >
              <div className="flex items-center justify-between gap-4">
                <div>
                  <p className="font-semibold text-ink">{operator.name}</p>
                  <p className="mt-1 text-xs">{operator.booth}</p>
                </div>
                <Badge variant={operator.status === "Actif" ? "success" : "muted"}>{operator.status}</Badge>
              </div>
              <div className="mt-4 grid grid-cols-2 gap-3">
                <div className="rounded-2xl bg-white/80 p-3">
                  <p className="text-xs uppercase tracking-[0.2em]">Passages</p>
                  <p className="mt-2 text-xl font-bold text-ink">{operator.passages_today}</p>
                </div>
                <div className="rounded-2xl bg-white/80 p-3">
                  <p className="text-xs uppercase tracking-[0.2em]">Revenu</p>
                  <p className="mt-2 text-xl font-bold text-ink">{formatCurrency(operator.revenue_today)}</p>
                </div>
              </div>
            </button>
          ))}
        </Card>

        {selectedOperator ? (
          <Card className="space-y-6">
            <div className="grid gap-4 md:grid-cols-3">
              {[
                ["Statut", selectedOperator.status, UserCog],
                ["Debut", formatDateTime(selectedOperator.shift_start), Clock3],
                ["Revenu", formatCurrency(selectedOperator.revenue_today), Activity],
              ].map(([label, value, Icon]) => (
                <div key={label} className="rounded-[24px] bg-muted p-4">
                  <div className="flex items-center justify-between">
                    <p className="text-xs uppercase tracking-[0.22em]">{label}</p>
                    <Icon className="h-4 w-4 text-primary" />
                  </div>
                  <p className="mt-3 text-lg font-bold text-ink">{value}</p>
                </div>
              ))}
            </div>

            <div className="overflow-x-auto">
              <Table>
                <TableHeader>
                  <TableRow>
                    <TableHead>Plaque</TableHead>
                    <TableHead>Voie</TableHead>
                    <TableHead>Mode</TableHead>
                    <TableHead>Montant</TableHead>
                    <TableHead>Heure</TableHead>
                  </TableRow>
                </TableHeader>
                <TableBody>
                  {selectedOperator.recent_passages.map((passage) => (
                    <TableRow key={passage.id}>
                      <TableCell className="font-semibold text-ink">{passage.plate}</TableCell>
                      <TableCell>{passage.booth}</TableCell>
                      <TableCell>{passage.payment_mode}</TableCell>
                      <TableCell>{formatCurrency(passage.amount)}</TableCell>
                      <TableCell>{formatDateTime(passage.created_at)}</TableCell>
                    </TableRow>
                  ))}
                </TableBody>
              </Table>
            </div>
          </Card>
        ) : null}
      </div>
    </PageShell>
  );
}
