import { useState } from "react";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Select } from "@/components/ui/select";
import { Textarea } from "@/components/ui/textarea";
import { api } from "@/lib/api";

export function IncidentForm({ onSuccess }) {
  const [form, setForm] = useState({
    title: "",
    severity: "medium",
    description: "",
  });
  const [submitting, setSubmitting] = useState(false);
  const [message, setMessage] = useState("");
  const [error, setError] = useState("");

  const handleSubmit = async (event) => {
    event.preventDefault();
    setSubmitting(true);
    setMessage("");
    setError("");

    try {
      await api.post("/api/incidents", form);
      setForm({ title: "", severity: "medium", description: "" });
      setMessage("Incident signale.");
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
        <label className="text-sm font-semibold text-ink">Titre</label>
        <Input
          placeholder="Barriere lente"
          value={form.title}
          onChange={(event) => setForm((current) => ({ ...current, title: event.target.value }))}
        />
      </div>
      <div className="space-y-2">
        <label className="text-sm font-semibold text-ink">Severite</label>
        <Select
          value={form.severity}
          onChange={(event) => setForm((current) => ({ ...current, severity: event.target.value }))}
        >
          <option value="low">Faible</option>
          <option value="medium">Moyenne</option>
          <option value="high">Elevee</option>
        </Select>
      </div>
      <div className="space-y-2">
        <label className="text-sm font-semibold text-ink">Description</label>
        <Textarea
          placeholder="Preciser le contexte et l'impact sur la voie..."
          value={form.description}
          onChange={(event) => setForm((current) => ({ ...current, description: event.target.value }))}
        />
      </div>
      {message ? <div className="rounded-2xl bg-success/12 px-4 py-3 text-sm text-success">{message}</div> : null}
      {error ? <div className="rounded-2xl bg-danger/12 px-4 py-3 text-sm text-danger">{error}</div> : null}
      <Button className="w-full" disabled={submitting}>
        {submitting ? "Transmission..." : "Remonter l'incident"}
      </Button>
    </form>
  );
}
