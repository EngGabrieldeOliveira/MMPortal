import { useState, useEffect, useCallback } from 'react';
import { useNavigate } from 'react-router-dom';
import { Plus, Search } from 'lucide-react';
import { Button, Input, Table, Badge, EmptyState } from '../../components/ui';
import type { TableColumn } from '../../components/ui';
import { useApi } from '../../hooks/useApi';
import * as pedidoService from '../../services/comercial/pedido';
import type { Pedido } from '../../types/comercial';
import '../../styles/comercial.css';

export default function PedidosList() {
  const navigate = useNavigate();
  const [searchTerm, setSearchTerm] = useState('');
  const [currentPage, setCurrentPage] = useState(1);

  // Carregar pedidos
  const carregarPedidos = useCallback(() => pedidoService.listarPedidos({
      page: currentPage,
      limit: 10,
      search: searchTerm,
    }), [currentPage, searchTerm]);
  const { data: pedidosData, loading, error, refetch } = useApi(carregarPedidos);

  // Recarregar quando mudar página ou termo de busca
  useEffect(() => {
    refetch();
  }, [refetch]);

  const handleSearch = (e: React.ChangeEvent<HTMLInputElement>) => {
    setSearchTerm(e.target.value);
    setCurrentPage(1);
  };

  const handleDelete = async (id: number) => {
    if (window.confirm('Tem certeza que deseja deletar este pedido?')) {
      try {
        await pedidoService.deletarPedido(id);
        refetch();
      } catch {
        alert('Erro ao deletar pedido');
      }
    }
  };

  const getStatusColor = (status: string): 'success' | 'warning' | 'error' | 'info' => {
    const colors: Record<string, 'success' | 'warning' | 'error' | 'info'> = {
      pendente: 'warning',
      confirmado: 'info',
      enviado: 'info',
      entregue: 'success',
      cancelado: 'error',
    };
    return colors[status] || 'warning';
  };

  const formatarMoeda = (valor: number) => {
    return new Intl.NumberFormat('pt-BR', {
      style: 'currency',
      currency: 'BRL',
    }).format(valor);
  };

  const columns: TableColumn<Pedido>[] = [
    { key: 'numero', label: 'Número' },
    { key: 'cliente_nome', label: 'Cliente' },
    { key: 'valor_total', label: 'Valor', render: (valor: number) => formatarMoeda(valor) },
    { key: 'quantidade_itens', label: 'Itens' },
    { key: 'status', label: 'Status', render: (status: string) => <Badge color={getStatusColor(status)}>{status}</Badge> },
  ];

  if (loading) return <div style={{ padding: '2rem' }}>Carregando...</div>;

  return (
    <div className="comercial-container">
      <div className="comercial-header">
        <div>
          <h1>Pedidos</h1>
          <p>Gerenciamento de pedidos de clientes</p>
        </div>
        <Button
          variant="primary"
          onClick={() => navigate('/comercial/pedidos/novo')}
          icon={<Plus size={20} />}
        >
          Novo Pedido
        </Button>
      </div>

      {error && (
        <div className="error-message">
          Erro ao carregar pedidos: {error.message}
        </div>
      )}

      <div className="comercial-search">
        <Input
          type="text"
          placeholder="Buscar por número do pedido ou cliente..."
          value={searchTerm}
          onChange={handleSearch}
          icon={<Search size={20} />}
        />
      </div>

      {pedidosData?.data && pedidosData.data.length > 0 ? (
        <Table
          columns={columns}
          data={pedidosData.data}
          keyField="id"
          pagination={false}
          onEdit={(pedido: Pedido) => navigate(`/comercial/pedidos/${pedido.id}/editar`)}
          onDelete={(pedido: Pedido) => handleDelete(pedido.id)}
          actions
        />
      ) : (
        <EmptyState
          title="Nenhum pedido encontrado"
          description={searchTerm ? 'Tente refinar sua busca' : 'Crie seu primeiro pedido para começar'}
          action={{ label: 'Novo Pedido', onClick: () => navigate('/comercial/pedidos/novo') }}
        />
      )}

      {/* Pagination */}
      {pedidosData && pedidosData.last_page > 1 && (
        <div className="pagination">
          <Button
            disabled={currentPage === 1}
            onClick={() => setCurrentPage(currentPage - 1)}
          >
            Anterior
          </Button>
          <span className="pagination-info">
            Página {currentPage} de {pedidosData.last_page}
          </span>
          <Button
            disabled={currentPage === pedidosData.last_page}
            onClick={() => setCurrentPage(currentPage + 1)}
          >
            Próximo
          </Button>
        </div>
      )}
    </div>
  );
}
