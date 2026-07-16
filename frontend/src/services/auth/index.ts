import { post } from '../api/request';
import type { AuthResponse, LoginCredentials } from '../../types/auth';

const AUTH_TOKEN_KEY = 'auth_token';
const AUTH_USER_KEY = 'auth_user';

/**
 * Login do usuário
 */
export async function login(credentials: LoginCredentials): Promise<AuthResponse> {
  try {
    const response = await post<AuthResponse>('auth/login', {
      email: credentials.email,
      password: credentials.password,
    });

    if (response.token) {
      localStorage.setItem(AUTH_TOKEN_KEY, response.token);
      localStorage.setItem(AUTH_USER_KEY, JSON.stringify(response.user));
    }

    return response;
  } catch (error) {
    throw error;
  }
}

/**
 * Logout do usuário
 */
export async function logout(): Promise<void> {
  try {
    // Tentar notificar o servidor (opcional)
    await post('auth/logout');
  } catch (error) {
    // Ignorar erro ao fazer logout
    console.error('Erro ao fazer logout no servidor:', error);
  } finally {
    // Limpar localStorage
    localStorage.removeItem(AUTH_TOKEN_KEY);
    localStorage.removeItem(AUTH_USER_KEY);
  }
}

/**
 * Verificar se existe token válido
 */
export function getStoredToken(): string | null {
  return localStorage.getItem(AUTH_TOKEN_KEY);
}

/**
 * Verificar se existe usuário armazenado
 */
export function getStoredUser() {
  const userJson = localStorage.getItem(AUTH_USER_KEY);
  return userJson ? JSON.parse(userJson) : null;
}

/**
 * Verificar autenticação ao carregar app
 */
export async function checkAuth(): Promise<boolean> {
  const token = getStoredToken();
  if (!token) return false;

  try {
    // Tentar fazer uma requisição autenticada para validar token
    // Você pode usar qualquer endpoint que retorne dados do usuário
    // Por enquanto, apenas verificamos se tem token
    return true;
  } catch (error) {
    // Token inválido
    localStorage.removeItem(AUTH_TOKEN_KEY);
    localStorage.removeItem(AUTH_USER_KEY);
    return false;
  }
}

/**
 * Refresh do token
 */
export async function refreshToken(): Promise<string> {
  try {
    const response = await post<{ token: string }>('auth/refresh');
    if (response.token) {
      localStorage.setItem(AUTH_TOKEN_KEY, response.token);
    }
    return response.token;
  } catch (error) {
    // Token inválido, limpar e fazer logout
    localStorage.removeItem(AUTH_TOKEN_KEY);
    localStorage.removeItem(AUTH_USER_KEY);
    throw error;
  }
}

export default {
  login,
  logout,
  getStoredToken,
  getStoredUser,
  checkAuth,
  refreshToken,
};
