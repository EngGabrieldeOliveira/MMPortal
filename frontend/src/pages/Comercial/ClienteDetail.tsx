import { useCallback, useEffect, useState } from 'react';
import { useNavigate, useParams } from 'react-router-dom';
import { ArrowLeft, Building2, CalendarDays, Mail, MapPin, Phone, Pencil, FileText, Upload, FileQuestion } from 'lucide-react';
import { Badge, Button, Card, CardBody, CardHeader, CardTitle, EmptyState } from '../../components/ui';
import { useApi } from '../../hooks/useApi';
import * as clienteService from '../../services/comercial/cliente';
import '../../styles/cliente-detail.css';

type Aba = 'orcamentos' | 'obras' | 'contatos';

export default function ClienteDetail() {
  const { id } = useParams<{ id: string }>(); const navigate = useNavigate(); const [aba, setAba] = useState<Aba>('orcamentos');
  const buscar = useCallback(() => clienteService.obterCliente(Number(id)), [id]);
  const { data: cliente, loading, error, refetch } = useApi(buscar);
  useEffect(() => { refetch(); }, [refetch]);
  if (loading) return <div className="detail-loading">Carregando cliente...</div>;
  if (error || !cliente) return <div className="detail-loading">Cliente não encontrado.</div>;
  const localizacao = [cliente.logradouro, cliente.numero, cliente.bairro, cliente.cidade && `${cliente.cidade} - ${cliente.estado}`].filter(Boolean).join(', ') || `${cliente.cidade} - ${cliente.estado}`;
  const dataRegistro = cliente.created_at || cliente.data_criacao;
  return <div className="cliente-detail"><header className="cliente-detail-header"><button className="cliente-back" onClick={() => navigate('/comercial/clientes')} aria-label="Voltar"><ArrowLeft size={19} /></button><div className="cliente-title"><h1>{cliente.nome} <Badge color={cliente.status === 'ativo' ? 'success' : 'error'}>{cliente.status}</Badge></h1><p><Building2 size={16} /> {cliente.codigo} · {cliente.documento || 'Documento não informado'}</p></div><div className="cliente-actions"><Button variant="secondary" icon={<Pencil size={17} />} onClick={() => navigate(`/comercial/clientes/${cliente.id}/editar`)}>Editar</Button><Button onClick={() => navigate('/comercial/orcamentos/novo')}>Novo Orçamento</Button></div></header>
    <div className="cliente-detail-grid"><div className="cliente-left-column"><Card className="cliente-info-card"><CardHeader><CardTitle>Informações</CardTitle></CardHeader><CardBody><div className="info-block"><span>CONTATO PRINCIPAL</span><p><Mail size={16} /> {cliente.email}</p><p><Phone size={16} /> {cliente.telefone}</p></div><div className="info-block"><span>ENDEREÇO</span><p><MapPin size={16} /> {localizacao}</p></div><div className="info-block"><span>REGISTRO</span><p><CalendarDays size={16} /> Criado em {dataRegistro ? new Date(dataRegistro).toLocaleDateString('pt-BR') : '—'}</p></div></CardBody></Card><Card className="cliente-documentos"><CardHeader><CardTitle><FileText size={18} /> Documentos</CardTitle><Button size="sm" variant="secondary" icon={<Upload size={15} />}>Enviar documento</Button></CardHeader><CardBody><div className="documentos-vazio"><FileQuestion size={34} /><p>Nenhum documento enviado ainda</p></div></CardBody></Card></div>
      <Card className="cliente-tabs-card"><div className="cliente-tabs">{(['orcamentos', 'obras', 'contatos'] as Aba[]).map((item) => <button key={item} type="button" onClick={() => setAba(item)} className={aba === item ? 'cliente-tab-active' : ''}>{item.charAt(0).toUpperCase() + item.slice(1)}</button>)}</div><CardBody><EmptyState icon={<FileText size={42} />} title={`Nenhum ${aba === 'obras' ? 'registro de obra' : aba === 'orcamentos' ? 'orçamento' : 'contato adicional'}`} description={aba === 'orcamentos' ? 'Crie uma proposta comercial para este cliente.' : 'Os registros aparecerão aqui.'} /></CardBody></Card>
    </div></div>;
}
