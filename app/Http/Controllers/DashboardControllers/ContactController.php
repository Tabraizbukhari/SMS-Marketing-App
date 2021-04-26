<?php

namespace App\Http\Controllers\DashboardControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Contact;
use Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ContactsImport;


class ContactController extends Controller
{
    public $pagination;
    public function __construct()
    {
        $this->pagination  = 10;
    }
    public function index()
    {
        $data['contacts'] =  Contact::where('user_id', Auth::id())->paginate($this->pagination); 
        return view('dashboard.contact.index', $data);
    }

    public function create()
    {
        return view('dashboard.contact.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'   =>     'string|max:16',
            'number' =>     'sometimes|required|min:11|max:12||unique:contacts',
            'file'   =>     'sometimes|required|mimes:xlsx',
        ]);

        if($request->hasFile('file') && $request->file){
            
            try {
                Excel::import(new ContactsImport, $request->file);
            } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
                 $failures = $e->failures();
                 foreach ($failures as $failure) {
                     $failure->row(); // row that went wrong
                     $failure->attribute(); // either heading key (if using heading row concern) or column index
                     $failure->errors(); // Actual error messages from Laravel validator
                     $failure->values(); // The values of the row that has failed.

                     return redirect()->back()->withErrors([$failure->row() => $failure->errors()]);
                 }
            }
        }else{
            Contact::create([
                'user_id' => Auth::id(),
                'name'   => $request->name,
                'number' => $request->number,
            ]);
        }

        return redirect()->route('user.contacts')->with('success', 'contact created successfully');
    }

    public function destroy($id)
    {
        $contact    =  Contact::findOrFail(decrypt($id));
        $contact->delete();
        return redirect()->route('user.contacts')->with('success', 'contact deleted successfully');
    }

    public function edit($id)
    {
        $data['contact']    = Contact::findOrFail(decrypt($id));
        
        return view('dashboard.contact.edit', $data);
    }

    public function update($id, Request $request)
    {
        $contact = Contact::findOrFail(decrypt($id));
        $contact->update($request->all());
        return redirect()->route('user.contacts')->with('success', 'contact updated successfully');
    }
}
