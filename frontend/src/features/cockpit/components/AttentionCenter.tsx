import { AlertCircle, ArrowUpRight, CheckCircle2, CircleAlert } from 'lucide-react';
import { Link } from 'react-router-dom';
import type { AttentionItem, AttentionPriority } from '../types';

const priorityMeta: Record<AttentionPriority, { label: string; Icon: typeof CircleAlert }> = { critical: { label: 'Crítico', Icon: CircleAlert }, attention: { label: 'Atenção', Icon: AlertCircle }, normal: { label: 'Normal', Icon: CheckCircle2 } };

export function AttentionCenter({ items }: { items: AttentionItem[] }) {
  return <section className="cockpit-panel cockpit-attention"><div className="cockpit-panel-heading"><div><p className="cockpit-overline">Prioridades do dia</p><h2>Central de Atenção</h2></div><span className="cockpit-count">{items.length}</span></div><div className="cockpit-attention-list">{items.length === 0 && <div className="cockpit-empty-state">Nenhum alerta requer atenção neste momento.</div>}{items.map((item) => { const { Icon, label } = priorityMeta[item.priority]; return <article className={`cockpit-attention-item cockpit-priority-${item.priority}`} key={item.id}><span className="cockpit-priority-icon"><Icon size={18} /></span><div className="cockpit-attention-content"><div className="cockpit-attention-title"><h3>{item.title}</h3><span>{label}</span></div><p>{item.description}</p><small>{new Intl.DateTimeFormat('pt-BR').format(new Date(`${item.date}T12:00:00`))}</small></div><Link to={item.href} className="cockpit-open-button">Abrir <ArrowUpRight size={14} /></Link></article>; })}</div></section>;
}
