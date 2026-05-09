import { useEffect, useState } from "react";
import { api } from "@/lib/api";

export function useApiData(path, initialValue = null) {
  const [data, setData] = useState(initialValue);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  const load = async () => {
    setLoading(true);
    setError(null);

    try {
      const payload = await api.get(path);
      setData(payload.data);
    } catch (err) {
      setError(err.message);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    load();
  }, [path]);

  return {
    data,
    loading,
    error,
    reload: load,
    setData,
  };
}
