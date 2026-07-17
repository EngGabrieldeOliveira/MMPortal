import { get, patch, post, remove } from '../api/request';
import axiosInstance from '../api/client';
import type { PaginatedResponse } from '../../types/api';
import type { CreateSolicitacaoDTO, Solicitacao } from '../../types/comercial';

const ENDPOINT = 'comercial/solicitacoes';

export const listarSolicitacoes = (page = 1, limit = 10, status?: string): Promise<PaginatedResponse<Solicitacao>> =>
  get<PaginatedResponse<Solicitacao>>(ENDPOINT, { page, limit, status });

export const criarSolicitacao = (dados: CreateSolicitacaoDTO): Promise<Solicitacao> =>
  post<Solicitacao>(ENDPOINT, dados);

export const obterSolicitacao = (id: number): Promise<Solicitacao & { cliente?: { nome: string; codigo: string; email: string; telefone: string }; anexos: Array<{ id: number; nome_original: string; mime_type: string; tamanho: number }> }> =>
  get(`${ENDPOINT}/${id}`);

export const atualizarSolicitacao = (id: number, dados: { status?: Solicitacao['status']; postergada_ate?: string; proxima_acao_em?: string }) =>
  patch<Solicitacao>(`${ENDPOINT}/${id}`, dados);

export const anexarArquivos = (solicitacaoId: number, arquivos: File[]): Promise<{ ids: number[] }> => {
  const dados = new FormData();
  arquivos.forEach((arquivo) => dados.append('arquivos[]', arquivo));
  return post<{ ids: number[] }>(`${ENDPOINT}/${solicitacaoId}/anexos`, dados, { headers: { 'Content-Type': 'multipart/form-data' } });
};

export const renomearAnexo = (solicitacaoId: number, anexoId: number, nome_original: string) =>
  patch(`${ENDPOINT}/${solicitacaoId}/anexos/${anexoId}`, { nome_original });

export const excluirAnexo = (solicitacaoId: number, anexoId: number) =>
  remove(`${ENDPOINT}/${solicitacaoId}/anexos/${anexoId}`);

export const obterArquivoAnexo = async (solicitacaoId: number, anexoId: number, acao: 'visualizar' | 'baixar'): Promise<string> => {
  const resposta = await axiosInstance.get(`${ENDPOINT}/${solicitacaoId}/anexos/${anexoId}/${acao}`, { responseType: 'blob' });
  return URL.createObjectURL(resposta.data);
};
