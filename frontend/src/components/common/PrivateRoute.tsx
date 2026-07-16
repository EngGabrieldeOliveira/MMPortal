import { Navigate, useLocation } from 'react-router-dom';
import { useAuth } from '../../context/AuthContext';

export interface PrivateRouteProps {
  children: React.ReactNode;
}

/**
 * Componente que protege rotas
 * Se não estiver autenticado, redireciona para login
 */
export default function PrivateRoute({ children }: PrivateRouteProps) {
  const { isAuthenticated, isLoading } = useAuth();
  const location = useLocation();

  if (isLoading) {
    return (
      <div style={{ padding: '2rem', textAlign: 'center' }}>
        <p>Carregando...</p>
      </div>
    );
  }

  if (!isAuthenticated) {
    // Redirecionar para login, mantendo a página anterior no state
    return <Navigate to="/login" state={{ from: location }} replace />;
  }

  return <>{children}</>;
}
