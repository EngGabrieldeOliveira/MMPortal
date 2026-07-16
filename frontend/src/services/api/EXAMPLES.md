/**
 * Exemplos de uso dos serviços API
 * 
 * Este arquivo mostra como usar os diferentes services
 * e hooks criados na Sprint 03A
 */

// ============================================
// 1. USANDO API REQUEST FUNCTIONS DIRETAMENTE
// ============================================

import { api } from '@/services/api';

// GET - Buscar lista paginada
async function example1() {
  try {
    const response = await api.getList('clientes', {
      page: 1,
      per_page: 15,
      search: 'Silva',
    });
    console.log(response.data); // Array de clientes
  } catch (error) {
    console.error(error);
  }
}

// GET - Buscar por ID
async function example2() {
  try {
    const cliente = await api.getById('clientes', 1);
    console.log(cliente);
  } catch (error) {
    console.error(error);
  }
}

// POST - Criar novo
async function example3() {
  try {
    const novoCliente = await api.create('clientes', {
      nome: 'Novo Cliente',
      email: 'cliente@example.com',
    });
    console.log(novoCliente);
  } catch (error) {
    console.error(error);
  }
}

// PUT - Atualizar
async function example4() {
  try {
    const clienteAtualizado = await api.update('clientes', 1, {
      nome: 'Nome Atualizado',
    });
    console.log(clienteAtualizado);
  } catch (error) {
    console.error(error);
  }
}

// DELETE - Deletar
async function example5() {
  try {
    await api.destroy('clientes', 1);
    console.log('Deletado com sucesso');
  } catch (error) {
    console.error(error);
  }
}

// ============================================
// 2. USANDO HOOK useApi (COM ESTADO)
// ============================================

import { useApi } from '@/hooks';

function ComponentComUseApi() {
  // Exemplo simples
  const { data, loading, error, refetch } = useApi(
    () => api.getList('clientes'),
    { immediate: true } // Executar ao montar
  );

  if (loading) return <div>Carregando...</div>;
  if (error) return <div>Erro: {error.message}</div>;

  return (
    <div>
      <button onClick={() => refetch()}>Recarregar</button>
      {data?.map((cliente: any) => (
        <div key={cliente.id}>{cliente.nome}</div>
      ))}
    </div>
  );
}

// ============================================
// 3. USANDO HOOK useFetch (SEM AUTO-EXECUTE)
// ============================================

import { useFetch } from '@/hooks';

function ComponentComUseFetch() {
  const { data, loading, error, execute } = useFetch({
    onSuccess: (data) => console.log('Sucesso!', data),
    onError: (error) => console.log('Erro!', error),
  });

  const handleClick = async () => {
    await execute(() => api.create('clientes', { nome: 'Novo' }));
  };

  return (
    <div>
      <button onClick={handleClick} disabled={loading}>
        {loading ? 'Criando...' : 'Criar Cliente'}
      </button>
      {error && <div>Erro: {error.message}</div>}
    </div>
  );
}

// ============================================
// 4. USANDO HOOK useForm (COM VALIDAÇÃO)
// ============================================

import { useForm } from '@/hooks';
import { Input, Button } from '@/components/ui';

interface ClienteForm {
  nome: string;
  email: string;
  telefone: string;
}

function FormCliente() {
  const { values, errors, isSubmitting, handleChange, handleBlur, handleSubmit } =
    useForm<ClienteForm>({
      initialValues: {
        nome: '',
        email: '',
        telefone: '',
      },
      validate: (values) => {
        const errors: Record<string, string | undefined> = {};
        if (!values.nome) errors.nome = 'Nome é obrigatório';
        if (!values.email) errors.email = 'Email é obrigatório';
        if (values.email && !values.email.includes('@')) {
          errors.email = 'Email inválido';
        }
        return errors;
      },
      onSubmit: async (values) => {
        await api.create('clientes', values);
      },
      onSuccess: () => alert('Cliente criado!'),
      onError: (error) => alert(`Erro: ${error.message}`),
    });

  return (
    <form onSubmit={handleSubmit}>
      <Input
        label="Nome"
        name="nome"
        value={values.nome}
        onChange={handleChange}
        onBlur={handleBlur}
        error={errors.nome}
      />
      <Input
        label="Email"
        name="email"
        type="email"
        value={values.email}
        onChange={handleChange}
        onBlur={handleBlur}
        error={errors.email}
      />
      <Input
        label="Telefone"
        name="telefone"
        value={values.telefone}
        onChange={handleChange}
        onBlur={handleBlur}
      />
      <Button type="submit" loading={isSubmitting}>
        {isSubmitting ? 'Salvando...' : 'Salvar'}
      </Button>
    </form>
  );
}

// ============================================
// 5. COMBINANDO HOOKS (PATTERN COMUM)
// ============================================

function ListaClientesComPaginacao() {
  const { data, loading, error, refetch } = useApi(
    () => api.getList('clientes', { page: 1, per_page: 10 }),
    { immediate: true }
  );

  const { execute: deleteCliente, loading: deleting } = useFetch({
    onSuccess: () => {
      alert('Deletado!');
      refetch(); // Recarregar lista após deletar
    },
  });

  return (
    <div>
      {loading && <div>Carregando...</div>}
      {error && <div>Erro: {error.message}</div>}
      {data?.data.map((cliente: any) => (
        <div key={cliente.id}>
          <span>{cliente.nome}</span>
          <button
            onClick={() => deleteCliente(() => api.destroy('clientes', cliente.id))}
            disabled={deleting}
          >
            Deletar
          </button>
        </div>
      ))}
    </div>
  );
}

export {
  example1,
  example2,
  example3,
  example4,
  example5,
  ComponentComUseApi,
  ComponentComUseFetch,
  FormCliente,
  ListaClientesComPaginacao,
};
