import { createBrowserRouter, Navigate } from 'react-router-dom';
import MainLayout from '../layouts/MainLayout';
import Dashboard from '../pages/Dashboard/Dashboard';
import ComponentShowcase from '../pages/ComponentShowcase';

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
  {
    path: '/',
    element: <MainLayout />,
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
        path: '__showcase',
        element: <ComponentShowcase />,
      },
      // Modules will be added here
      // {
      //   path: 'comercial',
      //   element: <Comercial />,
      // },
      // {
      //   path: 'engenharia',
      //   element: <Engenharia />,
      // },
      // {
      //   path: 'producao',
      //   element: <Producao />,
      // },
      // {
      //   path: 'compras',
      //   element: <Compras />,
      // },
      // {
      //   path: 'estoque',
      //   element: <Estoque />,
      // },
      // {
      //   path: 'financeiro',
      //   element: <Financeiro />,
      // },
      // {
      //   path: 'rh',
      //   element: <RH />,
      // },
      // {
      //   path: 'agenda',
      //   element: <Agenda />,
      // },
      // {
      //   path: 'configuracoes',
      //   element: <Configuracoes />,
      // },
    ],
  },
  // Auth routes
  // {
  //   path: '/login',
  //   element: <Login />,
  // },
  {
    path: '*',
    element: <Navigate to="/dashboard" replace />,
  },
]);
