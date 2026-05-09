import { RotateCcw, ShieldCheck, UserPlus } from "lucide-react";
import { useState } from "react";
import { Button } from "@/components/ui/button";
import { Card } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Select } from "@/components/ui/select";
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table";
import { PageLoader } from "@/components/shared/page-loader";
import { SectionHeading } from "@/components/shared/section-heading";
import { PageShell } from "@/components/layout/page-shell";
import { useApiData } from "@/hooks/use-api-data";
import { useDocumentTitle } from "@/hooks/use-document-title";
import { api } from "@/lib/api";
import { formatCurrency, formatDateTime } from "@/lib/utils";

const DEFAULT_FORM = {
  company: "",
  contact_name: "",
  plate: "",
  plan: "Mensuel",
};

export default function SubscribersPage() {
  useDocumentTitle("Abonnes");

  const { data, loading, error, reload } = useApiData("/api/subscribers", {
    summary: {},
    items: [],
  });
  const [form, setForm] = useState(DEFAULT_FORM);
  const [message, setMessage] = useState("");
  const [submitting, setSubmitting] = useState(false);

  const handleCreate = async (event) => {
    event.preventDefault();
    setSubmitting(true);
    setMessage("");

    try {
      await api.post("/api/subscribers", form);
      setForm(DEFAULT_FORM);
      setMessage("Abonnement ajoute.");
      reload();
    } catch (err) {
      setMessage(err.message);
    } finally {
      setSubmitting(false);
    }
  };

  const handleRenew = async (id) => {
    setMessage("");

    try {
      await api.patch(`/api/subscribers/${id}/renew`, {});
      setMessage("Abonnement renouvele.");
      reload();
    } catch (err) {
      setMessage(err.message);
    }
  };

  if (loading) {
    return <PageLoader label="Chargement des abonnes..." />;
  }

  return (
    <PageShell>
      <SectionHeading
        kicker="Transit recurrent"
        title="Abonnes et laissez-passer"
        description="Suivi des plaques sous contrat, des renouvellements critiques et des revenus recurrents."
      />

      {error ? <div className="rounded-2xl bg-danger/12 p-4 text-danger">{error}</div> : null}
      {message ? <div className="rounded-2xl bg-primary/10 p-4 text-primary">{message}</div> : null}

      <div className="grid gap-4 xl:grid-cols-4">
        {[
          ["Actifs", data.summary.active, ShieldCheck],
          ["Critiques", data.summary.critical, RotateCcw],
          ["Revenu mensuel", data.summary.monthly_revenue_formatted, ShieldCheck],
          ["Remise moyenne", `${data.summary.average_discount || 0}%`, UserPlus],
        ].map(([label, value, Icon]) => (
          <Card key={label} className="flex items-center justify-between">
            <div>
              <p className="text-xs uppercase tracking-[0.24em] text-muted-foreground">{label}</p>
              <p className="mt-3 text-2xl font-black text-ink">{value || 0}</p>
            </div>
            <div className="rounded-2xl bg-primary/10 p-3 text-primary">
              <Icon className="h-5 w-5" />
            </div>
          </Card>
        ))}
      </div>

      <div className="grid gap-6 xl:grid-cols-[0.7fr_1.3fr]">
        <Card>
          <h3 className="text-xl font-bold text-ink">Nouveau contrat</h3>
          <p className="mt-2 text-sm">Ajout rapide d'un abonne pour les passages repetes.</p>
          <form className="mt-6 space-y-4" onSubmit={handleCreate}>
            <Input
              placeholder="Entreprise"
              value={form.company}
              onChange={(event) => setForm((current) => ({ ...current, company: event.target.value }))}
            />
            <Input
              placeholder="Contact"
              value={form.contact_name}
              onChange={(event) => setForm((current) => ({ ...current, contact_name: event.target.value }))}
            />
            <Input
              placeholder="Plaque"
              value={form.plate}
              onChange={(event) => setForm((current) => ({ ...current, plate: event.target.value.toUpperCase() }))}
            />
            <Select value={form.plan} onChange={(event) => setForm((current) => ({ ...current, plan: event.target.value }))}>
              <option value="Mensuel">Mensuel</option>
              <option value="Trimestriel">Trimestriel</option>
              <option value="Annuel">Annuel</option>
            </Select>
            <Button className="w-full" disabled={submitting}>
              {submitting ? "Creation..." : "Ajouter l'abonne"}
            </Button>
          </form>
        </Card>

        <Card className="overflow-x-auto">
          <Table>
            <TableHeader>
              <TableRow>
                <TableHead>Plaque</TableHead>
                <TableHead>Entreprise</TableHead>
                <TableHead>Plan</TableHead>
                <TableHead>Expiration</TableHead>
                <TableHead>Mensualite</TableHead>
                <TableHead />
              </TableRow>
            </TableHeader>
            <TableBody>
              {data.items.map((item) => (
                <TableRow key={item.id}>
                  <TableCell className="font-semibold text-ink">{item.plate}</TableCell>
                  <TableCell>
                    <div>
                      <p className="font-medium text-ink">{item.company}</p>
                      <p className="text-xs">{item.contact_name}</p>
                    </div>
                  </TableCell>
                  <TableCell>{item.plan}</TableCell>
                  <TableCell>{formatDateTime(item.expires_at)}</TableCell>
                  <TableCell>{formatCurrency(item.monthly_fee)}</TableCell>
                  <TableCell className="text-right">
                    <Button size="sm" variant="outline" onClick={() => handleRenew(item.id)}>
                      Renouveler
                    </Button>
                  </TableCell>
                </TableRow>
              ))}
            </TableBody>
          </Table>
        </Card>
      </div>
    </PageShell>
  );
}
