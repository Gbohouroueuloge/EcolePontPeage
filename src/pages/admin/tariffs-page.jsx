import { Save } from "lucide-react";
import { useState } from "react";
import { Button } from "@/components/ui/button";
import { Card } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { PageLoader } from "@/components/shared/page-loader";
import { SectionHeading } from "@/components/shared/section-heading";
import { PageShell } from "@/components/layout/page-shell";
import { useApiData } from "@/hooks/use-api-data";
import { useDocumentTitle } from "@/hooks/use-document-title";
import { api } from "@/lib/api";

export default function TariffsPage() {
  useDocumentTitle("Tarifs");

  const { data, loading, error, reload } = useApiData("/api/tariffs", []);
  const [saving, setSaving] = useState(false);
  const [message, setMessage] = useState("");
  const [form, setForm] = useState([]);

  const tariffs = form.length ? form : data || [];

  const handleChange = (id, field, value) => {
    const base = form.length ? form : data;
    setForm(base.map((item) => (item.id === id ? { ...item, [field]: value } : item)));
  };

  const handleSave = async () => {
    setSaving(true);
    setMessage("");

    try {
      await api.put("/api/tariffs", {
        items: tariffs.map((item) => ({
          id: item.id,
          label: item.label,
          description: item.description,
          price: Number(item.price),
        })),
      });
      setMessage("Grille tarifaire mise a jour.");
      setForm([]);
      reload();
    } catch (err) {
      setMessage(err.message);
    } finally {
      setSaving(false);
    }
  };

  if (loading) {
    return <PageLoader label="Chargement des tarifs..." />;
  }

  return (
    <PageShell>
      <SectionHeading
        kicker="Gestion tarifaire"
        title="Tarifs vehicules et classes de passage"
        description="Ajustez les prix, les libelles et les descriptifs utilises en caisse et dans les rapports."
        actions={
          <Button onClick={handleSave} disabled={saving}>
            <Save className="h-4 w-4" />
            {saving ? "Enregistrement..." : "Enregistrer"}
          </Button>
        }
      />

      {error ? <div className="rounded-2xl bg-danger/12 p-4 text-danger">{error}</div> : null}
      {message ? <div className="rounded-2xl bg-primary/10 p-4 text-primary">{message}</div> : null}

      <div className="grid gap-5 xl:grid-cols-2">
        {tariffs.map((tariff) => (
          <Card key={tariff.id} className="space-y-5">
            <div className="space-y-2">
              <label className="text-sm font-semibold text-ink">Libelle</label>
              <Input value={tariff.label} onChange={(event) => handleChange(tariff.id, "label", event.target.value)} />
            </div>
            <div className="space-y-2">
              <label className="text-sm font-semibold text-ink">Description</label>
              <Input
                value={tariff.description}
                onChange={(event) => handleChange(tariff.id, "description", event.target.value)}
              />
            </div>
            <div className="space-y-2">
              <label className="text-sm font-semibold text-ink">Prix (FCFA)</label>
              <Input
                type="number"
                value={tariff.price}
                onChange={(event) => handleChange(tariff.id, "price", event.target.value)}
              />
            </div>
          </Card>
        ))}
      </div>
    </PageShell>
  );
}
