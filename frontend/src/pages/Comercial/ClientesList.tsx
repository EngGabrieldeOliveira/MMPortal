import { useState, useEffect, useCallback } from 'react';
import { useNavigate } from 'react-router-dom';
import { Building2, Plus, Search, UserRound } from 'lucide-react';
import { Button, Input, Select, Table, Badge, EmptyState } from '../../components/ui';
import type { TableColumn } from '../../components/ui';
import { useApi } from '../../hooks/useApi';
import * as clienteService from '../../services/comercial/cliente';
import type { Cliente } from '../../types/comercial';
import '../../styles/comercial.css';

export default function ClientesList() {
  const navigate = useNavigate();
  const [searchTerm, setSearchTerm] = useState('');
  const [currentPage, setCurrentPage] = useState(1);
  const [status, setStatus] = useState('');

  // Carregar clientes
  const carregarClientes = useCallback(() => clienteService.listarClientes({
      page: currentPage,
      limit: 10,
      search: searchTerm,
      status,
    }), [currentPage, searchTerm, status]);
  const { data: clientesData, loading, error, refetch } = useApi(carregarClientes);

  // Recarregar quando mudar página ou termo de busca
  useEffect(() => {
    refetch();
  }, [refetch]);

  const handleSearch = (e: React.ChangeEvent<HTMLInputElement>) => {
    setSearchTerm(e.target.value);
    setCurrentPage(1);
  };

  const handleDelete = async (id: number) => {
    if (window.confirm('Tem certeza que deseja deletar este cliente?')) {
      try {
        await clienteService.deletarCliente(id);
        refetch();
      } catch {
        alert('Erro ao deletar cliente');
      }
    }
  };

  const getStatusColor = (status: string) => {
    const colors: Record<string, 'success' | 'warning' | 'error'> = {
      ativo: 'success',
      inativo: 'error',
      suspenso: 'error',
    };
    return colors[status] || 'warning';
  };

  const columns: TableColumn<Cliente>[] = [
    { key: 'tipo', label: 'Tipo', render: (tipo: Cliente['tipo']) => <span className="cliente-tipo-icon" title={tipo === 'juridica' ? 'Pessoa jurídica' : 'Pessoa física'}>{tipo === 'juridica' ? <Building2 size={17} /> : <UserRound size={17} />}</span> },
    { key: 'nome', label: 'Nome', render: (nome: string) => <span className="cliente-nome">{nome}</span> },
    { key: 'documento', label: 'Documento', render: (documento?: string) => <span className="cliente-secundario">{documento || '—'}</span> },
    { key: 'cidade', label: 'Localização', render: (_cidade: string, cliente: Cliente) => <span className="cliente-secundario">{cliente.cidade} - {cliente.estado}</span> },
    { key: 'status', label: 'Status', render: (status: string) => <Badge color={getStatusColor(status)}>{status}</Badge> },
  ];

  if (loading) return <div style={{ padding: '2rem' }}>Carregando...</div>;

  return (
    <div className="comercial-container">
      <div className="comercial-header">
        <div>
          <h1>Clientes</h1>
          <p>Gerenciamento de clientes da empresa</p>
        </div>
        <Button
          variant="primary"
          onClick={() => navigate('/comercial/clientes/novo')}
          icon={<Plus size={20} />}
        >
          Novo Cliente
        </Button>
      </div>

      {error && (
        <div className="error-message">
          Erro ao carregar clientes: {error.message}
        </div>
      )}

      <div className="comercial-search">
        <Input
          type="text"
          placeholder="Buscar por nome, email ou empresa..."
          value={searchTerm}
          onChange={handleSearch}
          icon={<Search size={20} />}
        />
        <Select value={status} onChange={(event) => { setStatus(event.target.value); setCurrentPage(1); }} options={[{ value: '', label: 'Todos os status' }, { value: 'ativo', label: 'Ativos' }, { value: 'inativo', label: 'Inativos' }, { value: 'suspenso', label: 'Suspensos' }]} />
      </div>

      {clientesData?.data && clientesData.data.length > 0 ? (
        <Table
          columns={columns}
          data={clientesData.data}
          keyField="id"
          pagination={false}
          onRowClick={(cliente: Cliente) => navigate(`/comercial/clientes/${cliente.id}`)}
          onEdit={(cliente: Cliente) => navigate(`/comercial/clientes/${cliente.id}`)}
          onDelete={(cliente: Cliente) => handleDelete(cliente.id)}
          actions
        />
      ) : (
        <EmptyState
          title="Nenhum cliente encontrado"
          description={searchTerm ? 'Tente refinar sua busca' : 'Crie seu primeiro cliente para começar'}
          action={{ label: 'Novo Cliente', onClick: () => navigate('/comercial/clientes/novo') }}
        />
      )}

      {/* Pagination */}
      {clientesData && clientesData.last_page > 1 && (
        <div className="pagination">
          <Button
            disabled={currentPage === 1}
            onClick={() => setCurrentPage(currentPage - 1)}
          >
            Anterior
          </Button>
          <span className="pagination-info">
            Página {currentPage} de {clientesData.last_page}
          </span>
          <Button
            disabled={currentPage === clientesData.last_page}
            onClick={() => setCurrentPage(currentPage + 1)}
          >
            Próximo
          </Button>
        </div>
      )}
    </div>
  );
}
