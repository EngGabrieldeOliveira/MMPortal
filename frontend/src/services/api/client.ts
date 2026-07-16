import axios from 'axios';
import type { AxiosInstance, AxiosError } from 'axios';
import type { ApiError } from '../../types/api';

// Create axios instance
const axiosInstance: AxiosInstance = axios.create({
  baseURL: import.meta.env.VITE_API_URL || 'http://localhost:8000/api',
  timeout: 30000,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
});

// Request interceptor
axiosInstance.interceptors.request.use(
  (config) => {
    // Add auth token if available
    const token = localStorage.getItem('auth_token');
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }

    // Add timestamp for cache busting if needed
    if (config.method === 'get') {
      config.params = {
        ...config.params,
        _t: Date.now(),
      };
    }

    return config;
  },
  (error) => Promise.reject(error)
);

// Response interceptor
axiosInstance.interceptors.response.use(
  (response) => {
    // Return the data from successful response
    return response.data;
  },
  (error: AxiosError) => {
    const apiError: ApiError = new Error(
      error.response?.statusText || 'Erro na requisição'
    );

    apiError.status = error.response?.status;
    apiError.code = error.code;
    apiError.data = error.response?.data;

    // Handle specific status codes
    if (error.response?.status === 401) {
      // Unauthorized - clear token and redirect to login
      localStorage.removeItem('auth_token');
      window.location.href = '/login';
    }

    if (error.response?.status === 403) {
      // Forbidden
      console.error('Acesso negado');
    }

    if (error.response?.status === 404) {
      // Not found
      apiError.message = 'Recurso não encontrado';
    }

    if (error.response && error.response.status >= 500) {
      // Server error
      apiError.message = 'Erro no servidor. Tente novamente mais tarde.';
    } else if (!error.response) {
      // Network error or no response
      apiError.message = 'Erro de conexão. Verifique sua internet.';
    }

    return Promise.reject(apiError);
  }
);

export default axiosInstance;
