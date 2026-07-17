import type { AxiosRequestConfig, AxiosResponse } from 'axios';
import axiosInstance from './client';
import type { ApiResponse, ListParams, PaginatedResponse } from '../../types/api';

function unwrap<T>(response: ApiResponse<T>): T { return response.data; }
type EnvelopeResponse<T> = AxiosResponse<ApiResponse<T>>;

export async function get<T = unknown>(endpoint: string, params?: Record<string, unknown>): Promise<T> {
  const response = await axiosInstance.get<ApiResponse<T>, EnvelopeResponse<T>>(endpoint, { params });
  return unwrap(response.data);
}

export async function post<T = unknown>(endpoint: string, data?: unknown, config?: AxiosRequestConfig): Promise<T> {
  const response = await axiosInstance.post<ApiResponse<T>, EnvelopeResponse<T>>(endpoint, data, config);
  return unwrap(response.data);
}

export async function put<T = unknown>(endpoint: string, data?: unknown, config?: AxiosRequestConfig): Promise<T> {
  const response = await axiosInstance.put<ApiResponse<T>, EnvelopeResponse<T>>(endpoint, data, config);
  return unwrap(response.data);
}

export async function patch<T = unknown>(endpoint: string, data?: unknown, config?: AxiosRequestConfig): Promise<T> {
  const response = await axiosInstance.patch<ApiResponse<T>, EnvelopeResponse<T>>(endpoint, data, config);
  return unwrap(response.data);
}

export async function remove<T = unknown>(endpoint: string, config?: AxiosRequestConfig): Promise<T> {
  const response = await axiosInstance.delete<ApiResponse<T>, EnvelopeResponse<T>>(endpoint, config);
  return unwrap(response.data);
}

export async function getList<T = unknown>(endpoint: string, params?: ListParams): Promise<PaginatedResponse<T>> {
  const queryParams = { page: params?.page || 1, per_page: params?.per_page || 15, ...(params?.search && { search: params.search }), ...(params?.sort && { sort: params.sort }), ...params?.filter };
  const response = await axiosInstance.get<ApiResponse<PaginatedResponse<T>>, EnvelopeResponse<PaginatedResponse<T>>>(endpoint, { params: queryParams });
  return unwrap(response.data);
}

export const getById = <T = unknown>(endpoint: string, id: string | number): Promise<T> => get<T>(`${endpoint}/${id}`);
export const create = <T = unknown>(endpoint: string, data: unknown): Promise<T> => post<T>(endpoint, data);
export const update = <T = unknown>(endpoint: string, id: string | number, data: unknown): Promise<T> => put<T>(`${endpoint}/${id}`, data);
export const destroy = <T = unknown>(endpoint: string, id: string | number): Promise<T> => remove<T>(`${endpoint}/${id}`);
