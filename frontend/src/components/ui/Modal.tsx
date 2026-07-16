import clsx from 'clsx';
import { X } from 'lucide-react';
import { useEffect } from 'react';
import Button from './Button';
import '../../styles/components/modal.css';

export interface ModalProps {
  isOpen: boolean;
  title: string;
  children: React.ReactNode;
  onClose: () => void;
  onConfirm?: () => void;
  confirmText?: string;
  cancelText?: string;
  size?: 'sm' | 'md' | 'lg';
  confirmLoading?: boolean;
  isDangerous?: boolean;
}

export default function Modal({
  isOpen,
  title,
  children,
  onClose,
  onConfirm,
  confirmText = 'Confirmar',
  cancelText = 'Cancelar',
  size = 'md',
  confirmLoading = false,
  isDangerous = false,
}: ModalProps) {
  useEffect(() => {
    if (isOpen) {
      document.body.style.overflow = 'hidden';
    } else {
      document.body.style.overflow = 'unset';
    }

    return () => {
      document.body.style.overflow = 'unset';
    };
  }, [isOpen]);

  if (!isOpen) return null;

  return (
    <div className="modal-overlay" onClick={onClose}>
      <div
        className={clsx('modal', `modal-${size}`)}
        onClick={(e) => e.stopPropagation()}
      >
        <div className="modal-header">
          <h2 className="modal-title">{title}</h2>
          <button className="modal-close" onClick={onClose} aria-label="Fechar">
            <X size={20} />
          </button>
        </div>

        <div className="modal-body">{children}</div>

        {(onConfirm || cancelText) && (
          <div className="modal-footer">
            <Button variant="ghost" onClick={onClose} disabled={confirmLoading}>
              {cancelText}
            </Button>
            {onConfirm && (
              <Button
                variant={isDangerous ? 'danger' : 'primary'}
                onClick={onConfirm}
                loading={confirmLoading}
              >
                {confirmText}
              </Button>
            )}
          </div>
        )}
      </div>
    </div>
  );
}
