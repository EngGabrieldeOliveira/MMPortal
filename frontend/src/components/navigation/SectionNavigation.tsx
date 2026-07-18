import { useEffect, useState } from 'react';
import type { LucideIcon } from 'lucide-react';
import '../../styles/components/section-navigation.css';

export interface SectionNavigationItem { id: string; label: string; icon: LucideIcon; disabled?: boolean; }
export default function SectionNavigation({ items }: { items: SectionNavigationItem[] }) {
  const [active, setActive] = useState(items[0]?.id);
  useEffect(() => { const observer = new IntersectionObserver((entries) => { const visible = entries.filter((entry) => entry.isIntersecting).sort((a, b) => b.intersectionRatio - a.intersectionRatio)[0]; if (visible) setActive(visible.target.id); }, { rootMargin: '-20% 0px -65% 0px', threshold: [0.1, 0.5] }); items.forEach((item) => { const element = document.getElementById(item.id); if (element) observer.observe(element); }); return () => observer.disconnect(); }, [items]);
  return <nav className="section-navigation" aria-label="Seções da página"><span>Seções</span>{items.map((item) => { const Icon = item.icon; return <button type="button" key={item.id} className={active === item.id ? 'active' : ''} disabled={item.disabled} onClick={() => document.getElementById(item.id)?.scrollIntoView({ behavior: 'smooth', block: 'start' })}><Icon size={17} />{item.label}</button>; })}</nav>;
}
