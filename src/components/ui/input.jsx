import { cn } from "@/lib/utils";

export function Input({ className, ...props }) {
  return (
    <input
      className={cn(
        "flex h-12 w-full rounded-lg border-0 bg-[#f8f3eb] px-4 py-3 text-sm text-foreground outline-none ring-1 ring-[#c5c6ce]/40 transition-all placeholder:text-muted-foreground/80 focus:ring-2 focus:ring-secondary/40",
        className,
      )}
      {...props}
    />
  );
}
