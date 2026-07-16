import { Outlet } from 'react-router-dom';
import Header from '../components/layout/Header';
import Sidebar from '../components/layout/Sidebar';
import '../styles/layout.css';

export default function MainLayout() {
  return (
    <div className="layout-container">
      {/* Sidebar */}
      <aside className="layout-sidebar">
        <Sidebar />
      </aside>

      {/* Main content area */}
      <div className="layout-main">
        {/* Header */}
        <header className="layout-header">
          <Header />
        </header>

        {/* Page content */}
        <main className="layout-content">
          <Outlet />
        </main>
      </div>
    </div>
  );
}
