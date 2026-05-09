import { createContext, useContext, useEffect, useState } from "react";
import { api } from "@/lib/api";

const AuthContext = createContext(null);

export function AuthProvider({ children }) {
  const [user, setUser] = useState(null);
  const [loading, setLoading] = useState(true);

  const refresh = async () => {
    const token = window.localStorage.getItem("pontpeage_token");
    if (!token) {
      setLoading(false);
      setUser(null);
      return;
    }

    try {
      const response = await api.get("/api/auth/me");
      setUser(response.data.user);
    } catch {
      window.localStorage.removeItem("pontpeage_token");
      setUser(null);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    refresh();
  }, []);

  const login = async (credentials) => {
    const response = await api.post("/api/auth/login", credentials);
    window.localStorage.setItem("pontpeage_token", response.data.token);
    setUser(response.data.user);
    return response.data.user;
  };

  const logout = async () => {
    try {
      await api.post("/api/auth/logout", {});
    } catch {
      // noop
    } finally {
      window.localStorage.removeItem("pontpeage_token");
      setUser(null);
    }
  };

  return (
    <AuthContext.Provider
      value={{
        user,
        loading,
        refresh,
        login,
        logout,
        isAuthenticated: Boolean(user),
      }}
    >
      {children}
    </AuthContext.Provider>
  );
}

export function useAuth() {
  const context = useContext(AuthContext);

  if (!context) {
    throw new Error("useAuth must be used inside AuthProvider");
  }

  return context;
}
