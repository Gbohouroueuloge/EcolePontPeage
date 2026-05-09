import { ArrowUpRight } from "lucide-react";
import { motion } from "motion/react";
import { Card } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { cn } from "@/lib/utils";

export function StatCard({ icon: Icon, label, value, change, tone = "primary" }) {
  const toneMap = {
    primary: "border-secondary text-primary",
    accent: "border-sky text-sky",
    secondary: "border-secondary text-secondary-foreground",
    success: "border-success text-success",
    danger: "border-danger text-danger",
  };

  return (
    <motion.div initial={{ opacity: 0, y: 12 }} animate={{ opacity: 1, y: 0 }}>
      <Card className={cn("border-l-4", toneMap[tone])}>
        <div className="flex items-start justify-between gap-4">
          <div>
            <p className="font-mono text-[11px] font-bold uppercase tracking-[0.28em] text-muted-foreground">
              {label}
            </p>
            <h3 className="mt-4 text-3xl font-black text-primary">{value}</h3>
          </div>
          <div className="rounded-lg bg-[#f8f3eb] p-3 shadow-sm">
            <Icon className="h-5 w-5" />
          </div>
        </div>
        {change ? (
          <div className="mt-6 flex items-center gap-2">
            <Badge variant={tone === "danger" ? "danger" : "success"}>
              <ArrowUpRight className="h-3 w-3" />
              {change}
            </Badge>
            <span className="text-xs text-muted-foreground">vs hier a la meme heure</span>
          </div>
        ) : null}
      </Card>
    </motion.div>
  );
}
