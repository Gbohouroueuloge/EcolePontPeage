import { Save, Settings2 } from "lucide-react";
import { useState } from "react";
import { Button } from "@/components/ui/button";
import { Card } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Select } from "@/components/ui/select";
import { PageLoader } from "@/components/shared/page-loader";
import { SectionHeading } from "@/components/shared/section-heading";
import { PageShell } from "@/components/layout/page-shell";
import { useApiData } from "@/hooks/use-api-data";
import { useDocumentTitle } from "@/hooks/use-document-title";
import { api } from "@/lib/api";

export default function SettingsPage() {
  useDocumentTitle("Parametres");

  const { data, loading, error, reload } = useApiData("/api/settings", {});
  const [form, setForm] = useState(null);
  const [saving, setSaving] = useState(false);
  const [message, setMessage] = useState("");

  const settings = form || data;

  const updateField = (field, value) => setForm((current) => ({ ...(current || data), [field]: value }));

  const handleSave = async () => {
    setSaving(true);
    setMessage("");
    try {
      await api.put("/api/settings", settings);
      setForm(null);
      setMessage("Parametres sauvegardes.");
      reload();
    } catch (err) {
      setMessage(err.message);
    } finally {
      setSaving(false);
    }
  };

  if (loading) {
    return <PageLoader label="Chargement des parametres..." />;
  }

  return (
    <PageShell>
      <SectionHeading
        kicker="Configuration"
        title="Parametres d'exploitation"
        description="Reglez les seuils, l'affichage et la cadence de supervision pour le pont."
        actions={
          <Button onClick={handleSave} disabled={saving}>
            <Save className="h-4 w-4" />
            {saving ? "Sauvegarde..." : "Sauvegarder"}
          </Button>
        }
      />

      {error ? <div className="rounded-2xl bg-danger/12 p-4 text-danger">{error}</div> : null}
      {message ? <div className="rounded-2xl bg-primary/10 p-4 text-primary">{message}</div> : null}

      <div className="grid gap-6 xl:grid-cols-2">
        <Card className="space-y-5">
          <div className="flex items-center gap-3">
            <Settings2 className="h-5 w-5 text-primary" />
            <h3 className="text-xl font-bold text-ink">Pilotage systeme</h3>
          </div>
          <div className="space-y-2">
            <label className="text-sm font-semibold text-ink">Nom du site</label>
            <Input value={settings.site_name || ""} onChange={(event) => updateField("site_name", event.target.value)} />
          </div>
          <div className="space-y-2">
            <label className="text-sm font-semibold text-ink">Devise</label>
            <Input value={settings.currency || ""} onChange={(event) => updateField("currency", event.target.value)} />
          </div>
          <div className="space-y-2">
            <label className="text-sm font-semibold text-ink">Fuseau horaire</label>
            <Input value={settings.timezone || ""} onChange={(event) => updateField("timezone", event.target.value)} />
          </div>
        </Card>

        <Card className="space-y-5">
          <div className="space-y-2">
            <label className="text-sm font-semibold text-ink">Mode d'affichage</label>
            <Select value={settings.dashboard_mode || ""} onChange={(event) => updateField("dashboard_mode", event.target.value)}>
              <option value="live">Live</option>
              <option value="balanced">Equilibre</option>
              <option value="audit">Audit</option>
            </Select>
          </div>
          <div className="space-y-2">
            <label className="text-sm font-semibold text-ink">Seuil d'alerte incident</label>
            <Input
              type="number"
              value={settings.alert_threshold || 0}
              onChange={(event) => updateField("alert_threshold", event.target.value)}
            />
          </div>
          <div className="space-y-2">
            <label className="text-sm font-semibold text-ink">Frequence de rafraichissement (sec)</label>
            <Input
              type="number"
              value={settings.refresh_interval || 0}
              onChange={(event) => updateField("refresh_interval", event.target.value)}
            />
          </div>
        </Card>
      </div>
    </PageShell>
  );
}
