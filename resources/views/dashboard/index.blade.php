
<x-dashboard>
	<main class="content">
		<div class="container-fluid p-0">
            <div class="row">
                	<div class="col-xl-6 col-xxl-5 d-flex">
							<div class="w-100">
								<div class="row">
									<div class="col-sm-6">
										<div class="card">
											<div class="card-body">
												<h5 class="card-title mb-4">SMS</h5>
												<h1 class="mt-1 mb-3">{{$sms_count}}</h1>
												<span class="text-success"> <i class="mdi mdi-arrow-bottom-right"></i>Total Amount Of Sms</span>
											</div>
										</div>
                                        @if(Auth::user()->type == 'admin')
										<div class="card">
											<div class="card-body">
												<h5 class="card-title mb-4">Reseller</h5>
												<h1 class="mt-1 mb-3">{{$reseller_count}}</h1>
												<div class="mb-1">
												    <span class="text-success"> <i class="mdi mdi-arrow-bottom-right"></i>Total number of reseller</span>
												</div>
											</div>
										</div>
                                        @endif
									</div>
									<div class="col-sm-6">
                                    @if(Auth::user()->type == 'user' && Auth::user()->getUserData->register_as == 'reseller')
										<div class="card">
											<div class="card-body">
												<h5 class="card-title mb-4">Customer</h5>
												<h1 class="mt-1 mb-3">{{ $customer_count }}</h1>
												<div class="mb-1">
													<span class="text-success"> <i class="mdi mdi-arrow-bottom-right"></i>Customer by {{ (Auth::user()->type == 'admin')? 'Admin' : 'reseller' }}</span></br>
													<span class="text-muted">Total Count Of Customer</span>
												</div>
											</div>
										</div>
                                    @endif
									</div>
								
                                </div>
							</div>
						</div>
            </div>
        </div>
    </main>
</x-dashboard>