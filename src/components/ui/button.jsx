import { cva } from "class-variance-authority";
import { cn } from "@/lib/utils";

const buttonVariants = cva(
  "inline-flex items-center justify-center gap-2 rounded-lg font-label text-sm font-semibold transition-all duration-300 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 active:scale-[0.98]",
  {
    variants: {
      variant: {
        default:
          "gold-glow bg-primary text-primary-foreground hover:bg-[#0d1f3c]",
        secondary:
          "bg-secondary text-secondary-foreground shadow-[0_0_20px_rgba(201,144,26,0.18)] hover:bg-[#f5b029]",
        outline:
          "border border-primary/15 bg-white text-primary hover:bg-[#f8f3eb]",
        ghost: "text-foreground hover:bg-[#f2ede5]",
        danger: "bg-danger/15 text-danger hover:bg-danger hover:text-white",
      },
      size: {
        default: "h-11 px-5",
        sm: "h-9 px-3.5 text-xs",
        lg: "h-12 px-6 uppercase tracking-[0.2em]",
        icon: "h-10 w-10 rounded-full",
      },
    },
    defaultVariants: {
      variant: "default",
      size: "default",
    },
  },
);

export function Button({ className, variant, size, ...props }) {
  return <button className={cn(buttonVariants({ variant, size }), className)} {...props} />;
}
