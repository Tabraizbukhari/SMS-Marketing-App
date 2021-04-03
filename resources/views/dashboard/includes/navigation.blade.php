<nav class="navbar navbar-expand navbar-light navbar-bg">
	<a class="sidebar-toggle d-flex">
		<i class="hamburger align-self-center"></i>
	</a>
	<div class="navbar-collapse collapse">
		<ul class="navbar-nav navbar-align">
		@if(Auth::user()->type =='admin')
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
			@endif
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
						<button type="button" class="dropdown-item" data-toggle="modal" data-target="#upload_logo">
							Upload Logo
						</button>

					<div class="dropdown-divider"></div>
					<form method="get" action="{{ route('logout') }}">@csrf
						<button class="dropdown-item" type="submit">Logout</button>
					</form>
				</div>
			</li>

				
		</ul>
	</div>
</nav>

<!-- Modal -->
<div class="modal fade" id="upload_logo" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
		<form method="post" action="{{ route('logo.update') }}" enctype="multipart/form-data">	 @csrf
			<div class="modal-body">
				<div class="form-group">
					<button type="button" class="close float-right" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="form-group">
					<lable>Select Logo file <span class="text-danger">*</span></lable>
					<div class="custom-file">
						<input type="file" name="logo" required class="custom-file-input" id="logofile">
						<label class="custom-file-label" for="logofile">Choose your logo file</label>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="submit" class="btn btn-success btn-block">Upload</button>
			</div>
		</form>
    </div>
  </div>
</div>