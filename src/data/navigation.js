import {
  BarChart3,
  BellRing,
  CreditCard,
  Gauge,
  ReceiptText,
  Settings,
  ShieldCheck,
  Users,
  Wallet,
} from "lucide-react";

export const adminNavigation = [
  { to: "/app/admin", label: "Dashboard", icon: Gauge, end: true, section: "Vue generale" },
  { to: "/app/admin/history", label: "Historiques", icon: ReceiptText, section: "Vue generale" },
  { to: "/app/admin/tariffs", label: "Tarifs", icon: Wallet, section: "Gestion" },
  { to: "/app/admin/subscribers", label: "Abonnes", icon: ShieldCheck, section: "Gestion" },
  { to: "/app/admin/operators", label: "Operateurs", icon: Users, section: "Systeme" },
  { to: "/app/admin/reports", label: "Rapports", icon: BarChart3, section: "Systeme" },
  { to: "/app/admin/settings", label: "Parametres", icon: Settings, section: "Systeme" },
];

export const operatorNavigation = [
  { to: "/app/operator", label: "Passage", icon: Gauge, end: true },
  { to: "/app/operator/cashier", label: "Caisse", icon: CreditCard },
  { to: "/app/operator/incidents", label: "Incident", icon: BellRing },
  { to: "/app/operator/shift", label: "Mon service", icon: Users },
];
