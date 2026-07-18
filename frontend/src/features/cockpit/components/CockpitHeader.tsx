import { CalendarDays, ClipboardPlus, FilePlus2, Plus, UserPlus } from 'lucide-react';
import { Link } from 'react-router-dom';

export function CockpitHeader() {
  return (
    <header className="cockpit-header">
      <div>
        <p className="cockpit-eyebrow">Visão geral da empresa</p>
        <h1>Cockpit Executivo</h1>
        <p className="cockpit-subtitle">Indicadores essenciais para decisão da diretoria.</p>
      </div>
      <div className="cockpit-header-actions">
        <div className="cockpit-quick-actions" aria-label="Ações rápidas">
          <Link to="/comercial/clientes/novo"><UserPlus size={15} /> Novo Cliente</Link>
          <Link to="/comercial/orcamentos/novo"><FilePlus2 size={15} /> Novo Orçamento</Link>
          <Link to="/comercial/solicitacoes/nova"><Plus size={15} /> Nova Solicitação</Link>
          <button type="button" disabled title="A criação de OS será disponibilizada no módulo operacional."><ClipboardPlus size={15} /> Nova OS</button>
        </div>
        <div className="cockpit-header-meta" aria-label="Informações de atualização"><span><CalendarDays size={15} /> Atualizado na abertura</span></div>
      </div>
    </header>
  );
}
