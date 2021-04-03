<x-dashboard>
    <main class="d-flex w-100">
		<div class="container d-flex flex-column">
			<div class="row vh-100">
				<div class="col-sm-10 col-md-8 col-lg-6 mx-auto d-table h-100">
					<div class="d-table-cell align-middle">

						@if($errors->any())
							@foreach ($errors->all() as $error)
								<div class="alert alert-danger alert-dismissible" role="alert">
								<button type="button" class="btn-close" data-dismiss="alert" aria-label="Close"></button>
								<div class="alert-message">
									{{ $error }}
								</div>
								</div>
							@endforeach
						@endif
						
						<div class="text-center mt-4">
							<h1 class="h2">Welcome back,</h1>
							<p class="lead">
								Sign in to your account to continue
							</p>
						</div>

						<div class="card">
							<div class="card-body">
								<div class="m-sm-4">
									<div class="text-center">
										@if($logo_img != NULL)
											<img src="{{ url(Storage::url($logo_img)) }}" alt="logo not found" class="img-fluid" width="132" height="132" />
										@else
											<img src="{{ url('logo/logo.png') }}" alt="synctechsol-logo" class="img-fluid" width="132" height="132" />
										@endif
									</div>
									<form method="post" action="{{ route('login.post') }}"> @csrf
										<div class="mb-3">
											<label class="form-label">Username</label>
											<input class="form-control form-control-lg" type="text" name="username" placeholder="Enter your Username" />
										</div>
										<div class="mb-3">
											<label class="form-label">Password</label>
											<input class="form-control form-control-lg" type="password" name="password" placeholder="Enter your password" />
											{{-- <small>
                                                <a href="pages-reset-password.html">Forgot password?</a>
                                            </small> --}}
										</div>
										<div class="text-center mt-3">
											{{-- <a href="index.html" class="btn btn-lg btn-primary">Sign in</a> --}}
											 <button type="submit" class="btn btn-lg btn-primary">Sign in</button>
										</div>
									</form>
								</div>
							</div>
						</div>

					</div>
				</div>
			</div>
		</div>
	</main>
</x-dashboard>
