<?php

namespace App\Imports;

use App\Models\Contact;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithValidation;
use Auth;

class ContactsImport implements ToModel, WithHeadingRow, WithValidation
{
    use Importable;


    
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        if(!Contact::where('number', $row['number'])->exists()){
            return new Contact([
                'name'    => $row['name'],
                'number'  => $row['number'],
                'user_id' => Auth::id(),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'number' => 'required|min:10|max:11',
            'name' => 'required',

        ];
    }
}
