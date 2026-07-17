import { get, post } from '../api/request';
import type { PaginatedResponse } from '../../types/api';
import type { Orcamento } from '../../types/comercial';

export const listarOrcamentos = (page = 1, limit = 10, status?: string): Promise<PaginatedResponse<Orcamento>> =>
  get<PaginatedResponse<Orcamento>>('comercial/orcamentos', { page, limit, status });

export const criarOrcamento = (solicitacaoId: number, dados: { validade_ate?: string; observacoes?: string; itens: Array<{ descricao: string; quantidade: number; unidade: string; valor_unitario: number }> }): Promise<Orcamento> =>
  post<Orcamento>(`comercial/solicitacoes/${solicitacaoId}/orcamentos`, dados);

export const criarOrcamentoDireto = (dados: { cliente_id: number; validade_ate?: string; observacoes?: string; itens: Array<{ descricao: string; quantidade: number; unidade: string; valor_unitario: number }> }): Promise<Orcamento> =>
  post<Orcamento>('comercial/orcamentos', dados);

export const enviarOrcamento = (id: number): Promise<Orcamento> =>
  post<Orcamento>(`comercial/orcamentos/${id}/enviar`);

export const decidirOrcamento = (id: number, decisao: 'aceito' | 'recusado' | 'postergado'): Promise<Orcamento> =>
  post<Orcamento>(`comercial/orcamentos/${id}/decisao`, { decisao });
