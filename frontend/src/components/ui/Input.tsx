import clsx from 'clsx';
import { AlertCircle } from 'lucide-react';
import '../../styles/components/input.css';

export interface InputProps extends React.InputHTMLAttributes<HTMLInputElement> {
  label?: string;
  error?: string;
  hint?: string;
  icon?: React.ReactNode;
}

export default function Input({
  label,
  error,
  hint,
  icon,
  className,
  type = 'text',
  ...props
}: InputProps) {
  return (
    <div className="input-wrapper">
      {label && (
        <label className={clsx('input-label', { 'input-label-required': props.required })}>
          {label}
        </label>
      )}

      <div className="input-container">
        {icon && <span className="input-icon">{icon}</span>}
        <input
          type={type}
          className={clsx('input', { 'input-error': error, 'input-with-icon': icon }, className)}
          {...props}
        />
      </div>

      {error && (
        <div className="input-error-message">
          <AlertCircle size={14} />
          <span>{error}</span>
        </div>
      )}

      {hint && !error && <p className="input-hint">{hint}</p>}
    </div>
  );
}
