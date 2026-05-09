import { cn } from "@/lib/utils";

export function SectionHeading({ kicker, title, description, actions, className }) {
  return (
    <div
      className={cn(
        "flex flex-col gap-5 rounded-[28px] border border-[#e7e2da] bg-white/80 px-6 py-6 shadow-[0_10px_30px_rgba(0,7,25,0.04)] lg:flex-row lg:items-end lg:justify-between",
        className,
      )}
    >
      <div className="space-y-3">
        {kicker ? <p className="section-kicker">{kicker}</p> : null}
        <div className="space-y-2">
          <h1 className="text-3xl font-black text-primary md:text-5xl">{title}</h1>
          {description ? <p className="max-w-3xl text-base leading-relaxed">{description}</p> : null}
        </div>
      </div>
      {actions ? <div className="flex flex-wrap gap-3">{actions}</div> : null}
    </div>
  );
}
