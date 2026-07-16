import { useState, useCallback, useRef } from 'react';
import { ApiError } from '../../types/api';

export interface UseApiState<T> {
  data: T | null;
  loading: boolean;
  error: ApiError | null;
}

export interface UseApiOptions {
  onSuccess?: (data: any) => void;
  onError?: (error: ApiError) => void;
  immediate?: boolean;
}

export interface UseApiReturn<T> extends UseApiState<T> {
  refetch: (newParams?: any) => Promise<void>;
  reset: () => void;
}

/**
 * Hook para fazer requisições e gerenciar estado
 * @param requestFn - Função que faz a requisição
 * @param options - Opções do hook
 */
export function useApi<T = any>(
  requestFn: () => Promise<T>,
  options?: UseApiOptions
): UseApiReturn<T> {
  const [state, setState] = useState<UseApiState<T>>({
    data: null,
    loading: false,
    error: null,
  });

  const abortControllerRef = useRef<AbortController | null>(null);

  const execute = useCallback(async () => {
    try {
      abortControllerRef.current?.abort();
      abortControllerRef.current = new AbortController();

      setState({ data: null, loading: true, error: null });

      const result = await requestFn();

      setState({ data: result, loading: false, error: null });
      options?.onSuccess?.(result);

      return result;
    } catch (err) {
      const error = err as ApiError;
      if (error.message !== 'The operation was aborted') {
        setState({ data: null, loading: false, error });
        options?.onError?.(error);
      }
    }
  }, [requestFn, options]);

  const refetch = useCallback(
    async (newParams?: any) => {
      await execute();
    },
    [execute]
  );

  const reset = useCallback(() => {
    setState({ data: null, loading: false, error: null });
  }, []);

  // Execute on mount if immediate is true
  if (options?.immediate && !state.loading && !state.data) {
    execute();
  }

  return {
    ...state,
    refetch,
    reset,
  };
}
