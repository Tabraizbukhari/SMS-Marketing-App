<x-dashboard>

<main class="content">
	<div class="container-fluid p-0">
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
         @if(session()->has('success'))
        	<div class="alert alert-success alert-dismissible" role="alert">
            <button type="button" class="btn-close" data-dismiss="alert" aria-label="Close"></button>
            <div class="alert-message">
              {{ session()->get('success') }}
            </div>
          </div>
          @endif
        <div class="row d-flex justify-content-center">
            <div class="col-12 col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">
                          <a href="{{ route('user.contacts') }}" class="btn btn-outline-dark float-right" >
                          <span class="align-middle" data-feather="chevron-left" ></span>back
                          </a>
                        </h2>
                    </div>
                    
                    <div class="card-body">
                        <form method="post" action="{{ route('user.contacts.update', encrypt($contact->id)) }}" enctype="multipart/form-data"> @csrf
                            <div class="form-group">
                                <label >Name</label>
                                <input type="text" required class="form-control" value="{{ (old('name'))? old('name') : $contact['name'] }}"  name="name" placeholder="Enter the Name">
                            </div>
                            <div class="form-group">
                                <label >Number</label>
                                <input type="text" required class="form-control" value="{{ (old('number'))? old('number') : $contact['number'] }}" name="number" placeholder="Enter the Number (03022322933)">
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">Update</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
</x-dashboard>
