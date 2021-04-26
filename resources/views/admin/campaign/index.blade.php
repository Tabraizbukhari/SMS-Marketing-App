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
									<h2 class="card-title">Sms Campaign</h5>
								</div>
								<table class="table table-bordered">
									<thead>
										<tr>
                                        <th>ID</th>
                                        <th>Campaign By</th>
                                        <th>Campaign Name</th>
                                        <th>Campaign Status</th>
                                        <th>Campaign Date</th>
                                        <th>Campaign File Download</th>
                                        <th>Created at</th>
										</tr>
									</thead>
									<tbody>
                                    @foreach ($campaign as $c )                                        
                                        <tr>
                                            <td>{{$c->id}}</td>
                                            <td>{{$c->getAdmin->name }}</td>
                                            <td>{{$c->name}}</td>
                                            <td>{{$c->status}}</td>
                                            <td>{{$c->campaign_date}}</td>
                                            <td><a href="{{ route("admin.message.campaign.file", encrypt($c->id)) }}" class="btn btn-primary">{{$c->file_name}}</a></td>
                                            <td>{{$c->created_at}}</td>
                                        </tr>
                                    @endforeach

									</tbody>
								</table>
                                <div class="float-right">
                                {{ $campaign->links() }}
                                </div>
							</div>
						</div>
          </div>
        </div>
    </main>

@endsection
