<nav class="navbar navbar-expand navbar-light navbar-bg">
	<a class="sidebar-toggle d-flex">
		<i class="hamburger align-self-center"></i>
	</a>
	<div class="navbar-collapse collapse">
		<ul class="navbar-nav navbar-align">
			<li class="nav-item dropdown">
				<a class="nav-link dropdown-toggle d-none d-sm-inline-block" href="#" data-toggle="dropdown">
					<span class="text-dark">{{ Auth::user()->name }}</span>
				</a>
				<div class="dropdown-menu dropdown-menu-right">
					<button type="button" class="dropdown-item" data-toggle="modal" data-target="#upload_logo">
						Upload Logo
					</button>
					<div class="dropdown-divider"></div>
					<form method="get" action="{{ route('user.logout') }}">@csrf
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
		<form method="post" action="{{ route('user.logo.update') }}" enctype="multipart/form-data">	 @csrf
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