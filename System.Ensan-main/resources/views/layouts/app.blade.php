<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>مؤسسة إنسان</title>
  <link rel="icon" href="{{ asset('logo.png') }}" type="image/png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  <link href="{{ asset('css/style.css') }}" rel="stylesheet">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
  <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
  <style>
      :root {
          /* Brand Identity - SaaS Clean Style */
          --primary: #22C55E;
          --primary-dark: #16A34A;
          --primary-light: #f0fdf4;
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
          --bs-primary: #22C55E;
          --bs-primary-rgb: 34, 197, 94;
          
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

      .sidebar-fixed .list-group-item {
          border: none !important;
          border-radius: var(--radius-md) !important;
          margin-bottom: 0.25rem;
          font-weight: 500;
          font-size: 0.95rem;
          background: transparent !important;
          transition: transform 0.3s ease, background 0.3s ease;
      }

      .sidebar-fixed .list-group-item i {
          transition: transform 0.3s ease;
      }

      .sidebar-fixed .list-group-item:hover {
          color: var(--primary) !important;
          background: var(--primary-subtle) !important;
          transform: translateX(-5px);
      }

      .sidebar-fixed .list-group-item:hover i {
          color: var(--primary) !important;
      }

      .sidebar-fixed .list-group-item.active {
          background: var(--primary) !important;
          color: white !important;
          box-shadow: 0 0 15px rgba(34, 197, 94, 0.4) !important;
          transform: translateX(-5px);
      }

      .sidebar-fixed .list-group-item.active i {
          color: white !important;
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
          .main-content { margin-right: 0; }
          .sidebar.show { transform: translateX(0); }
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
      }
      html[dir="rtl"] .select2-container--bootstrap-5 .select2-selection--single .select2-selection__clear {
          right: auto;
          left: 0.75rem;
      }
      html[dir="rtl"] .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
          padding-right: 0;
          padding-left: 0;
          text-align: right;
      }

      /* Global Layout Improvements */
      body:not(.theme-dark) {
          background-color: var(--bg-soft) !important;
          color: var(--text-main) !important;
          font-family: 'Tajawal', sans-serif;
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

      .btn-glass-pill {
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
      }
      .btn-glass-pill:hover {
          background: var(--bg) !important;
          color: var(--primary) !important;
          transform: translateY(-2px);
          box-shadow: 0 10px 20px rgba(0,0,0,0.05);
          border-color: var(--primary) !important;
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

      /* Toggle Icon Rotation */
      .elite-sidebar-item[aria-expanded="true"] .sidebar-toggle-icon {
          transform: rotate(180deg);
      }
      .sidebar-toggle-icon { transition: transform 0.3s ease; font-size: 0.8rem; }
</style>
</style>
    <style>
        /* Global protection against blurry screens by disabling backdrop filters */
        * {
            backdrop-filter: none !important;
            -webkit-backdrop-filter: none !important;
        }

        /* ======================================================
         * Modal: Global Solid Background (Light Mode)
         * ====================================================== */
        .modal-content {
            background-color: #ffffff !important;
        }
        .modal-footer {
            background-color: #ffffff;
        }

        /* ======================================================
         * Modal: Dark Mode - All Modal Sections
         * Targets both theme-dark class (custom) and data-bs-theme (Bootstrap 5)
         * ====================================================== */
        body.theme-dark .modal-content,
        [data-bs-theme="dark"] .modal-content,
        [data-theme="dark"] .modal-content {
            background-color: #1a1d21 !important;
            color: #e2e8f0 !important;
        }

        body.theme-dark .modal-body,
        [data-bs-theme="dark"] .modal-body,
        [data-theme="dark"] .modal-body {
            background-color: #1a1d21 !important;
            color: #e2e8f0 !important;
        }

        body.theme-dark .modal-footer,
        [data-bs-theme="dark"] .modal-footer,
        [data-theme="dark"] .modal-footer {
            background-color: #1a1d21 !important;
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

      /* Premium Global Toasts */
      .toast-container-elite {
          position: fixed;
          top: 30px;
          left: 50%;
          transform: translateX(-50%);
          z-index: 10000;
          display: flex;
          flex-direction: column;
          gap: 12px;
          pointer-events: none;
      }
      .toast-elite {
          min-width: 350px;
          max-width: 500px;
          padding: 16px 20px;
          border-radius: 16px;
          background: rgba(255, 255, 255, 0.9);
          backdrop-filter: blur(15px);
          -webkit-backdrop-filter: blur(15px);
          box-shadow: 0 15px 40px rgba(0,0,0,0.15);
          display: flex;
          align-items: center;
          justify-content: space-between;
          border: 1px solid rgba(255,255,255,0.2);
          pointer-events: auto;
          transition: 0.3s;
          direction: rtl;
      }
      body.theme-dark .toast-elite {
          background: rgba(30, 41, 59, 0.9);
          border-color: rgba(255,255,255,0.05);
          box-shadow: 0 20px 50px rgba(0,0,0,0.4);
      }
      .toast-elite.success { border-right: 6px solid #10b981; }
      .toast-elite.error { border-right: 6px solid #ef4444; }
      
      .toast-content { display: flex; align-items: center; gap: 15px; }
      .toast-icon { font-size: 1.5rem; }
      .success .toast-icon { color: #10b981; }
      .error .toast-icon { color: #ef4444; }
      
      .toast-text { display: flex; flex-direction: column; text-align: right; }
      .toast-title { font-weight: 800; font-size: 0.95rem; color: var(--ws-text-primary); }
      .toast-msg { font-size: 0.85rem; color: var(--ws-text-secondary); margin-top: 2px; }
      
      .btn-close-toast {
          background: none;
          border: none;
          font-size: 1.5rem;
          color: var(--ws-text-secondary);
          opacity: 0.5;
          transition: 0.2s;
          padding: 0 10px 0 0;
      }
      .btn-close-toast:hover { opacity: 1; transform: scale(1.1); }

      @keyframes toastInSimple {
          from { opacity: 0; transform: translateY(-40px) scale(0.9); }
          to { opacity: 1; transform: translateY(0) scale(1); }
      }
      .animate-toast-in { animation: toastInSimple 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275) both; }
      .toast-fade-out { opacity: 0; transform: translateY(-20px); pointer-events: none; }
</style>
  @yield('styles')
</head>

<body>
  <div id="global-loader">
      <div class="spinner-premium"></div>
  </div>

  {{-- Premium Global Notifications (Toasts) --}}
  <div class="toast-container-elite" id="toast-container">
      @if(session('success'))
          <div class="toast-elite success animate-toast-in">
              <div class="toast-content">
                  <i class="bi bi-check-circle-fill toast-icon"></i>
                  <div class="toast-text">
                      <span class="toast-title">تم بنجاح</span>
                      <span class="toast-msg">{{ session('success') }}</span>
                  </div>
              </div>
              <button type="button" class="btn-close-toast" onclick="this.parentElement.remove()">&times;</button>
          </div>
      @endif

      @if(session('error') || $errors->any())
          <div class="toast-elite error animate-toast-in">
              <div class="toast-content">
                  <i class="bi bi-exclamation-triangle-fill toast-icon"></i>
                  <div class="toast-text">
                      <span class="toast-title">خطأ في النظام</span>
                      <span class="toast-msg">{{ session('error') ?? $errors->first() }}</span>
                  </div>
              </div>
              <button type="button" class="btn-close-toast" onclick="this.parentElement.remove()">&times;</button>
          </div>
      @endif
  </div>



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
            @if(file_exists(public_path('logo.png')))
              <img src="{{ asset('logo.png') }}" alt="إنسان" loading="lazy">
            @else
              <span class="fw-800 text-primary">إنسان</span>
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

            @if($navUser && $navUser->hasPermission('notifications.view') && !$navUser->roles->contains('key', 'finance'))
              <button class="btn btn-glass-pill position-relative" 
                type="button" data-bs-toggle="offcanvas" data-bs-target="#notifOffcanvas">
                <i class="bi bi-bell fs-5"></i>
              </button>
            @endif

            <button id="themeToggle" class="btn btn-glass-pill" type="button" aria-label="تبديل الثيم">
              <i class="bi bi-moon fs-5"></i>
            </button>

            @if($navUser)
              <div class="dropdown ms-2">
                <button class="btn btn-link p-0 d-flex align-items-center gap-2 text-decoration-none shadow-none" data-bs-toggle="dropdown">
                  <div class="elite-avatar-wrapper shadow-premium border-2 border-primary-subtle rounded-circle" style="width: 42px; height: 42px; padding: 2px; background: var(--ws-bg-card);">
                    @if($navUser->profile_photo_path)
                      <img src="{{ $navUser->image_url }}" alt="{{ $navUser->name }}" class="rounded-circle w-100 h-100 object-fit-cover shadow-sm">
                    @else
                      <div class="bg-gradient-primary text-white rounded-circle w-100 h-100 d-flex align-items-center justify-content-center fw-bold fs-6 shadow-sm">
                        {{ strtoupper(substr($navUser->name, 0, 1)) }}
                      </div>
                    @endif
                  </div>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-3 p-2 rounded-4">
                  <li class="px-3 py-3 text-center bg-light rounded-4 mb-2">
                    <div class="fw-bold text-dark">{{ $navUser->name }}</div>
                    <div class="small text-muted">{{ $navUser->email }}</div>
                  </li>
                  <li><a class="dropdown-item rounded-3" href="{{ route('users.show', $navUser->id) }}"><i class="bi bi-person me-2"></i> الملف الشخصي</a></li>
                  <li><hr class="dropdown-divider"></li>
                  <li>
                    <form method="POST" action="{{ route('logout') }}">@csrf<button class="dropdown-item text-danger rounded-3"><i class="bi bi-box-arrow-right me-2"></i> خروج</button></form>
                  </li>
                </ul>
              </div>
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

      @if($user && ($user->hasPermission('manage_logistics') || $user->hasPermission('delegates.view') || $user->hasPermission('travel_routes.view') || $user->hasPermission('trips.view') || $user->hasPermission('kafr_el_sheikh_deliveries.view') || $user->hasPermission('kafr_el_sheikh_services.view')))
        <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
          data-bs-toggle="collapse" href="#delegatesCollapse" role="button"
          aria-expanded="{{ (request()->routeIs('logistics.*') || request()->routeIs('delegates.*') || request()->routeIs('travel-routes.*') || request()->routeIs('trips.*') || request()->routeIs('kafr-el-sheikh-deliveries.*') || request()->routeIs('kafr-el-sheikh-services.*')) ? 'true' : 'false' }}"
          aria-controls="delegatesCollapse">
          <span><i class="bi bi-signpost-2"></i> اللوجيستك</span>
          <i class="bi bi-chevron-down sidebar-toggle-icon"></i>
        </a>
        <div class="collapse {{ (request()->routeIs('logistics.*') || request()->routeIs('delegates.*') || request()->routeIs('travel-routes.*') || request()->routeIs('trips.*') || request()->routeIs('kafr-el-sheikh-deliveries.*') || request()->routeIs('kafr-el-sheikh-services.*')) ? 'show' : '' }} sub-list" id="delegatesCollapse">
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
            @if($user->hasPermission('kafr_el_sheikh_services.view'))
              <a href="{{ route('kafr-el-sheikh-services.index') }}" 
                 class="list-group-item list-group-item-action {{ request()->routeIs('kafr-el-sheikh-services.*') ? 'active' : '' }}">
                 <i class="bi bi-tools"></i><span>خدمات كفر الشيخ</span>
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
                <i class="bi bi-chevron-down sidebar-toggle-icon" style="font-size: 0.8em;"></i>
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
                <i class="bi bi-chevron-down sidebar-toggle-icon" style="font-size: 0.8em;"></i>
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

            @if(($user->hasRole('hr') || $user->hasPermission('tasks.view') || $user->hasPermission('tasks.view_own')) && !$user->hasRole('finance'))
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

      @if($user && ($user->hasPermission('manage_specialized_services') || $user->hasPermission('school_collaborations.view') || $user->hasPermission('memberships.view') || $user->hasPermission('oncology_medicine_reps.view') || $user->hasPermission('kafr_el_sheikh_brokers.view') || $user->hasPermission('tanta_workers.view')))
        <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
          data-bs-toggle="collapse" href="#collaborationsCollapse" role="button"
          aria-expanded="{{ request()->routeIs('school-collaborations.*') || request()->routeIs('memberships.*') || request()->routeIs('oncology-medicine-reps.*') || request()->routeIs('kafr-el-sheikh-brokers.*') || request()->routeIs('tanta-workers.*') ? 'true' : 'false' }}"
          aria-controls="collaborationsCollapse">
          <span><i class="bi bi-diagram-3"></i> التعاونات والشراكات والعضوية</span>
          <i class="bi bi-chevron-down sidebar-toggle-icon"></i>
        </a>
        <div class="collapse {{ request()->routeIs('school-collaborations.*') || request()->routeIs('memberships.*') || request()->routeIs('oncology-medicine-reps.*') || request()->routeIs('kafr-el-sheikh-brokers.*') || request()->routeIs('tanta-workers.*') ? 'show' : '' }} sub-list" id="collaborationsCollapse">
          <div class="list-group list-group-flush ps-3">
            @if($user->hasPermission('school_collaborations.view'))
              <a href="{{ route('school-collaborations.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('school-collaborations.*') ? 'active' : '' }}"><i class="bi bi-building"></i><span>تعاونات المدارس</span></a>
            @endif
            @if($user->hasPermission('memberships.view'))
              <a href="{{ route('memberships.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('memberships.*') ? 'active' : '' }}"><i class="bi bi-person-badge"></i><span>العضويات</span></a>
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
                <i class="bi bi-chevron-down sidebar-toggle-icon" style="font-size: 0.8em;"></i>
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
  <div class="container content-wrapper fade-in">
    @if(!isset($hideGlobalAlerts) || !$hideGlobalAlerts)
      @include('partials.alerts')
    @endif
    @yield('content')

    <footer class="mt-5 pt-4 pb-4 text-center text-muted small">
      <div class="container">
        <div class="d-flex justify-content-center align-items-center mb-2">
          @if(file_exists(public_path('logo.png')))
            <img src="{{ asset('logo.png') }}" alt="logo" height="120" style="opacity: 1">
          @endif
        </div>
        <p class="mb-0 fw-medium text-secondary">جميع الحقوق محفوظة مؤسسة إنسان {{ date('Y') }} &copy;</p>
      </div>
    </footer>
  </div>
  @if($navUser && $navUser->hasPermission('notifications.view'))
    <div class="offcanvas offcanvas-end" tabindex="-1" id="notifOffcanvas" aria-labelledby="notifOffcanvasLabel">
      <div class="offcanvas-header">
        <h5 id="notifOffcanvasLabel" class="mb-0">الإشعارات</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
      </div>
      <div class="offcanvas-body">
        <div class="d-flex gap-2 mb-2">
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
        var fKey = 'sidebar.notifications.filter';
        var render = function (items) {
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
            
            // Header: Icon and Label
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
            
            // Text Content
            var textPara = document.createElement('p');
            textPara.className = 'mb-3 lh-base fw-medium';
            textPara.style.fontSize = '0.95rem';
            textPara.textContent = n.text;
            
            // Action
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
          fetch('{{ route('notifications.index') }}?format=json').then(function (r) { return r.json() }).then(function (d) { render(d.items || []); }).catch(function () { render([]); });
        };
        off.addEventListener('shown.bs.offcanvas', load);
        filter.addEventListener('change', function () { localStorage.setItem(fKey, filter.value || ''); load(); });
        var saved = localStorage.getItem(fKey); if (saved !== null) filter.value = saved;
      });
    </script>
  @endif
  <div class="sidebar-overlay" id="sidebarOverlay"></div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
       var loader = document.getElementById('global-loader');
       
       // Show loader on form submit (if not prevented)
       document.body.addEventListener('submit', function(e) {
           if (!e.defaultPrevented) {
               loader.classList.add('show');
           }
       });

       // Show loader on link click (if internal and not #/javascript)
       document.body.addEventListener('click', function(e) {
           if (e.defaultPrevented) return;
           var a = e.target.closest('a');
           if (a && 
               a.href && 
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
       window.addEventListener('pageshow', function(event) {
            if (event.persisted && loader) {
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
                       popup: 'premium-swal-popup rounded-4 border-0 shadow-lg',
                       title: 'fw-bold mb-3',
                       confirmButton: 'btn btn-primary px-4 py-2 mx-2',
                       cancelButton: 'btn btn-secondary px-4 py-2 mx-2'
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
        document.body.classList.toggle('theme-dark', v === 'dark');
        if (themeBtn) { 
            themeBtn.innerHTML = v === 'dark' ? '<i class="bi bi-sun"></i>' : '<i class="bi bi-moon"></i>'; 
            // Also update the sidebar toggle color if needed
            const sidebarToggle = document.getElementById('sidebarToggle');
            if(sidebarToggle) {
                sidebarToggle.classList.toggle('text-white', v === 'dark');
                sidebarToggle.classList.toggle('text-dark', v !== 'dark');
            }
        }
      };

      var curTheme = localStorage.getItem(themeKey) || 'light';
      applyTheme(curTheme);

      if (themeBtn) {
        themeBtn.addEventListener('click', function () {
          curTheme = (document.body.classList.contains('theme-dark') ? 'light' : 'dark');
          localStorage.setItem(themeKey, curTheme);
          applyTheme(curTheme);
        });
      }
    });
  </script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
        // --- Cross-Tab Sync Logic ---
        const syncChannel = new BroadcastChannel('ensan_app_sync');
        
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
            setTimeout(() => {
                toast.classList.add('toast-fade-out');
                setTimeout(() => toast.remove(), 500);
            }, 5000);
        });
    }

    function showToast(msg, type = 'success') {
        const container = document.getElementById('toast-container');
        if (!container) return;

        const toast = document.createElement('div');
        toast.className = `toast-elite ${type} animate-toast-in`;
        const icon = type === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill';
        const title = type === 'success' ? 'تم بنجاح' : 'تنبيه في النظام';
        
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

    document.addEventListener('DOMContentLoaded', initToasts);
  </script>
  @yield('scripts')
</body>

</html>


