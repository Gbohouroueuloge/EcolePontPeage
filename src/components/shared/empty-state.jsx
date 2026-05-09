import { Button } from "@/components/ui/button";

export function EmptyState({ title, description, actionLabel, onAction }) {
  return (
    <div className="glass-panel rounded-[28px] p-10 text-center">
      <p className="section-kicker mb-3">Zone vide</p>
      <h3 className="text-2xl font-bold">{title}</h3>
      <p className="mx-auto mt-3 max-w-xl text-sm">{description}</p>
      {actionLabel ? (
        <Button className="mt-6" onClick={onAction}>
          {actionLabel}
        </Button>
      ) : null}
    </div>
  );
}
