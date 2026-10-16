<!DOCTYPE html>
<html lang="ar" dir="rtl" class="{{ (isset($_COOKIE['ui_theme']) && $_COOKIE['ui_theme'] === 'dark') ? 'theme-dark' : '' }}" data-bs-theme="{{ (isset($_COOKIE['ui_theme']) && $_COOKIE['ui_theme'] === 'dark') ? 'dark' : 'light' }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>مؤسسة انسان الخيرية</title>
  {{-- Anti-FOUC Immediate Theme Script: prevents white flash on refresh/load --}}
  <script>
    (function () {
      try {
        var theme = localStorage.getItem('ui.theme');
        if (!theme) {
          var m = document.cookie.match(/(?:^|; )ui_theme=([^;]*)/);
          if (m) theme = decodeURIComponent(m[1]);
        }
        if (theme === 'dark' || (!theme && window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
          document.documentElement.classList.add('theme-dark');
          document.documentElement.setAttribute('data-bs-theme', 'dark');
          document.documentElement.style.backgroundColor = '#020617';
          document.documentElement.style.color = '#f8fafc';
        } else if (theme === 'light') {
          document.documentElement.classList.remove('theme-dark');
          document.documentElement.setAttribute('data-bs-theme', 'light');
          document.documentElement.style.backgroundColor = '';
          document.documentElement.style.color = '';
        }
      } catch (e) {}
    })();
  </script>
  <style>
    /* Critical Anti-Flash Styles */
    html.theme-dark,
    html.theme-dark body {
      background-color: #020617 !important;
      color: #f8fafc !important;
    }
  </style>
  <link rel="icon" href="{{ asset('images/heart-icon.png') }}" type="image/png">
  <link href="{{ asset('css/bootstrap.rtl.min.css') }}" rel="stylesheet">
  <link href="{{ asset('css/bootstrap-icons.css') }}" rel="stylesheet">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
  <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <link rel="stylesheet" href="{{ asset('css/style.css') }}?v={{ filemtime(public_path('css/style.css')) }}">
  <style>
      :root {
          /* Brand Identity - SaaS Clean Style */
          --primary: #059669;
          --primary-dark: #047857;
          --primary-light: #ecfdf5;
          --secondary: #111111;
          --text-main: #111111;
          --text-muted: #6B7280;
          --border: #E5E7EB;
          --bg: #FFFFFF;
          --bg-soft: #F9FAFB;
          --white: #FFFFFF;

          /* UI Tokens */
          --shadow-sm: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
          --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
          --radius: 12px;
          --radius-sm: 8px;

          /* Bootstrap 5 Native Overrides - Essential for opacity utilities */
          --bs-primary: #059669;
          --bs-primary-rgb: 5, 150, 105;

          /* Legacy Compatibility */
          --ws-primary: var(--primary);
          --ws-bg-page: var(--bg-soft);
          --ws-bg-card: var(--bg);
          --ws-bg-input: var(--bg);
          --ws-text-primary: var(--text-main);
          --ws-text-secondary: var(--text-muted);
          --ws-border: var(--border);      /* Premium Sidebar Overhaul */
      }
      .sidebar-fixed {
          background: linear-gradient(135deg, var(--bg-card) 0%, var(--bg-soft) 100%);
      }

      .theme-dark .sidebar-fixed {
          background: linear-gradient(135deg, #0f172a 0%, #020617 100%);
      }

      /* ======================================================
       * Premium Sidebar & Section Navigation Overhaul
       * ====================================================== */
      .sidebar-fixed {
          padding: 0.75rem 0.5rem !important;
      }

      .sidebar-fixed .list-group-item {
          border: none !important;
          border-radius: 10px !important;
          margin-bottom: 0.35rem;
          font-weight: 500;
          font-size: 0.93rem;
          background: transparent !important;
          color: #94a3b8 !important;
          padding: 0.65rem 0.85rem !important;
          display: flex !important;
          align-items: center !important;
          justify-content: space-between;
          text-decoration: none !important;
          transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1), background-color 0.25s ease, color 0.25s ease, box-shadow 0.25s ease !important;
          cursor: pointer;
      }

      /* Flex group for Icon + Label (collapsible and non-collapsible items) */
      .sidebar-fixed .list-group-item > span:first-child,
      .sidebar-fixed .list-group-item:not([data-bs-toggle="collapse"]) {
          display: inline-flex !important;
          align-items: center !important;
          gap: 0.75rem !important;
          flex-grow: 1;
      }

      /* Section Leading Icon Container */
      .sidebar-fixed .list-group-item i:not(.sidebar-toggle-icon) {
          display: inline-flex !important;
          align-items: center !important;
          justify-content: center !important;
          width: 32px !important;
          height: 32px !important;
          min-width: 32px !important;
          border-radius: 8px !important;
          font-size: 1.15rem !important;
          color: #94a3b8 !important;
          background: rgba(255, 255, 255, 0.04) !important;
          transition: all 0.25s cubic-bezier(0.34, 1.56, 0.64, 1) !important;
          flex-shrink: 0;
      }

      /* Hover States */
      .sidebar-fixed .list-group-item:hover {
          color: #f8fafc !important;
          background: rgba(255, 255, 255, 0.06) !important;
          transform: translateX(-4px) !important;
      }

      .sidebar-fixed .list-group-item:hover i:not(.sidebar-toggle-icon) {
          color: #10b981 !important;
          background: rgba(16, 185, 129, 0.14) !important;
          transform: scale(1.1) rotate(-3deg);
          box-shadow: 0 0 12px rgba(16, 185, 129, 0.25);
      }

      /* Active Main Section Link */
      .sidebar-fixed .list-group-item.active {
          background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
          color: #ffffff !important;
          font-weight: 600;
          box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4) !important;
          transform: translateX(-4px);
      }

      .sidebar-fixed .list-group-item.active i:not(.sidebar-toggle-icon) {
          color: #ffffff !important;
          background: rgba(255, 255, 255, 0.2) !important;
          box-shadow: none !important;
      }

      /* Collapsed vs Expanded Parent Styling */
      .sidebar-fixed a[data-bs-toggle="collapse"][aria-expanded="true"] {
          color: #f8fafc !important;
          background: rgba(255, 255, 255, 0.05) !important;
      }

      .sidebar-fixed a[data-bs-toggle="collapse"][aria-expanded="true"] i:not(.sidebar-toggle-icon) {
          color: #10b981 !important;
          background: rgba(16, 185, 129, 0.12) !important;
      }

      /* ======================================================
       * Arrow Toggle Icon (Chevron) - Elite User Experience
       * ====================================================== */
      .sidebar-toggle-icon {
          display: inline-flex !important;
          align-items: center !important;
          justify-content: center !important;
          width: 26px !important;
          height: 26px !important;
          min-width: 26px !important;
          border-radius: 50% !important;
          font-size: 0.72rem !important;
          color: #94a3b8 !important;
          background: rgba(255, 255, 255, 0.04) !important;
          border: 1px solid rgba(255, 255, 255, 0.07) !important;
          transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1), background-color 0.2s ease, color 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease !important;
          margin-inline-start: auto !important;
          flex-shrink: 0;
      }

      .sidebar-fixed .list-group-item:hover .sidebar-toggle-icon {
          color: #f8fafc !important;
          background: rgba(255, 255, 255, 0.12) !important;
          border-color: rgba(255, 255, 255, 0.18) !important;
      }

      /* Rotate 180° when expanded (pointing UP with vibrant emerald indicator) */
      .sidebar-fixed .list-group-item[aria-expanded="true"] .sidebar-toggle-icon,
      .sidebar-fixed a[data-bs-toggle="collapse"][aria-expanded="true"] .sidebar-toggle-icon,
      .sidebar-fixed a[data-bs-toggle="collapse"]:not(.collapsed) .sidebar-toggle-icon,
      .elite-sidebar-item[aria-expanded="true"] .sidebar-toggle-icon {
          transform: rotate(180deg) !important;
          color: #10b981 !important;
          background: rgba(16, 185, 129, 0.18) !important;
          border-color: rgba(16, 185, 129, 0.45) !important;
          box-shadow: 0 0 10px rgba(16, 185, 129, 0.3) !important;
      }

      /* When explicitly collapsed */
      .sidebar-fixed a[data-bs-toggle="collapse"].collapsed .sidebar-toggle-icon,
      .sidebar-fixed a[data-bs-toggle="collapse"][aria-expanded="false"] .sidebar-toggle-icon {
          transform: rotate(0deg) !important;
      }

      /* Light Mode Overrides for Crisp Contrast */
      html:not(.theme-dark) .sidebar-fixed .list-group-item {
          color: #475569 !important;
      }

      html:not(.theme-dark) .sidebar-fixed .list-group-item i:not(.sidebar-toggle-icon) {
          color: #64748b !important;
          background: rgba(0, 0, 0, 0.04) !important;
      }

      html:not(.theme-dark) .sidebar-fixed .list-group-item:hover {
          color: #0f172a !important;
          background: rgba(0, 0, 0, 0.04) !important;
      }

      html:not(.theme-dark) .sidebar-fixed .list-group-item:hover i:not(.sidebar-toggle-icon) {
          color: #059669 !important;
          background: rgba(5, 150, 105, 0.12) !important;
          box-shadow: 0 0 10px rgba(5, 150, 105, 0.15);
      }

      html:not(.theme-dark) .sidebar-fixed .sidebar-toggle-icon {
          color: #64748b !important;
          background: rgba(0, 0, 0, 0.03) !important;
          border-color: rgba(0, 0, 0, 0.08) !important;
      }

      html:not(.theme-dark) .sidebar-fixed .list-group-item:hover .sidebar-toggle-icon {
          color: #0f172a !important;
          background: rgba(0, 0, 0, 0.08) !important;
          border-color: rgba(0, 0, 0, 0.15) !important;
      }

      html:not(.theme-dark) .sidebar-fixed .list-group-item[aria-expanded="true"] .sidebar-toggle-icon,
      html:not(.theme-dark) .sidebar-fixed a[data-bs-toggle="collapse"][aria-expanded="true"] .sidebar-toggle-icon,
      html:not(.theme-dark) .sidebar-fixed a[data-bs-toggle="collapse"]:not(.collapsed) .sidebar-toggle-icon {
          color: #059669 !important;
          background: rgba(5, 150, 105, 0.12) !important;
          border-color: rgba(5, 150, 105, 0.35) !important;
          box-shadow: 0 0 8px rgba(5, 150, 105, 0.2) !important;
      }

      /* Submenu items indentation & clean tree connector */
      .sidebar-fixed .collapse > .list-group,
      .sidebar-fixed .collapse.sub-list > .list-group {
          padding-right: 0.75rem !important;
          margin-right: 1.15rem !important;
          border-right: 1.5px solid rgba(255, 255, 255, 0.08) !important;
          margin-top: 0.15rem;
          margin-bottom: 0.35rem;
      }

      html:not(.theme-dark) .sidebar-fixed .collapse > .list-group,
      html:not(.theme-dark) .sidebar-fixed .collapse.sub-list > .list-group {
          border-right-color: rgba(0, 0, 0, 0.08) !important;
      }

      .sidebar-fixed .collapse .list-group-item {
          padding: 0.45rem 0.75rem !important;
          font-size: 0.86rem !important;
          margin-bottom: 2px !important;
          gap: 0.6rem !important;
      }

      .sidebar-fixed .collapse .list-group-item i:not(.sidebar-toggle-icon) {
          width: 24px !important;
          height: 24px !important;
          min-width: 24px !important;
          font-size: 0.95rem !important;
          background: transparent !important;
          box-shadow: none !important;
      }

      /* Status Pulse Indicator */
      .status-pulse {
          width: 10px;
          height: 10px;
          background: var(--primary);
          border-radius: 50%;
          display: inline-block;
          margin-right: 8px;
          position: relative;
          box-shadow: 0 0 0 rgba(34, 197, 94, 0.4);
          animation: pulseGlow 2s infinite;
      }

      .main-content {
          margin-right: var(--sidebar-width);
          padding: 2rem;
          min-height: calc(100vh - var(--nav-height));
          transition: var(--transition-smooth);
      }

      @media (max-width: 992px) {
          .sidebar { transform: translateX(100%); }
          .main-content { margin-right: 0; padding: 1.25rem 0.85rem; }
          .sidebar.show { transform: translateX(0); }
      }

      @media (max-width: 768px) {
          .main-content {
              padding: 0.85rem 0.5rem !important;
          }
          .container-fluid {
              padding-left: 0.4rem !important;
              padding-right: 0.4rem !important;
          }
          .card {
              border-radius: 16px !important;
          }
          .card-header {
              padding: 0.85rem 1rem !important;
          }
          .card-body {
              padding: 1rem !important;
          }
          .table-responsive {
              -webkit-overflow-scrolling: touch !important;
              border-radius: 12px;
          }
          .modal-dialog {
              margin: 0.5rem !important;
          }
          .modal-content {
              border-radius: 20px !important;
          }
          .btn-group {
              flex-wrap: wrap;
          }
      }

      /* Glassmorphism Cards */
      .glass-card {
          background: rgba(255, 255, 255, 0.7) !important;
          backdrop-filter: blur(10px);
          -webkit-backdrop-filter: blur(10px);
          border: 1px solid rgba(255, 255, 255, 0.2) !important;
      }

      .theme-dark .glass-card {
          background: rgba(15, 23, 42, 0.7) !important;
          border: 1px solid rgba(255, 255, 255, 0.05) !important;
      }

      .fade-in { animation: fadeIn 0.4s ease-in-out; }
      @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

      /* Select2 RTL Fixes */
      html[dir="rtl"] .select2-container--bootstrap-5 .select2-selection--single {
          padding-right: 0.75rem;
          padding-left: 2.5rem;
          position: relative;
          overflow: hidden;
      }
      html[dir="rtl"] .select2-container--bootstrap-5 .select2-selection--single .select2-selection__clear {
          right: auto;
          left: 0.75rem;
          z-index: 10;
      }
      html[dir="rtl"] .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
          padding-right: 0;
          padding-left: 2rem;
          text-align: right;
          overflow: hidden;
          text-overflow: ellipsis;
          white-space: nowrap;
          box-sizing: border-box;
      }

      /* Global Layout Improvements */
      body:not(.theme-dark) {
          background-color: var(--bg-soft) !important;
          color: var(--text-main) !important;
          font-family: 'IBM Plex Sans Arabic', sans-serif;
      }

      /* Premium Card System */
      body:not(.theme-dark) .card,
      body:not(.theme-dark) .glass-card,
      body:not(.theme-dark) .ws-card,
      body:not(.theme-dark) .premium-card-dark {
          background: var(--bg) !important;
          border: 1px solid var(--border) !important;
          box-shadow: var(--shadow-sm) !important;
          border-radius: var(--radius) !important;
      }

      /* Forms & Inputs */
      body:not(.theme-dark) .form-control,
      body:not(.theme-dark) .form-select,
      body:not(.theme-dark) .field-lux,
      body:not(.theme-dark) .ws-input {
          background: var(--bg) !important;
          border: 1px solid var(--border) !important;
          color: var(--text-main) !important;
          border-radius: var(--radius-sm) !important;
          padding: 0.6rem 1rem;
          transition: all 0.2s ease;
      }

      body:not(.theme-dark) .form-control:focus,
      body:not(.theme-dark) .ws-input:focus {
          border-color: var(--primary) !important;
          box-shadow: 0 0 0 4px rgba(34, 197, 94, 0.1) !important;
          outline: none;
      }

      /* Buttons Standard */
      .btn-primary {
          background-color: var(--primary) !important;
          border-color: var(--primary) !important;
          color: white !important;
          font-weight: 600;
          border-radius: var(--radius-sm);
      }
      .btn-primary:hover {
          background-color: var(--primary-dark) !important;
          border-color: var(--primary-dark) !important;
      }

      .text-primary { color: var(--primary) !important; }
      .bg-primary { background-color: var(--primary) !important; }

      /* Global Visibility Fix: Force white text/icons on SOLID primary backgrounds ONLY */
      .bg-primary:not([class*="bg-opacity-"]),
      .btn-primary,
      .badge.bg-primary {
          color: #ffffff !important;
      }

      .bg-primary:not([class*="bg-opacity-"]) i,
      .bg-primary:not([class*="bg-opacity-"]) .text-primary {
          color: #ffffff !important;
      }

      /* Transitions and Utility Support */
      .bg-primary-light { background-color: rgba(var(--bs-primary-rgb), 0.08) !important; }
      .bg-opacity-10 { --bs-bg-opacity: 0.1 !important; background-color: rgba(var(--bs-primary-rgb), var(--bs-bg-opacity)) !important; }

      .bg-primary-light.text-primary,
      .bg-primary-light i,
      [class*="bg-opacity-"] .text-primary,
      [class*="bg-opacity-"] i {
          color: var(--primary) !important;
      }

      .btn-outline-primary {
          color: var(--primary) !important;
          border-color: var(--primary) !important;
          border-radius: var(--radius-sm);
      }
      .btn-outline-primary:hover {
          background-color: var(--primary) !important;
          color: white !important;
      }

      .btn-secondary {
          background-color: #F3F4F6 !important;
          border-color: #E5E7EB !important;
          color: #111111 !important;
          font-weight: 600;
          border-radius: var(--radius-sm);
      }
      .btn-secondary:hover {
          background-color: #E5E7EB !important;
          border-color: #D1D5DB !important;
      }

      .btn-success {
          background-color: #10B981 !important;
          border-color: #10B981 !important;
          color: white !important;
          font-weight: 600;
          border-radius: var(--radius-sm);
      }
      .btn-success:hover {
          background-color: #059669 !important;
          border-color: #059669 !important;
      }

      .btn-danger {
          background-color: #EF4444 !important;
          border-color: #EF4444 !important;
          color: white !important;
          font-weight: 600;
          border-radius: var(--radius-sm);
      }
      .btn-danger:hover {
          background-color: #DC2626 !important;
          border-color: #DC2626 !important;
      }

      .btn-warning {
          background-color: #F59E0B !important;
          border-color: #F59E0B !important;
          color: #111111 !important;
          font-weight: 600;
          border-radius: var(--radius-sm);
      }

      .btn-info {
          background-color: #3B82F6 !important;
          border-color: #3B82F6 !important;
          color: white !important;
          font-weight: 600;
          border-radius: var(--radius-sm);
      }

      /* --- ELITE NAVBAR DESIGN SYSTEM --- */
      .navbar {
          background-color: var(--bg) !important;
          border-bottom: 1px solid var(--border) !important;
          box-shadow: 0 4px 15px rgba(0,0,0,0.02) !important;
          transition: var(--sidebar-transition);
          padding: 0 1.5rem !important;
      }
      @media (min-width: 992px) {
          .navbar { height: 100px; }
      }
      @media (max-width: 991.98px) {
          .navbar { height: 60px; padding: 0 1rem !important; }
      }

      .search-wrapper-elite {
          position: relative;
          transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
          width: 320px;
      }
      .search-wrapper-elite:focus-within {
          width: 450px;
      }

      .search-input-elite {
          height: 44px;
          padding-right: 45px !important;
          background: var(--bg-soft) !important;
          border: 1.5px solid transparent !important;
          border-radius: 50px !important;
          font-size: 0.92rem;
          transition: all 0.3s ease;
      }
      .search-input-elite:focus {
          background: var(--bg) !important;
          border-color: var(--primary) !important;
          box-shadow: 0 0 0 4px rgba(34, 197, 94, 0.12) !important;
      }

      .btn-glass-pill,
      #notificationBellBtn {
          width: 44px;
          height: 44px;
          border-radius: 50px !important;
          display: flex;
          align-items: center;
          justify-content: center;
          background: var(--bg-soft) !important;
          border: 1px solid var(--border) !important;
          color: var(--text-muted) !important;
          transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
          padding: 0 !important;
          position: relative !important;
          overflow: visible !important;
      }
      .btn-glass-pill:hover {
          background: var(--bg) !important;
          color: var(--primary) !important;
          transform: translateY(-2px);
          box-shadow: 0 10px 20px rgba(0,0,0,0.05);
          border-color: var(--primary) !important;
      }
      .notif-bell-badge {
          position: absolute !important;
          top: -2px !important;
          right: -2px !important;
          min-width: 22px !important;
          height: 22px !important;
          padding: 0 5px !important;
          border-radius: 999px !important;
          background: #ef4444 !important;
          background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%) !important;
          color: #ffffff !important;
          font-size: 0.74rem !important;
          font-weight: 800 !important;
          line-height: 22px !important;
          display: inline-flex !important;
          align-items: center !important;
          justify-content: center !important;
          text-align: center !important;
          box-shadow: 0 2px 8px rgba(239, 68, 68, 0.6), 0 0 0 2px var(--bg, #ffffff) !important;
          animation: bellBadgePulse 2.5s infinite !important;
          pointer-events: none !important;
          z-index: 999 !important;
          font-family: inherit !important;
          letter-spacing: normal !important;
      }
      .theme-dark .notif-bell-badge,
      html.theme-dark .notif-bell-badge,
      [data-bs-theme="dark"] .notif-bell-badge,
      body.dark-mode .notif-bell-badge {
          box-shadow: 0 2px 8px rgba(239, 68, 68, 0.75), 0 0 0 2px #1e293b !important;
      }
      @keyframes bellBadgePulse {
          0%, 100% { transform: scale(1); }
          50% { transform: scale(1.12); }
      }

      .btn-reports-elite {
          background: var(--primary-light) !important;
          color: var(--primary) !important;
          border: none !important;
          height: 44px;
          border-radius: 50px !important;
          padding: 0 20px !important;
          font-weight: 700;
          font-size: 0.88rem;
          display: flex;
          align-items: center;
          gap: 10px;
          transition: all 0.3s ease;
      }
      .btn-reports-elite:hover {
          background: var(--primary) !important;
          color: white !important;
          transform: translateY(-2px);
          box-shadow: 0 8px 25px rgba(34, 197, 94, 0.3);
      }

      .navbar-logo-elite img {
          width: auto;
          transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
          object-fit: contain;
          margin-top: -5px; /* Slight adjustment to center visually */
          filter: drop-shadow(0 5px 15px rgba(0,0,0,0.1));
      }

      body.theme-dark .navbar-logo-elite img,
      [data-bs-theme="dark"] .navbar-logo-elite img {
          filter: drop-shadow(0 0 12px rgba(255, 255, 255, 0.6)) brightness(1.2) contrast(1.1);
      }
      @media (min-width: 992px) {
          .navbar-logo-elite img { height: 110px; }
      }
      @media (max-width: 991.98px) {
          .navbar-logo-elite img { height: 50px; }
      }
      .navbar-logo-elite:hover img {
          transform: scale(1.1) translateY(-2px);
      }

      /* --- ELITE SIDEBAR DESIGN SYSTEM --- */
      :root {
          --sidebar-width: 280px;
          --sidebar-collapsed-width: 80px;
          --sidebar-bg: var(--bg);
          --sidebar-border: var(--border);
          --sidebar-item-radius: 12px;
          --sidebar-transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);

          /* Domain Colors */
          --clr-finance: #10b981;
          --clr-logistics: #3b82f6;
          --clr-hr: #f59e0b;
          --clr-digital: #8b5cf6;
          --clr-social: #ec4899;
          --clr-admin: #64748b;
      }

      .sidebar-fixed {
          background-color: var(--sidebar-bg) !important;
          border-left: 1px solid var(--sidebar-border) !important;
          box-shadow: 10px 0 30px rgba(0,0,0,0.02);
          transition: var(--sidebar-transition);
          z-index: 1030;
          overflow-x: hidden;
          overflow-y: auto;
          scrollbar-width: none; /* Firefox: hide scrollbar completely */
          -ms-overflow-style: none; /* IE/Edge: hide scrollbar */
      }
      @media (min-width: 992px) {
          .sidebar-fixed { width: var(--sidebar-width); }
      }
      @media (max-width: 991.98px) {
          .sidebar-fixed {
            position: fixed;
            top: 60px;
            height: calc(100vh - 60px);
            width: 85% !important;
            max-width: 320px;
            transform: translateX(100%);
            right: 0;
            padding-bottom: 2rem;
          }
          .sidebar-fixed.show {
            transform: translateX(0);
          }
      }

      .sidebar-fixed::-webkit-scrollbar { display: none; } /* Chrome/Safari: hide scrollbar completely */

      .sidebar-section-header {
          padding: 1.5rem 1.5rem 0.6rem 1.5rem;
          font-size: 0.72rem;
          font-weight: 800;
          text-transform: uppercase;
          letter-spacing: 0.05em;
          color: var(--text-muted);
          opacity: 0.6;
          display: flex;
          align-items: center;
          gap: 10px;
      }
      .sidebar-section-header::after { content: ''; flex: 1; height: 1px; background: var(--border); opacity: 0.5; }

      .elite-nav-container { padding: 0.5rem 0.8rem; }

      .elite-sidebar-item {
          position: relative;
          display: flex;
          align-items: center;
          gap: 14px;
          padding: 12px 18px;
          margin-bottom: 4px;
          border-radius: var(--sidebar-item-radius);
          color: var(--text-muted) !important;
          text-decoration: none !important;
          font-weight: 500;
          font-size: 0.94rem;
          transition: var(--sidebar-transition);
          border: 1px solid transparent;
      }

      .elite-sidebar-item i {
          font-size: 1.25rem;
          transition: var(--sidebar-transition);
          min-width: 24px;
          display: flex;
          justify-content: center;
      }

      .elite-sidebar-item:hover {
          background-color: var(--bg-soft) !important;
          color: var(--primary) !important;
          transform: translateX(-4px);
      }

      .elite-sidebar-item.active {
          background-color: var(--primary-light) !important;
          color: var(--primary) !important;
          font-weight: 700;
      }

      .elite-sidebar-item.active::before {
          content: '';
          position: absolute;
          right: 0;
          top: 20%;
          height: 60%;
          width: 4px;
          background: var(--primary);
          border-radius: 4px 0 0 4px;
          box-shadow: -2px 0 10px var(--primary);
      }

      /* Domain Icon Coloring */
      .icon-finance { color: var(--clr-finance) !important; }
      .icon-logistics { color: var(--clr-logistics) !important; }
      .icon-hr { color: var(--clr-hr) !important; }
      .icon-digital { color: var(--clr-digital) !important; }
      .icon-social { color: var(--clr-social) !important; }
      .icon-admin { color: var(--clr-admin) !important; }

      /* Submenu Elite Styling */
      .elite-submenu {
          padding-right: 2.2rem;
          border-right: 1px solid var(--border);
          margin-right: 1.5rem;
          margin-bottom: 10px;
          margin-top: -2px;
      }

      .elite-submenu .elite-sidebar-item {
          padding: 8px 14px;
          font-size: 0.88rem;
          opacity: 0.85;
      }
    </style>
    <style>

        /* ======================================================
         * Modal: Global Solid Background (Light Mode)
         * ====================================================== */
        .modal-content {
            background-color: #ffffff !important;
            opacity: 1 !important;
            backdrop-filter: none !important;
            -webkit-backdrop-filter: none !important;
        }
        .modal-footer {
            background-color: #ffffff !important;
        }

        /* ======================================================
         * Modal: Dark Mode - All Modal Sections
         * Targets both theme-dark class (custom) and data-bs-theme (Bootstrap 5)
         * ====================================================== */
        html.theme-dark .modal-content,
        body.theme-dark .modal-content,
        .theme-dark .modal-content,
        [data-bs-theme="dark"] .modal-content,
        [data-theme="dark"] .modal-content {
            background-color: #0f172a !important;
            color: #e2e8f0 !important;
            opacity: 1 !important;
            backdrop-filter: none !important;
            -webkit-backdrop-filter: none !important;
        }

        html.theme-dark .modal-body,
        body.theme-dark .modal-body,
        .theme-dark .modal-body,
        [data-bs-theme="dark"] .modal-body,
        [data-theme="dark"] .modal-body {
            background-color: #0f172a !important;
            color: #e2e8f0 !important;
        }

        html.theme-dark .modal-header,
        body.theme-dark .modal-header,
        .theme-dark .modal-header,
        [data-bs-theme="dark"] .modal-header,
        [data-theme="dark"] .modal-header {
            background-color: #0f172a !important;
            color: #e2e8f0 !important;
        }

        html.theme-dark .modal-footer,
        body.theme-dark .modal-footer,
        .theme-dark .modal-footer,
        [data-bs-theme="dark"] .modal-footer,
        [data-theme="dark"] .modal-footer {
            background-color: #0f172a !important;
            border-top: 1px solid rgba(255,255,255,0.07) !important;
        }

        /* Fix form labels in modals for dark mode */
        body.theme-dark .modal .form-label,
        body.theme-dark .modal label,
        [data-bs-theme="dark"] .modal .form-label,
        [data-bs-theme="dark"] .modal label {
            color: #cbd5e1 !important;
        }

        /* Fix section headers inside modal body */
        body.theme-dark .modal h6,
        [data-bs-theme="dark"] .modal h6 {
            color: #f1f5f9 !important;
        }

        /* Fix border-bottom separators in dark modals */
        body.theme-dark .modal .border-bottom,
        [data-bs-theme="dark"] .modal .border-bottom {
            border-color: rgba(255, 255, 255, 0.1) !important;
        }

        /* Prevent navbar transparency as requested */
        .navbar {
            background-color: var(--bg-card) !important;
            backdrop-filter: none !important;
            -webkit-backdrop-filter: none !important;
            box-shadow: var(--shadow-md) !important;
            opacity: 1 !important;
        }

        /* Hide Bootstrap default caret for profile dropdown */
        .profile-dropdown-toggle::after {
            display: none !important;
        }

        /* Notifications Offcanvas Dark Mode Styling */
        .theme-dark .offcanvas#notifOffcanvas {
            background-color: #0f172a !important;
            color: #f1f5f9 !important;
            border-right: 1px solid rgba(255, 255, 255, 0.1) !important;
        }
        .theme-dark .offcanvas#notifOffcanvas .offcanvas-header {
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        .theme-dark .offcanvas#notifOffcanvas .btn-close {
            filter: invert(1) grayscale(100%) brightness(200%);
        }
        .theme-dark .offcanvas#notifOffcanvas .offcanvas-body {
            background-color: #0f172a !important;
        }

        /* Ensure dropdown is completely solid, non-transparent and above all other elements */
        .dropdown-menu,
        .beneficiary-action-menu,
        [data-bs-toggle="dropdown"] + .dropdown-menu {
            background-color: #ffffff !important;
            background: #ffffff !important;
            opacity: 1 !important;
            backdrop-filter: none !important;
            -webkit-backdrop-filter: none !important;
            box-shadow: 0 16px 36px rgba(0, 0, 0, 0.18) !important;
            border: 1px solid rgba(0, 0, 0, 0.1) !important;
            z-index: 10060 !important;
        }

        body.theme-dark .dropdown-menu,
        body.theme-dark .beneficiary-action-menu,
        .theme-dark .dropdown-menu,
        .theme-dark .beneficiary-action-menu,
        [data-bs-theme="dark"] .dropdown-menu,
        [data-bs-theme="dark"] .beneficiary-action-menu,
        [data-theme="dark"] .dropdown-menu,
        [data-theme="dark"] .beneficiary-action-menu,
        html.theme-dark .dropdown-menu,
        html.theme-dark .beneficiary-action-menu {
            background-color: #0f172a !important;
            background: #0f172a !important;
            opacity: 1 !important;
            backdrop-filter: none !important;
            -webkit-backdrop-filter: none !important;
            border: 1px solid #334155 !important;
            box-shadow: 0 20px 45px rgba(0, 0, 0, 0.85) !important;
            z-index: 10060 !important;
        }

        /* Reset any button styles inside dropdown menu so buttons don't have opaque/white rectangles */
        .dropdown-menu .dropdown-item,
        .dropdown-menu button.dropdown-item,
        .dropdown-menu form button,
        .beneficiary-action-menu .dropdown-item,
        .beneficiary-action-menu button.dropdown-item,
        .beneficiary-action-menu form button {
            background-color: transparent !important;
            background: transparent !important;
            border: none !important;
            box-shadow: none !important;
            outline: none !important;
        }

        .dropdown-menu .dropdown-item:hover,
        .dropdown-menu button.dropdown-item:hover,
        .beneficiary-action-menu .dropdown-item:hover,
        .beneficiary-action-menu button.dropdown-item:hover {
            background-color: rgba(5, 150, 105, 0.12) !important;
            background: rgba(5, 150, 105, 0.12) !important;
            color: #059669 !important;
        }

        body.theme-dark .dropdown-menu .dropdown-item:hover,
        body.theme-dark .dropdown-menu button.dropdown-item:hover,
        .theme-dark .dropdown-menu .dropdown-item:hover,
        .theme-dark .dropdown-menu button.dropdown-item:hover,
        [data-bs-theme="dark"] .dropdown-menu .dropdown-item:hover,
        [data-bs-theme="dark"] .dropdown-menu button.dropdown-item:hover {
            background-color: rgba(16, 185, 129, 0.18) !important;
            background: rgba(16, 185, 129, 0.18) !important;
            color: #34d399 !important;
        }

        .dropdown.show,
        .dropdown:has(.show),
        .dropdown:has(.dropdown-menu.show) {
            position: relative !important;
            z-index: 10050 !important;
        }

        .kpi-card:has(.dropdown-menu.show),
        .card:has(.dropdown-menu.show),
        [class*="col-"]:has(.dropdown-menu.show),
        tr:has(.dropdown-menu.show) {
            position: relative !important;
            z-index: 10040 !important;
            overflow: visible !important;
        }

        /* Paper Sheets Immunity from Theme-Dark Text Inversion */
        #printableDonorSheet,
        #printableDonationVoucher,
        #printableBeneficiaryDossier,
        #printableBeneficiariesRoster,
        .official-voucher-card,
        .a4-paper-sheet,
        #universalPrintPaper,
        #universalPrintPaperBody {
            background-color: #ffffff !important;
            color: #0f172a !important;
        }

        html.theme-dark #printableDonorSheet,
        html.theme-dark #printableDonorSheet *,
        body.theme-dark #printableDonorSheet,
        body.theme-dark #printableDonorSheet *,
        [data-bs-theme="dark"] #printableDonorSheet,
        [data-bs-theme="dark"] #printableDonorSheet *,
        html.theme-dark #printableDonationVoucher,
        html.theme-dark #printableDonationVoucher *,
        body.theme-dark #printableDonationVoucher,
        body.theme-dark #printableDonationVoucher *,
        html.theme-dark #printableBeneficiaryDossier,
        html.theme-dark #printableBeneficiaryDossier *,
        body.theme-dark #printableBeneficiaryDossier,
        body.theme-dark #printableBeneficiaryDossier *,
        html.theme-dark #printableBeneficiariesRoster,
        html.theme-dark #printableBeneficiariesRoster *,
        body.theme-dark #printableBeneficiariesRoster,
        body.theme-dark #printableBeneficiariesRoster *,
        html.theme-dark .official-voucher-card,
        html.theme-dark .official-voucher-card *,
        body.theme-dark .official-voucher-card,
        body.theme-dark .official-voucher-card *,
        html.theme-dark .a4-paper-sheet,
        html.theme-dark .a4-paper-sheet *,
        body.theme-dark .a4-paper-sheet,
        body.theme-dark .a4-paper-sheet *,
        html.theme-dark #universalPrintPaper,
        html.theme-dark #universalPrintPaper *,
        body.theme-dark #universalPrintPaper,
        body.theme-dark #universalPrintPaper *,
        html.theme-dark #universalPrintPaperBody,
        html.theme-dark #universalPrintPaperBody * {
            color-scheme: light !important;
        }

        html.theme-dark #printableDonorSheet div,
        html.theme-dark #printableDonorSheet p,
        html.theme-dark #printableDonorSheet span,
        html.theme-dark #printableDonorSheet td,
        html.theme-dark #printableDonorSheet th,
        html.theme-dark #printableDonorSheet strong,
        html.theme-dark #printableDonorSheet b,
        html.theme-dark #printableDonorSheet h1,
        html.theme-dark #printableDonorSheet h2,
        html.theme-dark #printableDonorSheet h3,
        html.theme-dark #printableDonorSheet h4,
        html.theme-dark #printableDonorSheet h5,
        html.theme-dark #printableDonorSheet h6,
        html.theme-dark #printableDonorSheet small,
        body.theme-dark #printableDonorSheet div,
        body.theme-dark #printableDonorSheet p,
        body.theme-dark #printableDonorSheet span,
        body.theme-dark #printableDonorSheet td,
        body.theme-dark #printableDonorSheet th,
        body.theme-dark #printableDonorSheet strong,
        body.theme-dark #printableDonorSheet b,
        body.theme-dark #printableDonorSheet h1,
        body.theme-dark #printableDonorSheet h2,
        body.theme-dark #printableDonorSheet h3,
        body.theme-dark #printableDonorSheet h4,
        body.theme-dark #printableDonorSheet h5,
        body.theme-dark #printableDonorSheet h6,
        body.theme-dark #printableDonorSheet small,
        html.theme-dark #universalPrintPaper p,
        html.theme-dark #universalPrintPaper span,
        html.theme-dark #universalPrintPaper td,
        html.theme-dark #universalPrintPaper th,
        html.theme-dark #universalPrintPaper strong,
        html.theme-dark #universalPrintPaper b,
        html.theme-dark #universalPrintPaper h1,
        html.theme-dark #universalPrintPaper h2,
        html.theme-dark #universalPrintPaper h3,
        html.theme-dark #universalPrintPaper h4,
        html.theme-dark #universalPrintPaper h5,
        html.theme-dark #universalPrintPaper h6,
        body.theme-dark #universalPrintPaper p,
        body.theme-dark #universalPrintPaper span,
        body.theme-dark #universalPrintPaper td,
        body.theme-dark #universalPrintPaper th,
        body.theme-dark #universalPrintPaper strong,
        body.theme-dark #universalPrintPaper b,
        body.theme-dark #universalPrintPaper h3,
        body.theme-dark #universalPrintPaper h4,
        body.theme-dark #universalPrintPaper h5,
        body.theme-dark #universalPrintPaper h6,
        html.theme-dark #printableBeneficiaryDossier p,
        html.theme-dark #printableBeneficiaryDossier span,
        html.theme-dark #printableBeneficiaryDossier td,
        html.theme-dark #printableBeneficiaryDossier th,
        html.theme-dark #printableBeneficiaryDossier strong,
        html.theme-dark #printableBeneficiaryDossier b,
        html.theme-dark #printableBeneficiaryDossier h1,
        html.theme-dark #printableBeneficiaryDossier h2,
        html.theme-dark #printableBeneficiaryDossier h3,
        html.theme-dark #printableBeneficiaryDossier h4,
        html.theme-dark #printableBeneficiaryDossier h5,
        html.theme-dark #printableBeneficiaryDossier h6,
        body.theme-dark #printableBeneficiaryDossier p,
        body.theme-dark #printableBeneficiaryDossier span,
        body.theme-dark #printableBeneficiaryDossier td,
        body.theme-dark #printableBeneficiaryDossier th,
        body.theme-dark #printableBeneficiaryDossier strong,
        body.theme-dark #printableBeneficiaryDossier b,
        body.theme-dark #printableBeneficiaryDossier h1,
        body.theme-dark #printableBeneficiaryDossier h2,
        body.theme-dark #printableBeneficiaryDossier h3,
        body.theme-dark #printableBeneficiaryDossier h4,
        body.theme-dark #printableBeneficiaryDossier h5,
        body.theme-dark #printableBeneficiaryDossier h6,
        html.theme-dark #printableBeneficiariesRoster p,
        html.theme-dark #printableBeneficiariesRoster span,
        html.theme-dark #printableBeneficiariesRoster td,
        html.theme-dark #printableBeneficiariesRoster th,
        html.theme-dark #printableBeneficiariesRoster strong,
        html.theme-dark #printableBeneficiariesRoster b,
        html.theme-dark #printableBeneficiariesRoster h1,
        html.theme-dark #printableBeneficiariesRoster h2,
        html.theme-dark #printableBeneficiariesRoster h3,
        html.theme-dark #printableBeneficiariesRoster h4,
        html.theme-dark #printableBeneficiariesRoster h5,
        html.theme-dark #printableBeneficiariesRoster h6,
        body.theme-dark #printableBeneficiariesRoster p,
        body.theme-dark #printableBeneficiariesRoster span,
        body.theme-dark #printableBeneficiariesRoster td,
        body.theme-dark #printableBeneficiariesRoster th,
        body.theme-dark #printableBeneficiariesRoster strong,
        body.theme-dark #printableBeneficiariesRoster b,
        body.theme-dark #printableBeneficiariesRoster h1,
        body.theme-dark #printableBeneficiariesRoster h2,
        body.theme-dark #printableBeneficiariesRoster h3,
        body.theme-dark #printableBeneficiariesRoster h4,
        body.theme-dark #printableBeneficiariesRoster h5,
        body.theme-dark #printableBeneficiariesRoster h6,
        html.theme-dark .official-voucher-card div,
        html.theme-dark .official-voucher-card p,
        html.theme-dark .official-voucher-card span:not(.badge):not(.badge *),
        html.theme-dark .official-voucher-card td,
        html.theme-dark .official-voucher-card th,
        html.theme-dark .official-voucher-card strong,
        html.theme-dark .official-voucher-card b,
        html.theme-dark .official-voucher-card small,
        body.theme-dark .official-voucher-card div,
        body.theme-dark .official-voucher-card p,
        body.theme-dark .official-voucher-card span:not(.badge):not(.badge *),
        body.theme-dark .official-voucher-card td,
        body.theme-dark .official-voucher-card th,
        body.theme-dark .official-voucher-card strong,
        body.theme-dark .official-voucher-card b,
        body.theme-dark .official-voucher-card small,
        html.theme-dark #printableDonationVoucher div,
        html.theme-dark #printableDonationVoucher p,
        html.theme-dark #printableDonationVoucher span:not(.badge):not(.badge *),
        html.theme-dark #printableDonationVoucher td,
        html.theme-dark #printableDonationVoucher th,
        html.theme-dark #printableDonationVoucher strong,
        html.theme-dark #printableDonationVoucher b,
        html.theme-dark #printableDonationVoucher small,
        body.theme-dark #printableDonationVoucher div,
        body.theme-dark #printableDonationVoucher p,
        body.theme-dark #printableDonationVoucher span:not(.badge):not(.badge *),
        body.theme-dark #printableDonationVoucher td,
        body.theme-dark #printableDonationVoucher th,
        body.theme-dark #printableDonationVoucher strong,
        body.theme-dark #printableDonationVoucher b,
        body.theme-dark #printableDonationVoucher small {
            color: #0f172a !important;
        }

        /* Search Bar Enhancements */
        .search-input-group .form-control:focus {
            background-color: var(--white) !important;
            box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.15) !important;
            min-width: 300px !important;
        }
        .search-input-group button:hover i {
            transform: scale(1.2);
            color: var(--primary) !important;
        }
        .search-input-group button i {
            transition: transform 0.2s ease;
        }
          /* --- LIGHT MODE ADAPTATION --- */
      body:not(.theme-dark) {
          background-color: var(--ws-bg-page) !important;
          color: var(--ws-text-primary) !important;
      }
      body:not(.theme-dark) .member-card-premium {
          background: var(--ws-bg-card);
          border-color: var(--ws-border-card);
          box-shadow: 0 10px 25px rgba(0,0,0,0.05);
      }
      body:not(.theme-dark) .text-white,
      body:not(.theme-dark) .text-white-50 {
          color: var(--ws-text-primary) !important;
      }
      body:not(.theme-dark) .premium-hero-sleek .text-white,
      body:not(.theme-dark) .premium-hero-sleek .text-white-50 {
          color: #fff !important;
      }
      body:not(.theme-dark) .role-pill-premium {
          color: var(--blue-dark);
          background: rgba(59,130,246,0.15);
          border-color: rgba(59,130,246,0.2);
      }
      body:not(.theme-dark) .text-slate-400 {
          color: var(--ws-text-secondary);
      }
      body:not(.theme-dark) .btn-glass-blue {
          color: var(--blue-dark);
          background: rgba(37, 99, 235, 0.1);
          border-color: rgba(37, 99, 235, 0.2);
      }
      body:not(.theme-dark) .btn-glass-danger {
          color: #dc2626;
          background: rgba(220, 38, 38, 0.1);
          border-color: rgba(220, 38, 38, 0.2);
      }
      body:not(.theme-dark) .premium-modal-dark {
          background: var(--ws-bg-card);
      }
      body:not(.theme-dark) .premium-modal-dark .modal-header .text-white {
          color: var(--ws-text-primary) !important;
      }
      body:not(.theme-dark) .field-lux {
          background: var(--ws-bg-input);
          color: var(--ws-text-primary);
          border-color: var(--ws-border);
      }
      body:not(.theme-dark) .field-lux:focus {
          background: var(--ws-bg-input);
      }
      body:not(.theme-dark) .avatar-placeholder-premium {
          color: #fff; /* Keep placeholder icon white because of gradient */
      }
      body:not(.theme-dark) .btn-close-white {
          filter: invert(1) grayscale(100%) brightness(200%);
      }
      /* Premium Global Toasts - Modern Bottom-Left Redesign */
      .toast-container-elite {
          position: fixed;
          bottom: 24px;
          left: 24px;
          z-index: 10500;
          display: flex;
          flex-direction: column-reverse;
          gap: 12px;
          pointer-events: none;
          max-width: 440px;
          width: auto;
      }
      .toast-fade-out {
          opacity: 0 !important;
          transform: translateY(16px) scale(0.96) !important;
          transition: opacity 0.4s ease, transform 0.4s ease !important;
      }
      .toast-elite {
          min-width: 340px;
          max-width: 480px;
          padding: 16px 22px;
          border-radius: 14px;
          display: flex;
          align-items: center;
          gap: 14px;
          pointer-events: auto;
          direction: rtl;
          position: relative;
          overflow: hidden;
      }
      /* Success - Green Background */
      .toast-elite.success {
          background: linear-gradient(135deg, #10b981 0%, #059669 100%);
          box-shadow: 0 10px 40px -10px rgba(16, 185, 129, 0.5), 0 4px 12px rgba(16, 185, 129, 0.3);
      }
      /* Error - Red Background */
      .toast-elite.error {
          background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
          box-shadow: 0 10px 40px -10px rgba(239, 68, 68, 0.5), 0 4px 12px rgba(239, 68, 68, 0.3);
      }
      /* Information / pending review - Blue Background */
      .toast-elite.info {
          background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
          box-shadow: 0 10px 40px -10px rgba(37, 99, 235, 0.5), 0 4px 12px rgba(37, 99, 235, 0.3);
      }

      .toast-icon-wrapper {
          width: 38px;
          height: 38px;
          border-radius: 10px;
          display: flex;
          align-items: center;
          justify-content: center;
          flex-shrink: 0;
          background: rgba(255, 255, 255, 0.2);
          color: #ffffff;
      }

      .toast-icon { font-size: 1.3rem; color: #ffffff; }

      .toast-text {
          display: flex;
          flex-direction: column;
          text-align: right;
          flex-grow: 1;
      }
      .toast-title { font-weight: 700; font-size: 0.95rem; color: #ffffff; margin-bottom: 2px; }
      .toast-msg { font-size: 0.85rem; color: rgba(255,255,255,0.85); font-weight: 500; }
      .toast-review-link {
          color: #ffffff;
          font-size: 0.8rem;
          font-weight: 700;
          text-decoration: underline;
          text-underline-offset: 3px;
          margin-top: 5px;
          width: fit-content;
      }
      .toast-review-link:hover { color: #ffffff; opacity: 0.85; }

      .review-request-toast {
          width: min(520px, calc(100vw - 24px));
          max-width: 520px;
          align-items: flex-start;
      }
      .toast-review-action {
          display: inline-flex;
          align-items: center;
          justify-content: center;
          gap: 6px;
          width: fit-content;
          margin-top: 9px;
          padding: 7px 11px;
          border: 1px solid rgba(255, 255, 255, 0.45);
          border-radius: 9px;
          background: rgba(255, 255, 255, 0.14);
          color: #fff;
          font-size: 0.78rem;
          font-weight: 800;
          transition: background .2s ease, transform .2s ease;
      }
      .toast-review-action:hover {
          background: rgba(255, 255, 255, 0.24);
          transform: translateY      /* Review request details and inline execution - High Contrast & Clarity */
      .modal-backdrop.show {
          background-color: rgba(3, 7, 18, 0.82) !important;
          backdrop-filter: blur(10px) !important;
          -webkit-backdrop-filter: blur(10px) !important;
          opacity: 1 !important;
      }

      .review-action-modal {
          --review-surface: #ffffff;
          --review-body-bg: #f8fafc;
          --review-section-bg: #ffffff;
          --review-item-bg: #f1f5f9;
          --review-info-soft: #eff6ff;
          --review-info-border: #bfdbfe;
          --review-danger-soft: #fef2f2;
          --review-danger-border: #fca5a5;
          --review-success-soft: #ecfdf5;
          --review-neutral-soft: #e2e8f0;
          --review-icon-success: #d1fae5;
          --review-icon-primary: #dbeafe;
          --review-icon-danger: #fee2e2;
          --review-icon-warning: #fef3c7;
          --review-icon-secondary: #e2e8f0;
          --review-text: #0f172a;
          --review-muted: #475569;
          --review-border: #cbd5e1;
      }
      .theme-dark .review-action-modal {
          --review-surface: #0f172a;
          --review-body-bg: #090d16;
          --review-section-bg: #131c2e;
          --review-item-bg: #1e293b;
          --review-info-soft: rgba(30, 58, 138, 0.38);
          --review-info-border: rgba(59, 130, 246, 0.5);
          --review-danger-soft: rgba(127, 29, 29, 0.38);
          --review-danger-border: rgba(239, 68, 68, 0.5);
          --review-success-soft: rgba(6, 78, 59, 0.38);
          --review-neutral-soft: #1e293b;
          --review-icon-success: #064e3b;
          --review-icon-primary: #1e3a8a;
          --review-icon-danger: #7f1d1d;
          --review-icon-warning: #78350f;
          --review-icon-secondary: #334155;
          --review-text: #f8fafc;
          --review-muted: #cbd5e1;
          --review-border: rgba(255, 255, 255, 0.15);
      }
      .review-action-modal .modal-content {
          max-height: calc(100dvh - 2rem);
          overflow: hidden;
          border: 1px solid var(--review-border);
          border-radius: 20px;
          background: var(--review-surface) !important;
          color: var(--review-text);
          box-shadow: 0 25px 80px rgba(0, 0, 0, 0.75), 0 0 0 1px var(--review-border);
          opacity: 1 !important;
          backdrop-filter: none !important;
          -webkit-backdrop-filter: none !important;
          position: relative;
      }
      .review-action-modal .modal-content::before {
          content: '';
          position: absolute;
          top: 0;
          left: 0;
          right: 0;
          height: 4px;
          background: linear-gradient(90deg, #3b82f6, #6366f1, #8b5cf6);
          z-index: 10;
          border-radius: 20px 20px 0 0;
      }
      .review-action-modal .modal-content.review-modal--destructive::before {
          background: linear-gradient(90deg, #ef4444, #dc2626, #f59e0b) !important;
      }
      .review-action-modal .modal-header,
      .review-action-modal .modal-footer {
          border-color: var(--review-border);
          background: var(--review-surface) !important;
      }
      .review-action-modal .modal-header {
          padding: 1.1rem 1.4rem;
          border-bottom: 1px solid var(--review-border);
      }
      .review-action-modal .modal-body {
          padding: 1.25rem 1.4rem;
          background: var(--review-body-bg) !important;
          overscroll-behavior: contain;
      }
      .review-action-modal .modal-footer {
          justify-content: flex-start;
          gap: .65rem;
          padding: 1rem 1.4rem;
          border-top: 1px solid var(--review-border);
      }
      .review-action-modal .btn-close {
          margin: 0 auto 0 0;
          opacity: 0.85;
          transition: transform 0.2s ease, opacity 0.2s ease;
      }
      .review-action-modal .btn-close:hover {
          opacity: 1;
          transform: scale(1.1);
      }
      .theme-dark .review-action-modal .btn-close {
          filter: invert(1) grayscale(100%) brightness(200%);
      }
      .review-modal-heading {
          display: flex;
          align-items: center;
          gap: .85rem;
          min-width: 0;
      }
      .review-modal-icon {
          display: grid;
          place-items: center;
          flex: 0 0 48px;
          width: 48px;
          height: 48px;
          border-radius: 14px;
          font-size: 1.25rem;
          box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.1);
      }
      #reviewRequestActionModal .review-modal-icon--success { background: var(--review-icon-success) !important; color: #10b981; }
      #reviewRequestActionModal .review-modal-icon--primary { background: var(--review-icon-primary) !important; color: #3b82f6; }
      #reviewRequestActionModal .review-modal-icon--danger { background: var(--review-icon-danger) !important; color: #ef4444; }
      #reviewRequestActionModal .review-modal-icon--warning { background: var(--review-icon-warning) !important; color: #f59e0b; }
      #reviewRequestActionModal .review-modal-icon--secondary { background: var(--review-icon-secondary) !important; color: #94a3b8; }
      .review-modal-eyebrow {
          display: block;
          margin-bottom: 2px;
          color: var(--review-muted);
          font-size: .75rem;
          font-weight: 700;
          letter-spacing: 0.3px;
      }
      .review-action-modal .modal-title {
          overflow: hidden;
          color: var(--review-text);
          font-size: 1.15rem;
          font-weight: 800;
          text-overflow: ellipsis;
          white-space: nowrap;
      }
      .review-summary-grid {
          display: grid;
          grid-template-columns: repeat(4, minmax(0, 1fr));
          gap: .85rem;
      }
      #reviewRequestActionModal .review-summary-item {
          display: flex;
          align-items: center;
          gap: .75rem;
          min-width: 0;
          padding: .95rem 1rem;
          border: 1px solid var(--review-border);
          border-radius: 14px;
          background: var(--review-section-bg) !important;
          opacity: 1;
          box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
          transition: transform 0.2s ease, border-color 0.2s ease;
      }
      #reviewRequestActionModal .review-summary-item:hover {
          transform: translateY(-2px);
          border-color: rgba(59, 130, 246, 0.4);
      }
      .review-summary-item > i {
          flex: 0 0 36px;
          width: 36px;
          height: 36px;
          border-radius: 10px;
          display: grid;
          place-items: center;
          background: rgba(59, 130, 246, 0.12);
          color: #3b82f6;
          font-size: 1.1rem;
      }
      .review-summary-item span {
          min-width: 0;
      }
      .review-summary-item small,
      .review-summary-item strong {
          display: block;
          overflow: hidden;
          text-overflow: ellipsis;
          white-space: nowrap;
      }
      .review-summary-item small {
          margin-bottom: 3px;
          color: var(--review-muted);
          font-size: .72rem;
          font-weight: 600;
      }
      .review-summary-item strong {
          color: var(--review-text);
          font-size: .84rem;
          font-weight: 700;
      }
      #reviewRequestActionModal .review-effect {
          display: flex;
          align-items: flex-start;
          gap: .85rem;
          margin-top: 1rem;
          padding: 1rem 1.15rem;
          border: 1px solid var(--review-info-border);
          border-radius: 14px;
          background: var(--review-info-soft) !important;
          color: var(--review-text);
          box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
      }
      .review-effect > i {
          color: #3b82f6;
          font-size: 1.25rem;
          margin-top: 2px;
      }
      .review-effect strong {
          display: block;
          margin-bottom: .25rem;
          font-size: .88rem;
          font-weight: 800;
          color: var(--review-text);
      }
      .review-effect p {
          margin: 0;
          color: var(--review-muted);
          font-size: .82rem;
          line-height: 1.65;
          font-weight: 500;
      }
      #reviewRequestActionModal .review-effect--danger {
          border-color: var(--review-danger-border);
          background: var(--review-danger-soft) !important;
      }
      .review-effect--danger > i {
          color: #ef4444;
      }
      .review-effect--danger strong {
          color: #fca5a5;
      }
      .theme-dark .review-effect--danger p {
          color: #fecdd3;
      }
      #reviewRequestActionModal .review-details-section,
      #reviewRequestActionModal .review-execution-flow {
          margin-top: 1rem;
          padding: 1.15rem;
          border: 1px solid var(--review-border);
          border-radius: 16px;
          background: var(--review-section-bg) !important;
          box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
      }
      .review-section-title {
          display: flex;
          align-items: center;
          justify-content: space-between;
          gap: 1rem;
          margin-bottom: .9rem;
          color: var(--review-text);
          font-size: .92rem;
          font-weight: 800;
      }
      .review-section-title > span:first-child {
          display: inline-flex;
          align-items: center;
          gap: .5rem;
      }
      .review-section-title i {
          color: #3b82f6;
          font-size: 1.1rem;
      }
      #reviewRequestActionModal .review-fields-count {
          padding: .28rem .65rem;
          border-radius: 999px;
          background: var(--review-icon-primary) !important;
          color: #3b82f6;
          font-size: .7rem;
          font-weight: 800;
      }
      .review-fields-grid {
          display: grid;
          grid-template-columns: repeat(3, minmax(0, 1fr));
          gap: .75rem;
      }
      #reviewRequestActionModal .review-field-card {
          min-width: 0;
          padding: .85rem;
          border: 1px solid var(--review-border);
          border-radius: 12px;
          background: var(--review-item-bg) !important;
      }
      .review-field-card small,
      .review-field-card strong {
          display: block;
      }
      .review-field-card small {
          margin-bottom: .35rem;
          color: var(--review-muted);
          font-size: .72rem;
          font-weight: 600;
      }
      .review-field-card strong {
          overflow-wrap: anywhere;
          color: var(--review-text);
          font-size: .84rem;
          font-weight: 700;
          line-height: 1.55;
      }
      .review-diff-list {
          display: grid;
          gap: .75rem;
      }
      #reviewRequestActionModal .review-diff-card {
          padding: .85rem;
          border: 1px solid var(--review-border);
          border-radius: 13px;
          background: var(--review-item-bg) !important;
      }
      .review-diff-card h3 {
          margin: 0 0 .6rem;
          color: var(--review-text);
          font-size: .82rem;
          font-weight: 800;
      }
      .review-diff-values {
          display: grid;
          grid-template-columns: minmax(0, 1fr) 28px minmax(0, 1fr);
          align-items: center;
          gap: .5rem;
      }
      .review-value {
          min-width: 0;
          padding: .75rem;
          border-radius: 10px;
      }
      .review-value small,
      .review-value span {
          display: block;
      }
      .review-value small {
          margin-bottom: .3rem;
          font-size: .68rem;
          font-weight: 700;
      }
      .review-value span {
          overflow-wrap: anywhere;
          font-size: .82rem;
          font-weight: 700;
      }
      #reviewRequestActionModal .review-value--old {
          background: var(--review-neutral-soft) !important;
          color: var(--review-muted);
          border: 1px solid var(--review-border);
      }
      .review-value--old span {
          text-decoration: line-through;
          opacity: 0.8;
      }
      #reviewRequestActionModal .review-value--new {
          background: var(--review-success-soft) !important;
          color: #10b981;
          border: 1px solid rgba(16, 185, 129, 0.3);
      }
      .theme-dark .review-value--new {
          color: #6ee7b7;
      }
      .review-diff-arrow {
          color: #3b82f6;
          text-align: center;
          font-size: 1.1rem;
      }
      .review-preformatted {
          max-height: 150px;
          overflow: auto;
          white-space: pre-wrap;
      }
      #reviewRequestActionModal .review-empty-details {
          padding: 1.25rem;
          border-radius: 12px;
          background: var(--review-item-bg) !important;
          color: var(--review-muted);
          font-size: .84rem;
          font-weight: 600;
          text-align: center;
          border: 1px dashed var(--review-border);
      }
      .review-flow-steps {
          display: flex;
          align-items: flex-start;
          padding-top: .4rem;
      }
      .review-flow-step {
          display: flex;
          align-items: flex-start;
          gap: .65rem;
          flex: 0 1 220px;
          min-width: 0;
      }
      #reviewRequestActionModal .review-flow-step > span {
          display: grid;
          place-items: center;
          flex: 0 0 34px;
          width: 34px;
          height: 34px;
          border: 2px solid var(--review-border);
          border-radius: 50%;
          background: var(--review-section-bg) !important;
          color: var(--review-muted);
          font-size: .78rem;
          font-weight: 800;
          box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
      }
      .review-flow-step strong,
      .review-flow-step small {
          display: block;
      }
      .review-flow-step strong {
          color: var(--review-text);
          font-size: .8rem;
          font-weight: 800;
      }
      .review-flow-step small {
          margin-top: .2rem;
          color: var(--review-muted);
          font-size: .7rem;
          line-height: 1.5;
          font-weight: 500;
      }
      #reviewRequestActionModal .review-flow-step--done > span {
          border-color: #10b981;
          background: #10b981 !important;
          color: #fff;
          box-shadow: 0 0 12px rgba(16, 185, 129, 0.4);
      }
      #reviewRequestActionModal .review-flow-step--current > span {
          border-color: #3b82f6;
          background: #3b82f6 !important;
          color: #fff;
          box-shadow: 0 0 0 5px rgba(59, 130, 246, .2), 0 0 12px rgba(59, 130, 246, 0.4);
      }
      #reviewRequestActionModal .review-flow-line {
          flex: 1 1 35px;
          min-width: 22px;
          height: 3px;
          margin: 16px .4rem 0;
          border-radius: 2px;
          background: var(--review-border) !important;
      }
      #reviewRequestActionModal .review-reject-panel {
          margin-top: 1rem;
          padding: 1.15rem;
          border: 1px solid var(--review-danger-border);
          border-radius: 14px;
          background: var(--review-danger-soft) !important;
      }
      .review-reject-panel .form-label {
          color: var(--review-text);
          font-size: .84rem;
          font-weight: 800;
      }
      #reviewRequestActionModal .review-reject-panel .form-control {
          border-color: var(--review-border);
          background: var(--review-section-bg) !important;
          color: var(--review-text);
      }
      .review-approve-button,
      .review-reject-button,
      .review-history-button {
          display: inline-flex;
          align-items: center;
          justify-content: center;
          gap: .5rem;
          min-height: 44px;
          padding: 0 1.2rem;
          border-radius: 10px;
          font-size: .84rem;
          font-weight: 800;
          transition: all 0.2s ease;
      }
      .review-approve-button {
          border: 0;
          background: linear-gradient(135deg, #10b981 0%, #059669 100%);
          color: #fff;
          box-shadow: 0 4px 14px rgba(16, 185, 129, 0.35);
      }
      .review-approve-button:hover {
          background: linear-gradient(135deg, #059669 0%, #047857 100%);
          color: #fff;
          transform: translateY(-1px);
          box-shadow: 0 6px 18px rgba(16, 185, 129, 0.45);
      }
      .review-reject-button {
          border: 1px solid rgba(239, 68, 68, .4);
          background: rgba(239, 68, 68, .12);
          color: #ef4444;
      }
      .review-reject-button:hover {
          background: rgba(239, 68, 68, .22);
          color: #f87171;
          transform: translateY(-1px);
      }
      .review-history-button {
          border: 1px solid var(--review-border);
          background: var(--review-section-bg);
          color: var(--review-text);
      }
      .review-history-button:hover {
          border-color: #3b82f6;
          color: #3b82f6;
          transform: translateY(-1px);
      }
      .review-waiting-message {
          display: inline-flex;
          align-items: center;
          gap: .5rem;
          margin-inline-end: auto;
          color: #f59e0b;
          font-size: .82rem;
          font-weight: 700;
      }ght: 700;
      }

      @media (max-width: 991.98px) {
          .review-summary-grid {
              grid-template-columns: repeat(2, minmax(0, 1fr));
          }
          .review-fields-grid {
              grid-template-columns: repeat(2, minmax(0, 1fr));
          }
      }
      @media (max-width: 575.98px) {
          .toast-container-elite {
              bottom: 16px;
              left: 12px;
              right: 12px;
              width: calc(100% - 24px);
              max-width: none;
          }
          .review-request-toast {
              min-width: 0;
              width: 100%;
              padding: 13px;
          }
          .review-action-modal .modal-dialog {
              align-items: flex-end;
              min-height: 100dvh;
              margin: 0;
          }
          .review-action-modal .modal-content {
              max-height: 94dvh;
              border-width: 1px 0 0;
              border-radius: 20px 20px 0 0;
          }
          .review-action-modal .modal-header,
          .review-action-modal .modal-body,
          .review-action-modal .modal-footer {
              padding-inline: .9rem;
          }
          .review-summary-grid,
          .review-fields-grid {
              grid-template-columns: 1fr;
          }
          .review-diff-values {
              grid-template-columns: 1fr;
          }
          .review-diff-arrow {
              transform: rotate(-90deg);
          }
          .review-flow-steps {
              flex-direction: column;
              gap: .7rem;
          }
          .review-flow-step {
              flex-basis: auto;
          }
          .review-flow-line {
              display: none;
          }
          .review-action-modal .modal-footer {
              display: grid;
              grid-template-columns: 1fr 1fr;
          }
          .review-action-modal .modal-footer form,
          .review-action-modal .modal-footer .btn {
              width: 100%;
          }
          .review-waiting-message {
              grid-column: 1 / -1;
          }
      }

      .btn-close-toast {
          background: none;
          border: none;
          font-size: 1.5rem;
          color: rgba(255,255,255,0.6);
          cursor: pointer;
          transition: 0.2s;
          padding: 0 5px;
          margin-right: auto;
          opacity: 0.8;
          display: flex;
          align-items: center;
          justify-content: center;
          line-height: 1;
      }
      .btn-close-toast:hover { opacity: 1; color: #ffffff; transform: scale(1.1); }

      @keyframes toastSlideUpLeft {
          0% { opacity: 0; transform: translateY(24px) scale(0.95); }
          100% { opacity: 1; transform: translateY(0) scale(1); }
      }
      .animate-toast-in { animation: toastSlideUpLeft 0.35s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
</style>

{{-- ============================================================ --}}
{{-- SAFARI / WEBKIT COMPATIBILITY FIXES --}}
{{-- ============================================================ --}}
<style>
  /* 1. Safari: calc() with CSS custom properties fix */
  @supports (-webkit-touch-callout: none) {
      .sidebar-fixed {
          /* iOS Safari 100vh fix (address bar bug) */
          height: -webkit-fill-available;
          max-height: calc(100vh - 100px);
      }
      body {
          min-height: -webkit-fill-available;
      }
  }

  /* 2. Safari: Flex gap fallback (Safari < 14) */
  @supports not (gap: 1rem) {
      .d-flex > * + * { margin-right: 0.5rem; }
      .gap-1 > * + * { margin-right: 0.25rem; }
      .gap-2 > * + * { margin-right: 0.5rem; }
      .gap-3 > * + * { margin-right: 0.75rem; }
      .gap-4 > * + * { margin-right: 1rem; }
  }

  /* 3. Safari: backdrop-filter needs -webkit- prefix */
  .glass-card,
  .navbar,
  .toast-elite {
      -webkit-backdrop-filter: var(--glass-blur, blur(12px));
  }

  /* 4. Safari: -webkit-text-fill-color fixes for gradient text */
  .success .toast-icon,
  .error .toast-icon {
      -webkit-background-clip: text;
      background-clip: text;
  }

  /* 5. Safari: position: sticky fix inside overflow:auto containers */
  .sidebar-fixed {
      -webkit-overflow-scrolling: touch;
  }

  /* 6. Safari: list-group-item display flex RTL fix */
  .list-group-item {
      display: -webkit-box;
      display: -webkit-flex;
      display: flex;
      -webkit-box-align: center;
      -webkit-align-items: center;
      align-items: center;
  }

  /* 7. Safari: button styles reset */
  button {
      -webkit-appearance: none;
  }

  /* 8. Safari: transition on transform for sidebar items */
  .list-group-item:hover,
  .sidebar-fixed .list-group-item:hover {
      -webkit-transform: translateX(-4px);
      transform: translateX(-4px);
  }

  /* 9. Safari: min-height calc fix for content wrapper */
  .content-wrapper {
      min-height: calc(100vh - 100px); /* Fallback for when var() fails */
      min-height: calc(100vh - var(--nav-height, 100px));
  }

  /* 10. iOS Safari: fixed navbar height fix */
  @supports (-webkit-touch-callout: none) {
      .navbar {
          height: 60px;
      }
      @media (min-width: 992px) {
          .navbar {
              height: 100px;
          }
          .sidebar-fixed {
              top: 100px;
              max-height: calc(100vh - 100px);
          }
          .content-wrapper {
              min-height: calc(100vh - 100px);
          }
      }
      @media (max-width: 991.98px) {
          .navbar {
              height: 60px;
          }
          .sidebar-fixed {
              top: 60px;
              max-height: calc(100vh - 60px);
          }
      }
  }

  /* 11. Safari: object-fit polyfill class */
  .object-fit-cover {
      -o-object-fit: cover;
      object-fit: cover;
  }

  /* 12. Safari: search-wrapper-elite width transition fix */
  .search-wrapper-elite {
      -webkit-transition: width 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
      transition: width 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
  }
</style>

  @yield('styles')
</head>

<body class="{{ (isset($_COOKIE['ui_theme']) && $_COOKIE['ui_theme'] === 'dark') ? 'theme-dark' : '' }}">
  <script>
    if (document.documentElement.classList.contains('theme-dark')) {
      document.body.classList.add('theme-dark');
      document.body.style.backgroundColor = '#020617';
    }
  </script>
  <div id="global-loader">
      <div class="spinner-premium"></div>
  </div>

  @php
      $reviewNoticeRequest = null;
      $reviewNoticeUser = auth()->user();

      if (session('review_request_notice') && session('review_request_id') && $reviewNoticeUser) {
          $reviewNoticeRequest = \App\Models\ChangeRequest::with(['user', 'reviewer'])
              ->find(session('review_request_id'));

          if ($reviewNoticeRequest?->model_id) {
              $reviewNoticeRequest->load('subject');
          }

          $canSeeReviewNotice = $reviewNoticeRequest
              && ($reviewNoticeRequest->user_id === $reviewNoticeUser->id || $reviewNoticeUser->hasRole('admin'));

          if (! $canSeeReviewNotice) {
              $reviewNoticeRequest = null;
          }
      }

      $canExecuteReviewRequest = $reviewNoticeRequest
          && $reviewNoticeRequest->status === 'pending'
          && $reviewNoticeUser?->hasRole('admin');
  @endphp

  {{-- Premium Global Notifications (Toasts) --}}
  <div class="toast-container-elite" id="toast-container">
      @if($reviewNoticeRequest)
          @include('partials.review_request_toast')
      @endif
      @if(session('success'))
          <div class="toast-elite success animate-toast-in">
              <div class="toast-icon-wrapper">
                  <i class="bi bi-check-lg toast-icon"></i>
              </div>
              <div class="toast-text">
                  <div class="toast-title">تم التنفيذ بنجاح</div>
                  <span class="toast-msg">{{ session('success') }}</span>
              </div>
              <button type="button" class="btn-close-toast" onclick="this.parentElement.style.display='none'">&times;</button>
          </div>
      @endif

      @if(session('info') && ! $reviewNoticeRequest)
          <div class="toast-elite info animate-toast-in">
              <div class="toast-icon-wrapper">
                  <i class="bi bi-info-lg toast-icon"></i>
              </div>
              <div class="toast-text">
                  <div class="toast-title">تنبيه</div>
                  <span class="toast-msg">{{ session('info') }}</span>
              </div>
              <button type="button" class="btn-close-toast" onclick="this.parentElement.style.display='none'">&times;</button>
          </div>
      @endif

      @if(session('error') || $errors->any())
          <div class="toast-elite error animate-toast-in">
              <div class="toast-icon-wrapper">
                  <i class="bi bi-exclamation-triangle toast-icon"></i>
              </div>
              <div class="toast-text">
                  <div class="toast-title">تنبيه بالخطأ</div>
                  <span class="toast-msg">{{ session('error') ?? $errors->first() }}</span>
              </div>
              <button type="button" class="btn-close-toast" onclick="this.parentElement.style.display='none'">&times;</button>
          </div>
      @endif
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      setTimeout(function() {
        var toasts = document.querySelectorAll('.toast-elite:not([data-persistent="true"])');
        toasts.forEach(function(toast) {
          toast.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
          toast.style.opacity = '0';
          toast.style.transform = 'translateY(15px)';
          setTimeout(function() { if(toast.parentNode) toast.parentNode.removeChild(toast); }, 400);
        });
      }, 5000);
    });
  </script>

  @if($reviewNoticeRequest)
      @include('partials.review_request_modal')
  @endif



  @php $navUser = auth()->user(); @endphp
  <nav class="navbar border-bottom fixed-top">
    <div class="container-fluid px-0 h-100">
      <div class="d-flex align-items-center justify-content-between w-100 h-100">

        {{-- Right Side: Toggle & Brand --}}
        <div class="d-flex align-items-center gap-3">
          <button class="btn btn-link p-0 text-body elite-sidebar-toggle" id="sidebarToggle" title="تبديل القائمة">
            <i class="bi bi-list fs-2"></i>
          </button>
          <a class="navbar-brand navbar-logo-elite me-0" href="/">
            @if(file_exists(public_path('images/heart-icon.png')))
              <img src="{{ asset('images/heart-icon.png') }}" alt="شعار إنسان" loading="lazy" style="height: 50px !important; width: auto !important;">
            @else
              <span class="fw-800 text-primary">مؤسسة إنسان الخيرية</span>
            @endif
          </a>
        </div>

        {{-- Center: Search --}}
        <div class="d-none d-lg-flex flex-grow-1 justify-content-center px-5">
            <form action="{{ route('reports.index') }}" method="GET" class="search-wrapper-elite">
              <div class="position-relative">
                <input type="text" class="form-control search-input-elite" name="q" placeholder="ابحث عن تقارير، متبرعين، أو مهام...">
                <button type="submit" class="btn border-0 position-absolute top-50 end-0 translate-middle-y me-2 rounded-circle d-flex align-items-center justify-content-center" style="width: 34px; height: 34px;">
                    <i class="bi bi-search text-primary"></i>
                </button>
              </div>
            </form>
        </div>

        {{-- Left Side: Actions --}}
        <div class="d-flex align-items-center gap-2">
            @if($navUser && $navUser->hasPermission('reports.view'))
              <a href="{{ route('reports.index') }}" class="btn btn-reports-elite d-none d-md-flex">
                  <i class="bi bi-graph-up"></i>
                  <span>التقارير</span>
              </a>
            @endif

            @if($navUser && $navUser->hasPermission('notifications.view'))
              <button class="btn btn-glass-pill" id="notificationBellBtn" style="overflow: visible !important;"
                type="button" data-bs-toggle="offcanvas" data-bs-target="#notifOffcanvas" aria-label="الإشعارات" title="الإشعارات">
                <i class="bi bi-bell fs-5"></i>
                <span id="notificationBellBadge" class="notif-bell-badge d-none">0</span>
              </button>
            @endif

            <button id="themeToggle" class="btn btn-glass-pill" type="button" aria-label="تبديل الثيم">
              <i class="bi bi-moon fs-5"></i>
            </button>

            @if($navUser)
              {{-- Profile Menu --}}
              <style>
                /* ── Profile Dropdown ── */
                #profileMenuDropdown {
                  display: none;
                  position: fixed;
                  z-index: 99999;
                  width: 250px;
                  border-radius: 16px;
                  padding: 8px;
                  background-color: #ffffff !important;
                  background: #ffffff !important;
                  opacity: 1 !important;
                  border: 1px solid #eceff3 !important;
                  box-shadow: 0 18px 50px rgba(15,23,42,0.16), 0 4px 14px rgba(15,23,42,0.08) !important;
                  overflow: hidden;
                  animation: pmenuIn 0.16s ease-out;
                }
                .sidebar-fixed {
                  background-color: #ffffff !important;
                  background: #ffffff !important;
                  opacity: 1 !important;
                  z-index: 1040 !important;
                }
                .theme-dark .sidebar-fixed {
                  background-color: #0f172a !important;
                  background: #0f172a !important;
                  opacity: 1 !important;
                }
                .offcanvas {
                  background-color: #ffffff !important;
                  opacity: 1 !important;
                }
                .theme-dark .offcanvas {
                  background-color: #0f172a !important;
                  opacity: 1 !important;
                }
                @keyframes pmenuIn {
                  from { opacity: 0; transform: translateY(-8px) scale(0.97); }
                  to   { opacity: 1; transform: translateY(0) scale(1); }
                }

                .pmenu-header {
                  display: flex;
                  flex-direction: column;
                  align-items: center;
                  text-align: center;
                  padding: 18px 16px 16px;
                  border-radius: 12px;
                  margin-bottom: 6px;
                  background: linear-gradient(160deg, #f0fdf8 0%, #f7fafc 100%) !important;
                  border: 1px solid #eef6f1 !important;
                }
                .pmenu-avatar {
                  width: 56px; height: 56px;
                  border-radius: 50%;
                  overflow: hidden;
                  margin-bottom: 10px;
                  border: 3px solid #ffffff;
                  box-shadow: 0 4px 12px rgba(16,185,129,0.25);
                }
                .pmenu-avatar img { width: 100%; height: 100%; object-fit: cover; }
                .pmenu-avatar .pmenu-initial {
                  width: 100%; height: 100%;
                  background: linear-gradient(135deg,#10b981,#059669);
                  color: #fff;
                  display: flex; align-items: center; justify-content: center;
                  font-weight: 700; font-size: 1.4rem;
                }
                .pmenu-name {
                  font-weight: 700;
                  font-size: 0.95rem;
                  color: #0f172a !important;
                  line-height: 1.3;
                }
                .pmenu-email {
                  font-size: 0.78rem;
                  color: #64748b !important;
                  margin-top: 3px;
                  direction: ltr;
                  word-break: break-all;
                }

                .pmenu-divider {
                  margin: 6px 8px;
                  border: none;
                  border-top: 1px solid #f1f5f9 !important;
                }

                .pmenu-item {
                  display: flex;
                  align-items: center;
                  gap: 11px;
                  padding: 11px 14px;
                  border-radius: 10px;
                  text-decoration: none;
                  font-size: 0.9rem;
                  font-weight: 500;
                  color: #1e293b !important;
                  transition: background 0.15s, transform 0.1s;
                  cursor: pointer;
                  width: 100%;
                  background: transparent;
                  border: none;
                  font-family: inherit;
                  text-align: right;
                }
                .pmenu-item span { color: #1e293b !important; }
                .pmenu-item i { font-size: 1.15rem; color: #10b981 !important; }
                .pmenu-item:hover { background: #f1f5f9 !important; }
                .pmenu-item:active { transform: scale(0.98); }

                .pmenu-item.danger,
                .pmenu-item.danger span { color: #ef4444 !important; }
                .pmenu-item.danger i { color: #ef4444 !important; }
                  #profileMenuDropdown .pmenu-name { color: #0f172a !important; }
                #profileMenuDropdown .pmenu-email { color: #64748b !important; }

                /* Dark mode: keep the whole account menu dark and readable. */
                .theme-dark #profileMenuDropdown {
                  background: #0f172a !important;
                  border-color: #334155 !important;
                  box-shadow: 0 18px 50px rgba(0,0,0,0.45), 0 4px 14px rgba(0,0,0,0.3) !important;
                }
                .theme-dark #profileMenuDropdown .pmenu-header {
                  background: linear-gradient(160deg, #172033 0%, #111827 100%) !important;
                  border-color: #273449 !important;
                }
                .theme-dark #profileMenuDropdown .pmenu-avatar {
                  border-color: #334155 !important;
                }
                .theme-dark #profileMenuDropdown .pmenu-name {
                  color: #f8fafc !important;
                }
                .theme-dark #profileMenuDropdown .pmenu-email {
                  color: #94a3b8 !important;
                }
                .theme-dark #profileMenuDropdown .pmenu-divider {
                  border-top-color: #334155 !important;
                }
                .theme-dark #profileMenuDropdown .pmenu-item,
                .theme-dark #profileMenuDropdown .pmenu-item span {
                  color: #e2e8f0 !important;
                }
                .theme-dark #profileMenuDropdown .pmenu-item:hover {
                  background: #1e293b !important;
                }
                .theme-dark #profileMenuDropdown .pmenu-item.danger,
                .theme-dark #profileMenuDropdown .pmenu-item.danger span,
                .theme-dark #profileMenuDropdown .pmenu-item.danger i {
                  color: #f87171 !important;
                }
                .theme-dark #profileMenuDropdown .pmenu-item.danger:hover {
                  background: rgba(239, 68, 68, 0.12) !important;
                }

                #profileMenuBtn {
                  background: transparent !important;
                  background-color: transparent !important;
                  border: none !important;
                  box-shadow: none !important;
                  outline: none !important;
                  padding: 0;
                  cursor: pointer;
                  display: flex;
                  align-items: center;
                  gap: 8px;
                }
                #profileMenuBtn .avatar-ring {
                  width: 42px; height: 42px;
                  border-radius: 50%;
                  overflow: hidden;
                  border: 2px solid var(--primary, #10b981);
                  flex-shrink: 0;
                  transition: box-shadow 0.2s;
                }
                #profileMenuBtn:hover .avatar-ring {
                  box-shadow: 0 0 0 3px rgba(16,185,129,0.25);
                }
                #profileMenuBtn .chevron {
                  font-size: 0.75rem;
                  color: #64748b;
                  transition: transform 0.2s;
                }
                .theme-dark #profileMenuBtn .chevron { color: #94a3b8; }
                #profileMenuBtn.open .chevron { transform: rotate(180deg); }
              </style>

              <div class="position-relative ms-2" id="profileMenuWrapper">
                <button type="button" id="profileMenuBtn" title="إعدادات الحساب">
                  <div class="avatar-ring position-relative">
                    <div style="width:100%;height:100%;background:linear-gradient(135deg,#10b981,#059669);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:1.1rem;border-radius:50%;">
                      {{ strtoupper(mb_substr($navUser->name ?? 'U', 0, 1)) }}
                    </div>
                    @if($navUser->profile_photo_path)
                      <img src="{{ $navUser->image_url }}" alt="" style="position:absolute;top:0;left:0;width:100%;height:100%;object-fit:cover;border-radius:50%;" onerror="this.style.display='none';">
                    @endif
                  </div>
                  <i class="bi bi-chevron-down chevron"></i>
                </button>

                <div id="profileMenuDropdown">
                  <div class="pmenu-header">
                    <div class="pmenu-avatar position-relative">
                      <div class="pmenu-initial">{{ strtoupper(mb_substr($navUser->name ?? 'U', 0, 1)) }}</div>
                      @if($navUser->profile_photo_path)
                        <img src="{{ $navUser->image_url }}" alt="" style="position:absolute;top:0;left:0;width:100%;height:100%;object-fit:cover;border-radius:50%;" onerror="this.style.display='none';">
                      @endif
                    </div>
                    <div class="pmenu-name">{{ $navUser->name }}</div>
                    <div class="pmenu-email">{{ $navUser->email }}</div>
                  </div>

                  <a href="{{ route('users.show', $navUser->id) }}" class="pmenu-item">
                    <i class="bi bi-person text-primary"></i>
                    <span>الملف الشخصي</span>
                  </a>

                  <hr class="pmenu-divider">

                  <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="pmenu-item danger">
                      <i class="bi bi-box-arrow-right"></i>
                      <span>تسجيل الخروج</span>
                    </button>
                  </form>
                </div>
              </div>

              <script>
              (function(){
                var btn  = document.getElementById('profileMenuBtn');
                var menu = document.getElementById('profileMenuDropdown');
                if (!btn || !menu) return;

                function openMenu() {
                  var rect = btn.getBoundingClientRect();
                  menu.style.top  = (rect.bottom + 8) + 'px';
                  menu.style.left = Math.max(8, rect.right - menu.offsetWidth) + 'px';
                  menu.style.display = 'block';
                  // recompute left after it has width
                  var w = menu.offsetWidth || 250;
                  menu.style.left = Math.max(8, rect.right - w) + 'px';
                  btn.classList.add('open');
                }
                function closeMenu() {
                  menu.style.display = 'none';
                  btn.classList.remove('open');
                }

                btn.addEventListener('click', function(e){
                  e.stopPropagation();
                  menu.style.display === 'block' ? closeMenu() : openMenu();
                });

                document.addEventListener('click', function(e){
                  if (!menu.contains(e.target) && !btn.contains(e.target)) closeMenu();
                });

                document.addEventListener('keydown', function(e){
                  if (e.key === 'Escape') closeMenu();
                });
              })();
              </script>
            @endif
        </div>
      </div>
    </div>
  </nav>

  {{-- Sidebar Overlay for Mobile --}}
  <div class="sidebar-overlay" id="sidebarOverlay"></div>

  <div class="card sidebar-fixed">
    <div class="list-group list-group-flush">
      @php $user = request()->user(); @endphp

      <a href="{{ route('dashboard.index') }}"
        class="list-group-item list-group-item-action {{ request()->routeIs('dashboard.*') ? 'active' : '' }}"><i
          class="bi bi-speedometer2"></i><span>لوحة التحكم</span></a>
      {{-- Donors & Donations (Hidden from Finance per request) --}}
      @if($user && !$user->roles->contains('key', 'finance'))
        @if($user->hasPermission('donors.view'))
          <a href="{{ route('donors.index') }}"
            class="list-group-item list-group-item-action {{ request()->routeIs('donors.*') ? 'active' : '' }}"><i
              class="bi bi-people"></i><span>المتبرعون</span></a>
        @endif
        @if($user->hasPermission('donations.view'))
          <a href="{{ route('donations.index') }}"
            class="list-group-item list-group-item-action {{ request()->routeIs('donations.*') ? 'active' : '' }}"><i
              class="bi bi-gift"></i><span>التبرعات</span></a>
        @endif
        @if($user->hasPermission('beneficiaries.view'))
          <a href="{{ route('beneficiaries.index') }}"
            class="list-group-item list-group-item-action {{ request()->routeIs('beneficiaries.*') ? 'active' : '' }}"><i
              class="bi bi-person-heart"></i><span>المستفيدون</span></a>
        @endif
      @endif

      @if($user && ($user->hasPermission('manage_logistics') || $user->hasPermission('delegates.view') || $user->hasPermission('travel_routes.view') || $user->hasPermission('trips.view') || $user->hasPermission('kafr_el_sheikh_deliveries.view')))
        <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
          data-bs-toggle="collapse" href="#delegatesCollapse" role="button"
          aria-expanded="{{ (request()->routeIs('logistics.*') || request()->routeIs('delegates.*') || request()->routeIs('travel-routes.*') || request()->routeIs('trips.*') || request()->routeIs('kafr-el-sheikh-deliveries.*')) ? 'true' : 'false' }}"
          aria-controls="delegatesCollapse">
          <span><i class="bi bi-signpost-2"></i> اللوجيستك</span>
          <i class="bi bi-chevron-down sidebar-toggle-icon"></i>
        </a>
        <div class="collapse {{ (request()->routeIs('logistics.*') || request()->routeIs('delegates.*') || request()->routeIs('travel-routes.*') || request()->routeIs('trips.*') || request()->routeIs('kafr-el-sheikh-deliveries.*')) ? 'show' : '' }} sub-list" id="delegatesCollapse">
          <div class="list-group list-group-flush ps-3">
            @if($user->hasPermission('manage_logistics'))
              <a href="{{ route('logistics.dashboard') }}"
                class="list-group-item list-group-item-action {{ request()->routeIs('logistics.dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2"></i><span>لوحة تحكم اللوجيستك</span>
              </a>
            @endif
            @if($user->hasPermission('delegates.view'))
              <a href="{{ route('delegates.index') }}"
                class="list-group-item list-group-item-action {{ request()->routeIs('delegates.*') ? 'active' : '' }}"><i
                  class="bi bi-person-badge"></i><span>المندوبون</span></a>
            @endif
            @if($user->hasPermission('travel_routes.view'))
              <a href="{{ route('travel-routes.index') }}"
                class="list-group-item list-group-item-action {{ request()->routeIs('travel-routes.*') ? 'active' : '' }}"><i
                  class="bi bi-geo"></i><span>خطوط السير</span></a>
            @endif
            @if($user->hasPermission('trips.view'))
              <a href="{{ route('trips.index') }}"
                class="list-group-item list-group-item-action {{ request()->routeIs('trips.*') ? 'active' : '' }}"><i
                  class="bi bi-pin-map"></i><span>الرحلات</span></a>
            @endif
            @if($user->hasPermission('kafr_el_sheikh_deliveries.view'))
              <a href="{{ route('kafr-el-sheikh-deliveries.index') }}"
                 class="list-group-item list-group-item-action {{ request()->routeIs('kafr-el-sheikh-deliveries.*') ? 'active' : '' }}">
                 <i class="bi bi-truck"></i><span>توصيلات كفر الشيخ</span>
              </a>
            @endif
          </div>
        </div>
      @endif

      @if($user && ($user->hasRole('hr') || $user->hasPermission('volunteers.view') || $user->hasPermission('users.view') || $user->hasPermission('hr.evaluations') || $user->hasPermission('payrolls.view') || $user->hasPermission('tasks.view')))
        <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
          data-bs-toggle="collapse" href="#hrCollapse" role="button"
          aria-expanded="{{ (request()->routeIs('volunteers.*') || request()->routeIs('volunteer-attendance.*') || request()->routeIs('volunteer-tasks.*') || request()->routeIs('volunteer-hours.*') || request()->routeIs('users.*') || request()->routeIs('employee-attendance.*') || request()->routeIs('employee-tasks.*') || request()->routeIs('leaves.*')) ? 'true' : 'false' }}"
          aria-controls="hrCollapse">
          <span><i class="bi bi-person-lines-fill"></i> الموارد البشرية HR</span>
          <i class="bi bi-chevron-down sidebar-toggle-icon"></i>
        </a>
        <div
          class="collapse {{ (request()->routeIs('volunteers.*') || request()->routeIs('volunteer-attendance.*') || request()->routeIs('volunteer-tasks.*') || request()->routeIs('volunteer-hours.*') || request()->routeIs('users.*') || request()->routeIs('employee-attendance.*') || request()->routeIs('employee-tasks.*') || request()->routeIs('leaves.*')) ? 'show' : '' }}"
          id="hrCollapse">
          <div class="list-group list-group-flush ps-3">
            <!-- Employees Submenu -->
            @if($user->hasRole('hr') || $user->hasPermission('users.view') || $user->hasPermission('employee_attendance.view') || $user->hasPermission('employee_tasks.view') || $user->hasPermission('payrolls.view'))
              <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
                data-bs-toggle="collapse" href="#empSubCollapse" role="button"
                aria-expanded="{{ (request()->routeIs('users.*') || request()->routeIs('employee-attendance.*') || request()->routeIs('employee-tasks.*') || request()->routeIs('leaves.*')) ? 'true' : 'false' }}"
                aria-controls="empSubCollapse">
                <span><i class="bi bi-person-badge"></i> الموظفين والمستخدمين</span>
                <i class="bi bi-chevron-down sidebar-toggle-icon"></i>
              </a>
              <div
                class="collapse {{ (request()->routeIs('users.*') || request()->routeIs('employee-attendance.*') || request()->routeIs('employee-tasks.*') || request()->routeIs('leaves.*')) ? 'show' : '' }}"
                id="empSubCollapse">
                <div class="list-group list-group-flush ps-3 border-start ms-3">
                  @if(($user->hasRole('hr') || $user->hasPermission('users.view')) && !$user->hasRole('finance'))
                    <a href="{{ route('users.index') }}"
                      class="list-group-item list-group-item-action border-0 {{ request()->routeIs('users.*') ? 'active' : '' }}">
                      <i class="bi bi-people"></i> قائمة الموظفين
                    </a>
                  @endif
                  @if($user->hasRole('hr') || $user->hasPermission('employee_attendance.view') || $user->hasPermission('employee_attendance.view_own'))
                    <a href="{{ route('employee-attendance.index') }}"
                      class="list-group-item list-group-item-action border-0 {{ request()->routeIs('employee-attendance.*') ? 'active' : '' }}">
                      <i class="bi bi-calendar-check"></i> حضور الموظفين
                    </a>
                  @endif
                  <a href="{{ route('leaves.index') }}"
                    class="list-group-item list-group-item-action border-0 {{ request()->routeIs('leaves.*') ? 'active' : '' }}">
                    <i class="bi bi-calendar2-week"></i> الإجازات
                  </a>
                  @if($user->hasPermission('employee_tasks.view') || $user->hasPermission('employee_tasks.view_own'))
                    <a href="{{ route('employee-tasks.index') }}"
                      class="list-group-item list-group-item-action border-0 {{ request()->routeIs('employee-tasks.*') ? 'active' : '' }}">
                      <i class="bi bi-list-check"></i> مهام الموظفين
                    </a>
                  @endif
                  @if($user->hasRole('hr') || $user->hasPermission('payrolls.view'))
                    <a href="{{ route('payrolls.index') }}"
                      class="list-group-item list-group-item-action border-0 {{ request()->routeIs('payrolls.*') ? 'active' : '' }}">
                      <i class="bi bi-cash-coin"></i> مسيرات الرواتب
                    </a>
                  @endif
                </div>
              </div>
            @endif

            <!-- Volunteers Submenu -->
            @if($user->hasRole('hr') || $user->hasPermission('volunteers.view') || $user->hasPermission('volunteer_attendance.view') || $user->hasPermission('volunteer_tasks.view') || $user->hasPermission('volunteer_hours.view') || $user->hasPermission('manage_volunteers_hr'))
              <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
                data-bs-toggle="collapse" href="#volSubCollapse" role="button"
                aria-expanded="{{ (request()->routeIs('volunteers.*') || request()->routeIs('volunteer-attendance.*') || request()->routeIs('volunteer-tasks.*') || request()->routeIs('volunteer-hours.*')) ? 'true' : 'false' }}"
                aria-controls="volSubCollapse">
                <span><i class="bi bi-person-hearts"></i> المتطوعين</span>
                <i class="bi bi-chevron-down sidebar-toggle-icon"></i>
              </a>
              <div
                class="collapse {{ (request()->routeIs('volunteers.*') || request()->routeIs('volunteer-attendance.*') || request()->routeIs('volunteer-tasks.*') || request()->routeIs('volunteer-hours.*')) ? 'show' : '' }}"
                id="volSubCollapse">
                <div class="list-group list-group-flush ps-3 border-start ms-3">
                  @if($user->hasRole('hr') || $user->hasPermission('volunteers.view'))
                    <a href="{{ route('volunteers.index') }}"
                      class="list-group-item list-group-item-action border-0 {{ request()->routeIs('volunteers.*') ? 'active' : '' }}">
                      <i class="bi bi-person-lines-fill"></i> قائمة المتطوعين
                    </a>
                  @endif
                  @if($user->hasRole('hr') || $user->hasPermission('volunteer_attendance.view') || $user->hasPermission('volunteer_attendance.view_own'))
                    <a href="{{ route('volunteer-attendance.index') }}"
                      class="list-group-item list-group-item-action border-0 {{ request()->routeIs('volunteer-attendance.*') ? 'active' : '' }}">
                      <i class="bi bi-calendar-event"></i> حضور المتطوعين
                    </a>
                  @endif

                  @if($user->hasRole('hr') || $user->hasPermission('volunteer_hours.view'))
                    <a href="{{ route('volunteer-hours.index') }}"
                      class="list-group-item list-group-item-action border-0 {{ request()->routeIs('volunteer-hours.*') ? 'active' : '' }}">
                      <i class="bi bi-clock-history"></i> ساعات التطوع
                    </a>
                  @endif
                </div>
              </div>
            @endif

            @if($user->hasRole('hr') || $user->hasPermission('hr.evaluations'))
              <a href="{{ route('hr.evaluations') }}"
                class="list-group-item list-group-item-action {{ request()->routeIs('hr.evaluations') ? 'active' : '' }}">
                <i class="bi bi-clipboard-check"></i> التقييمات
              </a>
            @endif

            @if(($user->hasRole('hr') || $user->hasPermission('tasks.view') || $user->hasPermission('view_own_tasks')) && !$user->hasRole('finance'))
              <a href="{{ route('tasks.index') }}"
                class="list-group-item list-group-item-action {{ request()->routeIs('tasks.*') ? 'active' : '' }}">
                <i class="bi bi-check2-square"></i> المهام العامة
              </a>
            @endif
          </div>
        </div>
      @endif

      {{-- Systems & Settings --}}
      @if($user && ($user->hasPermission('roles.view') || $user->hasPermission('audits.view')))
        <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
          data-bs-toggle="collapse" href="#settingsCollapse" role="button"
          aria-expanded="{{ (request()->routeIs('roles.*') || request()->routeIs('audits.*')) ? 'true' : 'false' }}"
          aria-controls="settingsCollapse">
          <span><i class="bi bi-gear"></i> الإعدادات والنظام</span>
          <i class="bi bi-chevron-down sidebar-toggle-icon"></i>
        </a>
        <div class="collapse {{ (request()->routeIs('roles.*') || request()->routeIs('audits.*')) ? 'show' : '' }} sub-list" id="settingsCollapse">
            @if($user->hasPermission('roles.view'))
              <a href="{{ route('roles.index') }}"
                class="list-group-item list-group-item-action {{ request()->routeIs('roles.*') ? 'active' : '' }}">
                <i class="bi bi-shield-lock"></i> الأدوار والصلاحيات
              </a>
            @endif
            @if($user->hasPermission('manage_change_requests'))
              <a href="{{ route('change-requests.index') }}"
                class="list-group-item list-group-item-action {{ request()->routeIs('change-requests.*') ? 'active' : '' }}">
                <i class="bi bi-patch-check"></i> طلبات المراجعة (الإلغاء والتعديل)
                @php $pendingCount = \App\Models\ChangeRequest::where('status', 'pending')->count(); @endphp
                @if($pendingCount > 0)
                  <span class="badge bg-danger rounded-pill float-start ms-2">{{ $pendingCount }}</span>
                @endif
              </a>
            @endif
            @if($user->hasPermission('audits.view'))
              <a href="{{ route('audits.index') }}"
                class="list-group-item list-group-item-action {{ request()->routeIs('audits.*') ? 'active' : '' }}">
                <i class="bi bi-activity"></i> السجلات Logs
              </a>
            @endif
        </div>
      @endif
      @php $u = request()->user();
      $isAdmin = $u && $u->roles()->where('key', 'admin')->exists(); @endphp

      <!-- Accounts Section -->
      @if($user && ($user->hasRole('finance') || $user->hasPermission('accounts.view') || $user->hasPermission('journal_entries.view') || $user->hasPermission('expenses.view') || $user->hasPermission('financial_closures.view')))
        <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
          data-bs-toggle="collapse" href="#accCollapse" role="button"
          aria-expanded="{{ (request()->routeIs('expenses.*') || request()->routeIs('closures.*') || request()->routeIs('accounts.*') || request()->routeIs('journal-entries.*') || request()->routeIs('treasuries.*') || request()->routeIs('revenues.*')) ? 'true' : 'false' }}"
          aria-controls="accCollapse">
          <span><i class="bi bi-calculator"></i> الحسابات</span>
          <i class="bi bi-chevron-down sidebar-toggle-icon"></i>
        </a>
        <div
          class="collapse {{ (request()->routeIs('expenses.*') || request()->routeIs('closures.*') || request()->routeIs('accounts.*') || request()->routeIs('journal-entries.*') || request()->routeIs('treasuries.*') || request()->routeIs('revenues.*')) ? 'show' : '' }}"
          id="accCollapse">
          <div class="list-group list-group-flush ps-3">
            @if($user->hasRole('finance') || $user->hasPermission('accounts.view'))
              <a href="{{ route('accounts.index') }}"
                class="list-group-item list-group-item-action {{ request()->routeIs('accounts.*') ? 'active' : '' }}">
                <i class="bi bi-diagram-3"></i> دليل الحسابات
              </a>
            @endif
            @if($user->hasRole('finance') || $user->hasPermission('journal_entries.view'))
              <a href="{{ route('journal-entries.index') }}"
                class="list-group-item list-group-item-action {{ request()->routeIs('journal-entries.*') ? 'active' : '' }}">
                <i class="bi bi-journal-text"></i> القيود اليومية
              </a>
            @endif
            @if($user->hasPermission('expenses.view'))
              <a href="{{ route('expenses.index') }}"
                class="list-group-item list-group-item-action {{ request()->routeIs('expenses.*') ? 'active' : '' }}">
                <i class="bi bi-cash-stack"></i> المصروفات
              </a>
            @endif
            @if($user->hasRole('finance') || $user->hasPermission('accounts.view'))
              <a href="{{ route('revenues.index') }}"
                class="list-group-item list-group-item-action {{ request()->routeIs('revenues.*') ? 'active' : '' }}">
                <i class="bi bi-graph-up-arrow"></i> الإيرادات والتحليل
              </a>
            @endif
            @if($user->hasRole('finance') || $user->hasPermission('accounts.view'))
              <a href="{{ route('treasuries.dashboard') }}"
                class="list-group-item list-group-item-action {{ request()->routeIs('treasuries.*') ? 'active' : '' }}">
                <i class="bi bi-safe"></i> الخزائن
              </a>
            @endif
            @if($user->hasPermission('financial_closures.view'))
              <a href="{{ route('closures.index') }}"
                class="list-group-item list-group-item-action {{ request()->routeIs('closures.*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-lock"></i> الإقفال المالي
              </a>
            @endif
          </div>
        </div>
      @endif

      @if($user && ($user->hasPermission('warehouses.view') || $user->hasPermission('items.view') || $user->hasPermission('inventory_transactions.view') || $user->hasPermission('suppliers.view')))
        <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
          data-bs-toggle="collapse" href="#invCollapse" role="button"
          aria-expanded="{{ (request()->routeIs('warehouses.*') || request()->routeIs('items.*') || request()->routeIs('inventory-transactions.*') || request()->routeIs('suppliers.*')) ? 'true' : 'false' }}"
          aria-controls="invCollapse">
          <span><i class="bi bi-building"></i> إدارة المخازن</span>
          <i class="bi bi-chevron-down sidebar-toggle-icon"></i>
        </a>
        <div
          class="collapse {{ (request()->routeIs('warehouses.*') || request()->routeIs('items.*') || request()->routeIs('inventory-transactions.*') || request()->routeIs('suppliers.*')) ? 'show' : '' }} sub-list"
          id="invCollapse">
          @if($user->hasPermission('warehouses.view'))
            <a href="{{ route('warehouses.index') }}"
              class="list-group-item list-group-item-action {{ request()->routeIs('warehouses.*') ? 'active' : '' }}"><i
                class="bi bi-building"></i><span>المخازن</span></a>
          @endif
          @if($user->hasPermission('items.view'))
            <a href="{{ route('items.index') }}"
              class="list-group-item list-group-item-action {{ request()->routeIs('items.*') ? 'active' : '' }}"><i
                class="bi bi-box"></i><span>الأصناف</span></a>
          @endif
          @if($user->hasPermission('suppliers.view'))
            <a href="{{ route('suppliers.index') }}"
                class="list-group-item list-group-item-action {{ request()->routeIs('suppliers.*') ? 'active' : '' }}"><i
                  class="bi bi-shop"></i><span>الموردين</span></a>
          @endif
          @if($user->hasPermission('inventory_transactions.view'))
            <a href="{{ route('inventory-transactions.index') }}"
              class="list-group-item list-group-item-action {{ request()->routeIs('inventory-transactions.*') ? 'active' : '' }}"><i
                class="bi bi-arrow-left-right"></i><span>حركات المخزون</span></a>
          @endif
        </div>
      @endif

      @if($user && !$user->hasRole('hr') && !$user->hasRole('marketer') && ($user->hasPermission('projects.view') || $user->hasPermission('manage_project')))
        <a href="{{ route('projects.index') }}"
          class="list-group-item list-group-item-action {{ request()->routeIs('projects.*') ? 'active' : '' }}"><i
            class="bi bi-kanban"></i><span>إدارة المشاريع</span></a>
      @endif

      @if($user && ($user->hasPermission('campaigns.view') || $user->hasPermission('ramadan_bags.view') || $user->hasPermission('ramadan_iftars.view')))
        <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
          data-bs-toggle="collapse" href="#campaignsCollapse" role="button"
          aria-expanded="{{ request()->routeIs('campaigns.*') || request()->routeIs('ramadan-bags.*') || request()->routeIs('ramadan-iftars.*') ? 'true' : 'false' }}"
          aria-controls="campaignsCollapse">
          <span><i class="bi bi-megaphone"></i> حملات موسمية</span>
          <i class="bi bi-chevron-down sidebar-toggle-icon"></i>
        </a>
        <div class="collapse {{ request()->routeIs('campaigns.*') || request()->routeIs('ramadan-bags.*') || request()->routeIs('ramadan-iftars.*') ? 'show' : '' }} sub-list" id="campaignsCollapse">
          <div class="list-group list-group-flush ps-3">
            @if($user->hasPermission('campaigns.view'))
              <a href="{{ route('campaigns.index') }}"
                class="list-group-item list-group-item-action {{ request()->routeIs('campaigns.*') ? 'active' : '' }}">
                <i class="bi bi-megaphone"></i><span>إدارة الحملات</span></a>
            @endif
            @if($user->hasPermission('ramadan_bags.view'))
              <a href="{{ route('ramadan-bags.index') }}"
                class="list-group-item list-group-item-action {{ request()->routeIs('ramadan-bags.*') ? 'active' : '' }}">
                <i class="bi bi-bag-heart"></i><span>شنط رمضان</span></a>
            @endif
            @if($user->hasPermission('ramadan_iftars.view'))
              <a href="{{ route('ramadan-iftars.index') }}"
                class="list-group-item list-group-item-action {{ request()->routeIs('ramadan-iftars.*') ? 'active' : '' }}">
                <i class="bi bi-cup-hot"></i><span>إفطارات رمضان</span></a>
            @endif
          </div>
        </div>
      @endif

      @if($user && ($user->hasPermission('manage_specialized_services') || $user->hasPermission('school_collaborations.view') || $user->hasPermission('memberships.view')))
        <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
          data-bs-toggle="collapse" href="#collaborationsCollapse" role="button"
          aria-expanded="{{ request()->routeIs('school-collaborations.*') || request()->routeIs('memberships.*') ? 'true' : 'false' }}"
          aria-controls="collaborationsCollapse">
          <span><i class="bi bi-diagram-3"></i> التعاونات والشراكات والعضوية</span>
          <i class="bi bi-chevron-down sidebar-toggle-icon"></i>
        </a>
        <div class="collapse {{ request()->routeIs('school-collaborations.*') || request()->routeIs('memberships.*') ? 'show' : '' }} sub-list" id="collaborationsCollapse">
          <div class="list-group list-group-flush ps-3">
            @if($user->hasPermission('school_collaborations.view'))
              <a href="{{ route('school-collaborations.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('school-collaborations.*') ? 'active' : '' }}"><i class="bi bi-building"></i><span>تعاونات المدارس</span></a>
            @endif
            @if($user->hasPermission('memberships.view'))
              <a href="{{ route('memberships.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('memberships.*') ? 'active' : '' }}"><i class="bi bi-person-badge"></i><span>العضويات</span></a>
            @endif
          </div>
        </div>
      @endif

      @if($user && ($user->hasPermission('kafr_el_sheikh_services.view') || $user->hasPermission('oncology_medicine_reps.view') || $user->hasPermission('kafr_el_sheikh_brokers.view') || $user->hasPermission('tanta_workers.view')))
        <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
          data-bs-toggle="collapse" href="#generalServicesCollapse" role="button"
          aria-expanded="{{ request()->routeIs('kafr-el-sheikh-services.*') || request()->routeIs('oncology-medicine-reps.*') || request()->routeIs('kafr-el-sheikh-brokers.*') || request()->routeIs('tanta-workers.*') ? 'true' : 'false' }}"
          aria-controls="generalServicesCollapse">
          <span><i class="bi bi-briefcase"></i> الخدمات العامة</span>
          <i class="bi bi-chevron-down sidebar-toggle-icon"></i>
        </a>
        <div class="collapse {{ request()->routeIs('kafr-el-sheikh-services.*') || request()->routeIs('oncology-medicine-reps.*') || request()->routeIs('kafr-el-sheikh-brokers.*') || request()->routeIs('tanta-workers.*') ? 'show' : '' }} sub-list" id="generalServicesCollapse">
          <div class="list-group list-group-flush ps-3">
            @if($user->hasPermission('kafr_el_sheikh_services.view'))
              <a href="{{ route('kafr-el-sheikh-services.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('kafr-el-sheikh-services.*') ? 'active' : '' }}"><i class="bi bi-tools"></i><span>خدمات كفر الشيخ</span></a>
            @endif
            @if($user->hasPermission('oncology_medicine_reps.view'))
              <a href="{{ route('oncology-medicine-reps.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('oncology-medicine-reps.*') ? 'active' : '' }}"><i class="bi bi-prescription2"></i><span>مناديب أدوية الأورام</span></a>
            @endif
            @if($user->hasPermission('kafr_el_sheikh_brokers.view'))
              <a href="{{ route('kafr-el-sheikh-brokers.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('kafr-el-sheikh-brokers.*') ? 'active' : '' }}"><i class="bi bi-pin-map"></i><span>سماسرة كفر الشيخ</span></a>
            @endif
            @if($user->hasPermission('tanta_workers.view'))
              <a href="{{ route('tanta-workers.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('tanta-workers.*') ? 'active' : '' }}"><i class="bi bi-people"></i><span>عمال باليومية (طنطا)</span></a>
            @endif
          </div>
        </div>
      @endif

      @if($user && ($user->hasPermission('visits.view') || $user->roles()->where('name', 'باحث ميداني')->exists() || $user->roles()->where('key', 'field_researcher')->exists()))
        <a href="{{ route('visits.index') }}"
          class="list-group-item list-group-item-action {{ request()->routeIs('visits.*') ? 'active' : '' }}"><i
            class="bi bi-geo-alt"></i><span>الزيارات الميدانية</span></a>
      @endif

      @if($user && ($user->hasPermission('reception.view') || $user->roles()->where('name', 'الاستقبال')->exists() || $user->roles()->where('key', 'receptionist')->exists()))
        <a href="{{ route('reception.index') }}"
          class="list-group-item list-group-item-action {{ request()->routeIs('reception.*') ? 'active' : '' }}"><i
            class="bi bi-telephone-inbound"></i><span>الاستقبال</span></a>
      @endif

      @if($user && $user->hasPermission('guest_houses.view'))
        <a href="{{ route('guest-houses.index') }}"
          class="list-group-item list-group-item-action {{ request()->routeIs('guest-houses.*') ? 'active' : '' }}"><i
            class="bi bi-house"></i><span>إدارة دار الضيافة</span></a>
      @endif

      @if($user && $user->hasPermission('workspaces.view'))
        <a href="{{ route('workspaces.index') }}"
          class="list-group-item list-group-item-action {{ request()->routeIs('workspaces.*') ? 'active' : '' }}"><i
            class="bi bi-easel"></i><span>إدارة workspace Ensan</span></a>
      @endif

      {{-- Website Management Unit --}}
      {{-- Website Management Unit --}}
      @if($user && ($user->hasPermission('website.view') || $user->hasPermission('website.settings.view_edit') || $user->hasPermission('website.headquarters.view') || $user->hasPermission('website.partners.view') || $user->hasPermission('website.board.view') || $user->hasPermission('website.content.view_edit') || $user->hasPermission('website.campaigns_content.view_edit') || $user->hasPermission('website.guest_house_content.view_edit') || $user->hasPermission('website.news.view') || $user->hasPermission('website.contact_messages.view') || $user->hasPermission('website.subscriptions.view') || $user->hasPermission('website.volunteer_requests.view') || $user->hasPermission('website.donation_page.view_edit') || $user->hasPermission('website.donation_settings.view_edit') || $user->hasPermission('website.accounts.view') || $user->hasPermission('website.donation_accounts.view')))
        <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
          data-bs-toggle="collapse" href="#websiteCollapse" role="button"
          aria-expanded="{{ request()->routeIs('website.*') ? 'true' : 'false' }}"
          aria-controls="websiteCollapse">
          <span><i class="bi bi-globe"></i> الموقع الإلكتروني</span>
          <i class="bi bi-chevron-down sidebar-toggle-icon"></i>
        </a>
        <div class="collapse {{ request()->routeIs('website.*') ? 'show' : '' }} sub-list" id="websiteCollapse">
          <div class="list-group list-group-flush ps-3">
            @if($user->hasPermission('website.settings.view_edit'))
              <a href="{{ route('website.settings.index') }}"
                class="list-group-item list-group-item-action {{ request()->routeIs('website.settings.*') ? 'active' : '' }}">
                <i class="bi bi-gear-wide-connected"></i><span>محتوى الصفحة الرئيسية</span>
              </a>
            @endif

            @if($user->hasPermission('website.headquarters.view'))
              <a href="{{ route('website.headquarters.index') }}"
                class="list-group-item list-group-item-action {{ request()->routeIs('website.headquarters.*') ? 'active' : '' }}">
                <i class="bi bi-geo-alt fs-5"></i><span>المقر والفروع</span>
              </a>
            @endif

            @if($user->hasPermission('website.partners.view'))
              <a href="{{ route('website.partners.index') }}"
                class="list-group-item list-group-item-action {{ request()->routeIs('website.partners.*') ? 'active' : '' }}">
                <i class="bi bi-award fs-5"></i><span>جدار الشرف</span>
              </a>
            @endif

            @if($user->hasPermission('website.board.view'))
              <a href="{{ route('website.board.index') }}"
                class="list-group-item list-group-item-action {{ request()->routeIs('website.board.*') ? 'active' : '' }}">
                <i class="bi bi-people"></i><span>مجلس الأمناء</span>
              </a>
            @endif

            @if($user->hasPermission('website.content.view_edit'))
              <a href="{{ route('website.content') }}"
                class="list-group-item list-group-item-action {{ request()->routeIs('website.content') ? 'active' : '' }}">
                <i class="bi bi-window-sidebar"></i><span>محتوى المشاريع</span>
              </a>
            @endif

            @if($user->hasPermission('website.campaigns_content.view_edit'))
              <a href="{{ route('website.campaigns.content') }}"
                class="list-group-item list-group-item-action {{ request()->routeIs('website.campaigns.content') ? 'active' : '' }}">
                <i class="bi bi-megaphone fs-5 text-warning"></i><span>محتوى الحملات</span>
              </a>
            @endif

            @if($user->hasPermission('website.guest_house_content.view_edit'))
              <a href="{{ route('website.guest-house.content') }}"
                class="list-group-item list-group-item-action {{ request()->routeIs('website.guest-house.*') ? 'active' : '' }}">
                <i class="bi bi-building fs-5 text-primary"></i><span>دار الضيافة</span>
              </a>
            @endif

            @if($user->hasPermission('website.news.view'))
              <a href="{{ route('website.news.index') }}"
                class="list-group-item list-group-item-action {{ request()->routeIs('website.news.*') ? 'active' : '' }}">
                <i class="bi bi-newspaper"></i><span>الأخبار والفعاليات</span>
              </a>
            @endif

            @if($user->hasPermission('website.contact_messages.view'))
              <a href="{{ route('website.contact-messages.index') }}"
                class="list-group-item list-group-item-action {{ (request()->routeIs('website.contact-messages.*') && !request()->routeIs('mobile.*')) ? 'active' : '' }}">
                <i class="bi bi-envelope"></i><span>تواصل معنا</span>
              </a>
            @endif

            @if($user->hasPermission('website.subscriptions.view'))
              <a href="{{ route('website.subscriptions.index') }}"
                class="list-group-item list-group-item-action {{ request()->routeIs('website.subscriptions.*') ? 'active' : '' }}">
                <i class="bi bi-envelope-paper"></i><span>النشرة الإخبارية</span>
              </a>
            @endif

            @if($user->hasPermission('website.volunteer_requests.view'))
              <a href="{{ route('website.volunteer-requests.index') }}"
                class="list-group-item list-group-item-action {{ (request()->routeIs('website.volunteer-requests.*') && !request()->routeIs('mobile.*')) ? 'active' : '' }}">
                <i class="bi bi-person-plus text-primary"></i><span>طلبات التطوع (الموقع)</span>
              </a>
            @endif

            @if($user->hasPermission('website.donation_page.view_edit'))
              <a href="{{ route('website.donation-page.index') }}"
                class="list-group-item list-group-item-action {{ request()->routeIs('website.donation-page.*') ? 'active' : '' }}">
                <i class="bi bi-heart-fill text-danger"></i><span>إعدادات صفحة التبرع</span>
              </a>
            @endif

            @if($user->hasPermission('website.donation_settings.view_edit'))
              <a href="{{ route('website.donation-settings.unified') }}"
                class="list-group-item list-group-item-action {{ (request()->routeIs('website.donation-settings.unified') || request()->routeIs('website.donation-settings.categories.*') || request()->routeIs('website.donation-settings.items.*')) ? 'active' : '' }}">
                <i class="bi bi-ui-checks-grid text-purple"></i><span>مجالات الدعم (الفئات)</span>
              </a>
            @endif

            @if($user->hasPermission('website.accounts.view'))
              <a href="{{ route('website.accounts.index') }}"
                class="list-group-item list-group-item-action {{ request()->routeIs('website.accounts.*') ? 'active' : '' }}">
                <i class="bi bi-person-badge text-info"></i><span>حسابات المتبرعين (دخول)</span>
              </a>
            @endif

            @if($user->hasPermission('website.donation_accounts.view'))
              <a href="{{ route('website.donation-accounts.index') }}"
                class="list-group-item list-group-item-action {{ request()->routeIs('website.donation-accounts.*') ? 'active' : '' }}">
                <i class="bi bi-wallet2 text-success"></i><span>حسابات تبرعات الويبسايت</span>
              </a>
            @endif

            <hr class="dropdown-divider opacity-10 mx-3 my-2">

            @if($user->hasPermission('website.content.view_edit'))
              <a href="{{ route('website.pages.index') }}"
                class="list-group-item list-group-item-action {{ request()->routeIs('website.pages.*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-text text-primary"></i><span>الصفحات الثابتة</span>
              </a>
              <a href="{{ route('website.cards.index') }}"
                class="list-group-item list-group-item-action {{ request()->routeIs('website.cards.*') ? 'active' : '' }}">
                <i class="bi bi-card-list text-info"></i><span>البطاقات التعريفية</span>
              </a>
            @endif

            @if($user->hasPermission('website.accounts.view'))
              <a href="{{ route('website.testimonials.index') }}"
                class="list-group-item list-group-item-action {{ request()->routeIs('website.testimonials.*') ? 'active' : '' }}">
                <i class="bi bi-chat-heart text-danger"></i><span>آراء المستفيدين</span>
              </a>
              <a href="{{ route('website.share-opinion') }}"
                class="list-group-item list-group-item-action {{ request()->routeIs('website.share-opinion') ? 'active' : '' }}">
                <i class="bi bi-chat-quote"></i><span>آراء المجتمع</span>
              </a>
            @endif
          </div>
        </div>
      @endif

      {{-- Mobile App Management Unit --}}
      @if($user && ($user->hasPermission('mobile.view') || $user->hasPermission('mobile.home_content.view_edit') || $user->hasPermission('mobile.news.view') || $user->hasPermission('mobile.volunteer_requests.view') || $user->hasPermission('mobile.case_applications.view') || $user->hasPermission('mobile.donations.view') || $user->hasPermission('mobile.donors.view') || $user->hasPermission('mobile.notifications.view') || $user->hasPermission('mobile.bookings.view')))
        <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
          data-bs-toggle="collapse" href="#mobileCollapse" role="button"
          aria-expanded="{{ (request()->routeIs('mobile.*') || request()->routeIs('website.volunteer-requests.*') || request()->routeIs('website.news.*') || request()->routeIs('website.bookings.*')) ? 'true' : 'false' }}"
          aria-controls="mobileCollapse">
          <span><i class="bi bi-phone"></i> تطبيق الموبايل</span>
          <i class="bi bi-chevron-down sidebar-toggle-icon"></i>
        </a>
        <div class="collapse {{ (request()->routeIs('mobile.*') || request()->routeIs('website.volunteer-requests.*') || request()->routeIs('website.news.*') || request()->routeIs('website.bookings.*')) ? 'show' : '' }} sub-list" id="mobileCollapse">
          <div class="list-group list-group-flush ps-3">
            @if($user->hasPermission('mobile.home_content.view_edit'))
              <a href="{{ route('mobile.home_content.index') }}"
                class="list-group-item list-group-item-action {{ request()->routeIs('mobile.home_content.*') ? 'active' : '' }}">
                <i class="bi bi-house-gear"></i><span>محتوى الصفحة الرئيسية</span>
              </a>
            @endif

            @if($user->hasPermission('mobile.news.view'))
              <a href="{{ route('mobile.news.index') }}"
                class="list-group-item list-group-item-action {{ request()->routeIs('mobile.news.*') ? 'active' : '' }}">
                <i class="bi bi-newspaper text-info"></i><span>أخبار التطبيق (منفصل)</span>
              </a>
            @endif

            @if($user->hasPermission('mobile.volunteer_requests.view'))
              <a href="{{ route('mobile.volunteer-requests.index') }}"
                class="list-group-item list-group-item-action {{ request()->routeIs('mobile.volunteer-requests.*') ? 'active' : '' }}">
                <i class="bi bi-person-heart text-danger"></i><span>طلبات التطوع (الموبايل)</span>
              </a>
            @endif

            @if($user->hasPermission('mobile.case_applications.view') || $user->hasPermission('mobile.bookings.view'))
              <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
                data-bs-toggle="collapse" href="#mobileCasesCollapse" role="button"
                aria-expanded="{{ request()->routeIs('mobile.case-applications.*') || request()->routeIs('website.bookings.*') ? 'true' : 'false' }}"
                aria-controls="mobileCasesCollapse">
                <span><i class="bi bi-heart-pulse text-danger"></i> طلبات الحالات المستحقة (الموبايل)</span>
                <i class="bi bi-chevron-down sidebar-toggle-icon"></i>
              </a>
              <div class="collapse {{ request()->routeIs('mobile.case-applications.*') || request()->routeIs('website.bookings.*') ? 'show' : '' }}" id="mobileCasesCollapse">
                <div class="list-group list-group-flush ps-3 border-start ms-3">
                  @if($user->hasPermission('mobile.case_applications.view'))
                    <a href="{{ route('mobile.case-applications.index', ['type' => 'zad']) }}"
                      class="list-group-item list-group-item-action border-0 {{ (request()->routeIs('mobile.case-applications.*') && request('type') == 'zad') ? 'active' : '' }}">
                      <i class="bi bi-star-fill text-danger"></i><span>طلبات مشروع زاد للموبايل</span>
                    </a>

                    <a href="{{ route('mobile.case-applications.index', ['type' => 'hope']) }}"
                      class="list-group-item list-group-item-action border-0 {{ (request()->routeIs('mobile.case-applications.*') && request('type') == 'hope') ? 'active' : '' }}">
                      <i class="bi bi-brightness-high-fill text-danger"></i><span>طلبات مشروع بعثاء الأمل للموبايل</span>
                    </a>
                  @endif

                  @if($user->hasPermission('mobile.bookings.view'))
                    <a href="{{ route('mobile.bookings.index') }}"
                      class="list-group-item list-group-item-action border-0 {{ request()->routeIs('mobile.bookings.*') ? 'active' : '' }}">
                      <i class="bi bi-buildings text-danger"></i><span>طلبات الحجز من الموبايل</span>
                    </a>
                  @endif
                </div>
              </div>
            @endif

            @if($user->hasPermission('mobile.donations.view'))
              <a href="{{ route('mobile.donations.index') }}"
                class="list-group-item list-group-item-action {{ request()->routeIs('mobile.donations.*') ? 'active' : '' }}">
                <i class="bi bi-cash-coin text-success"></i><span>سجلات التبرعات (الموبايل)</span>
              </a>
            @endif

            @if($user->hasPermission('mobile.donors.view'))
              <a href="{{ route('mobile.donors_auth.index') }}"
                class="list-group-item list-group-item-action {{ request()->routeIs('mobile.donors_auth.*') ? 'active' : '' }}">
                <i class="bi bi-person-badge-fill text-warning"></i><span>تسجيل الدخول للموبايل</span>
              </a>
            @endif

            @if($user->hasPermission('mobile.notifications.view'))
              <a href="{{ route('mobile.notifications.index') }}"
                class="list-group-item list-group-item-action {{ request()->routeIs('mobile.notifications.*') ? 'active' : '' }}">
                <i class="bi bi-bell"></i><span>الإشعارات (Push Notifications)</span>
              </a>
            @endif
          </div>
        </div>
      @endif



      @if($user && ($user->hasPermission('complaints.view') || $user->hasPermission('complaints.create') || $user->roles->contains('key', 'finance')))
        <a href="{{ route('complaints.index') }}"
          class="list-group-item list-group-item-action {{ request()->routeIs('complaints.*') ? 'active' : '' }}"><i
            class="bi bi-chat-dots"></i><span>الشكاوى</span></a>
      @endif


      @if($user && ($user->hasPermission('audits.view')))
        {{-- Already moved to Settings group --}}
      @endif
    </div>
  </div>
  <div class="content-wrapper fade-in">
    @if(!isset($hideGlobalAlerts) || !$hideGlobalAlerts)

    @endif
    @yield('content')

    <footer class="mt-5 pt-4 pb-4 text-center text-muted small">
      <div class="container">
        <div class="d-flex justify-content-center align-items-center mb-2">
          @if(file_exists(public_path('images/heart-icon.png')))
            <div class="d-flex align-items-center justify-content-center gap-2">
              <img src="{{ asset('images/heart-icon.png') }}" alt="logo" height="45" style="opacity: 1; width: auto !important;">
            </div>
          @endif
        </div>
        <p class="mb-0 fw-medium text-secondary">جميع الحقوق محفوظة مؤسسة إنسان {{ date('Y') }} &copy;</p>
      </div>
    </footer>
  </div>
  @if(session('show_admin_notifications') && $navUser?->hasRole('admin'))
    @include('partials.admin_login_notifications_modal')
  @endif

  @if($navUser && $navUser->hasPermission('notifications.view'))


    <div class="offcanvas offcanvas-end" tabindex="-1" id="notifOffcanvas" aria-labelledby="notifOffcanvasLabel">
      <div class="offcanvas-header border-bottom py-3">
        <div class="d-flex align-items-center gap-2">
          <h5 id="notifOffcanvasLabel" class="mb-0 fw-bold">الإشعارات</h5>
          <span id="notifOffcanvasCount" class="badge rounded-pill bg-danger-subtle text-danger border border-danger-subtle px-2 py-1" style="font-size: 0.78rem;">0</span>
        </div>
        <div class="d-flex align-items-center gap-2">
          <button type="button" id="testNotifSoundBtn" class="btn btn-sm btn-outline-primary rounded-pill px-2 py-1 d-flex align-items-center gap-1" style="font-size: 0.78rem;" title="تشغيل صوت التنبيه">
            <i class="bi bi-volume-up-fill"></i>
            <span>تجربة الصوت</span>
          </button>
          <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
      </div>
      <div class="offcanvas-body">
        <div class="d-flex gap-2 mb-3">
          <select id="notifSideFilter" class="form-select form-select-sm" style="width:auto">
            <option value="">الكل</option>
            <option value="success">نجاح</option>
            <option value="info">معلومة</option>
            <option value="warning">تحذير</option>
            <option value="danger">هام</option>
            <option value="secondary">عام</option>
          </select>
          <a href="{{ route('notifications.index') }}" class="btn btn-light btn-sm">عرض الكل</a>
        </div>
        <div id="notifSideList" class="d-flex flex-column gap-2"></div>
      </div>
    </div>
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        var off = document.getElementById('notifOffcanvas');
        var list = document.getElementById('notifSideList');
        var filter = document.getElementById('notifSideFilter');
        var bellBadge = document.getElementById('notificationBellBadge');
        var bellBtn = document.getElementById('notificationBellBtn');
        var testSoundBtn = document.getElementById('testNotifSoundBtn');
        var offcanvasCount = document.getElementById('notifOffcanvasCount');
        var fKey = 'sidebar.notifications.filter';
        var seenKey = 'notifications.seen.v1.{{ $navUser->id }}';
        var audioContext = null;
        var pendingSound = false;
        var lastItemCount = null;

        var notificationSignature = function (item) {
          return [item.category || '', item.type || '', item.text || '', item.link || ''].join('|');
        };

        var readSeen = function () {
          try { return JSON.parse(localStorage.getItem(seenKey) || '{}') || {}; }
          catch (e) { return {}; }
        };

        var saveSeen = function (seen) {
          var cutoff = Date.now() - (30 * 24 * 60 * 60 * 1000);
          Object.keys(seen).forEach(function (key) {
            if (Number(seen[key]) < cutoff) delete seen[key];
          });
          try { localStorage.setItem(seenKey, JSON.stringify(seen)); } catch (e) {}
        };

        var getAudioContext = function () {
          if (!audioContext) {
            var AudioContextClass = window.AudioContext || window.webkitAudioContext;
            if (AudioContextClass) {
              audioContext = new AudioContextClass();
            }
          }
          if (audioContext && audioContext.state === 'suspended') {
            audioContext.resume().catch(function () {});
          }
          return audioContext;
        };

        var synthesizeChime = function (context) {
          try {
            var now = context.currentTime;
            // Two-tone modern pleasant chime: A5 (880Hz) -> E6 (1318.5Hz)
            var notes = [
              { freq: 880, start: 0, duration: 0.20, gain: 0.28 },
              { freq: 1318.5, start: 0.13, duration: 0.36, gain: 0.32 }
            ];

            notes.forEach(function (n) {
              var osc = context.createOscillator();
              var gain = context.createGain();

              osc.type = 'sine';
              osc.frequency.setValueAtTime(n.freq, now + n.start);

              gain.gain.setValueAtTime(0.0001, now + n.start);
              gain.gain.linearRampToValueAtTime(n.gain, now + n.start + 0.02);
              gain.gain.exponentialRampToValueAtTime(0.0001, now + n.start + n.duration);

              osc.connect(gain);
              gain.connect(context.destination);

              osc.start(now + n.start);
              osc.stop(now + n.start + n.duration + 0.05);
            });
          } catch (e) {
            console.warn('Notification audio playback error:', e);
          }
        };

        var playNotificationSound = function () {
          var context = getAudioContext();
          if (!context) return;
          if (context.state !== 'running') {
            pendingSound = true;
            context.resume().then(function () {
              if (context.state === 'running') {
                pendingSound = false;
                synthesizeChime(context);
              }
            }).catch(function () {});
            return;
          }
          pendingSound = false;
          synthesizeChime(context);
        };
        window.playNotificationSound = playNotificationSound;

        var unlockNotificationSound = function () {
          var context = getAudioContext();
          if (!context) return;
          context.resume().then(function () {
            if (pendingSound) {
              playNotificationSound();
            }
          }).catch(function () {});
        };

        ['pointerdown', 'keydown', 'touchstart', 'click'].forEach(function (eventName) {
          document.addEventListener(eventName, unlockNotificationSound, { once: true, passive: true });
        });

        if (bellBtn) {
          bellBtn.addEventListener('click', function () {
            playNotificationSound();
          });
        }

        if (testSoundBtn) {
          testSoundBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            playNotificationSound();
          });
        }

        var notifyAboutNewItems = function (items) {
          if (!Array.isArray(items) || items.length === 0) return;
          var seen = readSeen();
          items.forEach(function (item) { seen[notificationSignature(item)] = Date.now(); });
          saveSeen(seen);
        };

        var updateBellBadge = function (items) {
          var count = Array.isArray(items) ? items.length : 0;
          if (bellBadge) {
            bellBadge.textContent = count > 99 ? '99+' : String(count);
            if (count > 0) {
              bellBadge.classList.remove('d-none');
              bellBadge.style.display = 'inline-flex';
            } else {
              bellBadge.classList.add('d-none');
              bellBadge.style.display = 'none';
            }
          }
          if (offcanvasCount) {
            offcanvasCount.textContent = count > 99 ? '99+' : String(count);
          }
        };

        var render = function (items) {
          updateBellBadge(items);
          list.innerHTML = '';
          if (!items || !items.length) {
            list.innerHTML = '<div class="alert alert-secondary text-center py-5 border-0 bg-opacity-10" style="background: var(--gray-100)"><i class="bi bi-bell-slash fs-1 d-block mb-3 opacity-25"></i><p class="mb-0 fw-medium">لا توجد إشعارات حالياً</p></div>';
            return;
          }
          var val = filter.value;
          items.forEach(function (n) {
            if (val && n.type !== val) return;

            var card = document.createElement('div');
            card.className = 'card border-0 mb-3 notification-card border-' + n.type;

            var cardBody = document.createElement('div');
            cardBody.className = 'card-body p-3';

            var iconMap = {
              'success': 'check-circle-fill',
              'info': 'info-circle-fill',
              'warning': 'exclamation-triangle-fill',
              'danger': 'exclamation-octagon-fill',
              'secondary': 'bell-fill'
            };

            var labelMap = {
              'success': 'نجاح',
              'info': 'معلومة',
              'warning': 'تحذير',
              'danger': 'هام',
              'secondary': 'عام'
            };

            var header = document.createElement('div');
            header.className = 'd-flex align-items-center justify-content-between mb-3';

            var headRight = document.createElement('div');
            headRight.className = 'd-flex align-items-center gap-2';

            var iconSpan = document.createElement('span');
            iconSpan.className = 'd-flex align-items-center justify-content-center rounded-circle bg-' + n.type + ' bg-opacity-10 text-' + n.type;
            iconSpan.style.width = '32px';
            iconSpan.style.height = '32px';
            iconSpan.innerHTML = '<i class="bi bi-' + (iconMap[n.type] || 'bell') + ' fs-5"></i>';

            var badge = document.createElement('span');
            badge.className = 'notif-label bg-' + n.type + ' bg-opacity-10 text-' + n.type + ' border border-' + n.type + ' border-opacity-25';
            badge.textContent = labelMap[n.type] || 'إشعار';

            headRight.appendChild(iconSpan);
            headRight.appendChild(badge);
            header.appendChild(headRight);

            var textPara = document.createElement('p');
            textPara.className = 'mb-3 lh-base fw-medium';
            textPara.style.fontSize = '0.95rem';
            textPara.textContent = n.text;

            var actionDiv = document.createElement('div');
            actionDiv.className = 'd-flex justify-content-start';
            var link = document.createElement('a');
            link.href = n.link;
            link.className = 'btn btn-' + n.type + ' btn-sm rounded-pill px-4 shadow-sm';
            link.innerHTML = '<i class="bi bi-box-arrow-up-right me-1"></i> فتح';
            actionDiv.appendChild(link);

            cardBody.appendChild(header);
            cardBody.appendChild(textPara);
            cardBody.appendChild(actionDiv);
            card.appendChild(cardBody);
            list.appendChild(card);
          });
        };

        var load = function () {
          fetch('{{ route('notifications.index') }}?format=json')
            .then(function (r) { if (!r.ok) throw new Error('Notification request failed'); return r.json(); })
            .then(function (d) {
              var items = d.items || [];
              var currentCount = items.length;

              render(items);
              notifyAboutNewItems(items);

              // Sound triggering logic:
              if (lastItemCount === null) {
                lastItemCount = currentCount;
                if (currentCount > 0 && !sessionStorage.getItem('ensan_notif_sound_played')) {
                  sessionStorage.setItem('ensan_notif_sound_played', '1');
                  playNotificationSound();
                }
              } else if (currentCount > lastItemCount) {
                lastItemCount = currentCount;
                playNotificationSound();
              } else {
                lastItemCount = currentCount;
              }
            })
            .catch(function () { render([]); });
        };

        off.addEventListener('shown.bs.offcanvas', load);
        filter.addEventListener('change', function () { localStorage.setItem(fKey, filter.value || ''); load(); });
        var saved = localStorage.getItem(fKey); if (saved !== null) filter.value = saved;
        load();
        document.addEventListener('visibilitychange', function () { if (!document.hidden) load(); });
        window.setInterval(load, 60000);
      });
    </script>
  @endif
  <div class="sidebar-overlay" id="sidebarOverlay"></div>

  <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
       var loader = document.getElementById('global-loader');

       // Show loader on form submit (if not prevented)
       document.body.addEventListener('submit', function(e) {
           if (!e.defaultPrevented && !e.target.hasAttribute('data-no-loader')) {
               loader.classList.add('show');
           }
       });

       // Show loader on link click (if internal and not #/javascript)
       document.body.addEventListener('click', function(e) {
           if (e.defaultPrevented) return;
           var a = e.target.closest('a');
           if (a &&
               a.href &&
               !a.hasAttribute('data-no-loader') && // Downloads keep the current page open
               !a.hasAttribute('download') &&
               !a.href.startsWith('javascript:') &&
               !a.href.includes('#') &&
               !a.hasAttribute('data-bs-toggle') && // Ignore modal/offcanvas triggers
               !a.hasAttribute('data-bs-dismiss') && // Ignore dismiss buttons
               a.target !== '_blank' &&
               !e.ctrlKey && !e.metaKey &&
               a.hostname === window.location.hostname
           ) {
               loader.classList.add('show');
           }
       });

       // Hide loader on pageshow (back button)
       window.addEventListener('pageshow', function() {
            if (loader) {
                 loader.classList.remove('show');
            }
       });

       // Modern Swal confirmation replacement
       document.addEventListener('click', function(e) {
           const btn = e.target.closest('button[type="submit"], button:not([type]), input[type="submit"]');
           if (!btn) return;

           const form = btn.closest('form');
           if (!form) return;

           const onsubmit = form.getAttribute('onsubmit');
           if (onsubmit && onsubmit.includes('confirm')) {
               e.preventDefault();
               e.stopPropagation();

               const msgMatch = onsubmit.match(/confirm\('([^']+)'\)/);
               const message = msgMatch ? msgMatch[1] : 'هل أنت متأكد؟';

               Swal.fire({
                   title: 'تأكيد الإجراء',
                   text: message,
                   icon: 'warning',
                   iconColor: '#10b981',
                   showCancelButton: true,
                   confirmButtonText: 'نعم، متأكد',
                   cancelButtonText: 'إلغاء',
                   background: document.body.classList.contains('theme-dark') ? '#1e293b' : '#fff',
                   color: document.body.classList.contains('theme-dark') ? '#f8fafc' : '#0f172a',
                   customClass: {
                       popup: 'premium-swal-popup',
                       icon: 'premium-swal-icon',
                       title: 'premium-swal-title',
                       htmlContainer: 'premium-swal-text',
                       actions: 'premium-swal-actions',
                       confirmButton: 'premium-swal-confirm btn-success',
                       cancelButton: 'premium-swal-cancel btn-dark'
                   },
                   buttonsStyling: false,
                   backdrop: 'rgba(2, 6, 23, 0.95)',
                   showClass: { popup: 'fade-in' }
               }).then((result) => {
                   if (result.isConfirmed) {
                       form.removeAttribute('onsubmit'); // Remove original confirm
                       if(loader) loader.classList.add('show');

                       // Safely submit the form bypassing any conflicting inputs named "submit"
                       HTMLFormElement.prototype.submit.call(form);
                   }
               });
           }
       });
    });
  </script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      flatpickr(".time-picker", {
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        altInput: true,
        altFormat: "h:i K",
        time_24hr: false
      });

      // Sidebar Toggle
      const sidebarToggle = document.getElementById('sidebarToggle');
      const sidebarOverlay = document.getElementById('sidebarOverlay');
      const sidebar = document.querySelector('.sidebar-fixed');
      const sidebarKey = 'ui.sidebar.collapsed';

      if (sidebarToggle && sidebar && sidebarOverlay) {
        // Load initial state
        const isCollapsed = localStorage.getItem(sidebarKey) === 'true';
        if (isCollapsed && window.innerWidth >= 992) {
          document.body.classList.add('sidebar-collapsed');
        }

        // --- Sidebar Scroll Persistence ---
        const scrollKey = 'ui.sidebar.scrollPos';
        const sidebarCard = sidebar; // This is .sidebar-fixed

        // Restore scroll position
        const savedScroll = sessionStorage.getItem(scrollKey);
        if (savedScroll) {
          sidebarCard.scrollTop = savedScroll;
        }

        // Also ensure active element is visible if it's a first load or position wasn't saved
        const activeItem = sidebarCard.querySelector('.list-group-item.active');
        if (activeItem && !savedScroll) {
           activeItem.scrollIntoView({ block: 'nearest' });
        }

        // Save scroll position on scroll
        sidebarCard.addEventListener('scroll', function() {
          sessionStorage.setItem(scrollKey, sidebarCard.scrollTop);
        }, { passive: true });

        function closeSidebar() {
          if (window.innerWidth < 992) {
            sidebar.classList.remove('show');
            sidebarOverlay.classList.remove('show');
          }
        }

        sidebarToggle.addEventListener('click', function() {
          if (window.innerWidth >= 992) {
            // Desktop toggle: collapse
            document.body.classList.toggle('sidebar-collapsed');
            localStorage.setItem(sidebarKey, document.body.classList.contains('sidebar-collapsed'));
          } else {
            // Mobile toggle: slide in/out
            sidebar.classList.toggle('show');
            sidebarOverlay.classList.toggle('show');
          }
        });

        sidebarOverlay.addEventListener('click', closeSidebar);

        sidebar.querySelectorAll('a:not([data-bs-toggle])').forEach(link => {
          link.addEventListener('click', () => {
            // Force save on click just to be sure
            sessionStorage.setItem(scrollKey, sidebarCard.scrollTop);
            if (window.innerWidth < 992) closeSidebar();
          });
        });
      }
    });
  </script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      var themeKey = 'ui.theme', themeBtn = document.getElementById('themeToggle');

      var applyTheme = function (v) {
        var isDark = (v === 'dark');
        document.documentElement.classList.toggle('theme-dark', isDark);
        document.body.classList.toggle('theme-dark', isDark);
        document.documentElement.setAttribute('data-bs-theme', isDark ? 'dark' : 'light');
        if (isDark) {
          document.documentElement.style.backgroundColor = '#020617';
          document.body.style.backgroundColor = '#020617';
        } else {
          document.documentElement.style.backgroundColor = '';
          document.body.style.backgroundColor = '';
        }
        document.cookie = 'ui_theme=' + (isDark ? 'dark' : 'light') + '; path=/; max-age=31536000; SameSite=Lax';
        if (themeBtn) {
            themeBtn.innerHTML = isDark ? '<i class="bi bi-sun"></i>' : '<i class="bi bi-moon"></i>';
            const sidebarToggle = document.getElementById('sidebarToggle');
            if(sidebarToggle) {
                sidebarToggle.classList.toggle('text-white', isDark);
                sidebarToggle.classList.toggle('text-dark', !isDark);
            }
        }
      };

      var curTheme = localStorage.getItem(themeKey);
      if (!curTheme) {
        var m = document.cookie.match(/(?:^|; )ui_theme=([^;]*)/);
        curTheme = m ? decodeURIComponent(m[1]) : (document.documentElement.classList.contains('theme-dark') ? 'dark' : 'light');
      }
      applyTheme(curTheme);

      if (themeBtn) {
        themeBtn.addEventListener('click', function () {
          curTheme = (document.documentElement.classList.contains('theme-dark') || document.body.classList.contains('theme-dark') ? 'light' : 'dark');
          localStorage.setItem(themeKey, curTheme);
          applyTheme(curTheme);
        });
      }
    });
  </script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
        // --- BroadcastChannel Safari Polyfill ---
        // Safari < 15.4 doesn't support BroadcastChannel
        if (typeof BroadcastChannel === 'undefined') {
            window.BroadcastChannel = function(name) {
                this._name = name;
                this.onmessage = null;
                this.postMessage = function() {};
                this.close = function() {};
            };
        }

        // --- Cross-Tab Sync Logic ---
        var syncChannel;
        try { syncChannel = new BroadcastChannel('ensan_app_sync'); } catch(e) { syncChannel = { postMessage: function(){}, onmessage: null }; }

        // 1. Notify other tabs if this tab just completed a successful action
        @if(session('success'))
            syncChannel.postMessage({
                type: 'REFRESH_DATA',
                message: '{{ session('success') }}',
                url: window.location.href,
                timestamp: Date.now()
            });
        @endif

        // 2. Listen for notifications from other tabs
        syncChannel.onmessage = (event) => {
            if (event.data.type === 'REFRESH_DATA') {
                // If the user is looking at this page, prompt for refresh
                if (document.visibilityState === 'visible') {
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: true,
                        confirmButtonText: '<i class="bi bi-arrow-clockwise me-1"></i> تحديث الآن',
                        timer: 15000,
                        timerProgressBar: true,
                        background: document.body.classList.contains('theme-dark') ? '#1e293b' : '#fff',
                        color: document.body.classList.contains('theme-dark') ? '#f8fafc' : '#0f172a',
                        customClass: {
                            confirmButton: 'btn btn-primary btn-sm px-3 ms-2',
                            popup: 'rounded-4 shadow-lg border-0'
                        },
                        buttonsStyling: false,
                    });

                    Toast.fire({
                        icon: 'info',
                        title: 'تحديثات جديدة متاحة',
                        text: event.data.message
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Show loader then reload
                            const loader = document.getElementById('global-loader');
                            if(loader) loader.classList.add('show');
                            window.location.reload();
                        }
                    });
                } else {
                    // If tab is in background, we could optionally mark it for auto-refresh on focus
                    window._needsRefresh = true;
                }
            }
        };

        // 3. Auto-refresh on focus if data was updated while tab was hidden
        window.addEventListener('focus', () => {
            if (window._needsRefresh) {
                window._needsRefresh = false;
                window.location.reload();
            }
        });
    // Premium Toast Auto-Hide & Dynamic Show
    function initToasts() {
        const toasts = document.querySelectorAll('.toast-elite');
        toasts.forEach(toast => {
            if (toast.dataset.persistent === 'true') return;

            const autoHideDelay = Number(toast.dataset.autoHide || 5000);
            setTimeout(() => {
                toast.classList.add('toast-fade-out');
                setTimeout(() => toast.remove(), 500);
            }, autoHideDelay);
        });
    }

    function showToast(msg, type = 'success') {
        const container = document.getElementById('toast-container');
        if (!container) return;

        const toast = document.createElement('div');
        toast.className = `toast-elite ${type} animate-toast-in`;
        const icon = type === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill';
        const title = type === 'success' ? 'تم التنفيذ بنجاح' : 'تنبيه في النظام';

        toast.innerHTML = `
            <div class="toast-content">
                <i class="bi ${icon} toast-icon"></i>
                <div class="toast-text">
                    <span class="toast-title">${title}</span>
                    <span class="toast-msg">${msg}</span>
                </div>
            </div>
            <button type="button" class="btn-close-toast" onclick="this.parentElement.remove()">&times;</button>
        `;

        container.appendChild(toast);
        setTimeout(() => {
            toast.classList.add('toast-fade-out');
            setTimeout(() => toast.remove(), 500);
        }, 5000);
    }

    document.addEventListener('DOMContentLoaded', function() {
        initToasts();
    });

    // Global Dropdown Stacking Handler: elevates card & dropdown above all siblings
    document.addEventListener('show.bs.dropdown', function (e) {
        const toggle = e.target;
        const dropdown = toggle.closest('.dropdown');
        if (dropdown) {
            dropdown.style.zIndex = '10050';
            dropdown.style.position = 'relative';
        }
        const parentCard = toggle.closest('.kpi-card, .card, [class*="col-"], tr');
        if (parentCard) {
            parentCard.style.zIndex = '10040';
            parentCard.style.overflow = 'visible';
        }
    });
    document.addEventListener('hidden.bs.dropdown', function (e) {
        const toggle = e.target;
        const dropdown = toggle.closest('.dropdown');
        if (dropdown) {
            dropdown.style.zIndex = '';
            dropdown.style.position = '';
        }
        const parentCard = toggle.closest('.kpi-card, .card, [class*="col-"], tr');
        if (parentCard) {
            parentCard.style.zIndex = '';
            parentCard.style.overflow = '';
        }
    });

    // Global Confirmation Handler for Delete Actions
    document.addEventListener('submit', function (e) {
        const form = e.target;
        if (form && form.hasAttribute('data-confirm-delete') && !form.dataset.confirmed) {
            e.preventDefault();
            e.stopPropagation();

            const title = form.getAttribute('data-confirm-title') || 'تأكيد الحذف';
            const name = form.getAttribute('data-name') || form.getAttribute('data-warehouse-name') || '';
            const message = form.getAttribute('data-confirm-message') || (name ? `هل أنت متأكد من رغبتك في حذف <strong>« ${name} »</strong>؟` : 'هل أنت متأكد من رغبتك في إتمام عملية الحذف؟');
            const isDark = document.documentElement.classList.contains('theme-dark') || document.body.classList.contains('theme-dark');

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: title,
                    html: `
                        <div style="text-align: center; padding: 6px 0;">
                            <p style="font-size: 1.05rem; margin-bottom: 12px; color: ${isDark ? '#e2e8f0' : '#1e293b'}; line-height: 1.6;">
                                ${message}
                            </p>
                            <div style="display: inline-flex; align-items: center; gap: 8px; padding: 8px 18px; background: ${isDark ? 'rgba(239, 68, 68, 0.15)' : '#fef2f2'}; color: #ef4444; border-radius: 9999px; font-size: 0.85rem; font-weight: 600; border: 1px solid ${isDark ? 'rgba(239, 68, 68, 0.3)' : '#fecaca'};">
                                <i class="bi bi-exclamation-triangle-fill"></i>
                                <span>تنبيه: لا يمكن التراجع عن هذا الإجراء بعد تنفيذه</span>
                            </div>
                        </div>
                    `,
                    icon: 'warning',
                    iconColor: '#ef4444',
                    showCancelButton: true,
                    confirmButtonText: '<i class="bi bi-trash3-fill me-1"></i> نعم، تأكيد الحذف',
                    cancelButtonText: '<i class="bi bi-x-lg me-1"></i> إلغاء الأمر',
                    reverseButtons: true,
                    background: isDark ? '#0f172a' : '#ffffff',
                    color: isDark ? '#f8fafc' : '#0f172a',
                    buttonsStyling: false,
                    customClass: {
                        popup: 'rounded-4 shadow-xl border-0 p-4',
                        confirmButton: 'btn btn-danger px-4 py-2 rounded-pill fw-bold shadow-sm',
                        cancelButton: 'btn btn-light px-4 py-2 rounded-pill fw-bold border ms-2',
                        actions: 'mt-3 d-flex gap-2 justify-content-center'
                    },
                    focusCancel: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.dataset.confirmed = 'true';
                        Swal.fire({
                            title: 'جاري الحذف...',
                            html: '<span class="text-muted small">يرجى الانتظار قليلاً...</span>',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            showConfirmButton: false,
                            background: isDark ? '#0f172a' : '#ffffff',
                            color: isDark ? '#f8fafc' : '#0f172a',
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });
                        form.submit();
                    }
                });
            } else {
                if (confirm('هل أنت متأكد من الحذف؟')) {
                    form.dataset.confirmed = 'true';
                    form.submit();
                }
            }
        }
    }, true);

    @if(session('success'))
        document.addEventListener('DOMContentLoaded', function () {
            setTimeout(function () {
                if (typeof window.playNotificationSound === 'function') {
                    window.playNotificationSound();
                }
            }, 300);
        });
    @endif
  </script>

  {{-- Universal Print Preview Component & Script --}}
  @include('components.universal_print_preview')
  <script src="{{ asset('js/print-preview.js') }}?v={{ file_exists(public_path('js/print-preview.js')) ? filemtime(public_path('js/print-preview.js')) : time() }}"></script>

  @yield('scripts')
</body>

</html>


