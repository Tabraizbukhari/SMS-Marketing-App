<nav class="navbar navbar-expand navbar-light navbar-bg">
	<a class="sidebar-toggle d-flex">
		<i class="hamburger align-self-center"></i>
	</a>
	<div class="navbar-collapse collapse">
		<ul class="navbar-nav navbar-align">
			<li class="nav-item dropdown">
				<a class="nav-link count-indicator" id="notificationDropdown" href="#" data-toggle="dropdown">
					Notification
					<span class="badge badge-success">3</span>
				</a>
				<div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list pb-0" aria-labelledby="notificationDropdown">
					<a class="dropdown-item py-3 border-bottom">
						<p class="mb-0 font-weight-medium float-left">You have 4 new notifications </p>
					</a>
					<hr />
					<a class="dropdown-item preview-item py-3">
						<div class="preview-thumbnail">
						<i class="mdi mdi-alert m-auto text-primary"></i>
						</div>
						<div class="preview-item-content">
						<h6 class="preview-subject font-weight-normal text-dark mb-1">Application Error</h6>
						<p class="font-weight-light small-text mb-0"> Just now </p>
						</div>
					</a>
					<hr/>
					<a class="btn btn-block">
						<span class="badge badge-pill badge-primary">View all</span>
					</a>
				</div>
				</li>
			<li class="nav-item dropdown">
				<a class="nav-link dropdown-toggle d-none d-sm-inline-block" href="#" data-toggle="dropdown">
					<span class="text-dark">{{ Auth::user()->name }}</span>
				</a>
				<div class="dropdown-menu dropdown-menu-right">
						@if(Auth::user()->type == 'admin')
						<button type="button" class="dropdown-item" data-toggle="modal" data-target="#addsms">
							Add Sms
						</button>
						@endif
					<div class="dropdown-divider"></div>
					<form method="get" action="{{ route('logout') }}">@csrf
						<button class="dropdown-item" type="submit">Logout</button>
					</form>
				</div>
			</li>

				
		</ul>
	</div>
</nav>