<nav class="navbar navbar-expand navbar-light navbar-bg">
				<a class="sidebar-toggle d-flex">
          <i class="hamburger align-self-center"></i>
        </a>

				{{-- <form class="d-none d-sm-inline-block">
					<div class="input-group input-group-navbar">
						<input type="text" class="form-control" placeholder="Search…" aria-label="Search">
						<button class="btn" type="button">
                            <i class="align-middle" data-feather="search"></i>
                        </button>
					</div>
				</form> --}}

				<div class="navbar-collapse collapse">
					<ul class="navbar-nav navbar-align">
						
						
						<li class="nav-item dropdown">
						

			    <a class="nav-link dropdown-toggle d-none d-sm-inline-block" href="#" data-toggle="dropdown">
                    {{-- <img src="img/avatars/avatar.jpg" class="avatar img-fluid rounded mr-1" alt="Charles Hall" /> --}}
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