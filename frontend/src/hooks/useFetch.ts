import { useState, useCallback } from 'react';
import { ApiError } from '../../types/api';

export interface UseFetchState<T> {
  data: T | null;
  loading: boolean;
  error: ApiError | null;
}

export interface UseFetchOptions {
  onSuccess?: (data: any) => void;
  onError?: (error: ApiError) => void;
}

export interface UseFetchReturn<T> extends UseFetchState<T> {
  execute: (requestFn: () => Promise<T>) => Promise<T | undefined>;
  reset: () => void;
}

/**
 * Hook para fazer fetch genérico com melhor controle
 */
export function useFetch<T = any>(
  options?: UseFetchOptions
): UseFetchReturn<T> {
  const [state, setState] = useState<UseFetchState<T>>({
    data: null,
    loading: false,
    error: null,
  });

  const execute = useCallback(
    async (requestFn: () => Promise<T>) => {
      try {
        setState({ data: null, loading: true, error: null });

        const result = await requestFn();

        setState({ data: result, loading: false, error: null });
        options?.onSuccess?.(result);

        return result;
      } catch (err) {
        const error = err as ApiError;
        setState({ data: null, loading: false, error });
        options?.onError?.(error);
      }
    },
    [options]
  );

  const reset = useCallback(() => {
    setState({ data: null, loading: false, error: null });
  }, []);

  return {
    ...state,
    execute,
    reset,
  };
}
