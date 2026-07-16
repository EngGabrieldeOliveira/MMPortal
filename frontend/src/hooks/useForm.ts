import { useState, useCallback } from 'react';
import { ApiError } from '../../types/api';

export interface UseFormState<T> {
  values: T;
  errors: Record<keyof T, string | undefined>;
  touched: Record<keyof T, boolean>;
  isSubmitting: boolean;
  isDirty: boolean;
}

export interface UseFormOptions<T> {
  initialValues: T;
  onSubmit: (values: T) => Promise<void> | void;
  validate?: (values: T) => Record<keyof T, string | undefined>;
  onSuccess?: () => void;
  onError?: (error: ApiError) => void;
}

export interface UseFormReturn<T> extends UseFormState<T> {
  handleChange: (
    e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement>
  ) => void;
  handleBlur: (
    e: React.FocusEvent<HTMLInputElement | HTMLSelectElement>
  ) => void;
  handleSubmit: (
    e: React.FormEvent<HTMLFormElement>
  ) => Promise<void>;
  setFieldValue: (field: keyof T, value: any) => void;
  setFieldError: (field: keyof T, error: string | undefined) => void;
  reset: () => void;
  setErrors: (errors: Record<keyof T, string | undefined>) => void;
}

/**
 * Hook para gerenciar forms com validação e submit
 */
export function useForm<T extends Record<string, any>>(
  options: UseFormOptions<T>
): UseFormReturn<T> {
  const [values, setValues] = useState<T>(options.initialValues);
  const [errors, setErrors] = useState<Record<keyof T, string | undefined>>(
    {} as Record<keyof T, string | undefined>
  );
  const [touched, setTouched] = useState<Record<keyof T, boolean>>(
    {} as Record<keyof T, boolean>
  );
  const [isSubmitting, setIsSubmitting] = useState(false);

  const isDirty = JSON.stringify(values) !== JSON.stringify(options.initialValues);

  const handleChange = useCallback(
    (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement>) => {
      const { name, value, type } = e.target;
      const fieldValue = type === 'checkbox' ? (e.target as HTMLInputElement).checked : value;

      setValues((prev) => ({
        ...prev,
        [name]: fieldValue,
      }));

      // Validate on change if field was touched
      if (touched[name as keyof T] && options.validate) {
        const newErrors = options.validate({
          ...values,
          [name]: fieldValue,
        });
        setErrors((prev) => ({
          ...prev,
          [name]: newErrors[name as keyof T],
        }));
      }
    },
    [values, touched, options]
  );

  const handleBlur = useCallback(
    (e: React.FocusEvent<HTMLInputElement | HTMLSelectElement>) => {
      const { name } = e.target;

      setTouched((prev) => ({
        ...prev,
        [name]: true,
      }));

      // Validate on blur
      if (options.validate) {
        const newErrors = options.validate(values);
        setErrors((prev) => ({
          ...prev,
          [name]: newErrors[name as keyof T],
        }));
      }
    },
    [values, options]
  );

  const handleSubmit = useCallback(
    async (e: React.FormEvent<HTMLFormElement>) => {
      e.preventDefault();

      // Validate before submit
      if (options.validate) {
        const newErrors = options.validate(values);
        setErrors(newErrors);

        // Check if there are errors
        if (Object.values(newErrors).some((error) => error)) {
          return;
        }
      }

      try {
        setIsSubmitting(true);
        await options.onSubmit(values);
        options.onSuccess?.();
      } catch (error) {
        const apiError = error as ApiError;
        options.onError?.(apiError);
      } finally {
        setIsSubmitting(false);
      }
    },
    [values, options]
  );

  const setFieldValue = useCallback(
    (field: keyof T, value: any) => {
      setValues((prev) => ({
        ...prev,
        [field]: value,
      }));
    },
    []
  );

  const setFieldError = useCallback(
    (field: keyof T, error: string | undefined) => {
      setErrors((prev) => ({
        ...prev,
        [field]: error,
      }));
    },
    []
  );

  const reset = useCallback(() => {
    setValues(options.initialValues);
    setErrors({} as Record<keyof T, string | undefined>);
    setTouched({} as Record<keyof T, boolean>);
    setIsSubmitting(false);
  }, [options.initialValues]);

  return {
    values,
    errors,
    touched,
    isSubmitting,
    isDirty,
    handleChange,
    handleBlur,
    handleSubmit,
    setFieldValue,
    setFieldError,
    reset,
    setErrors,
  };
}
