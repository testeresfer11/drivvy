 <!-- partial:partials/_sidebar.html -->

 <nav class="sidebar sidebar-offcanvas" id="sidebar">
    <div class="sidebar-brand-wrapper d-none d-lg-flex align-items-center justify-content-center fixed-top">
      <a class="sidebar-brand brand-logo" href="{{route('company.dashboard')}}">
        {{-- <img src="{{asset('admin/images/logo.svg')}}" alt="logo" /> --}}
        <h1>CARPOOL</h1>
      </a>
      <a class="sidebar-brand brand-logo-mini" href="{{route('company.dashboard')}}"><img src="{{asset('admin/images/logo-mini.svg')}}" alt="logo" /></a>
    </div>
    <ul class="nav  admin-sidebar">
      <li class="nav-item profile">
        <div class="profile-desc">
          <div class="profile-pic">
            <div class="count-indicator">
            <img class="img-xs rounded-circle"
                        @if (isset($user->userDetail) && !is_null($user->userDetail->profile)) 
                            src="{{ asset('storage/images/' . $user->userDetail->profile) }}"
                        @else
                            src="{{ asset('admin/images/faces/face15.jpg') }}" 
                        @endif
                        onerror="this.src = '{{ asset('admin/images/faces/face15.jpg') }}'"
                        alt="User profile picture">        
          
              <span class="count bg-success"></span>
            </div>
            <div class="profile-name">
              <h5 class="mb-0 font-weight-normal">{{UserNameById(authId())}}</h5>
            </div>
          </div>
        </div>
      </li>

      <!-- Dashboard Link -->
      <li class="nav-item menu-items {{ request()->routeIs('company.dashboard') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('company.dashboard') }}">
            <span class="menu-icon">
              <img src="{{asset('admin/images/dash.png')}}">
            </span>
            <span class="menu-title">Dashboard</span>
        </a>
      </li>
    <!--Shipment Link -->
    <li class="nav-item menu-items {{ request()->routeIs('company.shipment.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('company.shipment.list') }}">
            <span class="menu-icon">
              <img src="{{asset('admin/images/ship.png')}}">
            </span>
            <span class="menu-title">Shipment</span>
        </a>
      </li>
      <li class="nav-item menu-items {{ request()->routeIs('company.driver.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('company.driver.list') }}">
            <span class="menu-icon">
              <img src="{{asset('admin/images/drive.png')}}">
            </span>
            <span class="menu-title">Drivers</span>
        </a>
      </li>

      <!-- Profile Link -->
      <li class="nav-item menu-items {{ request()->routeIs('company.profile', 'admin.changePassword') ? 'active' : '' }}">
        <a class="nav-link {{ request()->routeIs('company.profile', 'company.changePassword') ? '' : 'collapsed' }}" data-toggle="collapse" href="#ui-basic" aria-expanded="{{ request()->routeIs('company.profile', 'company.changePassword') ? 'true' : 'false' }}" aria-controls="ui-basic">
            <span class="menu-icon">
                <i class="mdi mdi-laptop"></i>
            </span>
            <span class="menu-title">Profile</span>
            <i class="menu-arrow"></i>
        </a>
        <div class="collapse {{ request()->routeIs('company.profile', 'company.changePassword') ? 'show' : '' }}" id="ui-basic" data-id="{{ request()->routeIs('company.profile', 'company.changePassword') ? 'true' : 'false' }}">
            <ul class="nav flex-column sub-menu">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('company.profile') ? 'active' : '' }}" href="{{ route('company.profile') }}">Settings</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('company.changePassword') ? 'active' : '' }}" href="{{ route('company.changePassword') }}">Change Password</a>
                </li>
            </ul>
        </div>
      </li>

      <!-- User Management Link -->
      <li class="nav-item menu-items {{ request()->routeIs('company.user.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('company.user.list') }}">
            <span class="menu-icon">
                <i class="mdi mdi-contacts"></i>
            </span>
            <span class="menu-title">Customer Management</span>
        </a>
      </li>
  
     
     <!--  <li class="nav-item menu-items {{ request()->routeIs('company.company.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('company.company.list') }}">
            <span class="menu-icon">
                <i class="mdi mdi-contacts"></i>
            </span>
            <span class="menu-title">Company Management</span>
        </a>
      </li> -->

      <!-- Transactions Management Link -->
      <li class="nav-item menu-items {{ request()->routeIs('company.transaction.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{route('company.transaction.list')}}">
            <span class="menu-icon">
                <i class="mdi mdi-bank"></i>
            </span>
            <span class="menu-title">Transactions</span>
        </a>
      </li>

      <!-- Helpdesk Link -->
      <!-- <li class="nav-item menu-items {{ request()->routeIs('company.helpDesk.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('company.helpDesk.list',['type' => 'open']) }}">
            <span class="menu-icon">
                <i class="mdi mdi-desktop-mac"></i>
            </span>
            <span class="menu-title">Helpdesk</span>
        </a>
      </li> -->

      <!-- Config setting Link -->
      <!-- <li class="nav-item menu-items {{ request()->routeIs('company.config-setting.*') ? 'active' : '' }}">
        <a class="nav-link {{ request()->routeIs('company.config-setting.*') ? '' : 'collapsed' }}" data-toggle="collapse" href="#auth1" aria-expanded="{{ request()->routeIs('company.config-setting.*') ? 'true' : 'false' }}" aria-controls="auth1">
            <span class="menu-icon">
                <i class="mdi mdi-settings"></i>
            </span>
            <span class="menu-title">Setting</span>
            <i class="menu-arrow"></i>
        </a>
        <div class="collapse {{ request()->routeIs('company.config-setting.*') ? 'show' : '' }}" id="auth1">
            <ul class="nav flex-column sub-menu">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('company.config-setting.smtp') ? 'active' : '' }}" href="{{ route('company.config-setting.smtp') }}">SMTP Information</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('company.config-setting.stripe') ? 'active' : '' }}" href="{{ route('company.config-setting.stripe') }}">Stripe Payment</a>
                </li>
            </ul>
        </div>
      </li> -->

      <!-- Log Out Link -->
      <li class="nav-item menu-items">
        <a class="nav-link" href="{{route('company.logout')}}">
          <span class="menu-icon">
            <i class="mdi mdi-logout"></i>
          </span>
          <span class="menu-title">Log Out</span>
        </a>
      </li>
    </ul>
  </nav>
  <!-- partial -->