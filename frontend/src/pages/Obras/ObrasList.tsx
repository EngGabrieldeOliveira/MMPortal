import { useCallback, useEffect, useState } from 'react';
import { HardHat, Plus } from 'lucide-react';
import { Badge, Button, EmptyState, Select, Table } from '../../components/ui';
import type { TableColumn } from '../../components/ui';
import { useApi } from '../../hooks/useApi';
import * as pedidoService from '../../services/comercial/pedido';
import type { Pedido } from '../../types/comercial';
import '../../styles/comercial.css';

const cores: Record<string, 'info' | 'warning' | 'success' | 'error'> = { aberto: 'info', aguardando_pcp: 'warning', em_planejamento: 'warning', em_producao: 'info', aguardando_instalacao: 'warning', concluido: 'success', cancelado: 'error', pendente: 'warning', confirmado: 'info', enviado: 'info', entregue: 'success' };
const moeda = (valor: number) => new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(valor);

export default function ObrasList() {
  const [page, setPage] = useState(1);
  const [status, setStatus] = useState('');
  const carregar = useCallback(() => pedidoService.listarPedidos({ page, limit: 10, status }), [page, status]);
  const { data, loading, error, refetch } = useApi(carregar);
  useEffect(() => { refetch(); }, [refetch]);
  const columns: TableColumn<Pedido>[] = [
    { key: 'numero', label: 'Obra' },
    { key: 'cliente_nome', label: 'Cliente', render: (value) => value || '—' },
    { key: 'valor_total', label: 'Valor contratado', render: (value) => moeda(value) },
    { key: 'ordens_servico_count', label: 'OS', render: (value) => value ?? 0 },
    { key: 'status', label: 'Andamento', render: (value) => <Badge color={cores[value] || 'info'}>{value.replaceAll('_', ' ')}</Badge> },
  ];
  return <div className="comercial-container"><div className="comercial-header"><div><h1>Obras</h1><p>Pedidos aprovados, ordens de serviço e execução</p></div><Button icon={<Plus size={20} />}>Nova Obra</Button></div>
    {error && <div className="error-message">{error.message}</div>}
    <div className="comercial-search"><Select value={status} onChange={(event) => { setStatus(event.target.value); setPage(1); }} options={[{ value: '', label: 'Todos os status' }, ...Object.keys(cores).map((value) => ({ value, label: value.replaceAll('_', ' ') }))]} /></div>
    {data?.data?.length ? <Table columns={columns} data={data.data} keyField="id" loading={loading} pagination={false} /> : <EmptyState title="Nenhuma obra em operação" description="Obras surgem automaticamente quando um orçamento é aceito." icon={<HardHat size={48} />} />}
    {data && data.last_page > 1 && <div className="pagination"><Button disabled={page === 1} onClick={() => setPage(page - 1)}>Anterior</Button><span className="pagination-info">Página {page} de {data.last_page}</span><Button disabled={page === data.last_page} onClick={() => setPage(page + 1)}>Próximo</Button></div>}
  </div>;
}
