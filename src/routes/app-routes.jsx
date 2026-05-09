import { lazy, Suspense } from "react";
import { Navigate, Route, Routes } from "react-router-dom";
import AppShell from "@/components/layout/app-shell";
import { PageLoader } from "@/components/shared/page-loader";
import { useAuth } from "@/providers/auth-provider";
import { RouteGuard } from "@/routes/route-guard";

const AdminDashboardPage = lazy(() => import("@/pages/admin/dashboard-page"));
const HistoryPage = lazy(() => import("@/pages/admin/history-page"));
const OperatorsPage = lazy(() => import("@/pages/admin/operators-page"));
const ReportsPage = lazy(() => import("@/pages/admin/reports-page"));
const SettingsPage = lazy(() => import("@/pages/admin/settings-page"));
const SubscribersPage = lazy(() => import("@/pages/admin/subscribers-page"));
const TariffsPage = lazy(() => import("@/pages/admin/tariffs-page"));
const LoginPage = lazy(() => import("@/pages/auth/login-page"));
const CashierPage = lazy(() => import("@/pages/operator/cashier-page"));
const IncidentsPage = lazy(() => import("@/pages/operator/incidents-page"));
const OperatorDashboardPage = lazy(() => import("@/pages/operator/operator-dashboard-page"));
const ShiftPage = lazy(() => import("@/pages/operator/shift-page"));

function HomeRedirect() {
  const { loading, user } = useAuth();

  if (loading) {
    return <PageLoader />;
  }

  if (!user) {
    return <Navigate to="/login" replace />;
  }

  return <Navigate to={user.role === "admin" ? "/app/admin" : "/app/operator"} replace />;
}

export default function AppRoutes() {
  return (
    <Suspense fallback={<PageLoader label="Chargement de l'interface..." />}>
      <Routes>
        <Route path="/" element={<HomeRedirect />} />
        <Route path="/login" element={<LoginPage />} />

        <Route
          path="/app/admin"
          element={
            <RouteGuard role="admin">
              <AppShell>
                <AdminDashboardPage />
              </AppShell>
            </RouteGuard>
          }
        />
        <Route
          path="/app/admin/tariffs"
          element={
            <RouteGuard role="admin">
              <AppShell>
                <TariffsPage />
              </AppShell>
            </RouteGuard>
          }
        />
        <Route
          path="/app/admin/subscribers"
          element={
            <RouteGuard role="admin">
              <AppShell>
                <SubscribersPage />
              </AppShell>
            </RouteGuard>
          }
        />
        <Route
          path="/app/admin/operators"
          element={
            <RouteGuard role="admin">
              <AppShell>
                <OperatorsPage />
              </AppShell>
            </RouteGuard>
          }
        />
        <Route
          path="/app/admin/history"
          element={
            <RouteGuard role="admin">
              <AppShell>
                <HistoryPage />
              </AppShell>
            </RouteGuard>
          }
        />
        <Route
          path="/app/admin/reports"
          element={
            <RouteGuard role="admin">
              <AppShell>
                <ReportsPage />
              </AppShell>
            </RouteGuard>
          }
        />
        <Route
          path="/app/admin/settings"
          element={
            <RouteGuard role="admin">
              <AppShell>
                <SettingsPage />
              </AppShell>
            </RouteGuard>
          }
        />

        <Route
          path="/app/operator"
          element={
            <RouteGuard role="operateur">
              <AppShell>
                <OperatorDashboardPage />
              </AppShell>
            </RouteGuard>
          }
        />
        <Route
          path="/app/operator/cashier"
          element={
            <RouteGuard role="operateur">
              <AppShell>
                <CashierPage />
              </AppShell>
            </RouteGuard>
          }
        />
        <Route
          path="/app/operator/incidents"
          element={
            <RouteGuard role="operateur">
              <AppShell>
                <IncidentsPage />
              </AppShell>
            </RouteGuard>
          }
        />
        <Route
          path="/app/operator/shift"
          element={
            <RouteGuard role="operateur">
              <AppShell>
                <ShiftPage />
              </AppShell>
            </RouteGuard>
          }
        />

        <Route path="*" element={<Navigate to="/" replace />} />
      </Routes>
    </Suspense>
  );
}
