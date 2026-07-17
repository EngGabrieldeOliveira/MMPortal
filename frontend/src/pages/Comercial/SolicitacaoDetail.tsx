import { useCallback, useEffect, useRef, useState } from 'react';
import { useNavigate, useParams } from 'react-router-dom';
import { ArrowLeft, CalendarDays, Download, Eye, FileText, Mail, MapPin, Pencil, Phone, Trash2, Upload, UserRound } from 'lucide-react';
import { Badge, Button, Card, CardBody, CardHeader, CardTitle, EmptyState, Select } from '../../components/ui';
import { useApi } from '../../hooks/useApi';
import * as solicitacaoService from '../../services/comercial/solicitacao';
import '../../styles/cliente-detail.css';

const cores: Record<string, 'info' | 'warning' | 'success' | 'error'> = {
  nova: 'info', em_analise: 'warning', em_orcamento: 'warning', orcamento_enviado: 'info',
  negociacao: 'warning', postergada: 'warning', encerrada: 'error', convertida: 'success',
};

export default function SolicitacaoDetail() {
  const { id } = useParams<{ id: string }>();
  const navigate = useNavigate();
  const buscar = useCallback(() => solicitacaoService.obterSolicitacao(Number(id)), [id]);
  const { data: solicitacao, loading, error, refetch } = useApi(buscar);
  const [salvandoStatus, setSalvandoStatus] = useState(false);
  const [enviandoArquivos, setEnviandoArquivos] = useState(false);
  const inputArquivos = useRef<HTMLInputElement>(null);

  useEffect(() => { refetch(); }, [refetch]);

  const alterarStatus = async (status: string) => {
    if (!id || !solicitacao || status === solicitacao.status) return;
    setSalvandoStatus(true);
    try {
      await solicitacaoService.atualizarSolicitacao(Number(id), { status: status as typeof solicitacao.status });
      await refetch();
    } finally {
      setSalvandoStatus(false);
    }
  };

  const anexarDocumentos = async (event: React.ChangeEvent<HTMLInputElement>) => {
    const arquivos = Array.from(event.target.files || []);
    if (!id || arquivos.length === 0) return;
    setEnviandoArquivos(true);
    try {
      await solicitacaoService.anexarArquivos(Number(id), arquivos);
      await refetch();
    } finally {
      setEnviandoArquivos(false);
      event.target.value = '';
    }
  };

  const renomearAnexo = async (anexo: { id: number; nome_original: string }) => {
    const nome = window.prompt('Novo nome do arquivo:', anexo.nome_original)?.trim();
    if (!id || !nome || nome === anexo.nome_original) return;
    await solicitacaoService.renomearAnexo(Number(id), anexo.id, nome);
    await refetch();
  };

  const excluirAnexo = async (anexo: { id: number; nome_original: string }) => {
    if (!id || !window.confirm(`Excluir o arquivo “${anexo.nome_original}”?`)) return;
    await solicitacaoService.excluirAnexo(Number(id), anexo.id);
    await refetch();
  };

  const visualizarAnexo = async (anexo: { id: number }) => {
    if (!id) return;
    const janela = window.open('', '_blank');
    try {
      const url = await solicitacaoService.obterArquivoAnexo(Number(id), anexo.id, 'visualizar');
      if (janela) janela.location.href = url;
      else window.open(url, '_blank');
      window.setTimeout(() => URL.revokeObjectURL(url), 60_000);
    } catch {
      janela?.close();
    }
  };

  const baixarAnexo = async (anexo: { id: number; nome_original: string }) => {
    if (!id) return;
    const url = await solicitacaoService.obterArquivoAnexo(Number(id), anexo.id, 'baixar');
    const link = document.createElement('a');
    link.href = url;
    link.download = anexo.nome_original;
    document.body.appendChild(link);
    link.click();
    link.remove();
    window.setTimeout(() => URL.revokeObjectURL(url), 5_000);
  };

  if (loading) return <div className="detail-loading">Carregando solicitação...</div>;
  if (error || !solicitacao) return <div className="detail-loading">Solicitação não encontrada.</div>;

  return (
    <div className="cliente-detail">
      <header className="cliente-detail-header">
        <button className="cliente-back" onClick={() => navigate('/comercial/solicitacoes')} aria-label="Voltar"><ArrowLeft size={19} /></button>
        <div className="cliente-title">
          <h1>{solicitacao.codigo} <Badge color={cores[solicitacao.status] || 'info'}>{solicitacao.status.replaceAll('_', ' ')}</Badge></h1>
          <p><UserRound size={16} /> {solicitacao.nome_contato}</p>
        </div>
        <div className="cliente-actions">
          <Select aria-label="Status da solicitação" value={solicitacao.status} disabled={salvandoStatus || !['nova', 'em_analise'].includes(solicitacao.status)} onChange={(event) => alterarStatus(event.target.value)} options={[
            { value: 'nova', label: 'Nova' }, { value: 'em_analise', label: 'Em análise' },
          ]} />
          <Button onClick={() => navigate('/comercial/orcamentos/novo')}>Criar Orçamento</Button>
        </div>
      </header>
      <div className="cliente-detail-grid">
        <div className="cliente-left-column">
          <Card className="cliente-info-card"><CardHeader><CardTitle>Informações</CardTitle></CardHeader><CardBody>
            <div className="info-block"><span>CLIENTE / CONTATO</span><p><UserRound size={16} /> {solicitacao.cliente?.nome || solicitacao.nome_contato}</p><p><Mail size={16} /> {solicitacao.email_contato || 'E-mail não informado'}</p><p><Phone size={16} /> {solicitacao.telefone_contato || 'Telefone não informado'}</p></div>
            <div className="info-block"><span>ORIGEM E PRAZO</span><p><MapPin size={16} /> {solicitacao.origem}</p><p><CalendarDays size={16} /> {solicitacao.prazo_desejado || 'Sem prazo informado'}</p></div>
          </CardBody></Card>
          <Card className="cliente-documentos"><CardHeader><CardTitle><FileText size={18} /> Documentos</CardTitle><input ref={inputArquivos} hidden type="file" multiple accept=".jpg,.jpeg,.png,.webp,.pdf,.doc,.docx,.xls,.xlsx" onChange={anexarDocumentos} /><Button size="sm" variant="secondary" loading={enviandoArquivos} icon={<Upload size={15} />} onClick={() => inputArquivos.current?.click()}>Anexar</Button></CardHeader><CardBody>
            {solicitacao.anexos?.length ? solicitacao.anexos.map((anexo) => <div className="anexo-item" key={anexo.id}><FileText size={16} /><button type="button" className="anexo-nome" title="Visualizar arquivo" onClick={() => visualizarAnexo(anexo)}>{anexo.nome_original}</button><button type="button" className="anexo-acao" aria-label={`Visualizar ${anexo.nome_original}`} title="Visualizar" onClick={() => visualizarAnexo(anexo)}><Eye size={14} /></button><button type="button" className="anexo-acao" aria-label={`Baixar ${anexo.nome_original}`} title="Baixar" onClick={() => baixarAnexo(anexo)}><Download size={14} /></button><button type="button" className="anexo-acao" aria-label={`Renomear ${anexo.nome_original}`} title="Renomear" onClick={() => renomearAnexo(anexo)}><Pencil size={14} /></button><button type="button" className="anexo-acao anexo-excluir" aria-label={`Excluir ${anexo.nome_original}`} title="Excluir" onClick={() => excluirAnexo(anexo)}><Trash2 size={14} /></button></div>) : <EmptyState icon={<FileText size={36} />} title="Nenhum documento" description="Nenhum anexo foi enviado." />}
          </CardBody></Card>
        </div>
        <Card className="cliente-tabs-card"><CardHeader><CardTitle>Demanda do serviço</CardTitle></CardHeader><CardBody><p className="solicitacao-demanda">{solicitacao.descricao}</p></CardBody></Card>
      </div>
    </div>
  );
}
