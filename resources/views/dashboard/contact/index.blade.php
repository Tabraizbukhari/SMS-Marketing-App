
<x-dashboard>
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
                            <h2 class="card-title">Contacts 
                                <a href="{{ route('contacts.create') }}" class="btn btn-success float-right">Add New Contact</a>
                            </h5>
                        </div>
                        <table class="table table-bordered ">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Number</th>
                                    <th>Created at</th>
                                    <th>Actions</th> 
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($contacts as $contact)
                                    <tr>
                                        <td>{{ $contact->id }}</td>
                                        <td>{{ $contact->name }}</td>
                                        <td>{{ $contact->number }}</td>
                                        <td>{{ $contact->created_at }}</td>
                                        <td> 
                                            <a href="{{ route('contacts.edit', encrypt($contact->id)) }}" >Edit </a></br>
                                            <form method="POST" id="form1" action="{{ route('contacts.destroy', encrypt($contact->id))}}">
                                                @method('DELETE')
                                                @csrf
                                                <a href="#" class="text-danger" onclick="document.getElementById('form1').submit();">
                                                Delete
                                                </a>
                                            </form>
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

</x-dashboard>