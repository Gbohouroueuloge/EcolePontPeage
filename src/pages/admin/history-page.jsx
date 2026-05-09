import { History } from "lucide-react";
import { Badge } from "@/components/ui/badge";
import { Card } from "@/components/ui/card";
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table";
import { PageLoader } from "@/components/shared/page-loader";
import { SectionHeading } from "@/components/shared/section-heading";
import { PageShell } from "@/components/layout/page-shell";
import { useApiData } from "@/hooks/use-api-data";
import { useDocumentTitle } from "@/hooks/use-document-title";
import { formatCurrency, formatDateTime } from "@/lib/utils";

export default function HistoryPage() {
  useDocumentTitle("Historiques");

  const { data, loading, error } = useApiData("/api/history", {
    summary: {},
    items: [],
  });

  if (loading) {
    return <PageLoader label="Chargement des historiques..." />;
  }

  return (
    <PageShell>
      <SectionHeading
        kicker="Journal d'activite"
        title="Historique des passages"
        description="Trace detaillee des transactions, des cabines et des vehicules recents."
      />

      {error ? <div className="rounded-2xl bg-danger/12 p-4 text-danger">{error}</div> : null}

      <div className="grid gap-4 xl:grid-cols-3">
        {[
          ["Passages", data.summary.total_passages],
          ["Revenu cumule", data.summary.total_revenue_formatted],
          ["Modes actifs", data.summary.payment_modes],
        ].map(([label, value]) => (
          <Card key={label} className="flex items-center justify-between">
            <div>
              <p className="text-xs uppercase tracking-[0.24em] text-muted-foreground">{label}</p>
              <p className="mt-3 text-2xl font-black text-ink">{value}</p>
            </div>
            <History className="h-5 w-5 text-primary" />
          </Card>
        ))}
      </div>

      <Card className="overflow-x-auto">
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead>Plaque</TableHead>
              <TableHead>Vehicule</TableHead>
              <TableHead>Voie</TableHead>
              <TableHead>Operateur</TableHead>
              <TableHead>Mode</TableHead>
              <TableHead>Montant</TableHead>
              <TableHead>Statut</TableHead>
              <TableHead>Date</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            {data.items.map((item) => (
              <TableRow key={item.id}>
                <TableCell className="font-semibold text-ink">{item.plate}</TableCell>
                <TableCell>{item.tariff_label}</TableCell>
                <TableCell>{item.booth}</TableCell>
                <TableCell>{item.operator}</TableCell>
                <TableCell>{item.payment_mode}</TableCell>
                <TableCell>{formatCurrency(item.amount)}</TableCell>
                <TableCell>
                  <Badge variant={item.status === "Valide" ? "success" : "warning"}>{item.status}</Badge>
                </TableCell>
                <TableCell>{formatDateTime(item.created_at)}</TableCell>
              </TableRow>
            ))}
          </TableBody>
        </Table>
      </Card>
    </PageShell>
  );
}
