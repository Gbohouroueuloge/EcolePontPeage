import { Navigate, useLocation } from "react-router-dom";
import { PageLoader } from "@/components/shared/page-loader";
import { useAuth } from "@/providers/auth-provider";

export function RouteGuard({ children, role }) {
  const location = useLocation();
  const { user, loading } = useAuth();

  if (loading) {
    return <PageLoader label="Verification de la session..." />;
  }

  if (!user) {
    return <Navigate to="/login" state={{ from: location.pathname }} replace />;
  }

  if (role && user.role !== role) {
    return <Navigate to={user.role === "admin" ? "/app/admin" : "/app/operator"} replace />;
  }

  return children;
}
