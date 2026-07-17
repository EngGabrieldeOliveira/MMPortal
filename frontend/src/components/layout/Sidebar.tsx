import { useLocation, Link } from 'react-router-dom';
import { LayoutDashboard, ShoppingCart, ClipboardList, HardHat, Wrench, Package, Truck, Users, Settings, ChevronRight } from 'lucide-react';
import clsx from 'clsx';
import '../../styles/sidebar.css';

interface NavItem { id: string; label: string; icon: React.ReactNode; path: string; badge?: number; }
interface NavSection { label: string; items: NavItem[]; }

const navSections: NavSection[] = [
  { label: 'Visão geral', items: [{ id: 'dashboard', label: 'Dashboard', icon: <LayoutDashboard size={20} />, path: '/dashboard' }] },
  { label: 'Comercial', items: [
    { id: 'clientes', label: 'Clientes', icon: <Users size={20} />, path: '/comercial/clientes' },
    { id: 'solicitacoes', label: 'Solicitações', icon: <ClipboardList size={20} />, path: '/comercial/solicitacoes', badge: 3 },
    { id: 'orcamentos', label: 'Orçamentos', icon: <ShoppingCart size={20} />, path: '/comercial/orcamentos' },
  ] },
  { label: 'Operação', items: [
    { id: 'obras', label: 'Obras', icon: <HardHat size={20} />, path: '/obras' },
    { id: 'pcp', label: 'PCP', icon: <ClipboardList size={20} />, path: '/pcp' },
    { id: 'engenharia', label: 'Engenharia', icon: <Wrench size={20} />, path: '/engenharia' },
    { id: 'estoque', label: 'Estoque', icon: <Package size={20} />, path: '/estoque' },
  ] },
  { label: 'Pessoas', items: [{ id: 'colaboradores', label: 'Colaboradores', icon: <Users size={20} />, path: '/colaboradores' }] },
  { label: 'Suprimentos', items: [{ id: 'fornecedores', label: 'Fornecedores', icon: <Truck size={20} />, path: '/fornecedores' }] },
];

export default function Sidebar() {
  const location = useLocation();
  return <nav className="sidebar">
    <div className="sidebar-header"><div className="sidebar-logo"><div className="sidebar-logo-icon">M</div><span className="sidebar-logo-text">MMPortal</span></div></div>
    <div className="sidebar-nav">{navSections.map((section) => <div className="sidebar-section" key={section.label}>
      <p className="sidebar-section-title">{section.label}</p><ul className="sidebar-menu">{section.items.map((item) => {
        const isActive = location.pathname === item.path || location.pathname.startsWith(`${item.path}/`);
        return <li key={item.id}><Link to={item.path} className={clsx('sidebar-menu-item', { 'sidebar-menu-item-active': isActive })}>
          <span className="sidebar-menu-icon">{item.icon}</span><span className="sidebar-menu-label">{item.label}</span>{item.badge && <span className="sidebar-menu-badge">{item.badge}</span>}{isActive && <ChevronRight className="sidebar-menu-chevron" size={18} />}
        </Link></li>;
      })}</ul></div>)}</div>
    <div className="sidebar-footer"><Link to="/configuracoes" className="sidebar-footer-button"><Settings size={20} /><span>Configurações</span></Link></div>
  </nav>;
}
