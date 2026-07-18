import { Building2, ClipboardCheck, FileText, MessageSquareText, PackageCheck, ShoppingCart } from 'lucide-react';
import type { MovementItem } from '../types';

const movementIcons = { quote: FileText, work: ClipboardCheck, purchase: ShoppingCart, client: Building2, order: PackageCheck, request: MessageSquareText };
const movementModules = { quote: 'Comercial', work: 'Produção', purchase: 'Compras', client: 'Comercial', order: 'Financeiro', request: 'Comercial' };

export function RecentMovements({ movements }: { movements: MovementItem[] }) {
  return <section className="cockpit-panel cockpit-movements"><div className="cockpit-panel-heading"><div><p className="cockpit-overline">Linha do tempo</p><h2>Últimas movimentações</h2></div></div><ol className="cockpit-movement-list">{movements.length === 0 && <li className="cockpit-empty-state">Nenhuma movimentação registrada ainda.</li>}{movements.map((movement) => { const Icon = movementIcons[movement.type]; return <li key={movement.id}><span className={`cockpit-movement-icon cockpit-movement-${movement.type}`}><Icon size={17} /></span><div><div className="cockpit-movement-title"><h3>{movement.title}</h3><span>{movementModules[movement.type]}</span></div><p>{movement.description}</p></div><time>{movement.time}</time></li>; })}</ol></section>;
}
