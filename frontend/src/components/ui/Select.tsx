import clsx from 'clsx';
import { ChevronDown, AlertCircle } from 'lucide-react';
import '../../styles/components/select.css';

export interface SelectOption {
  value: string | number;
  label: string;
  disabled?: boolean;
}

export interface SelectProps extends React.SelectHTMLAttributes<HTMLSelectElement> {
  label?: string;
  error?: string;
  hint?: string;
  options: SelectOption[];
  placeholder?: string;
}

export default function Select({
  label,
  error,
  hint,
  options,
  placeholder,
  className,
  ...props
}: SelectProps) {
  return (
    <div className="select-wrapper">
      {label && (
        <label className={clsx('select-label', { 'select-label-required': props.required })}>
          {label}
        </label>
      )}

      <div className="select-container">
        <select
          className={clsx('select', { 'select-error': error }, className)}
          {...props}
        >
          {placeholder && (
            <option value="" disabled>
              {placeholder}
            </option>
          )}
          {options.map((option) => (
            <option
              key={option.value}
              value={option.value}
              disabled={option.disabled}
            >
              {option.label}
            </option>
          ))}
        </select>
        <ChevronDown className="select-icon" size={18} />
      </div>

      {error && (
        <div className="select-error-message">
          <AlertCircle size={14} />
          <span>{error}</span>
        </div>
      )}

      {hint && !error && <p className="select-hint">{hint}</p>}
    </div>
  );
}
