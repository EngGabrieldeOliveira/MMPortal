import { Bell, LogOut } from 'lucide-react';
import { useNavigate } from 'react-router-dom';
import { useAuth } from '../../context/AuthContext';
import clsx from 'clsx';
import '../../styles/header.css';

export default function Header() {
  const navigate = useNavigate();
  const { user, logout } = useAuth();

  const handleLogout = async () => {
    try {
      await logout();
      navigate('/login', { replace: true });
    } catch (error) {
      console.error('Erro ao fazer logout:', error);
    }
  };

  return (
    <header className="header">
      <div className="header-container">
        {/* Left side - Logo and title */}
        <div className="header-left">
          <div className="header-logo">
            <div className="logo-icon">MP</div>
            <span className="logo-text">MMPortal</span>
          </div>
        </div>

        {/* Right side - User actions */}
        <div className="header-right">
          {/* Notifications */}
          <button
            className="header-button header-button-icon"
            aria-label="Notificações"
            title="Notificações"
          >
            <Bell size={20} />
          </button>

          {/* User menu */}
          <div className="header-user">
            <div className="user-avatar">
              {user?.name?.charAt(0).toUpperCase() || 'U'}
            </div>
            <div className="user-info">
              <p className="user-name">{user?.name || 'Usuário'}</p>
              <p className="user-role">{user?.role || 'Usuário'}</p>
            </div>
          </div>

          {/* Logout button */}
          <button
            className={clsx('header-button', 'header-button-logout')}
            aria-label="Sair"
            title="Sair"
            onClick={handleLogout}
          >
            <LogOut size={20} />
          </button>
        </div>
      </div>
    </header>
  );
}
