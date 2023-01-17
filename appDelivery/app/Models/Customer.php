<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'image', 'address'];

    public function rules($id)
    {
        $id = $id === null ? 'null' : $id;


        return [
            'name' => "required|unique:customers,name," . $id . "|min:3",
            'image' => 'required|file|mimes:png,pdf',
            'address' => 'required'
        ];
    }


    public function feedback()
    {
        return [
            'required' => 'O field :attribute is required',
            'image.mimes' => 'Not Suported type,is alowed : png,pdf ',
            'name.unique' => 'Name already exists',
            'name.min' => 'minimum three characters'
        ];
    }
}
