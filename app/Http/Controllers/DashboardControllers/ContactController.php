<?php

namespace App\Http\Controllers\DashboardControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Contacts;
use Auth;

class ContactController extends Controller
{
    public $pagination;
    public function __construct()
    {
        $this->pagination  = 10;
    }
    public function index()
    {
        $data['contacts'] =  Contacts::paginate($this->pagination); 
        return view('dashboard.contact.index', $data);
    }

    public function create()
    {
        return view('dashboard.contact.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'string|max:16',
            'number' => 'required|min:11|max:12',
        ]);

        Contacts::create([
            'user_id' => Auth::id(),
            'name'   => $request->name,
            'number' => $request->number,
        ]);
        return redirect()->route('contacts')->with('success', 'contact created successfully');
    }

    public function destroy($id)
    {
        $contact    =  Contacts::findOrFail(decrypt($id));
        $contact->delete();
        return redirect()->route('contacts')->with('success', 'contact deleted successfully');
    }
}
