
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
                      <th>Send By</th>
                      <th>Contact Number</th>
                      <th>Message</th>
                      <th>Message Length</th>
                      <th>Price</th>
                      <th>Type</th>
                      <th>status</th>
                      <th>Delivery Date</th>
                      <th>Created at</th>
										</tr>
									</thead>
									<tbody>
                  @foreach ($messages as $msg)
                      <tr>
                        <td>{{ $msg->id }}</td>
                        <td>{{ $msg->getUser->name }}</td>
                        <td>{{ $msg->contact_number }}</td>
                        <td>{{ $msg->message }}</td>
                        <td>{{ $msg->message_length }}</td>
                        <td>{{ $msg->price }}</td>
                        <td>{{ $msg->type }}</td>
                        <td>{{ $msg->status }}</td>
                        <td>{{ $msg->send_date }}</td>
                        <td>{{ $msg->created_at }}</td>
                      </tr>
                  @endforeach
									</tbody>
								</table>
                <div class="float-right">
                  {{$messages->links()}}
                </div>
							</div>
						</div>
          </div>
        </div>
    </main>


</x-dashboard>
