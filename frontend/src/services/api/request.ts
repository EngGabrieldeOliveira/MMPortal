import axiosInstance from './client';
import type { ApiResponse, PaginatedResponse, ListParams } from '../../types/api';

/**
 * Generic GET request
 */
export async function get<T = any>(
  endpoint: string,
  params?: Record<string, any>
): Promise<T> {
  try {
    const response = await axiosInstance.get<ApiResponse<T>>(endpoint, {
      params,
    });
    return response.data as T;
  } catch (error) {
    throw error;
  }
}

/**
 * Generic POST request
 */
export async function post<T = any>(
  endpoint: string,
  data?: any,
  config?: any
): Promise<T> {
  try {
    const response = await axiosInstance.post<ApiResponse<T>>(
      endpoint,
      data,
      config
    );
    return response.data as T;
  } catch (error) {
    throw error;
  }
}

/**
 * Generic PUT request
 */
export async function put<T = any>(
  endpoint: string,
  data?: any,
  config?: any
): Promise<T> {
  try {
    const response = await axiosInstance.put<ApiResponse<T>>(
      endpoint,
      data,
      config
    );
    return response.data as T;
  } catch (error) {
    throw error;
  }
}

/**
 * Generic PATCH request
 */
export async function patch<T = any>(
  endpoint: string,
  data?: any,
  config?: any
): Promise<T> {
  try {
    const response = await axiosInstance.patch<ApiResponse<T>>(
      endpoint,
      data,
      config
    );
    return response.data as T;
  } catch (error) {
    throw error;
  }
}

/**
 * Generic DELETE request
 */
export async function remove<T = any>(
  endpoint: string,
  config?: any
): Promise<T> {
  try {
    const response = await axiosInstance.delete<ApiResponse<T>>(
      endpoint,
      config
    );
    return response.data as T;
  } catch (error) {
    throw error;
  }
}

/**
 * Get paginated list
 */
export async function getList<T = any>(
  endpoint: string,
  params?: ListParams
): Promise<PaginatedResponse<T>> {
  try {
    const queryParams = {
      page: params?.page || 1,
      per_page: params?.per_page || 15,
      ...(params?.search && { search: params.search }),
      ...(params?.sort && { sort: params.sort }),
      ...params?.filter,
    };

    const response = await axiosInstance.get<PaginatedResponse<T>>(
      endpoint,
      { params: queryParams }
    );
    return response.data;
  } catch (error) {
    throw error;
  }
}

/**
 * Get single resource by ID
 */
export async function getById<T = any>(
  endpoint: string,
  id: string | number
): Promise<T> {
  try {
    const response = await axiosInstance.get<ApiResponse<T>>(
      `${endpoint}/${id}`
    );
    return response.data as T;
  } catch (error) {
    throw error;
  }
}

/**
 * Create new resource
 */
export async function create<T = any>(
  endpoint: string,
  data: any
): Promise<T> {
  try {
    const response = await axiosInstance.post<ApiResponse<T>>(endpoint, data);
    return response.data as T;
  } catch (error) {
    throw error;
  }
}

/**
 * Update resource
 */
export async function update<T = any>(
  endpoint: string,
  id: string | number,
  data: any
): Promise<T> {
  try {
    const response = await axiosInstance.put<ApiResponse<T>>(
      `${endpoint}/${id}`,
      data
    );
    return response.data as T;
  } catch (error) {
    throw error;
  }
}

/**
 * Delete resource
 */
export async function destroy<T = any>(
  endpoint: string,
  id: string | number
): Promise<T> {
  try {
    const response = await axiosInstance.delete<ApiResponse<T>>(
      `${endpoint}/${id}`
    );
    return response.data as T;
  } catch (error) {
    throw error;
  }
}
