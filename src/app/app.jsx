import { AnimatePresence } from "motion/react";
import { useLocation } from "react-router-dom";
import AppRoutes from "@/routes/app-routes";
import { AuthProvider } from "@/providers/auth-provider";

export default function App() {
  const location = useLocation();

  return (
    <AuthProvider>
      <AnimatePresence mode="wait">
        <AppRoutes key={location.pathname} />
      </AnimatePresence>
    </AuthProvider>
  );
}
