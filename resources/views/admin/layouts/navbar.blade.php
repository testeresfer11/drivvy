 <!-- partial:partials/_sidebar.html -->

 <nav class="sidebar sidebar-offcanvas" id="sidebar">
    <div class="sidebar-brand-wrapper d-none d-lg-flex align-items-center justify-content-center fixed-top">
      <a class="sidebar-brand brand-logo" href="{{route('admin.dashboard')}}">


              <img src="{{url('/')}}/images/carpool_logo.png" alt="logo" title="Drivvy" />
      <a class="sidebar-brand brand-logo-mini" href="{{route('admin.dashboard')}}"><img src="{{asset('admin/images/logo-mini.svg')}}" alt="logo" /></a>
    </div>
    <ul class="nav">
    <br>
      <!-- Dashboard Link -->
      <li class="nav-item menu-items {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.dashboard') }}">
            <span class="menu-icon">
                <i class="mdi mdi-view-dashboard"></i>
            </span>
            <span class="menu-title">Dashboard</span>
        </a>
      </li>

      <!-- Profile Link -->
      <li class="nav-item menu-items {{ request()->routeIs('admin.profile', 'admin.changePassword') ? 'active' : '' }}">
        <a class="nav-link {{ request()->routeIs('admin.profile', 'admin.changePassword') ? '' : 'collapsed' }}" data-toggle="collapse" href="#ui-basic" aria-expanded="{{ request()->routeIs('admin.profile', 'admin.changePassword') ? 'true' : 'false' }}" aria-controls="ui-basic">
            <span class="menu-icon">
                <i class="mdi mdi-laptop"></i>
            </span>
            <span class="menu-title">Profile</span>
            <i class="menu-arrow"></i>
        </a>
        <div class="collapse {{ request()->routeIs('admin.profile', 'admin.changePassword') ? 'show' : '' }}" id="ui-basic" data-id="{{ request()->routeIs('admin.profile', 'admin.changePassword') ? 'true' : 'false' }}">
            <ul class="nav flex-column sub-menu">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.profile') ? 'active' : '' }}" href="{{ route('admin.profile') }}">Settings</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.changePassword') ? 'active' : '' }}" href="{{ route('admin.changePassword') }}">Change Password</a>
                </li>
            </ul>
        </div>
      </li>
      

      <!-- User Management Link -->
<li class="nav-item menu-items {{ request()->routeIs('admin.user.*') ? 'active' : '' }}">
    <a class="nav-link main-menu" href="javascript:void(0);">
        <span class="menu-icon">
            <i class="mdi mdi-account"></i>
        </span>
        <span class="menu-title">User Management</span>
    </a>
    <div class="submenu" style="display: none;">
        <ul>
            <li class="{{ request()->routeIs('admin.user.list') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('admin.user.list') }}">
                    <span class="menu-title">User List</span>
                </a>
            </li>
            <li class="{{ request()->routeIs('admin.user.deleted') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('admin.user.deleted') }}">
                    <span class="menu-title">Deleted Users</span>
                </a>
            </li>
        </ul>
    </div>
</li>


      <!-- Document Management Link -->
      <li class="nav-item menu-items {{ request()->routeIs('admin.document.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.document.list') }}">
            <span class="menu-icon">
                <i class="mdi mdi-file-document-edit"></i>
            </span>
            <span class="menu-title">Document Management</span>
        </a>
      </li>

      <!-- Ride Management Link -->
      <li class="nav-item menu-items {{ request()->routeIs('admin.vehicle.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.vehicle.list') }}">
            <span class="menu-icon">
                <i class="mdi mdi-car"></i>
            </span>
            <span class="menu-title">Vehicle Management</span>
        </a>
      </li>

      <!-- Ride Management Link -->
      <li class="nav-item menu-items {{ request()->routeIs('admin.ride.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.ride.list') }}">
            <span class="menu-icon">
                <i class="mdi mdi-motorbike"></i>
            </span>
            <span class="menu-title">Ride Management</span>
        </a>
      </li>

      <!-- Fare Management Link -->
      {{--<li class="nav-item menu-items {{ request()->routeIs('admin.fare.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.fare.list') }}">
            <span class="menu-icon">
                <i class="mdi mdi-motorbike"></i>
            </span>
            <span class="menu-title">Fare Management</span>
        </a>
      </li>--}}


      <!-- Car Management Link -->
      <li class="nav-item menu-items {{ request()->routeIs('admin.cars.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.cars.list') }}">
            <span class="menu-icon">
                <i class="mdi mdi-car"></i>
            </span>
            <span class="menu-title">Cars Management</span>
        </a>
      </li>

      <!-- Requests Management Link -->
      <!-- <li class="nav-item menu-items {{ request()->routeIs('admin.requests.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.requests.list') }}">
            <span class="menu-icon">
                <i class="mdi mdi-hail"></i>
            </span>
            <span class="menu-title">Requests Management</span>
        </a>
      </li> -->

      <!-- Review Management Link -->
        {{--<li class="nav-item menu-items {{ request()->routeIs('admin.review.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.review.list') }}">
            <span class="menu-icon">
                <i class="mdi mdi-comment"></i>
            </span>
            <span class="menu-title">Review Management</span>
        </a>
      </li>--}}

       <!-- Payment Management Link -->
       <li class="nav-item menu-items {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.payments.list') }}">
            <span class="menu-icon">
                <i class="mdi mdi-cash"></i>
            </span>
            <span class="menu-title">Payment Management</span>
        </a>
      </li>

            <!-- Report & analytics Link -->
       <li class="nav-item menu-items {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
        <a class="nav-link {{ request()->routeIs('admin.reports.*') ? '' : 'collapsed' }}" data-toggle="collapse" href="#auth1" aria-expanded="{{ request()->routeIs('admin.reports.*') ? 'true' : 'false' }}" aria-controls="auth1">
            <span class="menu-icon">
                <i class="mdi mdi-finance"></i>
            </span>
            <span class="menu-title">Reports  </span>
            <i class="menu-arrow"></i>
        </a>
        <div class="collapse {{ request()->routeIs('admin.reports.*') ? 'show' : '' }}" id="auth1">
            <ul class="nav flex-column sub-menu">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.reports.users') ? 'active' : '' }}" href="{{ route('admin.reports.users') }}"> User complaints</a>
                </li>
                
               
            </ul>
        </div>
      </li> 

     <ul class="navbar-nav">
    <li class="nav-item menu-items {{ request()->routeIs('admin.payout.*') ? 'active' : '' }}">
        <a class="nav-link {{ request()->routeIs('admin.payout.*') ? '' : 'collapsed' }}" data-toggle="collapse" href="#auth5" aria-expanded="{{ request()->routeIs('admin.payout.*') ? 'true' : 'false' }}" aria-controls="auth1">
            <span class="menu-icon">
                <i class="mdi mdi-cash"></i>
            </span>
            <span class="menu-title">Payout</span>
            <i class="menu-arrow"></i>
        </a>
        <div class="collapse {{ request()->routeIs('admin.payout.*') ? 'show' : '' }}" id="auth5">
            <ul class="nav flex-column sub-menu">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.payout.payout-list') ? 'active' : '' }}" href="{{ route('admin.payout.pending') }}">
                        Pending Payouts 
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.payout.completed') ? 'active' : '' }}" href="{{ route('admin.payout.completed') }}">
                        Completed Payouts
                    </a>
                </li>
                 <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.payout.pending.refund') ? 'active' : '' }}" href="{{ route('admin.payout.pending.refund') }}">
                        Pending Refunds
                    </a>
                </li>
                 <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.payout.completed.refund') ? 'active' : '' }}" href="{{ route('admin.payout.completed.refund') }}">
                        Completed Refunds
                    </a>
                </li>
                <!-- Additional submenu items can be added here -->
            </ul>
        </div>
    </li>
</ul>

      
      {{--<li class="nav-item menu-items {{ request()->routeIs('admin.messages.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.messages.list') }}">
            <span class="menu-icon">
                <i class="mdi mdi-chat"></i>
            </span>
            <span class="menu-title">Message Management</span>
        </a>
      </li>--}}

      <!-- Payment Management Link -->
      {{--<li class="nav-item menu-items {{ request()->routeIs('admin.policies.*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('admin.policies.list') }}">
            <span class="menu-icon">
                <i class="mdi mdi-copyright"></i>
            </span>
            <span class="menu-title">Policies Management</span>
        </a>
      </li>--}}


      <!-- Report & analytics Link -->
      

            <!-- Settings Link -->
      <li class="nav-item menu-items {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
        <a class="nav-link {{ request()->routeIs('admin.settings.*') ? '' : 'collapsed' }}" data-toggle="collapse" href="#auth2" aria-expanded="{{ request()->routeIs('admin.settings.*') ? 'true' : 'false' }}" aria-controls="auth2">
            <span class="menu-icon">
                <i class="mdi mdi-settings"></i>
            </span>
            <span class="menu-title">Settings </span>
            <i class="menu-arrow"></i>
        </a>
        <div class="collapse {{ request()->routeIs('admin.settings.*') ? 'show' : '' }}" id="auth2">
            <ul class="nav flex-column sub-menu">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.settings.general') ? 'active' : '' }}" href="{{ route('admin.settings.general') }}">General Settings</a>
                </li>
                {{--<li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.contentpage.content') ? 'active' : '' }}" href="{{ route('admin.contentpage.list') }}">Content</a>
                </li>--}}
                
                <!-- <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.settings.notifications') ? 'active' : '' }}" href="{{ route('admin.settings.notifications') }}">Notifications Settings</a>
                </li> -->
            </ul>
        </div>
      </li>

      <!-- Log Out Link -->
      <li class="nav-item menu-items">
        <a class="nav-link" href="{{route('logout')}}">
          <span class="menu-icon">
            <i class="mdi mdi-logout"></i>
          </span>
          <span class="menu-title">Log Out</span>
        </a>
      </li>
    </ul>
  </nav>
  <!-- partial -->
<script type="text/javascript">
  
$(document).ready(function() {
    $('.main-menu').click(function() {
        $(this).next('.submenu').slideToggle();
    });
});

</script>