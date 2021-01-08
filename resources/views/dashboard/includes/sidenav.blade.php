<nav id="sidebar" class="sidebar">
        <div class="sidebar-content js-simplebar">
            <a class="sidebar-brand" href="index.html">
                <span class="align-middle">SMA</span>
            </a>

            <ul class="sidebar-nav" >
                <li class="sidebar-header">
                    Sms Mangement App
                </li>

                <li class="sidebar-item">
                    <a class="sidebar-link" href="{{ route('admin.dashboard') }}">
                        <i class="align-middle" data-feather="sliders"></i> <span class="align-middle">Dashboard</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="{{ route('admin.masking.index') }}">
                        <i class="align-middle" data-feather="layers"></i> 
                        <span class="align-middle">Masking</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="{{ route('admin.reseller.index') }}">
                        <i class="align-middle" data-feather="users"></i> 
                        <span class="align-middle">Resellers</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link" href="{{ route('customer.index') }}">
                        <i class="align-middle" data-feather="user-plus"></i> 
                        <span class="align-middle">Customer</span>
                    </a>
                </li>
                <li class="sidebar-item" >
                    <a href="#auth" data-toggle="collapse" class="sidebar-link collapsed">
                        <i class="align-middle" data-feather="users"></i> <span class="align-middle">Messages</span>
                    </a>
                    <ul id="auth" class="sidebar-dropdown list-unstyled collapse " data-parent="#sidebar">
                        <li class="sidebar-item"><a class="sidebar-link" href="{{ route('message.create') }}">Compose Message</a></li>
                        <li class="sidebar-item"><a class="sidebar-link" href="{{ route('message.index') }}">Inbox</a></li>
                        <li class="sidebar-item"><a class="sidebar-link" href="{{ route('message.campaign') }}">Campaign</a></li>

                    </ul>
                </li>

                    <li class="sidebar-item">
                    <a class="sidebar-link" href="{{ route('transaction.index') }}">
                        <i class="align-middle" data-feather="money"></i> 
                        <span class="align-middle">Transaction</span>
                    </a>
                </li>
             
            </ul>
        </div>
    </nav>