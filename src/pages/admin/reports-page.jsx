import { Download, FileBarChart } from "lucide-react";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { LaneTrafficChart } from "@/components/charts/lane-traffic-chart";
import { PaymentMixChart } from "@/components/charts/payment-mix-chart";
import { RevenueAreaChart } from "@/components/charts/revenue-area-chart";
import { PageLoader } from "@/components/shared/page-loader";
import { SectionHeading } from "@/components/shared/section-heading";
import { PageShell } from "@/components/layout/page-shell";
import { useApiData } from "@/hooks/use-api-data";
import { useDocumentTitle } from "@/hooks/use-document-title";

function exportAsJson(data) {
  const blob = new Blob([JSON.stringify(data, null, 2)], { type: "application/json" });
  const url = URL.createObjectURL(blob);
  const link = document.createElement("a");
  link.href = url;
  link.download = "rapport-pont-peage.json";
  link.click();
  URL.revokeObjectURL(url);
}

export default function ReportsPage() {
  useDocumentTitle("Rapports");

  const { data, loading, error } = useApiData("/api/reports", {
    monthlyRevenue: [],
    vehicleBreakdown: [],
    boothPerformance: [],
    paymentMix: [],
  });

  if (loading) {
    return <PageLoader label="Generation des rapports..." />;
  }

  return (
    <PageShell>
      <SectionHeading
        kicker="Analyse"
        title="Rapports et tendances"
        description="Exports et visualisations pour la direction, la finance et l'exploitation."
        actions={
          <Button variant="secondary" onClick={() => exportAsJson(data)}>
            <Download className="h-4 w-4" />
            Exporter JSON
          </Button>
        }
      />

      {error ? <div className="rounded-2xl bg-danger/12 p-4 text-danger">{error}</div> : null}

      <div className="grid gap-6 xl:grid-cols-2">
        <Card>
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <FileBarChart className="h-5 w-5 text-primary" />
              Revenu mensuel
            </CardTitle>
          </CardHeader>
          <CardContent>
            <RevenueAreaChart data={data.monthlyRevenue} />
          </CardContent>
        </Card>

        <Card>
          <CardHeader>
            <CardTitle>Mix paiements</CardTitle>
          </CardHeader>
          <CardContent>
            <PaymentMixChart data={data.paymentMix} />
          </CardContent>
        </Card>
      </div>

      <div className="grid gap-6 xl:grid-cols-[1.15fr_0.85fr]">
        <Card>
          <CardHeader>
            <CardTitle>Performance des voies</CardTitle>
          </CardHeader>
          <CardContent>
            <LaneTrafficChart data={data.boothPerformance} />
          </CardContent>
        </Card>

        <Card>
          <CardHeader>
            <CardTitle>Repartition par categorie</CardTitle>
          </CardHeader>
          <CardContent className="space-y-3">
            {data.vehicleBreakdown.map((item) => (
              <div key={item.name} className="rounded-2xl bg-muted p-4">
                <div className="flex items-center justify-between">
                  <p className="font-semibold text-ink">{item.name}</p>
                  <p className="text-lg font-black text-ink">{item.value}</p>
                </div>
                <p className="mt-2 text-sm">{item.description}</p>
              </div>
            ))}
          </CardContent>
        </Card>
      </div>
    </PageShell>
  );
}
