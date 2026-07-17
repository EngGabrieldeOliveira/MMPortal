/**
 * Tipos para o módulo Comercial
 */

export interface Cliente {
  id: number;
  codigo: string;
  tipo: 'fisica' | 'juridica';
  nome: string;
  email: string;
  documento?: string;
  telefone: string;
  empresa: string;
  cidade: string;
  estado: string;
  cep?: string;
  logradouro?: string;
  numero?: string;
  complemento?: string;
  bairro?: string;
  status: 'ativo' | 'inativo' | 'suspenso';
  data_criacao: string;
  data_atualizacao: string;
  created_at?: string;
  updated_at?: string;
}

export interface Pedido {
  id: number;
  numero: string;
  cliente_id: number;
  cliente_nome?: string;
  valor_total: number;
  quantidade_itens: number;
  status: 'pendente' | 'confirmado' | 'enviado' | 'entregue' | 'cancelado';
  data_pedido: string;
  data_entrega_prevista?: string;
  data_entrega_real?: string;
  observacoes?: string;
  ordens_servico_count?: number;
}

export interface CreateClienteDTO {
  tipo: 'fisica' | 'juridica';
  nome: string;
  email: string;
  documento?: string;
  telefone: string;
  empresa: string;
  cidade: string;
  estado: string;
  cep?: string;
  logradouro?: string;
  numero?: string;
  complemento?: string;
  bairro?: string;
}

export interface UpdateClienteDTO extends Partial<CreateClienteDTO> {
  id: number;
}

export interface CreatePedidoDTO {
  cliente_id: number;
  valor_total: number;
  quantidade_itens: number;
  data_entrega_prevista?: string;
  observacoes?: string;
}

export interface UpdatePedidoDTO extends Partial<CreatePedidoDTO> {
  id: number;
  status?: string;
}

export interface ComercialFilters {
  status?: string;
  search?: string;
  page?: number;
  limit?: number;
}

export interface Solicitacao {
  id: number;
  codigo: string;
  cliente_id?: number;
  nome_contato: string;
  email_contato?: string;
  telefone_contato?: string;
  origem: 'whatsapp' | 'email' | 'telefone' | 'indicacao' | 'site' | 'visita' | 'outro';
  status: 'nova' | 'em_analise' | 'em_orcamento' | 'orcamento_enviado' | 'negociacao' | 'postergada' | 'encerrada';
  descricao: string;
  prazo_desejado?: string;
  proxima_acao_em?: string;
  cliente?: { nome: string; codigo: string; email: string; telefone: string };
  anexos?: Array<{ id: number; nome_original: string; mime_type: string; tamanho: number }>;
}

export interface CreateSolicitacaoDTO {
  cliente_id?: number;
  nome_contato: string;
  email_contato?: string;
  telefone_contato?: string;
  origem: Solicitacao['origem'];
  descricao: string;
  prazo_desejado?: string;
}

export interface Orcamento {
  id: number;
  numero: string;
  versao: number;
  status: 'rascunho' | 'enviado' | 'aceito' | 'recusado' | 'postergado';
  valor_total: number;
  validade_ate?: string;
  cliente?: { nome: string; empresa?: string };
  solicitacao?: { nome_contato: string };
}
