
<x-dashboard>
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
									<div class="col-md-6">
										<div class="card">
											<div class="card-body">
												<h5 class="card-title mb-4">My SMS</h5>
												<h1 class="mt-1 mb-3">{{$sms_count}}</h1>
												<span class="text-success"> <i class="mdi mdi-arrow-bottom-right"></i>Total Amount Of Sms User Have.</span>
											</div>
										</div>
										 @if(Auth::user()->type == 'admin')
										<div class="card">
											<div class="card-body">
												<h5 class="card-title mb-4">Message Transfer</h5>
												<h1 class="mt-1 mb-3">{{ $total_message_transfer }}</h1>
												<div class="mb-1">
													<span class="text-muted">Total Message transfer into User</span>
												</div>
											</div>
										</div>
                                    	@endif
										<div class="card">
											<div class="card-body">
												<h5 class="card-title mb-4">Message Send</h5>
												<h1 class="mt-1 mb-3">{{ $total_message_sending }}</h1>
												<div class="mb-1">
													<span class="text-muted">Total Count Of Message is sended</span>
												</div>
											</div>
										</div>
									</div>
									<div class="col-md-6">
									<div class="card">
										<div class="card-body">
											<h5 class="card-title mb-4">Message Send Succesfully</h5>
											<h1 class="mt-1 mb-3">{{ $message_send_successfully }}</h1>
											<div class="mb-1">
												<span class="text-muted">Total Count Of Message is sended</span>
											</div>
										</div>
									</div>
									<div class="card">
										<div class="card-body">
											<h5 class="card-title mb-4">Message send Failed</h5>
											<h1 class="mt-1 mb-3">{{ $message_not_send }}</h1>
											<div class="mb-1">
												<span class="text-muted">Total Count Of Message is sended</span>
											</div>
										</div>
									</div>
                                    @if(isset(Auth::user()->getUserData) && Auth::user()->getUserData->register_as != 'customer')
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
										<div class="card">
											<div class="card-body">
												<h5 class="card-title mb-4">Total Amount</h5>
												<h1 class="mt-1 mb-3">{{ ceil($profit) }}</h1>
												<div class="mb-1">
													<span class="text-success"> <i class="mdi mdi-arrow-bottom-right"></i>Customer by {{ (Auth::user()->type == 'admin')? 'Admin' : 'reseller' }}</span></br>
													<span class="text-muted">Total Amount of Message</span>
												</div>
											</div>
										</div>
										@if(Auth::user()->type == 'admin')
										<div class="card">
											<div class="card-body">
												<h5 class="card-title mb-4">Message Sended By Code</h5>
												<h1 class="mt-1 mb-3">{{ $message_send_code }}</h1>
												
											</div>
										</div>

										<div class="card">
											<div class="card-body">
												<h5 class="card-title mb-4">Message Sended By Masking</h5>
												<h1 class="mt-1 mb-3">{{ $message_send_masking }}</h1>
												
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