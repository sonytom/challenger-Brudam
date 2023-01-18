<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $fillable = ['customer_id', 'dateDelivery', 'taxSend','fragile'];

    public function rules($id)
    {
        $id = $id === null ? 'null' : $id;
        
        return [
            'customers_id' => "exists:customers,id",
            'dateDelivery' => 'required',
            //validation float does not exist 
            'taxSend' => 'required|numeric',
            'fragile' => 'required|digits_between:1,3'
        ];
    }

    public function customer(){
        return $this -> belongsTo('App\Models\Customer');
    }

}
