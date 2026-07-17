import { Outlet } from 'react-router-dom';

/**
 * Container do módulo Comercial
 * Renderiza as sub-rotas (ClientesList, PedidosList, etc)
 */
export default function Comercial() {
  return <Outlet />;
}
