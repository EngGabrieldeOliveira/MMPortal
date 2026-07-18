import { ArrowUpRight } from 'lucide-react';
import { Link } from 'react-router-dom';
import type { WorkSummary } from '../types';

const statusLabels = { 'on-track': 'No prazo', attention: 'Atenção', late: 'Atrasada' };

export function WorksList({ works }: { works: WorkSummary[] }) {
  return (
    <section className="cockpit-panel cockpit-works">
      <div className="cockpit-panel-heading">
        <div><p className="cockpit-overline">Operação</p><h2>Obras em acompanhamento</h2></div>
        <button type="button" className="cockpit-link-button">Ver obras <ArrowUpRight size={16} /></button>
      </div>
      <div className="cockpit-works-list">
        {works.length === 0 && <div className="cockpit-empty-state">Nenhuma obra em operação no momento.</div>}
        {works.map((work) => <Link to={work.href} className="cockpit-work-item" key={work.id}>
          <div className="cockpit-work-info"><span className={`cockpit-work-status cockpit-work-status-${work.status}`} /><div><h3>{work.name}</h3><p>{work.id} · {work.client}</p></div></div>
          <div className="cockpit-work-stage"><span>{work.stage}</span><strong>{work.progress}%</strong><div className="cockpit-progress"><i style={{ width: `${work.progress}%` }} /></div></div>
          <small className={`cockpit-work-due cockpit-work-due-${work.status}`}>{work.due_label}</small>
          <span className={`cockpit-work-badge cockpit-work-badge-${work.status}`}>{statusLabels[work.status]}</span>
        </Link>)}
      </div>
    </section>
  );
}
