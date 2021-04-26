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
									<h2 class="card-title">Masking Data 
                    <button class="btn btn-primary float-right" data-toggle="modal" data-target="#addmasking">Add New</button>
                  </h5>
								</div>
								<table class="table table-bordered table-responsive">
									<thead>
										<tr>
                      <th>ID</th>
                      <th>Title</th>
                      <th>Created at</th>
                      <th>Actions</th>
										</tr>
									</thead>
									<tbody>
									@foreach ($masking as $mask )                            
                    <tr>
                      <td>{{ $mask->id }}</td>
                      <td width="50%">{{ $mask->title }}</td>
											<td class="d-none d-md-table-cell">{{ $mask->created_at }}</td>
											<td width="20%" class="table-action">
                        <div class="d-flex">
                        
                          <a href="#Editmasking" class="mr-5" data-toggle="modal" data-target="#Editmasking{{ $mask->id }}">Edit</a>
                          
                          {{-- Edit masking modal --}}
                          <div class="modal fade" id="Editmasking{{ $mask->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h5 class="modal-title" id="exampleModalLongTitle">Edit Masking</h5>
                                  <button type="button" class="close btn btn-link" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                  </button>
                                </div>
                                <form method="POST" action="{{ route('admin.masking.update', encrypt($mask->id)) }}">@csrf
                                  <div class="modal-body">
                                  <div class="form-group">
                                      <label for="formGroupExampleInput">Masking Name:</label>
                                      <input type="text" name="title" class="form-control" required id="formGroupExampleInput" value="{{ $mask->title }}">
                                    </div>
                                  </div>
                                  <div class="modal-footer">
                                    <button type="submit" class="btn btn-success">Save Changes</button>
                                  </div>
                                </form>
                              </div>
                            </div>
                          </div>
                          {{-- Edit masking modal end --}}

                          <form method="post" id="form1" action="{{ route('admin.masking.destroy', encrypt($mask->id))}}">
                              @method('DELETE')
                              @csrf
                            <a href="#" class="text-danger" onclick="document.getElementById('form1').submit();">
                              Delete
                            </a>
                          </form>
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
  
{{-- Add masking modal --}}
<div class="modal fade" id="addmasking" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Add New Masking</h5>
        <button type="button" class="close btn btn-link" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form method="POST" action="{{ route('admin.masking.store') }}">@csrf
        <div class="modal-body">
        <div class="form-group">
            <label for="formGroupExampleInput">Masking Name:</label>
            <input type="text" name="title" class="form-control" required id="formGroupExampleInput" placeholder="ENTER THE MASKING TITLE.....">
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Create</button>
        </div>
      </form>
    </div>
  </div>
</div>
{{-- Add masking modal end --}}

@endsection
