
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

							<div class="col-md-4">
								<div class="card">
									<div class="card-body">
										<h5 class="card-title mb-4">User Remaining SMS</h5>
										<h1 class="mt-1 mb-3">{{$has_sms}}</h1>
										<span class="text-success"><i class="mdi mdi-arrow-bottom-right"></i>Total Amount Of Sms User has.</span> <br />
										<span class='text-secondary'>Total Amount Of Sent Message deduct according to message length.</span>
									</div>
								</div>
							</div>								
							<div class="col-md-4">
								<div class="card">
									<div class="card-body">
										<h5 class="card-title mb-4">Total Sent Message Successfully</h5>
										<h1 class="mt-1 mb-3">{{ $total_sentmessages }}</h1>
										<span class="text-success"> <i class="mdi mdi-arrow-bottom-right"></i>Total Amount Of Sent Message</span>
									</div>
								</div>
							</div>

							<div class="col-md-4">
								<div class="card">
									<div class="card-body">
										<h5 class="card-title mb-4">Length of Messages</h5>
										<h1 class="mt-1 mb-3">{{ $total_sentmessagesamount }}</h1>
										<span class="text-success"> <i class="mdi mdi-arrow-bottom-right"></i>Sum of Message length.</span>
									</div>
								</div>
							</div>

							<div class="col-md-4">
								<div class="card">
									<div class="card-body">
										<h5 class="card-title mb-4">Whole credit of Sms</h5>
										<h1 class="mt-1 mb-3">{{ $total_transcation }}</h1>
										<span class="text-success"> <i class="mdi mdi-arrow-bottom-right"></i>Total Amount Of Sms User Purchase.</span>
									</div>
								</div>
							</div>

							<div class="col-md-4">
								<div class="card">
									<div class="card-body">
										<h5 class="card-title mb-4">Failed Sms</h5>
										<h1 class="mt-1 mb-3">{{ $failed_messages }}</h1>
										<span class="text-success"> <i class="mdi mdi-arrow-bottom-right"></i>Total Amount Of Failed Sms.</span>
									</div>
								</div>
							</div>
								
						</div>

						


					</div>
				</div>
            </div>
        </div>
    </main>
</x-dashboard>