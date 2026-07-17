import clsx from 'clsx';
import { ArrowUpDown, ChevronLeft, ChevronRight, Pencil, Trash2 } from 'lucide-react';
import { useState } from 'react';
import '../../styles/components/table.css';

export interface TableColumn<T> {
  key: keyof T;
  label: string;
  sortable?: boolean;
  width?: string;
  render?: (value: any, row: T) => React.ReactNode;
}

export interface TableProps<T> {
  columns: TableColumn<T>[];
  data: T[];
  keyField: keyof T;
  loading?: boolean;
  emptyMessage?: string;
  onRowClick?: (row: T) => void;
  pagination?: boolean;
  pageSize?: number;
  actions?: boolean;
  onEdit?: (row: T) => void;
  onDelete?: (row: T) => void;
}

export default function Table<T extends Record<string, any>>({
  columns,
  data,
  keyField,
  loading = false,
  emptyMessage = 'Nenhum registro encontrado',
  onRowClick,
  pagination = true,
  pageSize = 10,
  actions = false,
  onEdit,
  onDelete,
}: TableProps<T>) {
  const [sortConfig, setSortConfig] = useState<{ key: keyof T; direction: 'asc' | 'desc' } | null>(null);
  const [currentPage, setCurrentPage] = useState(1);

  // Sorting
  let sortedData = [...data];
  if (sortConfig) {
    sortedData.sort((a, b) => {
      const aValue = a[sortConfig.key];
      const bValue = b[sortConfig.key];

      if (aValue < bValue) return sortConfig.direction === 'asc' ? -1 : 1;
      if (aValue > bValue) return sortConfig.direction === 'asc' ? 1 : -1;
      return 0;
    });
  }

  // Pagination
  const totalPages = pagination ? Math.ceil(sortedData.length / pageSize) : 1;
  const startIdx = (currentPage - 1) * pageSize;
  const endIdx = startIdx + pageSize;
  const paginatedData = pagination ? sortedData.slice(startIdx, endIdx) : sortedData;

  const handleSort = (column: TableColumn<T>) => {
    if (!column.sortable) return;

    setSortConfig((prev) => {
      if (prev?.key === column.key) {
        return prev.direction === 'asc'
          ? { key: column.key, direction: 'desc' }
          : null;
      }
      return { key: column.key, direction: 'asc' };
    });
  };

  if (loading) {
    return (
      <div className="table-container">
        <table className="table">
          <thead>
            <tr>
              {columns.map((col) => (
                <th key={String(col.key)}>{col.label}</th>
              ))}
              {actions && <th>Ações</th>}
            </tr>
          </thead>
          <tbody>
            <tr>
              <td colSpan={columns.length + Number(actions)} className="table-loading">
                Carregando...
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    );
  }

  if (paginatedData.length === 0) {
    return (
      <div className="table-container">
        <table className="table">
          <thead>
            <tr>
              {columns.map((col) => (
                <th key={String(col.key)}>{col.label}</th>
              ))}
              {actions && <th>Ações</th>}
            </tr>
          </thead>
          <tbody>
            <tr>
              <td colSpan={columns.length + Number(actions)} className="table-empty">
                {emptyMessage}
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    );
  }

  return (
    <div>
      <div className="table-container">
        <table className="table">
          <thead>
            <tr>
              {columns.map((col) => (
                <th
                  key={String(col.key)}
                  style={{ width: col.width }}
                  className={clsx({ 'table-sortable': col.sortable })}
                  onClick={() => handleSort(col)}
                >
                  <div className="table-header-content">
                    <span>{col.label}</span>
                    {col.sortable && (
                      <ArrowUpDown
                        size={14}
                        className={clsx('table-sort-icon', {
                          'table-sort-active':
                            sortConfig?.key === col.key,
                          'table-sort-desc':
                            sortConfig?.key === col.key &&
                            sortConfig.direction === 'desc',
                        })}
                      />
                    )}
                  </div>
                </th>
              ))}
              {actions && <th>Ações</th>}
            </tr>
          </thead>
          <tbody>
            {paginatedData.map((row) => (
              <tr
                key={String(row[keyField])}
                className={clsx({ 'table-clickable': onRowClick })}
                onClick={() => onRowClick?.(row)}
              >
                {columns.map((col) => (
                  <td key={String(col.key)}>
                    {col.render ? col.render(row[col.key], row) : row[col.key]}
                  </td>
                ))}
                {actions && (
                  <td className="table-actions">
                    {onEdit && <button type="button" className="table-action-button" aria-label="Editar" onClick={(event) => { event.stopPropagation(); onEdit(row); }}><Pencil size={16} /></button>}
                    {onDelete && <button type="button" className="table-action-button table-action-button-danger" aria-label="Excluir" onClick={(event) => { event.stopPropagation(); onDelete(row); }}><Trash2 size={16} /></button>}
                  </td>
                )}
              </tr>
            ))}
          </tbody>
        </table>
      </div>

      {pagination && totalPages > 1 && (
        <div className="table-pagination">
          <div className="pagination-info">
            Mostrando {startIdx + 1} a {Math.min(endIdx, sortedData.length)} de{' '}
            {sortedData.length}
          </div>
          <div className="pagination-buttons">
            <button
              onClick={() => setCurrentPage((p) => Math.max(p - 1, 1))}
              disabled={currentPage === 1}
              className="pagination-button"
            >
              <ChevronLeft size={18} />
            </button>

            <div className="pagination-pages">
              {Array.from({ length: totalPages }, (_, i) => i + 1).map((page) => (
                <button
                  key={page}
                  onClick={() => setCurrentPage(page)}
                  className={clsx('pagination-page', {
                    'pagination-page-active': page === currentPage,
                  })}
                >
                  {page}
                </button>
              ))}
            </div>

            <button
              onClick={() => setCurrentPage((p) => Math.min(p + 1, totalPages))}
              disabled={currentPage === totalPages}
              className="pagination-button"
            >
              <ChevronRight size={18} />
            </button>
          </div>
        </div>
      )}
    </div>
  );
}
