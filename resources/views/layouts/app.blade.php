<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#1a5f7a">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <title>@yield('title', __('messages.app_title'))</title>
    <link rel="icon" type="image/png" href="{{ asset('icons/icon-192.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('icons/icon-192.png') }}">
    <link rel="manifest" href="{{ asset('manifest.webmanifest') }}">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    @stack('styles')
    
    <style>
        :root {
            --primary-color: #1a5f7a;
            --secondary-color: #d4af37;
            --accent-color: #2d7a96;
            --light-bg: #f8f9fa;
            --dark-text: #2c3e50;
        }

        * {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        html {
            scrollbar-gutter: stable;
        }

        body {
            background-color: var(--light-bg);
            color: var(--dark-text);
            line-height: 1.6;
            overflow-y: scroll;
        }

        /* Header/Navbar */
        .navbar {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--accent-color) 100%);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .navbar-brand {
            font-size: 1.5rem;
            font-weight: bold;
            color: white !important;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .navbar-brand i {
            font-size: 2rem;
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.9) !important;
            margin-left: 15px;
            transition: color 0.3s;
        }

        .nav-link:hover {
            color: var(--secondary-color) !important;
        }

        .navbar-collapse a.active {
            color: var(--secondary-color) !important;
            border-bottom: 2px solid var(--secondary-color);
        }

        /* Footer */
        footer {
            background-color: var(--primary-color);
            color: white;
            margin-top: 50px;
            padding: 40px 0;
        }

        /* Cards */
        .card {
            border: none;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
        }

        /* Disable hover transforms while a modal is open */
        body.modal-open .card:hover {
            transform: none;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        /* Prevent modal/backdrop flicker */
        .modal,
        .modal-dialog,
        .modal-backdrop {
            transition: none !important;
            animation: none !important;
        }

        /* Prevent body padding jump when modal opens */
        body.modal-open {
            padding-right: 0 !important;
            overflow-y: scroll !important;
        }

        body.modal-open .navbar,
        body.modal-open .fixed-top,
        body.modal-open .fixed-bottom,
        body.modal-open .sticky-top {
            padding-right: 0 !important;
        }

        .card-header {
            background-color: var(--primary-color);
            color: white;
            font-weight: 600;
            border: none;
        }

        /* Buttons */
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            transition: all 0.3s;
        }

        .btn-primary:hover {
            background-color: var(--accent-color);
            border-color: var(--accent-color);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(26, 95, 122, 0.3);
        }

        .btn-secondary {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
            color: var(--primary-color);
            font-weight: 600;
        }

        .btn-secondary:hover {
            background-color: #c19b1a;
            border-color: #c19b1a;
            transform: translateY(-2px);
        }

        /* Forms */
        .form-control, .form-select {
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 10px 15px;
            transition: border-color 0.3s, box-shadow 0.3s;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(26, 95, 122, 0.25);
        }

        .form-label {
            font-weight: 600;
            color: var(--dark-text);
        }

        /* Alerts */
        .alert {
            border: none;
            border-radius: 6px;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
        }

        .alert-info {
            background-color: #d1ecf1;
            color: #0c5460;
        }

        /* Badges */
        .badge {
            padding: 6px 12px;
            font-weight: 600;
        }

        .badge-status-pending {
            background-color: #ffc107;
            color: #000;
        }

        .badge-status-upcoming {
            background-color: #6c757d;
            color: #fff;
        }

        .badge-status-ongoing {
            background-color: #fd7e14;
            color: #fff;
        }

        .badge-status-confirmed {
            background-color: #28a745;
            color: #fff;
        }

        .badge-status-completed {
            background-color: #17a2b8;
            color: #fff;
        }

        .badge-status-cancelled {
            background-color: #dc3545;
            color: #fff;
        }

        /* Tables */
        .table {
            background-color: white;
            border-radius: 6px;
            overflow: hidden;
        }

        .table thead {
            background-color: var(--primary-color);
            color: white;
        }

        .table tbody tr:hover {
            background-color: #f0f0f0;
        }

        /* Hero Section */
        .hero {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--accent-color) 100%);
            color: white;
            padding: 80px 0;
            text-align: center;
            margin-bottom: 40px;
        }

        .hero h1 {
            font-size: 3rem;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .hero p {
            font-size: 1.2rem;
            opacity: 0.9;
        }

        /* Stats */
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            border-left: 4px solid var(--primary-color);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .stat-card h3 {
            color: var(--primary-color);
            font-weight: bold;
            margin-bottom: 10px;
        }

        .stat-card p {
            color: #666;
            margin: 0;
        }

        /* Navigation Tabs */
        .nav-tabs {
            border-bottom: 2px solid #ddd;
        }

        .nav-tabs .nav-link {
            color: var(--dark-text);
            border: none;
            border-bottom: 3px solid transparent;
            transition: all 0.3s;
        }

        .nav-tabs .nav-link:hover {
            border-bottom-color: var(--secondary-color);
            color: var(--primary-color);
        }

        .nav-tabs .nav-link.active {
            color: white;
            background-color: var(--primary-color);
            border: none;
            border-bottom: 3px solid var(--secondary-color);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2rem;
            }

            .navbar-brand {
                font-size: 1.2rem;
            }
        }
    </style>

    @stack('css')
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('home') }}">
                <i class="fas fa-graduation-cap"></i>
                <span>{{ __('messages.app_brand') }}</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('exams.index') }}">
                            <i class="fas fa-book"></i> {{ __('messages.exams') }}
                        </a>
                    </li>
                    
                    @auth
                        @if(auth()->user()->isAdmin())
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="adminDropdown" role="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-cog"></i> {{ __('messages.admin_panel') }}
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="adminDropdown">
                                    <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}"><i class="fas fa-tachometer-alt me-2"></i>{{ __('messages.dashboard_tab') }}</a></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.students') }}"><i class="fas fa-graduation-cap me-2"></i>{{ __('messages.students_tab') }}</a></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.users') }}"><i class="fas fa-users me-2"></i>{{ __('messages.user_management') }}</a></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.payments.history') }}"><i class="fas fa-receipt me-2"></i>{{ __('messages.payment_history') }}</a></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.activity.logs') }}"><i class="fas fa-clipboard-list me-2"></i>{{ __('messages.activity_logs') }}</a></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.exams.completed-results') }}"><i class="fas fa-clipboard-check me-2"></i>{{ __('messages.completed_exam_results') }}</a></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.exams') }}"><i class="fas fa-book me-2"></i>{{ __('messages.exams_management') }}</a></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.examiners.tasks') }}"><i class="fas fa-user-check me-2"></i>{{ __('messages.examiner_tasks') }}</a></li>
                                    <li><a class="dropdown-item" href="{{ route('profile.show') }}"><i class="fas fa-user me-2"></i>{{ __('messages.profile') }}</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="fas fa-sign-out-alt"></i> {{ __('messages.logout') }}
                                    </a></li>
                                </ul>
                            </li>
                        @elseif(auth()->user()->isExaminer())
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="examinerDropdown" role="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-tachometer-alt"></i> {{ __('messages.dashboard_tab') }}
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="examinerDropdown">
                                    <li><a class="dropdown-item" href="{{ route('examiner.dashboard') }}"><i class="fas fa-chart-line me-2"></i>{{ __('messages.dashboard_tab') }}</a></li>
                                    <li><a class="dropdown-item" href="{{ route('profile.show') }}"><i class="fas fa-user me-2"></i>{{ __('messages.profile') }}</a></li>
                                    
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="fas fa-sign-out-alt me-2"></i>{{ __('messages.logout') }}
                                    </a></li>
                                </ul>
                            </li>
                        @else
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="studentDropdown" role="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-tachometer-alt"></i> {{ __('messages.dashboard_tab') }}
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="studentDropdown">
                                    <li><a class="dropdown-item" href="{{ route('dashboard') }}"><i class="fas fa-chart-line me-2"></i>{{ __('messages.dashboard_tab') }}</a></li>
                                    @if(auth()->user()->isStudent())
                                        <li><a class="dropdown-item" href="{{ route('dashboard') }}#my-results"><i class="fas fa-trophy me-2"></i>{{ __('messages.my_results') }}</a></li>
                                    @endif
                                    <li><a class="dropdown-item" href="{{ route('profile.show') }}"><i class="fas fa-user me-2"></i>{{ __('messages.profile') }}</a></li>
                                    
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="fas fa-sign-out-alt me-2"></i>{{ __('messages.logout') }}
                                    </a></li>
                                </ul>
                            </li>
                        @endif
                        <li class="nav-item">
                            <span class="nav-link">
                                <i class="fas fa-user"></i> {{ auth()->user()->first_name ?? auth()->user()->name }}
                            </span>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">
                                <i class="fas fa-sign-in-alt"></i> {{ __('messages.login') }}
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">
                                <i class="fas fa-user-plus"></i> {{ __('messages.register') }}
                            </a>
                        </li>
                    @endauth
                    
                    <!-- Language Selector -->
                    <li class="nav-item dropdown ms-2">
                        <a class="nav-link dropdown-toggle" href="#" id="languageDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-globe"></i> {{ __('messages.language') }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="languageDropdown">
                            <li><a class="dropdown-item {{ app()->getLocale() === 'fr' ? 'active' : '' }}" href="?lang=fr"><i class="flag-icon-fr"></i> {{ __('messages.language_french') }}</a></li>
                            <li><a class="dropdown-item {{ app()->getLocale() === 'de' ? 'active' : '' }}" href="?lang=de"><i class="flag-icon-de"></i> {{ __('messages.language_german') }}</a></li>
                            <li><a class="dropdown-item {{ app()->getLocale() === 'en' ? 'active' : '' }}" href="?lang=en"><i class="flag-icon-en"></i> {{ __('messages.language_english') }}</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Alerts -->
    <div class="container mt-3">
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle"></i>
                <strong>{{ __('messages.errors') }}</strong>
                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    </div>

    <!-- Main content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h5><i class="fas fa-graduation-cap"></i> {{ __('messages.footer_title') }}</h5>
                    <p>{{ __('messages.footer_description') }}</p>
                </div>
                <div class="col-md-4 mb-4">
                    <h5>{{ __('messages.quick_links') }}</h5>
                    <ul class="list-unstyled">
                        <li><a href="{{ route('exams.index') }}" class="text-white-50" style="text-decoration: none;">{{ __('messages.exams') }}</a></li>
                        <li><a href="{{ route('login') }}" class="text-white-50" style="text-decoration: none;">{{ __('messages.login') }}</a></li>
                        <li><a href="{{ route('register') }}" class="text-white-50" style="text-decoration: none;">{{ __('messages.register') }}</a></li>
                    </ul>
                </div>
                <div class="col-md-4 mb-4">
                    <h5>{{ __('messages.contacts') }}</h5>
                    <p class="text-white-50 mb-0">
                        <i class="fas fa-envelope"></i> info@examenosd.cial-de.com<br>
                        <i class="fas fa-phone"></i> +228 90 33 32 32
                    </p>
                </div>
            </div>
            <hr class="bg-white-50">
            <div class="text-center text-white-50">
                <p class="mb-0">&copy; 2026 {{ __('messages.footer_title') }}. {{ __('messages.all_rights_reserved') }}</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Logout form -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>

    <!-- Global confirm modal -->
    <div class="modal fade" id="confirmActionModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ __('messages.confirm_action') }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <p id="confirmActionMessage">{{ __('messages.are_you_sure') }}</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('messages.cancel_btn') }}</button>
            <button type="button" class="btn btn-primary" id="confirmActionSubmit">{{ __('messages.confirm') }}</button>
          </div>
        </div>
      </div>
    </div>

    <script>
    (function(){
      var modalEl = document.getElementById('confirmActionModal');
      if(!modalEl) return;
      var bsModal = new bootstrap.Modal(modalEl);
      var messageEl = document.getElementById('confirmActionMessage');
      var submitBtn = document.getElementById('confirmActionSubmit');
      var currentForm = null;

      document.addEventListener('click', function(e){
        var btn = e.target.closest('.confirm-action');
        if(!btn) return;
        e.preventDefault();
        currentForm = btn.closest('form');
        var msg = btn.getAttribute('data-confirm-message') || '{{ __('messages.are_you_sure') }}';
        messageEl.textContent = msg;
        bsModal.show();
      });

      submitBtn.addEventListener('click', function(){
        if(currentForm){
          currentForm.submit();
        }
        bsModal.hide();
      });
    })();
    </script>

        <script>
        document.addEventListener('click', function(e){
            var btn = e.target.closest('[data-toggle-password]');
            if(!btn) return;
            var targetId = btn.getAttribute('data-target');
            if(!targetId) return;
            var input = document.getElementById(targetId);
            if(!input) return;

            var isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';

            var icon = btn.querySelector('i');
            if(icon){
                icon.classList.toggle('fa-eye', !isPassword);
                icon.classList.toggle('fa-eye-slash', isPassword);
            }
        });
        </script>

    @stack('js')
</body>
</html>
