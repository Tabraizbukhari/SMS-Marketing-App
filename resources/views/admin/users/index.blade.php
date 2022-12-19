@extends('layouts.adminlayout')
@section('content')
    <main class="content">
        <div class="container-fluid p-0">

            @if (session()->has('success'))
                <div class="alert alert-success alert-dismissible" role="alert">
                    <button type="button" class="btn-close" data-dismiss="alert" aria-label="Close"></button>
                    <div class="alert-message">
                        {{ session()->get('success') }}
                    </div>
                </div>
            @endif

            @if ($errors->any())
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
                                    <h5 class="pt-2">{{ $title }}</h5>
                                </div>
                                <div class="d-inline-flex float-right">
                                    <button type="button" class="btn btn-primary float-right" data-toggle="modal"
                                        data-target="#addUser">
                                        Add Users
                                    </button>
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
                                    <th>Register As</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($user as $u)
                                    <tr>
                                        <td>#{{ $u->id }}</td>
                                        <td>{{ $u->full_name }} </td>
                                        <td>{{ $u->username }} <br />
                                            <small> {{ $u->email }} </small>
                                        </td>
                                        <td>{{ $u->UserData->has_sms }}</td>
                                        <td>{{ $u->UserData->price_per_sms }}</td>
                                        <td>{{ $u->UserData->Invoice_charges }}</td>
                                        <td>{{ $u->type }}</td>
                                        <td><span
                                                class="badge {{ $u->is_blocked == 0 ? 'badge-success' : 'badge-dark' }}">{{ $u->is_blocked == 0 ? 'active' : 'blocked' }}</span>
                                        </td>
                                        <td>{{ $u->register_as }}</td>
                                        <td>{{ $u->formated_created_at }}</td>
                                        <td>
                                            <div class="btn-group">
                                                <a class="btn text-dark btn-link btn-lg " type="button"
                                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fas fa-ellipsis-h"></i>
                                                </a>
                                                <div class="dropdown-menu">
                                                    <a href="{{ route('admin.user.customer', encrypt($u->id)) }}"
                                                        class="dropdown-item">View Customers</a>
                                                    <a href="JavaScript:void(0)" class="dropdown-item" data-toggle="modal"
                                                        data-target="#viewDetails{{ $u->id }}">View Details</a>
                                                    <a href="JavaScript:void(0)" class="dropdown-item" data-toggle="modal"
                                                        data-target="#addSMS{{ $u->id }}">Add Sms</a>
                                                    <div class="dropdown-divider"></div>
                                                    <a href="{{ route('admin.user.blocked', encrypt($u->id)) }}"
                                                        class="dropdown-item text-info">{{ $u->is_blocked == 0 ? 'Blocked' : 'un-blocked' }}</a>
                                                    <a href="{{ route('admin.user.edit', encrypt($u->id)) }}"
                                                        class="text-primary dropdown-item">Edit</a>
                                                    <form method="POST"
                                                        action="{{ route('admin.user.destroy', encrypt($u->id)) }}">
                                                        @method('DELETE')
                                                        @csrf
                                                        <button type="submit" class="text-danger dropdown-item">
                                                            Delete
                                                        </button>
                                                    </form>

                                                </div>
                                            </div>
                                            <!-- Modal addSMS -->
                                            <div class="modal fade" id="addSMS{{ $u->id }}" tabindex="-1"
                                                role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="exampleModalLongTitle">Add Reseller
                                                                Amount</h5>
                                                            <button type="button" class="close" data-dismiss="modal"
                                                                aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <form method='post'
                                                            action="{{ route('admin.transaction.amount.post', encrypt($u->id)) }}">
                                                            @csrf
                                                            <div class="modal-body">
                                                                <input class="form-control" required type="Number"
                                                                    value="" max="{{ Auth::user()->has_sms }}"
                                                                    name="amount">
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-dismiss="modal">Close</button>
                                                                <button type="submit" class="btn btn-primary">Save
                                                                    changes</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Modal addSMS END -->

                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="d-flex justify-content-end  p-3">
                            {{ $user->links() }}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </main>


    <!-- Modal Add Reseller -->
    <div class="modal fade" id="addUser" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <a href="{{ route('admin.user.create', 'masking') }}" class="btn btn-outline-info">
                                <div class="card btn-outline-info">
                                    <div class="card-body">
                                        <div class="card-title">
                                            <p class="h1">Masking Reseller</p>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="{{ route('admin.user.create', 'code') }}" class="btn btn-outline-info">
                                <div class="card btn-outline-info">
                                    <div class="card-body">
                                        <div class="card-title">
                                            <p class="h1">Code Reseller</p>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Add Reseller End -->
@endsection
