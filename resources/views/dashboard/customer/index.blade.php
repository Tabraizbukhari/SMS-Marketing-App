
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
                    <h2 class="card-title">Customers 
                        <a href="{{ route("customer.create") }}" class="btn btn-primary float-right" >Add New</a>
                    </h5>
                    </div>
                    <table class="table table-bordered table-responsive">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Customer By</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>No. SMS</th>
                                <th>Cost Of SMS</th>
                                <th>Maskings/Code</th>
                                <th>Message Send</th>
                                <th>Total Amount</th>
                                <th>Created</th>
                                @if(Auth::user()->type == 'user')
                                  <th>Add Amount</th>
                                @else 
                                  <th>API</th>
                                @endif
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach ($user as $u)
                          <tr>
                            <td>{{ $u->id }}</td>
                            <td>{{ $u->getCustomerAddBy()->first()->name }}</td>
                            <td>{{ $u->name }}</td>
                            <td>{{ $u->email }}</td>
                            <td>{{ $u->sms }}</td>
                            <td>{{ $u->price }}</td>
                            <td>
                            @if(count($u->getResellerMasking) > 0)
                              @foreach ($u->getResellerMasking as $masking )
                                  <span class="badge bg-info rounded-pill">{{ $masking->title }}</span>
                              @endforeach
                            @else
                            99059
                            @endif
                            </td>
                            <td>{{ $u->getAllMessages()->count() }}</td>
                            <td>{{ $u->myprofit }}</td>
                            <td>{{ $u->created_at }}</td>
                          @if(Auth::user()->type == 'user')
                            <td>
                              <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#add{{$u->id}}">
                                Add
                              </button>

                                  <!-- Modal -->
                              <div class="modal fade" id="add{{$u->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                  <div class="modal-content">
                                    <div class="modal-header">
                                      <h5 class="modal-title" id="exampleModalLongTitle">Add Customer Amount</h5>
                                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                      </button>
                                    </div>
                                    <form method='post' action="{{ route('transaction.amount.post', encrypt($u->id)) }}"> @csrf
                                      <div class="modal-body">
                                        <input class="form-control" type="Number" value="{{$u->sms}}" min="0" max="{{ Auth::user()->sms }}" name="sms">
                                        <input class="form-control" type="hidden" value="{{$u->getUserData->register_as}}" name="type"> 
                                      </div>
                                      <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Save changes</button>
                                      </div>
                                    </form>
                                  </div>
                                </div>
                              </div>
                            </td>
                          @else
                            <td>
                              <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#add{{$u->id}}">
                                Add / Update
                              </button>

                              <div class="modal fade" id="add{{$u->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                  <div class="modal-content">
                                    <div class="modal-header">
                                      <h5 class="modal-title" id="exampleModalLongTitle">Create or Update Customer Api</h5>
                                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                      </button>
                                    </div>
                                    <form method='post' action="{{ route('customer.api.create.update', encrypt($u->id)) }}"> @csrf
                                      <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label">Api Url<span class="text-danger">*</span></label>
                                            <input type="text" name="api_url" class="form-control" value='{{ old('api_url')??$u->getUserSmsApi->api_url }}' placeholder="enter api Url" >
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Api Name<span class="text-danger">*</span></label>
                                            <input type="text" id='api_name' class="form-control" name="api_name" placeholder="Enter user api name" value="{{ old('api_name')??$u->getUserSmsApi->api_username }}">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Api Password<span class="text-danger">*</span></label>
                                            <input type="password" id='api_password' class="form-control" name="api_password" placeholder="Enter user api password" value="{{ old('api_password')??$u->getUserSmsApi->api_password }}">
                                        </div> 
                                      </div>
                                      <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Save</button>
                                      </div>
                                    </form>
                                  </div>
                                </div>
                              </div>
                            </td>
                          @endif

                            <td>
                              <a href="{{ route('customer.edit', encrypt($u->id)) }}" >Edit </a></br>
                              <form method="POST" id="form1" action="{{ route('customer.destroy', encrypt($u->id))}}">
                                  @method('DELETE')
                                  @csrf
                                <a href="#" class="text-danger" onclick="document.getElementById('form1').submit();">
                                  Delete
                                </a>
                              </form>
                            </td>
                          <tr>
                        @endforeach                    
                        </tbody>
                    </table>
                </div>
            </div>
          </div>
        </div>
    </main>
</x-dashboard>