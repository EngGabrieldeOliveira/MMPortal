import { get, post } from '../api/request';
import type { AuthResponse, LoginCredentials, User } from '../../types/auth';

const AUTH_TOKEN_KEY = 'auth_token';
const AUTH_USER_KEY = 'auth_user';

export async function login(credentials: LoginCredentials): Promise<AuthResponse> {
  const response = await post<AuthResponse>('auth/login', credentials);
  localStorage.setItem(AUTH_TOKEN_KEY, response.token);
  localStorage.setItem(AUTH_USER_KEY, JSON.stringify(response.user));
  return response;
}

export async function logout(): Promise<void> {
  try { await post('auth/logout'); } finally { localStorage.removeItem(AUTH_TOKEN_KEY); localStorage.removeItem(AUTH_USER_KEY); }
}

export const getStoredToken = (): string | null => localStorage.getItem(AUTH_TOKEN_KEY);
export const getStoredUser = (): User | null => {
  const value = localStorage.getItem(AUTH_USER_KEY);
  return value ? JSON.parse(value) as User : null;
};

export async function checkAuth(): Promise<boolean> {
  if (!getStoredToken()) return false;
  try {
    const user = await get<User>('auth/me');
    localStorage.setItem(AUTH_USER_KEY, JSON.stringify(user));
    return true;
  } catch {
    localStorage.removeItem(AUTH_TOKEN_KEY);
    localStorage.removeItem(AUTH_USER_KEY);
    return false;
  }
}

export default { login, logout, getStoredToken, getStoredUser, checkAuth };
