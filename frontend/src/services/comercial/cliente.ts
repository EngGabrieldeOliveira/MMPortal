/**
 * Serviço de API para Clientes
 */

import { get, post, put, remove } from '../api/request';
import type { Cliente, CreateClienteDTO, ComercialFilters } from '../../types/comercial';
import type { PaginatedResponse } from '../../types/api';

const ENDPOINT = 'comercial/clientes';

/**
 * Listar todos os clientes com paginação
 */
export const listarClientes = async (
  { page = 1, limit = 10, search, status }: ComercialFilters = {},
): Promise<PaginatedResponse<Cliente>> => {
  return get<PaginatedResponse<Cliente>>(ENDPOINT, {
    page,
    limit,
    search,
    status,
  });
};

/**
 * Obter um cliente por ID
 */
export const obterCliente = async (id: number): Promise<Cliente> => {
  return get<Cliente>(`${ENDPOINT}/${id}`);
};

/**
 * Criar um novo cliente
 */
export const criarCliente = async (dados: CreateClienteDTO): Promise<Cliente> => {
  return post<Cliente>(ENDPOINT, dados);
};

/**
 * Atualizar um cliente existente
 */
export const atualizarCliente = async (id: number, dados: Partial<CreateClienteDTO> & { status?: Cliente['status'] }): Promise<Cliente> => {
  return put<Cliente>(`${ENDPOINT}/${id}`, dados);
};

/**
 * Deletar um cliente
 */
export const deletarCliente = async (id: number): Promise<void> => {
  await remove<void>(`${ENDPOINT}/${id}`);
};

/**
 * Buscar clientes por nome ou email
 */
export const buscarClientes = async (termo: string): Promise<Cliente[]> => {
  return get<Cliente[]>(`${ENDPOINT}/search`, { q: termo });
};

/**
 * Obter estatísticas de clientes
 */
export const obterEstatisticasClientes = async () => {
  return get<{
    total: number;
    ativos: number;
    inativos: number;
    suspensos: number;
    novos_este_mes: number;
  }>(`${ENDPOINT}/estatisticas`);
};

