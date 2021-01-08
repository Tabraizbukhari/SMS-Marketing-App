
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
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Customer By</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>No. SMS</th>
                                <th>Cost Of SMS</th>
                                <th>Maskings</th>
                                <th>Created</th>
                                <th>Customers</th>
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
                              @foreach ($u->getResellerMasking as $masking )
                                  <span class="badge bg-info rounded-pill">{{ $masking->title }}</span>
                              @endforeach
                            </td>
                            <td>{{ $u->created_at }}</td>
                            <td>View</td>
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