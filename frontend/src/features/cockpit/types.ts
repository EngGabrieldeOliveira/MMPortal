export type AttentionPriority = 'critical' | 'attention' | 'normal';

export interface CockpitKpi {
  id: string;
  label: string;
  value: string;
  description: string;
  trend?: string;
  tone: 'orange' | 'yellow' | 'blue' | 'violet' | 'green' | 'emerald';
}

export interface AttentionItem {
  id: string;
  priority: AttentionPriority;
  title: string;
  description: string;
  href: string;
  date: string;
}

export interface ChartPoint {
  label: string;
  value: number;
}

export interface WorkSummary {
  id: string;
  name: string;
  client: string;
  stage: string;
  progress: number;
  due_label: string;
  status: 'on-track' | 'attention' | 'late';
  href: string;
}

export interface MovementItem {
  id: string;
  type: 'quote' | 'work' | 'purchase' | 'client' | 'order' | 'request';
  title: string;
  description: string;
  time: string;
}

export interface CockpitData {
  kpis: CockpitKpi[];
  attention_items: AttentionItem[];
  production: ChartPoint[];
  revenue: ChartPoint[];
  works: WorkSummary[];
  movements: MovementItem[];
}
