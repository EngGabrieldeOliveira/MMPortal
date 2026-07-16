import { TrendingUp, Users, ShoppingCart, DollarSign } from 'lucide-react';
import Chart from 'react-apexcharts';
import '../../styles/dashboard.css';

interface StatCard {
  title: string;
  value: string | number;
  change: number;
  icon: React.ReactNode;
  color: string;
}

const stats: StatCard[] = [
  {
    title: 'Receita Total',
    value: 'R$ 45.230,50',
    change: 12.5,
    icon: <DollarSign size={24} />,
    color: '#2563eb',
  },
  {
    title: 'Novos Clientes',
    value: '234',
    change: 8.2,
    icon: <Users size={24} />,
    color: '#16a34a',
  },
  {
    title: 'Pedidos',
    value: '1.265',
    change: 3.1,
    icon: <ShoppingCart size={24} />,
    color: '#ea580c',
  },
  {
    title: 'Crescimento',
    value: '24.5%',
    change: 5.7,
    icon: <TrendingUp size={24} />,
    color: '#8b5cf6',
  },
];

const chartOptions = {
  chart: {
    type: 'area' as const,
    toolbar: { show: false },
    sparkline: { enabled: false },
  },
  dataLabels: { enabled: false },
  stroke: {
    curve: 'smooth' as const,
    width: 2,
  },
  fill: {
    type: 'gradient',
    gradient: {
      shadeIntensity: 1,
      opacityFrom: 0.45,
      opacityTo: 0.05,
      stops: [20, 100, 100, 100],
    },
  },
  xaxis: {
    categories: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul'],
  },
  tooltip: {
    theme: 'light' as const,
  },
};

const chartSeries = [
  {
    name: 'Vendas',
    data: [10, 41, 35, 51, 49, 62, 69],
  },
];

export default function Dashboard() {
  return (
    <div className="dashboard">
      <div className="dashboard-header">
        <h1 className="dashboard-title">Dashboard</h1>
        <p className="dashboard-subtitle">Bem-vindo ao MMPortal</p>
      </div>

      {/* Stats Grid */}
      <section className="dashboard-stats">
        <div className="stats-grid">
          {stats.map((stat, index) => (
            <div key={index} className="stat-card">
              <div className="stat-header">
                <h3 className="stat-title">{stat.title}</h3>
                <div className="stat-icon" style={{ color: stat.color }}>
                  {stat.icon}
                </div>
              </div>
              <div className="stat-body">
                <p className="stat-value">{stat.value}</p>
                <p className={`stat-change ${stat.change >= 0 ? 'positive' : 'negative'}`}>
                  {stat.change >= 0 ? '+' : ''}{stat.change}% vs último período
                </p>
              </div>
            </div>
          ))}
        </div>
      </section>

      {/* Charts and Tables */}
      <section className="dashboard-content">
        <div className="content-grid">
          {/* Chart */}
          <div className="chart-container">
            <div className="chart-header">
              <h2 className="chart-title">Vendas - Últimos 7 dias</h2>
            </div>
            <div className="chart-body">
              <Chart
                options={chartOptions}
                series={chartSeries}
                type="area"
                height={300}
              />
            </div>
          </div>

          {/* Activity */}
          <div className="activity-container">
            <div className="activity-header">
              <h2 className="activity-title">Atividades Recentes</h2>
            </div>
            <div className="activity-body">
              <ul className="activity-list">
                <li className="activity-item">
                  <span className="activity-dot"></span>
                  <p>Novo pedido recebido - Pedido #1234</p>
                  <time>há 2 horas</time>
                </li>
                <li className="activity-item">
                  <span className="activity-dot"></span>
                  <p>Produto adicionado ao estoque - SKU 45892</p>
                  <time>há 4 horas</time>
                </li>
                <li className="activity-item">
                  <span className="activity-dot"></span>
                  <p>Novo cliente cadastrado - João Silva</p>
                  <time>há 6 horas</time>
                </li>
                <li className="activity-item">
                  <span className="activity-dot"></span>
                  <p>Pagamento recebido - Nota #5689</p>
                  <time>há 8 horas</time>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </section>
    </div>
  );
}
