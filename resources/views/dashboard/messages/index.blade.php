
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

					<div class="row">
            <div class="col-12 col-xl-12">             
							<div class="card">
								<div class="card-header">
									<h2 class="card-title">Masking Data 
                    <button class="btn btn-primary float-right" data-toggle="modal" data-target="#addmasking">Add New</button>
                  </h5>
								</div>
								<table class="table table-bordered">
									<thead>
										<tr>
                      <th>ID</th>
                      <th>Title</th>
                      <th>Created at</th>
                      <th>Actions</th>
										</tr>
									</thead>
									<tbody>
								
									</tbody>
								</table>
							</div>
						</div>
          </div>
        </div>
    </main>


</x-dashboard>
