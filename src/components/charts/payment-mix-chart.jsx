import { Cell, Pie, PieChart, ResponsiveContainer, Tooltip } from "recharts";
import { formatCurrency, formatNumber } from "@/lib/utils";

const COLORS = ["#0f4f74", "#f6b93b", "#19a974", "#ef4444"];

export function PaymentMixChart({ data = [] }) {
  return (
    <div className="grid gap-6 md:grid-cols-[260px_1fr] md:items-center">
      <div className="h-[240px] w-full">
        <ResponsiveContainer width="100%" height="100%">
          <PieChart>
            <Pie innerRadius={65} outerRadius={96} data={data} dataKey="value" nameKey="name" paddingAngle={3}>
              {data.map((entry, index) => (
                <Cell key={entry.name} fill={COLORS[index % COLORS.length]} />
              ))}
            </Pie>
            <Tooltip formatter={(value, _name, payload) => [`${formatNumber(value)} passages`, payload?.payload?.name]} />
          </PieChart>
        </ResponsiveContainer>
      </div>
      <div className="space-y-4">
        {data.map((item, index) => (
          <div key={item.name} className="flex items-center justify-between rounded-2xl bg-white/70 px-4 py-3">
            <div className="flex items-center gap-3">
              <span className="h-3 w-3 rounded-full" style={{ backgroundColor: COLORS[index % COLORS.length] }} />
              <div>
                <p className="text-sm font-semibold text-ink">{item.name}</p>
                <p className="text-xs">{formatCurrency(item.amount)}</p>
              </div>
            </div>
            <span className="text-sm font-bold text-ink">{formatNumber(item.value)}</span>
          </div>
        ))}
      </div>
    </div>
  );
}
