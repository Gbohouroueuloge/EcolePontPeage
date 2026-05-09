import { motion } from "motion/react";
import { cn } from "@/lib/utils";

export function PageShell({ className, children }) {
  return (
    <motion.section
      initial={{ opacity: 0, y: 16 }}
      animate={{ opacity: 1, y: 0 }}
      exit={{ opacity: 0, y: -10 }}
      transition={{ duration: 0.28 }}
      className={cn("space-y-8", className)}
    >
      {children}
    </motion.section>
  );
}
