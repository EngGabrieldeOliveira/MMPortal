import { useCallback, useEffect } from 'react';
import { useNavigate, useParams } from 'react-router-dom';
import { ArrowLeft } from 'lucide-react';
import { Button, Card, CardBody, CardHeader, CardTitle, Input, Select } from '../../components/ui';
import { useApi } from '../../hooks/useApi';
import { useForm } from '../../hooks/useForm';
import * as clienteService from '../../services/comercial/cliente';
import * as pedidoService from '../../services/comercial/pedido';
import type { Cliente, CreatePedidoDTO } from '../../types/comercial';
import '../../styles/comercial.css';

interface PedidoFormValues extends Omit<CreatePedidoDTO, 'cliente_id' | 'valor_total' | 'quantidade_itens'> {
  cliente_id: string;
  valor_total: string;
  quantidade_itens: string;
}

const CAMPOS_PEDIDO: Array<keyof PedidoFormValues> = [
  'cliente_id', 'valor_total', 'quantidade_itens', 'data_entrega_prevista', 'observacoes',
];

export default function PedidoForm() {
  const navigate = useNavigate();
  const { id } = useParams<{ id: string }>();
  const isEdicao = Boolean(id);

  const buscarPedido = useCallback(
    () => id ? pedidoService.obterPedido(Number(id)) : Promise.resolve(null),
    [id],
  );
  const buscarClientes = useCallback(
    () => clienteService.listarClientes({ limit: 100 }),
    [],
  );
  const { data: pedido, refetch: carregarPedido } = useApi(buscarPedido);
  const { data: clientesData, loading: carregandoClientes, refetch: carregarClientes } = useApi(buscarClientes);

  const { values, errors, touched, isSubmitting, handleBlur, handleChange, handleSubmit, setFieldValue } =
    useForm<PedidoFormValues>({
      initialValues: {
        cliente_id: '',
        valor_total: '',
        quantidade_itens: '',
        data_entrega_prevista: '',
        observacoes: '',
      },
      validate: (formValues) => {
        const formErrors: Record<keyof PedidoFormValues, string | undefined> = {
          cliente_id: undefined, valor_total: undefined, quantidade_itens: undefined,
          data_entrega_prevista: undefined, observacoes: undefined,
        };
        if (!formValues.cliente_id) formErrors.cliente_id = 'Cliente é obrigatório';
        if (!formValues.valor_total || Number(formValues.valor_total) <= 0) formErrors.valor_total = 'Informe um valor maior que zero';
        if (!formValues.quantidade_itens || Number(formValues.quantidade_itens) <= 0) formErrors.quantidade_itens = 'Informe ao menos um item';
        return formErrors;
      },
      onSubmit: async (formValues) => {
        const dados: CreatePedidoDTO = {
          cliente_id: Number(formValues.cliente_id),
          valor_total: Number(formValues.valor_total),
          quantidade_itens: Number(formValues.quantidade_itens),
          data_entrega_prevista: formValues.data_entrega_prevista || undefined,
          observacoes: formValues.observacoes || undefined,
        };
        if (isEdicao) await pedidoService.atualizarPedido(Number(id), dados);
        else await pedidoService.criarPedido(dados);
        navigate('/comercial/pedidos');
      },
    });

  useEffect(() => {
    carregarClientes();
  }, [carregarClientes]);

  useEffect(() => {
    if (isEdicao) carregarPedido();
  }, [isEdicao, carregarPedido]);

  useEffect(() => {
    if (!pedido) return;
    const valores: PedidoFormValues = {
      cliente_id: String(pedido.cliente_id),
      valor_total: String(pedido.valor_total),
      quantidade_itens: String(pedido.quantidade_itens),
      data_entrega_prevista: pedido.data_entrega_prevista ?? '',
      observacoes: pedido.observacoes ?? '',
    };
    CAMPOS_PEDIDO.forEach((campo) => setFieldValue(campo, valores[campo]));
  }, [pedido, setFieldValue]);

  const clientes = clientesData?.data ?? [];
  const opcoesClientes = clientes.map((cliente: Cliente) => ({
    value: String(cliente.id),
    label: cliente.empresa ? `${cliente.nome} — ${cliente.empresa}` : cliente.nome,
  }));

  return (
    <div className="comercial-container">
      <div className="comercial-header">
        <Button variant="ghost" onClick={() => navigate('/comercial/pedidos')} icon={<ArrowLeft size={20} />}>
          Voltar
        </Button>
      </div>

      <Card>
        <CardHeader><CardTitle>{isEdicao ? 'Editar Pedido' : 'Novo Pedido'}</CardTitle></CardHeader>
        <CardBody>
          <form onSubmit={handleSubmit} className="form-grid">
            <div className="form-group form-group-full">
              <label htmlFor="cliente_id">Cliente</label>
              <Select
                id="cliente_id"
                name="cliente_id"
                value={values.cliente_id}
                onChange={handleChange}
                onBlur={handleBlur}
                options={opcoesClientes}
                placeholder={carregandoClientes ? 'Carregando clientes...' : 'Selecione um cliente'}
                disabled={carregandoClientes}
                error={touched.cliente_id ? errors.cliente_id : undefined}
              />
            </div>
            <div className="form-group">
              <label htmlFor="valor_total">Valor total</label>
              <Input id="valor_total" name="valor_total" type="number" min="0.01" step="0.01" value={values.valor_total} onChange={handleChange} onBlur={handleBlur} error={touched.valor_total ? errors.valor_total : undefined} placeholder="0,00" />
            </div>
            <div className="form-group">
              <label htmlFor="quantidade_itens">Quantidade de itens</label>
              <Input id="quantidade_itens" name="quantidade_itens" type="number" min="1" step="1" value={values.quantidade_itens} onChange={handleChange} onBlur={handleBlur} error={touched.quantidade_itens ? errors.quantidade_itens : undefined} placeholder="0" />
            </div>
            <div className="form-group">
              <label htmlFor="data_entrega_prevista">Entrega prevista</label>
              <Input id="data_entrega_prevista" name="data_entrega_prevista" type="date" value={values.data_entrega_prevista} onChange={handleChange} onBlur={handleBlur} />
            </div>
            <div className="form-group form-group-full">
              <label htmlFor="observacoes">Observações</label>
              <Input id="observacoes" name="observacoes" value={values.observacoes} onChange={handleChange} onBlur={handleBlur} placeholder="Observações do pedido" />
            </div>
            <div className="form-actions">
              <Button type="button" variant="secondary" onClick={() => navigate('/comercial/pedidos')}>Cancelar</Button>
              <Button type="submit" variant="primary" loading={isSubmitting}>{isEdicao ? 'Atualizar' : 'Criar'} Pedido</Button>
            </div>
          </form>
        </CardBody>
      </Card>
    </div>
  );
}
