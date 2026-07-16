import { useState } from 'react';
import {
  Button,
  Card,
  CardHeader,
  CardBody,
  CardFooter,
  CardTitle,
  Input,
  Select,
  Table,
  Modal,
  Badge,
  EmptyState,
} from '../components/ui';
import { CheckCircle } from 'lucide-react';
import '../styles/showcase.css';

interface DemoItem {
  id: number;
  name: string;
  category: string;
  status: 'active' | 'inactive' | 'pending';
  value: number;
}

const demoData: DemoItem[] = [
  { id: 1, name: 'Item 1', category: 'Categoria A', status: 'active', value: 100 },
  { id: 2, name: 'Item 2', category: 'Categoria B', status: 'pending', value: 200 },
  { id: 3, name: 'Item 3', category: 'Categoria A', status: 'inactive', value: 150 },
  { id: 4, name: 'Item 4', category: 'Categoria C', status: 'active', value: 300 },
  { id: 5, name: 'Item 5', category: 'Categoria B', status: 'active', value: 250 },
];

const tableColumns = [
  { key: 'name' as const, label: 'Nome', sortable: true },
  { key: 'category' as const, label: 'Categoria', sortable: true },
  { key: 'status' as const, label: 'Status', sortable: true, render: (value: string) => {
    const variants: Record<string, 'success' | 'danger' | 'warning'> = {
      active: 'success',
      inactive: 'danger',
      pending: 'warning',
    };
    return <Badge variant={variants[value]}>{value}</Badge>;
  }},
  { key: 'value' as const, label: 'Valor', sortable: true, render: (value: number) => `R$ ${value.toLocaleString()}` },
];

export default function ComponentShowcase() {
  const [modalOpen, setModalOpen] = useState(false);
  const [formData, setFormData] = useState({ name: '', category: '', email: '' });
  const [emptyMode, setEmptyMode] = useState(false);

  return (
    <div className="showcase">
      <div className="showcase-header">
        <h1>Component Showcase</h1>
        <p>UI Kit do MMPortal - Todos os componentes em ação</p>
      </div>

      {/* Buttons */}
      <Card className="showcase-card">
        <CardHeader>
          <CardTitle>Buttons</CardTitle>
        </CardHeader>
        <CardBody>
          <div className="showcase-grid">
            <div>
              <p className="showcase-label">Primary</p>
              <div className="showcase-items">
                <Button>Padrão</Button>
                <Button size="sm">Pequeno</Button>
                <Button size="lg">Grande</Button>
                <Button loading>Carregando</Button>
                <Button disabled>Desabilitado</Button>
              </div>
            </div>
            <div>
              <p className="showcase-label">Secondary</p>
              <div className="showcase-items">
                <Button variant="secondary">Padrão</Button>
                <Button variant="secondary" size="sm">Pequeno</Button>
                <Button variant="secondary" size="lg">Grande</Button>
              </div>
            </div>
            <div>
              <p className="showcase-label">Danger</p>
              <div className="showcase-items">
                <Button variant="danger">Padrão</Button>
                <Button variant="danger" size="sm">Pequeno</Button>
                <Button variant="danger" size="lg">Grande</Button>
              </div>
            </div>
            <div>
              <p className="showcase-label">Ghost</p>
              <div className="showcase-items">
                <Button variant="ghost">Padrão</Button>
                <Button variant="ghost" size="sm">Pequeno</Button>
                <Button variant="ghost" size="lg">Grande</Button>
              </div>
            </div>
          </div>
        </CardBody>
      </Card>

      {/* Form Components */}
      <Card className="showcase-card">
        <CardHeader>
          <CardTitle>Form Components</CardTitle>
        </CardHeader>
        <CardBody>
          <div className="showcase-form">
            <Input
              label="Nome completo"
              placeholder="Digite seu nome"
              value={formData.name}
              onChange={(e) => setFormData({ ...formData, name: e.target.value })}
            />
            <Input
              label="Email"
              type="email"
              placeholder="seu@email.com"
              value={formData.email}
              onChange={(e) => setFormData({ ...formData, email: e.target.value })}
            />
            <Select
              label="Categoria"
              placeholder="Selecione uma categoria"
              value={formData.category}
              onChange={(e) => setFormData({ ...formData, category: e.target.value })}
              options={[
                { value: 'cat-a', label: 'Categoria A' },
                { value: 'cat-b', label: 'Categoria B' },
                { value: 'cat-c', label: 'Categoria C' },
              ]}
            />
            <Input
              label="Campo com erro"
              placeholder="Este campo tem erro"
              error="Este é um exemplo de mensagem de erro"
            />
            <Input
              label="Campo com dica"
              placeholder="Digite aqui"
              hint="Esta é uma dica para ajudar o usuário"
            />
          </div>
        </CardBody>
      </Card>

      {/* Badges */}
      <Card className="showcase-card">
        <CardHeader>
          <CardTitle>Badges</CardTitle>
        </CardHeader>
        <CardBody>
          <div className="showcase-badges">
            <div>
              <p className="showcase-label">Sizes</p>
              <div className="showcase-items">
                <Badge size="sm">Pequeno</Badge>
                <Badge size="md">Médio</Badge>
                <Badge size="lg">Grande</Badge>
              </div>
            </div>
            <div>
              <p className="showcase-label">Variants</p>
              <div className="showcase-items">
                <Badge variant="default">Default</Badge>
                <Badge variant="success">Success</Badge>
                <Badge variant="danger">Danger</Badge>
                <Badge variant="warning">Warning</Badge>
                <Badge variant="info">Info</Badge>
              </div>
            </div>
          </div>
        </CardBody>
      </Card>

      {/* Table */}
      <Card className="showcase-card">
        <CardHeader>
          <CardTitle>Table</CardTitle>
        </CardHeader>
        <CardBody>
          <Table<DemoItem>
            columns={tableColumns}
            data={emptyMode ? [] : demoData}
            keyField="id"
            emptyMessage="Nenhum item encontrado"
          />
          <div style={{ marginTop: '1rem' }}>
            <Button
              variant="secondary"
              size="sm"
              onClick={() => setEmptyMode(!emptyMode)}
            >
              {emptyMode ? 'Mostrar Dados' : 'Mostrar Vazio'}
            </Button>
          </div>
        </CardBody>
      </Card>

      {/* Empty State */}
      <Card className="showcase-card">
        <CardHeader>
          <CardTitle>Empty State</CardTitle>
        </CardHeader>
        <CardBody>
          <EmptyState
            title="Nenhum resultado encontrado"
            description="Tente ajustar seus critérios de busca ou criar um novo item"
            icon={<CheckCircle size={48} />}
            action={{ label: 'Criar Novo', onClick: () => alert('Criar novo!') }}
          />
        </CardBody>
      </Card>

      {/* Modal */}
      <Card className="showcase-card">
        <CardHeader>
          <CardTitle>Modal</CardTitle>
        </CardHeader>
        <CardBody>
          <div className="showcase-items">
            <Button onClick={() => setModalOpen(true)}>Abrir Modal</Button>
          </div>
        </CardBody>
      </Card>

      <Modal
        isOpen={modalOpen}
        title="Exemplo de Modal"
        onClose={() => setModalOpen(false)}
        onConfirm={() => {
          alert('Confirmado!');
          setModalOpen(false);
        }}
        confirmText="Confirmar"
        cancelText="Cancelar"
        size="md"
      >
        <p>Este é um exemplo de modal. Você pode colocar qualquer conteúdo aqui.</p>
        <p>Inclui header, body, footer e pode ser customizado de várias formas.</p>
      </Modal>
    </div>
  );
}
