import { useCallback, useEffect, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { FileText, Plus } from 'lucide-react';
import { Badge, Button, EmptyState, Select, Table } from '../../components/ui';
import type { TableColumn } from '../../components/ui';
import { useApi } from '../../hooks/useApi';
import * as orcamentoService from '../../services/comercial/orcamento';
import type { Orcamento } from '../../types/comercial';
import '../../styles/comercial.css';

const cores: Record<Orcamento['status'], 'info' | 'warning' | 'success' | 'error'> = { rascunho: 'warning', enviado: 'info', aceito: 'success', recusado: 'error', postergado: 'warning' };
const moeda = (valor: number) => new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(valor);

export default function OrcamentosList() {
  const navigate = useNavigate();
  const [page, setPage] = useState(1);
  const [status, setStatus] = useState('');
  const carregar = useCallback(() => orcamentoService.listarOrcamentos(page, 10, status), [page, status]);
  const { data, loading, error, refetch } = useApi(carregar);
  useEffect(() => { refetch(); }, [refetch]);
  const atualizarStatus = async (id: number, acao: 'enviar' | 'aceito' | 'recusado' | 'postergado') => {
    try {
      if (acao === 'enviar') await orcamentoService.enviarOrcamento(id);
      else await orcamentoService.decidirOrcamento(id, acao);
      refetch();
    } catch (requestError) { window.alert(requestError instanceof Error ? requestError.message : 'Não foi possível atualizar o orçamento.'); }
  };
  const columns: TableColumn<Orcamento>[] = [
    { key: 'numero', label: 'Orçamento', render: (value, row) => `${value} · V${row.versao}` },
    { key: 'cliente', label: 'Cliente', render: (_value, row) => row.cliente?.empresa || row.cliente?.nome || row.solicitacao?.nome_contato || '—' },
    { key: 'valor_total', label: 'Valor', render: (value) => moeda(value) },
    { key: 'validade_ate', label: 'Validade', render: (value) => value || '—' },
    { key: 'status', label: 'Status', render: (value: Orcamento['status']) => <Badge color={cores[value]}>{value}</Badge> },
    { key: 'id', label: 'Ação', render: (id: number, row) => {
      if (row.status === 'rascunho') return <Button size="sm" onClick={() => atualizarStatus(id, 'enviar')}>Enviar</Button>;
      if (row.status === 'enviado') return <div className="budget-actions"><Button size="sm" onClick={() => atualizarStatus(id, 'aceito')}>Aceitar</Button><Button size="sm" variant="secondary" onClick={() => atualizarStatus(id, 'postergado')}>Postergar</Button><Button size="sm" variant="danger" onClick={() => atualizarStatus(id, 'recusado')}>Recusar</Button></div>;
      return '—';
    } },
  ];
  return <div className="comercial-container"><div className="comercial-header"><div><h1>Orçamentos</h1><p>Propostas comerciais, versões e decisões dos clientes</p></div><Button variant="primary" icon={<Plus size={20} />} onClick={() => navigate('/comercial/orcamentos/novo')}>Novo Orçamento</Button></div>
    {error && <div className="error-message">{error.message}</div>}
    <div className="comercial-search"><Select value={status} onChange={(event) => { setStatus(event.target.value); setPage(1); }} options={[{ value: '', label: 'Todos os status' }, ...Object.keys(cores).map((value) => ({ value, label: value }))]} /></div>
    {data?.data?.length ? <Table columns={columns} data={data.data} keyField="id" pagination={false} loading={loading} /> : <EmptyState title="Nenhum orçamento criado" description="Crie um orçamento a partir de uma solicitação em análise." icon={<FileText size={48} />} />}
    {data && data.last_page > 1 && <div className="pagination"><Button disabled={page === 1} onClick={() => setPage(page - 1)}>Anterior</Button><span className="pagination-info">Página {page} de {data.last_page}</span><Button disabled={page === data.last_page} onClick={() => setPage(page + 1)}>Próximo</Button></div>}
  </div>;
}
