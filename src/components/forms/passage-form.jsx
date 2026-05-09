import { useState } from "react";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Select } from "@/components/ui/select";
import { api } from "@/lib/api";

const DEFAULT_FORM = {
  plate: "",
  tariff_id: "",
  payment_mode: "Carte",
};

export function PassageForm({ tariffs = [], defaultMode = "Carte", onSuccess }) {
  const [form, setForm] = useState({ ...DEFAULT_FORM, payment_mode: defaultMode });
  const [submitting, setSubmitting] = useState(false);
  const [message, setMessage] = useState("");
  const [error, setError] = useState("");

  const handleSubmit = async (event) => {
    event.preventDefault();
    setSubmitting(true);
    setMessage("");
    setError("");

    try {
      await api.post("/api/passages", form);
      setForm({ ...DEFAULT_FORM, payment_mode: defaultMode });
      setMessage("Passage enregistre avec succes.");
      onSuccess?.();
    } catch (err) {
      setError(err.message);
    } finally {
      setSubmitting(false);
    }
  };

  return (
    <form className="space-y-4" onSubmit={handleSubmit}>
      <div className="space-y-2">
        <label className="text-sm font-semibold text-ink">Immatriculation</label>
        <Input
          placeholder="AA-123-BB"
          value={form.plate}
          onChange={(event) => setForm((current) => ({ ...current, plate: event.target.value.toUpperCase() }))}
        />
      </div>

      <div className="space-y-2">
        <label className="text-sm font-semibold text-ink">Categorie</label>
        <Select
          value={form.tariff_id}
          onChange={(event) => setForm((current) => ({ ...current, tariff_id: event.target.value }))}
        >
          <option value="">Selectionner une categorie</option>
          {tariffs.map((tariff) => (
            <option key={tariff.id} value={tariff.id}>
              {tariff.label} - {tariff.price_formatted}
            </option>
          ))}
        </Select>
      </div>

      <div className="space-y-2">
        <label className="text-sm font-semibold text-ink">Mode de paiement</label>
        <Select
          value={form.payment_mode}
          onChange={(event) => setForm((current) => ({ ...current, payment_mode: event.target.value }))}
        >
          <option value="Carte">Carte</option>
          <option value="Especes">Especes</option>
          <option value="Mobile Money">Mobile Money</option>
          <option value="Abonnement">Abonnement</option>
        </Select>
      </div>

      {message ? <div className="rounded-2xl bg-success/12 px-4 py-3 text-sm text-success">{message}</div> : null}
      {error ? <div className="rounded-2xl bg-danger/12 px-4 py-3 text-sm text-danger">{error}</div> : null}

      <Button className="w-full" disabled={submitting}>
        {submitting ? "Validation..." : "Valider le passage"}
      </Button>
    </form>
  );
}
