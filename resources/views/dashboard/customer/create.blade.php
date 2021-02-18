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
        <div class="row d-flex justify-content-center">
            <div class="col-12 col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">Add Customers
                          <a href="{{ route('customer.index') }}" class="btn btn-outline-dark float-right" >
                          <span class="align-middle" data-feather="chevron-left" ></span>back
                          </a>
                        </h2>
                    </div>
                    <div class="card-body">
                        <form method="post" action="{{ route('customer.store') }}"> @csrf
                            <div class="row form-group">
                                <div class="col-md-6">
                                    <label class="form-label">Email address <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" name="email" placeholder="Email" value="{{ old('email') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Password <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control" name="password" placeholder="Password" value="{{ old('password') }}">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Username <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="name" placeholder="username" value="{{ old('username') }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Phone Number</label>
                                <input type="text" class="form-control" name="phone_number" placeholder="phone number" value="{{ old('phone_number') }}">
                            </div>
                            <div class="row form-group">
                                <div class="col-md-6">
                                    <label class="form-label">SMS<span class="text-danger">*</span></label>
                                    <input type="Number" class="form-control"  min="5" max="{{ Auth::user()->sms }}" name="sms" placeholder="Number of sms" value="{{ old('sms') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Cost<span class="text-danger">*</span></label>
                                    <input type="Number" step="any" class="form-control" name="cost" placeholder="Cost of per sms" value="{{ old('cost') }}">
                                </div>
                            </div>
                            @if(Auth::user()->type == 'user')
                                @if(Auth::user()->getUserSmsApi->type == 'masking')
                                    <div class="mb-3">
                                        <label class="form-label">Masking</label>
                                        <select class="select2 form-control" name="masking"  data-placeholder="Select multiple masking">
                                            <option value="" selected disabled>Select Masking</option>
                                            @foreach ($maskings as $masking )
                                                <option {{ (old('masking') == $masking->id)? 'selected': null }} value="{{ $masking->id }}">{{ $masking->title }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @else
                                    <div class="mb-3">
                                        <label class="form-label">Api Url<span class="text-danger">*</span></label>
                                        <input type="text" name="api_url" class="form-control" >
                                    </div>
                                @endif
                            @else
                            <div class="row form-group">
                                <div class="col-md-6">
                                    <label class="form-label">Select any one <span class="text-danger">*</span></label>
                                    </br>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" required type="radio" name="type" id="code" value="single">
                                        <label class="form-check-label" for="inlineRadio1">code</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" required type="radio" name="type" id="masking" value="bulk">
                                        <label class="form-check-label" for="inlineRadio2">masking</label>
                                    </div>
                                </div>
                            </div>
                            <div id="maskingElement" class="mb-3">
                            
                            </div>

                            @endif
                            <div class="mb-3">
                                <label class="form-label">Api Name<span class="text-danger">*</span></label>
                                <input type="text" id='api_name' class="form-control" name="api_name" placeholder="Enter user api name" value="{{ old('api_name') }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Api Password<span class="text-danger">*</span></label>
                                <input type="password" id='api_password' class="form-control" name="api_password" placeholder="Enter user api password" value="{{ old('api_password') }}">
                            </div>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
    $(function(){
       
       var masking = @json($maskings, JSON_PRETTY_PRINT);
        $('#code').change(function(){
            if ($(this).is(':checked')) {
                $('#maskingElement').empty();
                var h_html = `<label class="form-label">Api Url <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="api_url" placeholder="Enter user api url" value="{{ old('api_password') }}">`;      
                 $('#maskingElement').append(h_html);
            }
        });

        $('#masking').change(function(){
            $('#maskingElement').empty();
            if ($(this).is(':checked')) {
                var $v_html =   '<label class="form-label">Masking <span class="text-danger">*</span></label>'+
                                '<select id="selectmask" class="select2 form-control" name="masking" data-placeholder="Select multiple masking">'+
                                '</select>';
                $("#maskingElement").append($v_html);
        
                var output = [{id:'', text: ''}];
                $.each(masking, function(mas, value){
                    output.push({id: value.id,text:value.title});
                });
        
                $("#selectmask").html('').select2({data: output,
                    placeholder: "Select a masking",
                    allowClear: true
                    });
        
                }
            });
    });
</script>
</x-dashboard>
