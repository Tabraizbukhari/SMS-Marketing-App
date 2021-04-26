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
                    <h2 class="card-title">
                      <div class="d-inline-flex">
                      <h5 class="pt-2">Reseller Customers</h5>
                      </div>
                    </h5>
                    </div>
                    <table class="table ">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Full Name</th>
                                <th>Username & Email</th>
                                <th>Has sms</th>
                                <th>Per Sms Cost</th>
                                <th>Invoice Charges</th>
                                <th>Message Type</th>
                                <th>Is Blocked</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach ($user as $u)
                          <tr>
                            <td>{{ $u->id }}</td>
                            <td>{{ $u->full_name }} </td>
                            <td>{{ $u->username }} <br />
                                <small> {{$u->email}} </small>
                            </td>
                            <td>{{ $u->UserData->has_sms }}</td>
                            <td>{{ $u->UserData->price_per_sms }}</td>
                            <td>{{ $u->UserData->Invoice_charges }}</td>
                            <td>{{ $u->type }}</td>
                            <td><span class="badge {{ ($u->is_blocked == 0)? 'badge-success' : 'badge-dark' }}">{{ ($u->is_blocked == 0)? 'active' : 'blocked' }}</span></td>
                            <td>{{ $u->formated_created_at }}</td>
                            <td>
                              <div class="btn-group">
                                <a class="btn text-dark btn-link btn-lg " type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                  <i class="fas fa-ellipsis-h"></i>
                                </a>
                                <div class="dropdown-menu">
                                  <a href="JavaScript:void(0)" class="dropdown-item" data-toggle="modal" data-target="#viewDetails{{$u->id}}">View Details</a>

                                 
                                  <!-- <form method="POST"  action="{{ route('admin.user.destroy', encrypt($u->id))}}">
                                      @method('DELETE')
                                      @csrf
                                      <button type="submit" class="text-danger dropdown-item" >
                                        Delete
                                      </button>
                                  </form> -->

                                </div>
                            </div>
                            </td>
                          </tr>
                        @endforeach                    
                        </tbody>
                    </table>
                </div>
            </div>
          </div>
        </div>
    </main>
@endsection