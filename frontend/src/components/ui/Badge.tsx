import clsx from 'clsx';
import '../../styles/components/badge.css';

export type BadgeVariant = 'success' | 'danger' | 'warning' | 'info' | 'default';
export type BadgeSize = 'sm' | 'md' | 'lg';

export interface BadgeProps extends React.HTMLAttributes<HTMLSpanElement> {
  variant?: BadgeVariant;
  /** Compatibilidade com telas que usam a nomenclatura de cor. */
  color?: BadgeVariant | 'error';
  size?: BadgeSize;
  children: React.ReactNode;
}

export default function Badge({
  variant = 'default',
  color,
  size = 'md',
  className,
  children,
  ...props
}: BadgeProps) {
  return (
    <span
      className={clsx(
        'badge',
        `badge-${color === 'error' ? 'danger' : color || variant}`,
        `badge-${size}`,
        className
      )}
      {...props}
    >
      {children}
    </span>
  );
}
