import { useLocation, Link } from 'react-router-dom';
import { BriefcaseBusiness, CalendarDays, ChevronRight, Factory, Landmark, LayoutDashboard, Package, Settings, ShoppingCart, UsersRound, Wrench } from 'lucide-react';
import clsx from 'clsx';
import '../../styles/sidebar.css';
import '../../styles/sidebar-refinement.css';

interface NavItem { id: string; label: string; icon: React.ReactNode; path?: string; disabled?: boolean; }
interface NavSection { label: string; items: NavItem[]; }

const navSections: NavSection[] = [
  { label: 'Principal', items: [{ id: 'dashboard', label: 'Dashboard', icon: <LayoutDashboard size={19} />, path: '/dashboard' }] },
  { label: 'Operação', items: [{ id: 'comercial', label: 'Comercial', icon: <BriefcaseBusiness size={19} />, path: '/comercial/clientes' }, { id: 'engenharia', label: 'Engenharia', icon: <Wrench size={19} />, disabled: true }, { id: 'producao', label: 'Produção', icon: <Factory size={19} />, disabled: true }] },
  { label: 'Suprimentos e finanças', items: [{ id: 'compras', label: 'Compras', icon: <ShoppingCart size={19} />, disabled: true }, { id: 'estoque', label: 'Estoque', icon: <Package size={19} />, disabled: true }, { id: 'financeiro', label: 'Financeiro', icon: <Landmark size={19} />, disabled: true }] },
  { label: 'Pessoas e sistema', items: [{ id: 'rh', label: 'RH', icon: <UsersRound size={19} />, disabled: true }, { id: 'agenda', label: 'Agenda', icon: <CalendarDays size={19} />, disabled: true }, { id: 'configuracoes', label: 'Configurações', icon: <Settings size={19} />, disabled: true }] },
];

export default function Sidebar() {
  const location = useLocation();
  return <nav className="sidebar"><div className="sidebar-header"><div className="sidebar-logo"><div className="sidebar-logo-icon">M</div><span className="sidebar-logo-text">MMPortal</span></div></div><div className="sidebar-nav">{navSections.map((section) => <div className="sidebar-section" key={section.label}><p className="sidebar-section-title">{section.label}</p><ul className="sidebar-menu">{section.items.map((item) => { const isActive = Boolean(item.path && (location.pathname === item.path || location.pathname.startsWith(`${item.path}/`))); return <li key={item.id}>{item.disabled ? <span className="sidebar-menu-item sidebar-menu-item-disabled" title="Módulo em preparação"><span className="sidebar-menu-icon">{item.icon}</span><span className="sidebar-menu-label">{item.label}</span><small>em breve</small></span> : <Link to={item.path!} className={clsx('sidebar-menu-item', { 'sidebar-menu-item-active': isActive })}><span className="sidebar-menu-icon">{item.icon}</span><span className="sidebar-menu-label">{item.label}</span>{isActive && <ChevronRight className="sidebar-menu-chevron" size={18} />}</Link>}</li>; })}</ul></div>)}</div><div className="sidebar-footer"><span className="sidebar-footer-button sidebar-footer-disabled"><Settings size={19} /><span>Configurações</span><small>em breve</small></span></div></nav>;
}
