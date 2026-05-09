import { Coins, Landmark } from "lucide-react";
import { Badge } from "@/components/ui/badge";
import { Card } from "@/components/ui/card";
import { PassageForm } from "@/components/forms/passage-form";
import { PageLoader } from "@/components/shared/page-loader";
import { SectionHeading } from "@/components/shared/section-heading";
import { PageShell } from "@/components/layout/page-shell";
import { useApiData } from "@/hooks/use-api-data";
import { useDocumentTitle } from "@/hooks/use-document-title";

export default function CashierPage() {
  useDocumentTitle("Caisse");

  const { data, loading, error, reload } = useApiData("/api/dashboard/operator", {
    booth: {},
    tariffs: [],
    shift: {},
  });

  if (loading) {
    return <PageLoader label="Chargement de la caisse..." />;
  }

  return (
    <PageShell>
      <SectionHeading
        kicker="Encaissement"
        title="Interface caisse rapide"
        description="Version simplifiee pour enregistrer un paiement sans quitter le poste."
      />

      {error ? <div className="rounded-2xl bg-danger/12 p-4 text-danger">{error}</div> : null}

      <div className="grid gap-6 xl:grid-cols-[0.9fr_1.1fr]">
        <Card className="hero-mesh text-white">
          <div className="flex items-center justify-between">
            <div>
              <p className="text-xs uppercase tracking-[0.3em] text-white/55">Voie active</p>
              <h3 className="mt-3 text-3xl font-black text-white">{data.booth?.lane_code}</h3>
            </div>
            <Badge variant="warning" className="bg-white/10 text-white">
              {data.booth?.status}
            </Badge>
          </div>
          <div className="mt-8 grid gap-4 md:grid-cols-2">
            <div className="rounded-[26px] border border-white/10 bg-white/10 p-5">
              <div className="flex items-center gap-3 text-secondary">
                <Landmark className="h-5 w-5" />
                <span className="text-sm font-semibold text-white">Revenu du service</span>
              </div>
              <p className="mt-4 text-3xl font-black text-white">{data.shift.collected_formatted}</p>
            </div>
            <div className="rounded-[26px] border border-white/10 bg-white/10 p-5">
              <div className="flex items-center gap-3 text-secondary">
                <Coins className="h-5 w-5" />
                <span className="text-sm font-semibold text-white">Objectif</span>
              </div>
              <p className="mt-4 text-3xl font-black text-white">{data.shift.target_formatted}</p>
            </div>
          </div>
        </Card>

        <Card>
          <h3 className="text-xl font-bold text-ink">Validation express</h3>
          <p className="mt-2 text-sm">Formulaire oriente caisse, ideal pour carte et especes.</p>
          <div className="mt-6">
            <PassageForm tariffs={data.tariffs} defaultMode="Especes" onSuccess={reload} />
          </div>
        </Card>
      </div>
    </PageShell>
  );
}
