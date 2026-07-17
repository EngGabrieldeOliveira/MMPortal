/**
 * Serviço de API para Pedidos
 */

import { get, post, put, remove } from '../api/request';
import type { Pedido, CreatePedidoDTO, ComercialFilters } from '../../types/comercial';
import type { PaginatedResponse } from '../../types/api';

const ENDPOINT = 'comercial/pedidos';

/**
 * Listar todos os pedidos com paginação
 */
export const listarPedidos = async (
  { page = 1, limit = 10, search, status }: ComercialFilters = {},
): Promise<PaginatedResponse<Pedido>> => {
  return get<PaginatedResponse<Pedido>>(ENDPOINT, {
    page,
    limit,
    search,
    status,
  });
};

/**
 * Obter um pedido por ID
 */
export const obterPedido = async (id: number): Promise<Pedido> => {
  return get<Pedido>(`${ENDPOINT}/${id}`);
};

/**
 * Criar um novo pedido
 */
export const criarPedido = async (dados: CreatePedidoDTO): Promise<Pedido> => {
  return post<Pedido>(ENDPOINT, dados);
};

/**
 * Atualizar um pedido existente
 */
export const atualizarPedido = async (id: number, dados: Partial<CreatePedidoDTO>): Promise<Pedido> => {
  return put<Pedido>(`${ENDPOINT}/${id}`, dados);
};

/**
 * Deletar um pedido
 */
export const deletarPedido = async (id: number): Promise<void> => {
  await remove<void>(`${ENDPOINT}/${id}`);
};

/**
 * Buscar pedidos por número ou cliente
 */
export const buscarPedidos = async (termo: string): Promise<Pedido[]> => {
  return get<Pedido[]>(`${ENDPOINT}/search`, { q: termo });
};

/**
 * Obter pedidos de um cliente específico
 */
export const obterPedidosCliente = async (clienteId: number): Promise<Pedido[]> => {
  return get<Pedido[]>(`${ENDPOINT}/cliente/${clienteId}`);
};

/**
 * Atualizar status de um pedido
 */
export const atualizarStatusPedido = async (
  id: number,
  status: 'pendente' | 'confirmado' | 'enviado' | 'entregue' | 'cancelado',
): Promise<Pedido> => {
  return put<Pedido>(`${ENDPOINT}/${id}`, { status });
};

/**
 * Obter estatísticas de pedidos
 */
export const obterEstatisticasPedidos = async () => {
  return get<{
    total: number;
    pendentes: number;
    confirmados: number;
    enviados: number;
    entregues: number;
    cancelados: number;
    valor_total: number;
    valor_medio: number;
  }>(`${ENDPOINT}/estatisticas`);
};

