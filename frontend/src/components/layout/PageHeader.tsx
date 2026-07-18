import type { ReactNode } from 'react';
import { ChevronRight } from 'lucide-react';
import { Badge } from '../ui';
import '../../styles/components/page-header.css';

interface PageHeaderProps {
  breadcrumb?: string[];
  title: string;
  subtitle?: string;
  status?: { label: string; color: 'success' | 'error' | 'warning' | 'info' };
  meta?: ReactNode;
  actions?: ReactNode;
}

export default function PageHeader({ breadcrumb, title, subtitle, status, meta, actions }: PageHeaderProps) {
  return <header className="page-header"><div className="page-header-content">{breadcrumb?.length ? <nav className="page-header-breadcrumb" aria-label="Breadcrumb">{breadcrumb.map((item, index) => <span key={`${item}-${index}`}>{index > 0 && <ChevronRight size={13} />}{item}</span>)}</nav> : null}<div className="page-header-title"><h1>{title}</h1>{status && <Badge color={status.color}>{status.label}</Badge>}</div>{subtitle && <p>{subtitle}</p>}{meta && <div className="page-header-meta">{meta}</div>}</div>{actions && <div className="page-header-actions">{actions}</div>}</header>;
}
