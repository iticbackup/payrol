<div class="topbar">
    <nav class="navbar-custom">
        <ul class="list-unstyled topbar-nav float-end mb-0">

            <li class="dropdown">
                <a class="nav-link dropdown-toggle waves-effect waves-light nav-user" data-bs-toggle="dropdown" href="#" role="button"
                    aria-haspopup="false" aria-expanded="false">
                    <span class="ms-1 nav-user-name hidden-sm">{{ Auth::user()->name }}</span>
                    <div class="avatar-box thumb-xs align-self-center">
                        <span class="avatar-title bg-dark rounded-circle"><i class="fas fa-user"></i></span>
                    </div>
                </a>
                <div class="dropdown-menu dropdown-menu-end">
                    <a class="dropdown-item" href="javascrip:void()"><i data-feather="user" class="align-self-center icon-xs icon-dual me-1"></i> Profile</a>
                    <a class="dropdown-item" href="#"><i data-feather="settings" class="align-self-center icon-xs icon-dual me-1"></i> Settings</a>
                    <div class="dropdown-divider mb-0"></div>
                    <a class="dropdown-item" href="javascript:void();" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i data-feather="power" class="align-self-center icon-xs icon-dual me-1"></i> <span key="t-logout">Logout</span></a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </div>
            </li>
        </ul>

        <ul class="list-unstyled topbar-nav mb-0">
            <li>
                <button class="nav-link button-menu-mobile">
                    <i data-feather="menu" class="align-self-center topbar-icon"></i>
                </button>
            </li>
        </ul>
    </nav>
</div>