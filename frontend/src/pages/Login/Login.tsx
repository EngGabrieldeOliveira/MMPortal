import { useState } from 'react';
import { useNavigate, useLocation } from 'react-router-dom';
import { useAuth } from '../../context/AuthContext';
import { Button, Input } from '../../components/ui';
import { AlertCircle } from 'lucide-react';
import '../../styles/login.css';

interface LoginForm {
  email: string;
  password: string;
  remember: boolean;
}

export default function Login() {
  const navigate = useNavigate();
  const location = useLocation();
  const { login, isLoading, error } = useAuth();

  const [formData, setFormData] = useState<LoginForm>({
    email: '',
    password: '',
    remember: false,
  });

  const [formError, setFormError] = useState<string>('');

  const handleChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const { name, value, type, checked } = e.target;
    setFormData({
      ...formData,
      [name]: type === 'checkbox' ? checked : value,
    });
  };

  const handleSubmit = async (e: React.FormEvent<HTMLFormElement>) => {
    e.preventDefault();
    setFormError('');

    try {
      // Validação básica
      if (!formData.email || !formData.password) {
        setFormError('Email e senha são obrigatórios');
        return;
      }

      // Fazer login
      await login({
        email: formData.email,
        password: formData.password,
        remember: formData.remember,
      });

      // Redirecionar para a página anterior ou dashboard
      const from = (location.state?.from?.pathname as string) || '/dashboard';
      navigate(from, { replace: true });
    } catch (err) {
      // Erro já está no estado do auth
      console.error('Login error:', err);
    }
  };

  return (
    <div className="login-container">
      <div className="login-card">
        <div className="login-header">
          <div className="login-logo">
            <div className="login-logo-icon">MP</div>
            <div>
              <h1 className="login-logo-text">MMPortal</h1>
              <p className="login-logo-subtitle">Sistema de Gestão Integrada</p>
            </div>
          </div>
        </div>

        <div className="login-content">
          <h2 className="login-title">Bem-vindo</h2>
          <p className="login-description">
            Entre com suas credenciais para acessar o sistema
          </p>

          {(formError || error) && (
            <div className="login-error">
              <AlertCircle size={18} />
              <span>{formError || error}</span>
            </div>
          )}

          <form onSubmit={handleSubmit} className="login-form">
            <Input
              label="Email"
              type="email"
              name="email"
              placeholder="seu@email.com"
              value={formData.email}
              onChange={handleChange}
              required
            />

            <Input
              label="Senha"
              type="password"
              name="password"
              placeholder="Sua senha"
              value={formData.password}
              onChange={handleChange}
              required
            />

            <div className="login-remember">
              <input
                type="checkbox"
                id="remember"
                name="remember"
                checked={formData.remember}
                onChange={handleChange}
              />
              <label htmlFor="remember">Lembrar-me neste computador</label>
            </div>

            <Button
              type="submit"
              className="login-button"
              loading={isLoading}
              disabled={isLoading}
            >
              {isLoading ? 'Entrando...' : 'Entrar'}
            </Button>
          </form>

          <div className="login-footer">
            <p>
              Credenciais de teste:
              <br />
              <small>
                Email: <strong>admin@mmportal.local</strong>
                <br />
                Senha: <strong>admin123</strong>
              </small>
            </p>
          </div>
        </div>
      </div>

      <div className="login-background">
        <div className="login-bg-element bg-1"></div>
        <div className="login-bg-element bg-2"></div>
        <div className="login-bg-element bg-3"></div>
      </div>
    </div>
  );
}
