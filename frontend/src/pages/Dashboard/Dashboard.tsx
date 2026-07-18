import { AlertTriangle, RefreshCw } from 'lucide-react';
import { AttentionCenter } from '../../features/cockpit/components/AttentionCenter';
import { CockpitHeader } from '../../features/cockpit/components/CockpitHeader';
import { KpiCard } from '../../features/cockpit/components/KpiCard';
import { MetricChart } from '../../features/cockpit/components/MetricChart';
import { RecentMovements } from '../../features/cockpit/components/RecentMovements';
import { WorksList } from '../../features/cockpit/components/WorksList';
import { useCockpitDashboard } from '../../hooks/useCockpitDashboard';
import '../../styles/dashboard.css';
import '../../styles/cockpit-refinement.css';

const kpiOrder = ['works', 'quotes', 'orders', 'revenue', 'purchases', 'clients', 'completed-works'];

function DashboardSkeleton() {
  return <><section className="cockpit-kpi-grid" aria-label="Carregando indicadores">{Array.from({ length: 7 }, (_, index) => <div className="cockpit-skeleton cockpit-skeleton-kpi" key={index} />)}</section><section className="cockpit-top-grid"><div className="cockpit-skeleton cockpit-skeleton-panel" /><div className="cockpit-skeleton cockpit-skeleton-panel" /></section><section className="cockpit-bottom-grid"><div className="cockpit-skeleton cockpit-skeleton-chart" /><div className="cockpit-skeleton cockpit-skeleton-chart" /><div className="cockpit-skeleton cockpit-skeleton-chart" /></section></>;
}

export default function Dashboard() {
  const { data, loading, error, retry } = useCockpitDashboard();
  const kpis = data ? [...data.kpis].sort((first, second) => kpiOrder.indexOf(first.id) - kpiOrder.indexOf(second.id)) : [];
  const productionTotal = data?.production.reduce((total, item) => total + item.value, 0) ?? 0;
  const revenue = data?.kpis.find((item) => item.id === 'revenue')?.value ?? 'R$ 0,00';

  return <div className="dashboard cockpit-dashboard"><CockpitHeader />{loading && !data && <DashboardSkeleton />}{error && !data && <section className="cockpit-feedback cockpit-error"><AlertTriangle size={22} /><div><strong>Não foi possível carregar o Cockpit.</strong><p>{error.message}</p></div><button type="button" onClick={retry}><RefreshCw size={16} /> Tentar novamente</button></section>}{data && <><section className="cockpit-kpi-grid" aria-label="Indicadores principais">{kpis.map((item) => <KpiCard item={item} key={item.id} />)}</section><section className="cockpit-top-grid"><AttentionCenter items={data.attention_items} /><WorksList works={data.works} /></section><section className="cockpit-bottom-grid"><MetricChart title="Produção da semana" subtitle="Etapas de fabricação concluídas por dia" value={`${productionTotal} concluídas`} points={data.production} variant="bar" indicators={[{ label: 'Meta', value: 'Não definida' }, { label: 'Previsto', value: `${productionTotal} etapas` }, { label: '%', value: '—' }]} /><MetricChart title="Faturamento mensal" subtitle="Pedidos registrados nos últimos sete meses (R$)" value={revenue} points={data.revenue} variant="line" indicators={[{ label: 'Meta', value: 'Não definida' }, { label: 'Previsto', value: revenue }, { label: '%', value: '—' }]} /><RecentMovements movements={data.movements} /></section></>}</div>;
}
