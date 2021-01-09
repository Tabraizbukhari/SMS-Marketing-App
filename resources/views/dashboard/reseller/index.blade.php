
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
                    <h2 class="card-title">Resellers 
                        <a href="{{ route("admin.reseller.create") }}" class="btn btn-primary float-right" >Add New</a>
                    </h5>
                    </div>
                    <table class="table table-bordered table-responsive">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Maskings</th>
                                <th>No. SMS</th>
                                <th>Cost Of SMS</th>
                                <th>Total Customers</th>
                                <th>My profit</th>
                                <th>Customer profit</th>
                                <th>Total Amount</th>
                                <th>Created</th>
                                <th>Customers</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach ($user as $u)
                        @php $count = 0; @endphp
                          <tr>
                            <td>{{ $u->id }}</td>
                            <td>{{ $u->name }}</td>
                            <td>{{ $u->email }}</td>
                            <td>
                              @foreach ($u->getResellerMasking as $masking )
                                  <span class="badge bg-info rounded-pill">{{ $masking->title }}</span>
                              @endforeach
                            </td>
                            <td>{{ $u->sms }}</td>
                            <td>{{ $u->price }}</td>
                            <td>{{ $u->customer_count }}</td>
                            <td>{{ $u->myprofit }}</td>
                            <td>@if(isset($u->getResellerCustomerProfit))
                                  @foreach ($u->getResellerCustomerProfit as $profit)
                                    {{ $count += $profit['myprofit']  }}
                                  @endforeach
                                @else 
                                    {{0}}
                                @endif
                            </td>
                            <td>{{ $u->myprofit + $count }}</td>
                            <td>{{ $u->created_at }}</td>
                            <td><a class="btn btn-info text-white" href="{{ route('admin.reseller.customer', encrypt($u->id)) }}">view </a></td>
                            <td>
                              <a href="{{ route('admin.reseller.edit', encrypt($u->id)) }}" >Edit</a>
                              <form method="POST" id="form1" action="{{ route('admin.reseller.destroy', encrypt($u->id))}}">
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