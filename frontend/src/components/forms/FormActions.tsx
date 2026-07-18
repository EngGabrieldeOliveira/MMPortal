import type { ReactNode } from 'react';
import { Button } from '../ui';
import '../../styles/components/form-actions.css';

interface FormActionsProps { onCancel: () => void; saving?: boolean; saveLabel?: string; extra?: ReactNode; }

export default function FormActions({ onCancel, saving = false, saveLabel = 'Salvar', extra }: FormActionsProps) {
  return <footer className="form-actions-standard"><Button type="button" variant="secondary" onClick={onCancel}>Cancelar</Button>{extra}<Button type="submit" loading={saving} disabled={saving}>{saveLabel}</Button></footer>;
}
