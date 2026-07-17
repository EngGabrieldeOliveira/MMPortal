import { createBrowserRouter, Navigate } from 'react-router-dom';
import MainLayout from '../layouts/MainLayout';
import Dashboard from '../pages/Dashboard/Dashboard';
import ComponentShowcase from '../pages/ComponentShowcase';
import Login from '../pages/Login/Login';
import PrivateRoute from '../components/common/PrivateRoute';
import Comercial from '../pages/Comercial';
import ClientesList from '../pages/Comercial/ClientesList';
import ClienteForm from '../pages/Comercial/ClienteForm';
import PedidosList from '../pages/Comercial/PedidosList';
import PedidoForm from '../pages/Comercial/PedidoForm';
import SolicitacoesList from '../pages/Comercial/SolicitacoesList';
import SolicitacaoForm from '../pages/Comercial/SolicitacaoForm';
import OrcamentosList from '../pages/Comercial/OrcamentosList';
import OrcamentoForm from '../pages/Comercial/OrcamentoForm';
import ObrasList from '../pages/Obras/ObrasList';
import ClienteDetail from '../pages/Comercial/ClienteDetail';
import SolicitacaoDetail from '../pages/Comercial/SolicitacaoDetail';

// Lazy loading for future modules
// import Comercial from '../pages/Comercial/Comercial';
// import Engenharia from '../pages/Engenharia/Engenharia';
// import Producao from '../pages/Produçao/Producao';
// import Compras from '../pages/Compras/Compras';
// import Estoque from '../pages/Estoque/Estoque';
// import Financeiro from '../pages/Financeiro/Financeiro';
// import RH from '../pages/RH/RH';
// import Agenda from '../pages/Agenda/Agenda';
// import Configuracoes from '../pages/Configuracoes/Configuracoes';
// import Login from '../pages/Login/Login';

export const router = createBrowserRouter([
  // Auth routes (sem proteção)
  {
    path: '/login',
    element: <Login />,
  },

  // Protected routes
  {
    path: '/',
    element: (
      <PrivateRoute>
        <MainLayout />
      </PrivateRoute>
    ),
    children: [
      {
        index: true,
        element: <Navigate to="/dashboard" replace />,
      },
      {
        path: 'dashboard',
        element: <Dashboard />,
      },
      {
        path: 'obras',
        element: <ObrasList />,
      },
      {
        path: '__showcase',
        element: <ComponentShowcase />,
      },
      // Comercial Module
      {
        path: 'comercial',
        element: <Comercial />,
        children: [
          {
            index: true,
            element: <Navigate to="/comercial/clientes" replace />,
          },
          {
            path: 'clientes',
            element: <ClientesList />,
          },
          {
            path: 'clientes/novo',
            element: <ClienteForm />,
          },
          {
            path: 'clientes/:id/editar',
            element: <ClienteForm />,
          },
          {
            path: 'clientes/:id',
            element: <ClienteDetail />,
          },
          {
            path: 'solicitacoes',
            element: <SolicitacoesList />,
          },
          {
            path: 'solicitacoes/nova',
            element: <SolicitacaoForm />,
          },
          {
            path: 'solicitacoes/:id',
            element: <SolicitacaoDetail />,
          },
          {
            path: 'orcamentos',
            element: <OrcamentosList />,
          },
          {
            path: 'orcamentos/novo',
            element: <OrcamentoForm />,
          },
          {
            path: 'pedidos',
            element: <PedidosList />,
          },
          {
            path: 'pedidos/novo',
            element: <PedidoForm />,
          },
          {
            path: 'pedidos/:id/editar',
            element: <PedidoForm />,
          },
        ],
      },
    ],
  },

  // Fallback
  {
    path: '*',
    element: <Navigate to="/login" replace />,
  },
]);
