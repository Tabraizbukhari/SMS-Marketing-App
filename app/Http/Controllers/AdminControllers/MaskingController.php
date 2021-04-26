<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Masking;

class MaskingController extends Controller
{
   
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public $pagination;
    public function __construct()
    {
        $this->pagination = 10;
    }
    public function index()
    {
        $data['masking'] = Masking::paginate($this->pagination);
        return view('admin.masking.index', $data);
    }

  
    public function create()
    {
        //
    }

  
    public function store(Request $request)
    {
        $request->validate(['title' => 'required|string']);
        $data = ['title' => $request->title];
        Masking::create($data);
        return redirect()->back()->with('success','Masking Created Successfully');
    }
    
    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        $masking = Masking::findOrFail(decrypt($id));
        $masking->update([
            'title' => $request->title,
        ]);
        return redirect()->back()->with('success','Masking Updated Successfully');
    }

    public function destroy($id)
    {
        $masking = Masking::findOrFail(decrypt($id));
        $masking->delete();
        return redirect()->back()->with('success','Masking Deleted Successfully');
    }
}
