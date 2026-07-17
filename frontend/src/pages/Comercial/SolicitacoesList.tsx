import { useCallback, useEffect, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { Plus, ClipboardList } from 'lucide-react';
import { Badge, Button, EmptyState, Select, Table } from '../../components/ui';
import type { TableColumn } from '../../components/ui';
import { useApi } from '../../hooks/useApi';
import * as solicitacaoService from '../../services/comercial/solicitacao';
import type { Solicitacao } from '../../types/comercial';
import '../../styles/comercial.css';

const statusColor: Record<Solicitacao['status'], 'info' | 'warning' | 'success' | 'error'> = { nova: 'info', em_analise: 'warning', em_orcamento: 'warning', orcamento_enviado: 'info', negociacao: 'warning', postergada: 'warning', encerrada: 'error' };

export default function SolicitacoesList() {
  const navigate = useNavigate();
  const [page, setPage] = useState(1);
  const [status, setStatus] = useState('');
  const carregar = useCallback(() => solicitacaoService.listarSolicitacoes(page, 10, status), [page, status]);
  const { data, loading, error, refetch } = useApi(carregar);
  useEffect(() => { refetch(); }, [refetch]);
  const columns: TableColumn<Solicitacao>[] = [
    { key: 'codigo', label: 'Código' },
    { key: 'nome_contato', label: 'Contato' },
    { key: 'origem', label: 'Origem' },
    { key: 'descricao', label: 'Demanda' },
    { key: 'prazo_desejado', label: 'Prazo desejado', render: (value) => value || '—' },
    { key: 'status', label: 'Status', render: (value: Solicitacao['status']) => <Badge color={statusColor[value]}>{value.replaceAll('_', ' ')}</Badge> },
  ];
  return <div className="comercial-container">
    <div className="comercial-header"><div><h1>Solicitações</h1><p>Demandas recebidas e oportunidades comerciais</p></div><Button variant="primary" icon={<Plus size={20} />} onClick={() => navigate('/comercial/solicitacoes/nova')}>Nova Solicitação</Button></div>
    {error && <div className="error-message">{error.message}</div>}
    <div className="comercial-search"><Select value={status} onChange={(event) => { setStatus(event.target.value); setPage(1); }} options={[{ value: '', label: 'Todas as solicitações' }, { value: 'nova', label: 'Nova' }, { value: 'em_analise', label: 'Em análise' }]} /></div>
    {data?.data?.length ? <Table columns={columns} data={data.data} keyField="id" pagination={false} loading={loading} onRowClick={(solicitacao: Solicitacao) => navigate(`/comercial/solicitacoes/${solicitacao.id}`)} /> : <EmptyState title="Nenhuma solicitação" description="Registre a primeira demanda recebida." icon={<ClipboardList size={48} />} />}
    {data && data.last_page > 1 && <div className="pagination"><Button disabled={page === 1} onClick={() => setPage(page - 1)}>Anterior</Button><span className="pagination-info">Página {page} de {data.last_page}</span><Button disabled={page === data.last_page} onClick={() => setPage(page + 1)}>Próximo</Button></div>}
  </div>;
}
