
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
									<h2 class="card-title">Messages Data
                  <!-- Button trigger modal -->
                  <button type="button" class="btn btn-primary float-right" data-toggle="modal" data-target="#exportt">
                    Export Data
                  </button>
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
                
							</div>
                <div class="float-right">
                  {{ $messages->links() }}
                </div>
						</div>
          </div>
        </div>
    </main>

<!-- Modal -->
<div class="modal fade" id="exportt" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Export data</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form method="post" action="{{ route('message.data.export') }}"> @csrf
        <div class="modal-body">
          <div class="row">
            <div class="col">
              <label> Start Date </label>
              <input type="date" name="start_date" class="form-control" placeholder="start date">
            <div class="col">
              <label> End Date </label>
              <input type="date" name="end_date" class="form-control" placeholder="end date">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-success">Export</button>
        </div>
      </form>
    </div>
  </div>
</div>
</x-dashboard>
