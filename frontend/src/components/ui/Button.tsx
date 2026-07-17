import clsx from 'clsx';
import { Loader } from 'lucide-react';
import '../../styles/components/button.css';

export interface ButtonProps
  extends React.ButtonHTMLAttributes<HTMLButtonElement> {
  variant?: 'primary' | 'secondary' | 'danger' | 'ghost';
  size?: 'sm' | 'md' | 'lg';
  loading?: boolean;
  icon?: React.ReactNode;
  children: React.ReactNode;
}

export default function Button({
  variant = 'primary',
  size = 'md',
  loading = false,
  icon,
  disabled = false,
  className,
  children,
  ...props
}: ButtonProps) {
  return (
    <button
      className={clsx(
        'button',
        `button-${variant}`,
        `button-${size}`,
        {
          'button-disabled': disabled || loading,
        },
        className
      )}
      disabled={disabled || loading}
      {...props}
    >
      {loading && <Loader className="button-loader" size={16} />}
      {!loading && icon}
      {children}
    </button>
  );
}
