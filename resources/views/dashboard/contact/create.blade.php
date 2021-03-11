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
                          <a href="{{ route('contacts') }}" class="btn btn-outline-dark float-right" >
                          <span class="align-middle" data-feather="chevron-left" ></span>back
                          </a>
                        </h2>
                    </div>
                    <div class="card-body">
                        <form method="post" action="{{ route('contacts.store') }}" enctype="multipart/form-data"> @csrf
                            <div class="form-group  pb-2">
                                <input id="buttonselect" type="button" onClick="checkStatus()"  value="Import contact" class="btn btn-outline-success float-right pb-2">
                            </div>
                            <div id="elements">
                                <div class="form-group">
                                    <label >Name</label>
                                    <input type="text" class="form-control"  name="name" placeholder="Enter the Name">
                                </div>
                                <div class="form-group">
                                    <label >Number</label>
                                    <input type="text" class="form-control" name="number" placeholder="Enter the Number (03022322933)">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
function checkStatus() {
    var id =  document.getElementById('buttonselect');
    id.onclick  = checkStatus2;
    id.value = 'Single';

    var Fromelements = document.getElementById('elements');
    Fromelements.innerHTML = `
        <div class="form-group">
            <label >Number</label>
            <input type="file" class="form-control" name="file">
        </div>
    `;
}

function checkStatus2() {
    var id =  document.getElementById('buttonselect');
    id.onclick  = checkStatus;
    id.value = 'Import';

    var Fromelements = document.getElementById('elements');
    Fromelements.innerHTML = `
        <div class="form-group">
            <label >Name</label>
            <input type="text" class="form-control"  name="name" placeholder="Enter the Name">
        </div>
        <div class="form-group">
            <label >Number</label>
            <input type="text" class="form-control" name="number" placeholder="Enter the Number (0302-2322933)">
        </div>
    `;
}
</script>
</x-dashboard>
