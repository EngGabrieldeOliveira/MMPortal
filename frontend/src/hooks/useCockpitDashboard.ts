import { useCallback, useEffect, useState } from 'react';
import type { CockpitData } from '../features/cockpit/types';
import { CockpitService } from '../services/cockpit/CockpitService';
import type { ApiError } from '../types/api';

const MAX_RETRIES = 2;
const RETRY_DELAY_MS = 900;

export function useCockpitDashboard() {
  const [data, setData] = useState<CockpitData | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<ApiError | null>(null);

  const load = useCallback(async (attempt = 0): Promise<void> => {
    setLoading(true);
    setError(null);

    try {
      setData(await CockpitService.obterDashboard());
      setLoading(false);
    } catch (caught) {
      const apiError = caught as ApiError;
      if (attempt < MAX_RETRIES) {
        window.setTimeout(() => { void load(attempt + 1); }, RETRY_DELAY_MS);
        return;
      }
      setError(apiError);
      setLoading(false);
    }
  }, []);

  useEffect(() => { void load(); }, [load]);

  const retry = useCallback(() => { void load(); }, [load]);

  return { data, loading, error, retry };
}
