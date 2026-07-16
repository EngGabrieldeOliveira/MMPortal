import { useLocation, Link } from 'react-router-dom';
import {
  LayoutDashboard,
  ShoppingCart,
  Briefcase,
  Package,
  Truck,
  Wallet,
  Users,
  Calendar,
  Settings,
  BarChart3,
  ChevronRight,
} from 'lucide-react';
import clsx from 'clsx';
import '../../styles/sidebar.css';

interface NavItem {
  id: string;
  label: string;
  icon: React.ReactNode;
  path: string;
  badge?: number;
}

const navItems: NavItem[] = [
  { id: 'dashboard', label: 'Dashboard', icon: <LayoutDashboard size={20} />, path: '/dashboard' },
  { id: 'comercial', label: 'Comercial', icon: <ShoppingCart size={20} />, path: '/comercial', badge: 3 },
  { id: 'engenharia', label: 'Engenharia', icon: <Briefcase size={20} />, path: '/engenharia' },
  { id: 'producao', label: 'Produção', icon: <BarChart3 size={20} />, path: '/producao' },
  { id: 'compras', label: 'Compras', icon: <Truck size={20} />, path: '/compras' },
  { id: 'estoque', label: 'Estoque', icon: <Package size={20} />, path: '/estoque', badge: 12 },
  { id: 'financeiro', label: 'Financeiro', icon: <Wallet size={20} />, path: '/financeiro' },
  { id: 'rh', label: 'RH', icon: <Users size={20} />, path: '/rh' },
  { id: 'agenda', label: 'Agenda', icon: <Calendar size={20} />, path: '/agenda' },
];

export default function Sidebar() {
  const location = useLocation();

  return (
    <nav className="sidebar">
      <div className="sidebar-header">
        <div className="sidebar-logo">
          <div className="sidebar-logo-icon">MP</div>
          <span className="sidebar-logo-text">MMPortal</span>
        </div>
      </div>

      <div className="sidebar-nav">
        <div className="sidebar-section">
          <p className="sidebar-section-title">Menu Principal</p>
          <ul className="sidebar-menu">
            {navItems.map((item) => {
              const isActive = location.pathname === item.path;
              return (
                <li key={item.id}>
                  <Link
                    to={item.path}
                    className={clsx('sidebar-menu-item', {
                      'sidebar-menu-item-active': isActive,
                    })}
                  >
                    <span className="sidebar-menu-icon">{item.icon}</span>
                    <span className="sidebar-menu-label">{item.label}</span>
                    {item.badge && (
                      <span className="sidebar-menu-badge">{item.badge}</span>
                    )}
                    {isActive && (
                      <ChevronRight className="sidebar-menu-chevron" size={18} />
                    )}
                  </Link>
                </li>
              );
            })}
          </ul>
        </div>
      </div>

      <div className="sidebar-footer">
        <Link to="/configuracoes" className="sidebar-footer-button">
          <Settings size={20} />
          <span>Configurações</span>
        </Link>
      </div>
    </nav>
  );
}
