@extends('layouts.adminlayout')
@section('content')
<main class="content">
		<div class="container-fluid p-0">
		   @if(session()->has('success'))
        	<div class="alert alert-success alert-dismissible" role="alert">
            <button type="button" class="btn-close" data-dismiss="alert" aria-label="Close"></button>
            <div class="alert-message">
              {{ session()->get('success') }}
            </div>
          </div>
          @endif

            <div class="row">
				<div class="col-md-12 d-flex">
					<div class="w-100">
                    
						<div class="row">
							<div class="col-md-4">
								<div class="card">
									<div class="card-body">
										<h5 class="card-title mb-4">Remaining SMS Balance</h5>
										<h1 class="mt-1 mb-3">{{ Auth::user()->has_sms }}</h1>
										<span class="text-success"> <i class="mdi mdi-arrow-bottom-right"></i>Total Amount Of Admin Sms Have.</span>
									</div>
								</div>
							</div>
                            <div class="col-md-4">
								<div class="card">
									<div class="card-body">
										<h5 class="card-title mb-4">Message Transfer</h5>
										<h1 class="mt-1 mb-3">{{ $transfer_sms }}</h1>
										<div class="mb-1">
											<span class="text-muted">Total Message transfer into User</span>
										</div>
									</div>
								</div>
							</div>

                            <div class="col-md-4">
								<div class="card">
									<div class="card-body">
										<h5 class="card-title mb-4">Total Users Count</h5>
										<h1 class="mt-1 mb-3">{{ $usersCount }}</h1>
										<span class="text-success"> <i class="mdi mdi-arrow-bottom-right"></i>Total Amount Of Users.</span>
									</div>
								</div>
                            </div>
                            <div class="col-md-4">
								<div class="card">
									<div class="card-body">
										<h5 class="card-title mb-4">Reseller Count</h5>
										<h1 class="mt-1 mb-3">{{ $resellerCount }}</h1>
										<div class="mb-1">
											<span class="text-muted">Total Amount Of Reseller</span>
										</div>
									</div>
								</div>
							</div>

                            <div class="col-md-4">
								<div class="card">
									<div class="card-body">
										<h5 class="card-title mb-4">Customer Count</h5>
										<h1 class="mt-1 mb-3">{{ $customerCount }}</h1>
										<span class="text-success"> <i class="mdi mdi-arrow-bottom-right"></i>Total Number Of Reseller Customer.</span>
									</div>
								</div>
                            </div>
                            <div class="col-md-4">
								<div class="card">
									<div class="card-body">
										<h5 class="card-title mb-4">Message Transfer</h5>
										<h1 class="mt-1 mb-3">{{ $transfer_sms }}</h1>
										<div class="mb-1">
											<span class="text-muted">Total Message transfer into User</span>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
            </div>
        </div>
    </main>
@endsection