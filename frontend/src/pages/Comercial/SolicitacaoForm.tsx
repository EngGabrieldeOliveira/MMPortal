import { useCallback, useEffect, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { ArrowLeft } from 'lucide-react';
import { Button, Card, CardBody, CardHeader, CardTitle, Input, Select } from '../../components/ui';
import { useApi } from '../../hooks/useApi';
import { useForm } from '../../hooks/useForm';
import * as clienteService from '../../services/comercial/cliente';
import * as solicitacaoService from '../../services/comercial/solicitacao';
import type { CreateSolicitacaoDTO } from '../../types/comercial';
import '../../styles/comercial.css';

interface FormValues extends Omit<CreateSolicitacaoDTO, 'cliente_id'> { cliente_id: string; }
const ORIGENS = ['whatsapp', 'email', 'telefone', 'indicacao', 'site', 'visita', 'outro'] as const;

export default function SolicitacaoForm() {
  const navigate = useNavigate();
  const [buscaCliente, setBuscaCliente] = useState('');
  const [anexos, setAnexos] = useState<File[]>([]);
  const buscarClientes = useCallback(() => clienteService.listarClientes({ limit: 30, search: buscaCliente }), [buscaCliente]);
  const { data: clientesData, loading: carregandoClientes, refetch } = useApi(buscarClientes);
  useEffect(() => { refetch(); }, [refetch]);
  const { values, errors, touched, isSubmitting, handleChange, handleBlur, handleSubmit } = useForm<FormValues>({
    initialValues: { cliente_id: '', nome_contato: '', email_contato: '', telefone_contato: '', origem: 'whatsapp', descricao: '', prazo_desejado: '' },
    validate: (form) => ({ cliente_id: undefined, nome_contato: (form.cliente_id && form.cliente_id !== 'novo') || form.nome_contato.trim() ? undefined : 'Informe o contato', email_contato: (form.cliente_id && form.cliente_id !== 'novo') || form.email_contato?.trim() ? undefined : 'Informe o e-mail', telefone_contato: (form.cliente_id && form.cliente_id !== 'novo') || form.telefone_contato?.trim() ? undefined : 'Informe o telefone', origem: undefined, descricao: form.descricao.trim() ? undefined : 'Descreva a demanda', prazo_desejado: undefined }),
    onSubmit: async (form) => {
      const cliente = (clientesData?.data ?? []).find((item) => item.id === Number(form.cliente_id));
      const solicitacao = await solicitacaoService.criarSolicitacao({ ...form, cliente_id: form.cliente_id && form.cliente_id !== 'novo' ? Number(form.cliente_id) : undefined, nome_contato: cliente?.nome || form.nome_contato, email_contato: cliente?.email || form.email_contato || undefined, telefone_contato: cliente?.telefone || form.telefone_contato || undefined, prazo_desejado: form.prazo_desejado || undefined });
      if (anexos.length) await solicitacaoService.anexarArquivos(solicitacao.id, anexos);
      navigate('/comercial/solicitacoes');
    },
  });
  const clientes = [{ value: 'novo', label: 'Cliente não cadastrado' }, ...(clientesData?.data ?? []).map((cliente) => ({ value: String(cliente.id), label: cliente.empresa ? `${cliente.nome} — ${cliente.empresa}` : cliente.nome }))];
  const clienteSelecionado = Boolean(values.cliente_id && values.cliente_id !== 'novo');
  return <div className="comercial-container"><div className="comercial-header"><Button variant="ghost" icon={<ArrowLeft size={20} />} onClick={() => navigate('/comercial/solicitacoes')}>Voltar</Button></div>
    <Card><CardHeader><CardTitle>Nova Solicitação</CardTitle></CardHeader><CardBody><form onSubmit={handleSubmit} className="form-grid">
      <div className="form-group form-group-full"><label htmlFor="cliente_id">Cliente</label><Input value={buscaCliente} onChange={(event) => setBuscaCliente(event.target.value)} placeholder="Buscar por nome, empresa, CPF ou CNPJ..." /><Select id="cliente_id" name="cliente_id" value={values.cliente_id} onChange={handleChange} onBlur={handleBlur} options={clientes} placeholder={carregandoClientes ? 'Buscando clientes...' : 'Selecione o cliente'} disabled={carregandoClientes} /></div>
      <div className="form-group"><label htmlFor="origem">Origem</label><Select id="origem" name="origem" value={values.origem} onChange={handleChange} onBlur={handleBlur} options={ORIGENS.map((origem) => ({ value: origem, label: origem.charAt(0).toUpperCase() + origem.slice(1) }))} /></div>
      {!clienteSelecionado && <><div className="form-group"><label htmlFor="nome_contato">Nome do contato</label><Input id="nome_contato" name="nome_contato" value={values.nome_contato} onChange={handleChange} onBlur={handleBlur} error={touched.nome_contato ? errors.nome_contato : undefined} /></div><div className="form-group"><label htmlFor="email_contato">E-mail</label><Input id="email_contato" name="email_contato" type="email" value={values.email_contato} onChange={handleChange} onBlur={handleBlur} error={touched.email_contato ? errors.email_contato : undefined} /></div><div className="form-group"><label htmlFor="telefone_contato">Telefone</label><Input id="telefone_contato" name="telefone_contato" value={values.telefone_contato} onChange={handleChange} onBlur={handleBlur} error={touched.telefone_contato ? errors.telefone_contato : undefined} /></div></>}
      <div className="form-group"><label htmlFor="prazo_desejado">Prazo desejado</label><Input id="prazo_desejado" name="prazo_desejado" type="date" value={values.prazo_desejado} onChange={handleChange} onBlur={handleBlur} /></div>
      <div className="form-group form-group-full"><label htmlFor="descricao">Demanda</label><Input id="descricao" name="descricao" value={values.descricao} onChange={handleChange} onBlur={handleBlur} error={touched.descricao ? errors.descricao : undefined} placeholder="Descreva o que o cliente precisa" /></div>
      <div className="form-group form-group-full"><label htmlFor="anexos">Fotos, PDF ou documentos</label><Input id="anexos" type="file" multiple accept="image/jpeg,image/png,image/webp,application/pdf,.doc,.docx,.xls,.xlsx" onChange={(event) => setAnexos(Array.from(event.target.files ?? []))} hint={anexos.length ? `${anexos.length} arquivo(s) selecionado(s)` : 'Até 10 arquivos, 10 MB por arquivo'} /></div>
      <div className="form-actions"><Button type="button" variant="secondary" onClick={() => navigate('/comercial/solicitacoes')}>Cancelar</Button><Button type="submit" loading={isSubmitting}>Registrar solicitação</Button></div>
    </form></CardBody></Card></div>;
}
