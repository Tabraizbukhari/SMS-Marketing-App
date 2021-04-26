
@extends('layouts.adminlayout')
@section('content')
	<main class="content">
		<div class="container-fluid p-0">
            <div class="row">
                <div class="col-md-12 d-flex">
                    <div class="w-100">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="form-group">
                                        </div>
                                  
                                        <div>
                                            <div class="form-group">
                                                <h2>Api Start Url</h2>
                                                <h4>{{ $api_url }}</h4> 
                                            </div>
                                           <div class="from-group">
                                                <h3> Return response in Json:</h3>
                                                <h4><strong>Error Response: </strong>{
                                                        "success": false,
                                                        "response": "Error message"
                                                    }</h4>

                                                <h4><strong>Success Response:</strong>{
                                                        "success": true,
                                                        "response": "Success message"
                                                    }</h4>
                                           </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection