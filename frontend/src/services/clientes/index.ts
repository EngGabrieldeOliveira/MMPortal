import { get, patch, post, put, remove } from '../api/request';
import type { ClienteDetalhado, ClienteFilters, ClientePayload, PaginatedClientes, UsuarioOpcao } from '../../types/clientes';

const endpoint = 'comercial/clientes';
export const clientesService = {
  listar: (filters: ClienteFilters = {}) => get<PaginatedClientes>(endpoint, filters as unknown as Record<string, unknown>),
  obter: (id: number) => get<ClienteDetalhado>(`${endpoint}/${id}`),
  criar: (payload: ClientePayload) => post<ClienteDetalhado>(endpoint, payload),
  atualizar: (id: number, payload: Partial<ClientePayload>) => put<ClienteDetalhado>(`${endpoint}/${id}`, payload),
  atualizarStatus: (id: number, status: 'ativo' | 'inativo') => patch<ClienteDetalhado>(`${endpoint}/${id}/status`, { status }),
  excluir: (id: number) => remove<void>(`${endpoint}/${id}`),
  contatos: (id: number) => get<PaginatedClientes>(`${endpoint}/${id}/contatos`),
  enderecos: (id: number) => get<ClienteDetalhado['endereco_cobranca']>(`${endpoint}/${id}/enderecos`),
  documentos: (id: number) => get<PaginatedClientes>(`${endpoint}/${id}/documentos`),
  opcoesResponsaveis: () => get<UsuarioOpcao[]>('comercial/usuarios/opcoes'),
};
