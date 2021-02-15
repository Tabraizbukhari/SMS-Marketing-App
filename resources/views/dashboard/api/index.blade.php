
<x-dashboard>
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
                                                <label>Api Start Url</label>
                                                <input class="form-control" type="text" disabled value="{{ $start_url }}"> 
                                            </div>
                                            <div class="form-group">
                                                <label>Api Username</label>
                                                <input class="form-control" type="text" disabled value="{{ $username }}"> 
                                            </div>
                                            <div class="form-group">
                                                <label>Api Password</label>
                                                <input class="form-control" type="text" disabled value="{{ $password }}"> 
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
</x-dashboard>