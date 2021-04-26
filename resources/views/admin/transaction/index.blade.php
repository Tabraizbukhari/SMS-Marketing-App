
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
                    <h2 class="card-title">Transactions 
                    </h5>
                    </div>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Transaction id</th>
                                <th>Title</th>
                                <th>Description</th>
                                <th>type</th>
                                <th>transfer Amount (SMS)</th>
                                <th>Created</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach ($transaction as $tr)
                            <tr>
                                <td>{{$tr->id}}</td>
                                <td>{{$tr->transaction_id}}</td>
                                <td>{{$tr->title}}</td>
                                <td>{{$tr->description}}</td>
                                <td>{{$tr->type}}</td>
                                <td>{{$tr->amount}}</td>
                                <td>{{$tr->created_at}}</td>
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