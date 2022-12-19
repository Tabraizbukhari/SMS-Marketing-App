<x-dashboard>
    <main class="content">
        <div class="container-fluid p-0">
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
            <div class="row d-flex justify-content-center">
                <div class="col-12 col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h2 class="card-title text-capitalize">Add Users
                                <a href="{{ route('user.customer.index') }}" class="btn btn-outline-dark float-right">
                                    <span class="align-middle" data-feather="chevron-left"></span>back
                                </a>
                            </h2>
                        </div>
                        <div class="card-body">
                            <form method="post" action="{{ route('user.customer.store', $type) }}"> @csrf
                                <div class="row form-group">
                                    <div class="col-md-6">
                                        <label class="form-label">First Name<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" required name="first_name"
                                            placeholder="Enter your first Name" value="{{ old('first_name') }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Last Name<span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" required name="last_name"
                                            placeholder="Enter your Last Name" value="{{ old('last_name') }}">
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-md-6">
                                        <label class="form-label">Email address <span
                                                class="text-danger">*</span></label>
                                        <input type="email" class="form-control" required name="email"
                                            placeholder="Email Address"value="{{ old('email') }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Password <span class="text-danger">*</span></label>
                                        <input type="password" class="form-control" required name="password"
                                            placeholder="Enter user password" value="{{ old('password') }}">
                                        <!-- <small>Enter the password if you want to change it, further skip it </small> -->
                                    </div>
                                </div>

                                <div class="row form-group">
                                    <div class="col-md-6">
                                        <label class="form-label">Phone Number </label>
                                        <input type="Number" class="form-control" name="phone_number"
                                            placeholder="phone number" value="{{ old('phone_number') }}">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Invoice Cost</label>
                                        <input type="Number" class="form-control" name="invoice_cost"
                                            placeholder="Monthly invoice cost" value="{{ old('invoice_cost') }}">
                                    </div>
                                </div>

                                <div class="row form-group">
                                    <div class="col-md-6">
                                        <label class="form-label">Cost Per Sms<span class="text-danger">*</span></label>
                                        <input type="Number" required class="form-control" step="any"
                                            name="cost" placeholder="Cost of per sms" value="{{ old('cost') }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">SMS<span class="text-danger">*</span></label>
                                        <input type="Number" required class="form-control" min="1"
                                            max="{{ Auth::user()->has_sms }}" name="sms"
                                            placeholder="Number of sms" value="{{ old('sms') }}">
                                    </div>
                                </div>
                                @if ($type == 'masking')
                                    <div class="row form-group">
                                        <label class="form-label">Select Masking (Mulitples)<span
                                                class="text-danger">*</span></label>
                                        <select class="select2 " required name="masking[]" multiple
                                            data-placeholder="Select multiple masking">
                                            @foreach ($maskings as $mask)
                                                <option
                                                    {{ old('masking') ? (in_array($mask->id, old('masking')) ? 'selected' : null) : null }}
                                                    value="{{ $mask->id }}">{{ $mask->title }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @else
                                    <div class="form-group">
                                        <label class="form-label">Sms Orginator code<span
                                                class="text-danger">*</span></label>
                                        <input type="text" required value="{{ isset($code) ? $code : '' }}"
                                            required class="form-control" name="code" placeholder="Code">
                                    </div>
                                @endif

                                <div class="form-group">
                                    <label class="form-label">Api Url <span class="text-danger">*</span></label>
                                    <input type="text"
                                        value="{{ old('api_url') ?? (Auth::user()->adminApi->api_url ?? App\Models\Admin::first()->adminApi->api_url) }}"
                                        class="form-control" name="api_url" placeholder="Code api Url">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Api Name</label>
                                    <input type="text" class="form-control" name="api_name"
                                        placeholder="Enter user api name" value="{{ old('api_name') }}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Api Password</label>
                                    <input type="text" class="form-control" name="api_password"
                                        placeholder="Enter user api password" value="{{ old('api_password') }}">
                                </div>
                                <button type="submit" class="btn btn-primary btn-block">Submit</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</x-dashboard>
