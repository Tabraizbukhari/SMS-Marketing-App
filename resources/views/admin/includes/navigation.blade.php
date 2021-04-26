<nav class="navbar navbar-expand navbar-light navbar-bg">
	<a class="sidebar-toggle d-flex">
		<i class="hamburger align-self-center"></i>
	</a>
	<div class="navbar-collapse collapse">
		<ul class="navbar-nav navbar-align">
			<li class="nav-item dropdown" id="notification_admin" >
				<a class="nav-link count-indicator" id="notificationDropdown" href="#" data-toggle="dropdown">
					Notification
					<span class="badge badge-success">@{{ unread_notification_count }}</span>
				</a>
				<div class="scroll dropdown-menu dropdown-menu-right navbar-dropdown preview-list pb-0" aria-labelledby="notificationDropdown">
					<a v-if="unread_notification_count" class="dropdown-item py-3 border-bottom text-dark">
						<p class="mb-0 font-weight-medium float-left">You have @{{ unread_notification_count }} new notifications </p>
					</a>
					<hr />
					<a class="dropdown-item preview-item py-3" :class="{ 'lightGrey': noti.unread == Null }" v-for="noti in notifications" :href="noti.redirect_url">
						<div class="preview-thumbnail">
						<i class="mdi mdi-alert m-auto text-primary"></i>
						</div>
						<div class="preview-item-content">
						<h6 class="preview-subject font-weight-normal text-dark mb-1">@{{noti.reseller}} Add a new customer @{{noti.customer}}</h6>
						<p class="font-weight-light small-text mb-0"> @{{noti.created}} </p>
						</div>
					</a>
					<hr/>
					     <infinite-loading  direction="bottom" :identifier="infiniteId"  spinner="waveDots"  @infinite="infiniteAdminNotification" >
							<div class="text-dark" slot="no-more"></div>
							<div class="text-dark" slot="no-results"></div>
						</infinite-loading>  
				</div>
			</li>
			<li class="nav-item dropdown">
				<a class="nav-link dropdown-toggle d-none d-sm-inline-block" href="#" data-toggle="dropdown">
					<span class="text-dark">{{ Auth::user()->name }}</span>
				</a>
				<div class="dropdown-menu dropdown-menu-right">
					<button type="button" class="dropdown-item" data-toggle="modal" data-target="#addsms">
						Add Sms
					</button>
					<div class="dropdown-divider"></div>
					<form method="get" action="{{ route('admin.logout') }}">@csrf
						<button class="dropdown-item" type="submit">Logout</button>
					</form>
				</div>
			</li>

				
		</ul>
	</div>
</nav>
