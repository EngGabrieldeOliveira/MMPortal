import { BriefcaseBusiness, CircleDollarSign, ClipboardCheck, ClipboardList, FileClock, PackageSearch, UsersRound } from 'lucide-react';
import type { CockpitKpi } from '../types';

const icons = { works: BriefcaseBusiness, 'completed-works': ClipboardCheck, quotes: FileClock, orders: ClipboardList, purchases: PackageSearch, clients: UsersRound, revenue: CircleDollarSign };
const trends: Record<string, string> = { works: '→ Operação atual', quotes: '→ Aguardando decisão', orders: '→ Em execução', revenue: '→ Projeção do mês', purchases: '→ Sem comparativo', clients: '→ Ação comercial', 'completed-works': '→ Histórico consolidado' };

export function KpiCard({ item }: { item: CockpitKpi }) {
  const Icon = icons[item.id as keyof typeof icons] ?? ClipboardList;

  return <article className={`cockpit-kpi cockpit-kpi-${item.tone}`}><div className="cockpit-kpi-top"><p>{item.label}</p><span className="cockpit-kpi-icon"><Icon size={19} /></span></div><strong>{item.value}</strong><p className="cockpit-kpi-description">{item.description}</p><span className="cockpit-kpi-trend">{item.trend ?? trends[item.id] ?? '→ Sem comparativo'}</span></article>;
}
