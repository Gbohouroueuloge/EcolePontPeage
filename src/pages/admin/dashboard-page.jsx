import {
  BellRing,
  Coins,
  CreditCard,
  ShieldCheck,
  TrafficCone,
  Users,
} from "lucide-react";
import { Badge } from "@/components/ui/badge";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table";
import { LaneTrafficChart } from "@/components/charts/lane-traffic-chart";
import { PaymentMixChart } from "@/components/charts/payment-mix-chart";
import { RevenueAreaChart } from "@/components/charts/revenue-area-chart";
import { PageLoader } from "@/components/shared/page-loader";
import { SectionHeading } from "@/components/shared/section-heading";
import { StatCard } from "@/components/shared/stat-card";
import { PageShell } from "@/components/layout/page-shell";
import { useApiData } from "@/hooks/use-api-data";
import { useDocumentTitle } from "@/hooks/use-document-title";
import { formatCurrency, formatDateTime } from "@/lib/utils";

export default function AdminDashboardPage() {
  useDocumentTitle("Dashboard Admin");

  const { data, loading, error } = useApiData("/api/dashboard/admin", {
    metrics: [],
    revenueTrend: [],
    laneTraffic: [],
    paymentMix: [],
    recentPassages: [],
    operators: [],
    alerts: [],
  });

  if (loading) {
    return <PageLoader label="Chargement du dashboard admin..." />;
  }

  if (error) {
    return <div className="rounded-2xl bg-danger/12 p-4 text-danger">{error}</div>;
  }

  const iconMap = [Coins, TrafficCone, Users, CreditCard];

  return (
    <PageShell>
      <SectionHeading
        kicker="Vue generale"
        title="Tableau de bord operationnel"
        description="Pilotage du pont, des voies et des revenus depuis une interface React temps reel."
      />

      <div className="grid gap-4 xl:grid-cols-4">
        {data.metrics.map((item, index) => (
          <StatCard
            key={item.label}
            icon={iconMap[index] || ShieldCheck}
            label={item.label}
            value={item.value}
            change={item.change}
            tone={item.tone}
          />
        ))}
      </div>

      <div className="grid gap-6 xl:grid-cols-[1.4fr_0.9fr]">
        <Card className="overflow-hidden">
          <CardHeader>
            <div>
              <CardTitle>Trafic et revenus sur 7 jours</CardTitle>
              <CardDescription>Projection quotidienne consolidee.</CardDescription>
            </div>
          </CardHeader>
          <RevenueAreaChart data={data.revenueTrend} />
        </Card>

        <Card>
          <CardHeader>
            <div>
              <CardTitle>Mix des paiements</CardTitle>
              <CardDescription>Repartition des passages et revenus par mode.</CardDescription>
            </div>
          </CardHeader>
          <PaymentMixChart data={data.paymentMix} />
        </Card>
      </div>

      <div className="grid gap-6 xl:grid-cols-[1.15fr_0.85fr]">
        <Card>
          <CardHeader>
            <div>
              <CardTitle>Performance par voie</CardTitle>
              <CardDescription>Comparaison passages / incidents par cabine.</CardDescription>
            </div>
          </CardHeader>
          <LaneTrafficChart data={data.laneTraffic} />
        </Card>

        <Card>
          <CardHeader>
            <div>
              <CardTitle>Alertes recentes</CardTitle>
              <CardDescription>Evenements a surveiller par la supervision.</CardDescription>
            </div>
          </CardHeader>
          <CardContent>
            {data.alerts.map((alert) => (
              <div key={alert.id} className="rounded-2xl border border-border bg-white/70 p-4">
                <div className="flex items-start justify-between gap-4">
                  <div>
                    <p className="font-semibold text-ink">{alert.title}</p>
                    <p className="mt-2 text-sm">{alert.description}</p>
                  </div>
                  <Badge
                    variant={
                      alert.severity === "high" ? "danger" : alert.severity === "medium" ? "warning" : "muted"
                    }
                  >
                    <BellRing className="h-3 w-3" />
                    {alert.severity}
                  </Badge>
                </div>
                <p className="mt-3 text-xs">{formatDateTime(alert.time)}</p>
              </div>
            ))}
          </CardContent>
        </Card>
      </div>

      <div className="grid gap-6 xl:grid-cols-[1.2fr_0.8fr]">
        <Card>
          <CardHeader>
            <div>
              <CardTitle>Derniers passages</CardTitle>
              <CardDescription>Suivi instantane de l'activite en cabine.</CardDescription>
            </div>
          </CardHeader>
          <div className="overflow-x-auto">
            <Table>
              <TableHeader>
                <TableRow>
                  <TableHead>Plaque</TableHead>
                  <TableHead>Voie</TableHead>
                  <TableHead>Operateur</TableHead>
                  <TableHead>Mode</TableHead>
                  <TableHead>Montant</TableHead>
                  <TableHead>Heure</TableHead>
                </TableRow>
              </TableHeader>
              <TableBody>
                {data.recentPassages.map((passage) => (
                  <TableRow key={passage.id}>
                    <TableCell className="font-semibold text-ink">{passage.plate}</TableCell>
                    <TableCell>{passage.booth}</TableCell>
                    <TableCell>{passage.operator}</TableCell>
                    <TableCell>{passage.payment_mode}</TableCell>
                    <TableCell>{formatCurrency(passage.amount)}</TableCell>
                    <TableCell>{formatDateTime(passage.created_at)}</TableCell>
                  </TableRow>
                ))}
              </TableBody>
            </Table>
          </div>
        </Card>

        <Card>
          <CardHeader>
            <div>
              <CardTitle>Charge des operateurs</CardTitle>
              <CardDescription>Activite et revenu individuel sur le service.</CardDescription>
            </div>
          </CardHeader>
          <CardContent>
            {data.operators.map((operator) => (
              <div key={operator.id} className="rounded-2xl border border-border bg-white/70 p-4">
                <div className="flex items-center justify-between">
                  <div>
                    <p className="font-semibold text-ink">{operator.name}</p>
                    <p className="mt-1 text-xs">{operator.booth}</p>
                  </div>
                  <Badge variant={operator.status === "Actif" ? "success" : "muted"}>{operator.status}</Badge>
                </div>
                <div className="mt-4 grid grid-cols-2 gap-3">
                  <div className="rounded-2xl bg-muted p-3">
                    <p className="text-xs uppercase tracking-[0.2em]">Passages</p>
                    <p className="mt-2 text-xl font-bold text-ink">{operator.passages_today}</p>
                  </div>
                  <div className="rounded-2xl bg-muted p-3">
                    <p className="text-xs uppercase tracking-[0.2em]">Revenu</p>
                    <p className="mt-2 text-xl font-bold text-ink">{formatCurrency(operator.revenue_today)}</p>
                  </div>
                </div>
              </div>
            ))}
          </CardContent>
        </Card>
      </div>
    </PageShell>
  );
}
