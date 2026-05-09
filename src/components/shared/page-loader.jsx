export function PageLoader({ label = "Chargement des donnees..." }) {
  return (
    <div className="flex min-h-[40vh] items-center justify-center">
      <div className="glass-panel flex items-center gap-3 rounded-xl px-5 py-4">
        <span className="status-dot animate-pulse bg-secondary" />
        <span className="text-sm font-medium text-ink">{label}</span>
      </div>
    </div>
  );
}
