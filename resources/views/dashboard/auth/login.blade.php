<x-dashboard>
    <div class="row">
        <div class="col-md-12">
            <div class="col-lg-3 col-md-2"></div>
            <div class="col-lg-6 col-md-8 p-top-login mt-5">
                <div class="col-lg-12 text-center ">
                    <h3> login Here.. </h3>
                </div>

                <div class="col-lg-12 login-form">
                    <div class="col-lg-12 login-form">
                        <form method='post' action="{{ route('login.post') }}"> @csrf
                            <div class="form-group">
                                <label class="form-control-label">USERNAME</label>
                                <input type="text" name="email" class="form-control">
                            </div>
                            <div class="form-group">
                                <label class="form-control-label">PASSWORD</label>
                                <input type="password" name="password" class="form-control" i>
                            </div>
                        
                            @if (isset($type) && $type == 'user')                                
                                <div class="form-group">
                                    <label for="exampleFormControlSelect1">Register As</label>
                                    <select class="form-control" name='register_as' id="exampleFormControlSelect1">
                                    <option disabled selected>Select User Registeration Type</option>
                                    <option value="reseller">Reseller</option>
                                    <option value="customer">Customer</option>
                                    </select>
                                </div>
                            @endif

                            <div class="form-group">
                            <button type="submit" class="btn btn-block btn-info btn-fill">LOGIN</button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-lg-3 col-md-2"></div>
            </div>
        </div>
        <style >
     
        </style>
</x-dashboard>
