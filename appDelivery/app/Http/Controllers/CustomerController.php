<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storages;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Models\Customer;
use Illuminate\Support\Facades\Storage;

class CustomerController extends Controller
{
    protected $customer;
    // TypeHitting Objeto
    //injeção
    public function __construct(Customer $customer)
    {
        $this->customer = $customer;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     * All registers
     */
    public function index()
    {
        $customers = $this->customer->all();
        return response()->json($customers, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreCustomerRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCustomerRequest $request)
    {
        $request->validate($this->customer->rules(null), $this->customer->feedback());

        //php artisan storage:link.
        $image = $request->file('image');
        $imgName = $image->store('path', 'public');

        $customer = $this->customer->create([
            'name' => $request->name,
            'image' => $imgName,
            'address' => $request->address
        ]);

        return response()->json($customer, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  Integer
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $customer = $this->customer->find($id);
        if ($customer === null) {
            return response()->json(['erro' => 'Não existe'], 404);
        }
        return response()->json($customer, 200);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateCustomerRequest  $request
     * @param  \App\Models\id  $ID
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCustomerRequest $request, $id)
    {
        $customer = $this->customer->find($id);

        if ($customer === null) {
            return response()->json(['erro' => 'Não existe'], 404);
        }

        if (request()->method() === 'PATCH') {
            $rulesDynamics = array();

            foreach ($customer->rules($id) as $input => $rule) {

                if (array_key_exists($input, $request->all())) {
                    $rulesDynamics[$input] = $rule;
                }
            }
            $request->validate($rulesDynamics, $this->customer->feedback());
        } else {
            $request->validate($this->customer->rules($id), $this->customer->feedback());
        }

        //remove arquivo antigo caso um novo seja upado
        if ($request->file('image')) {
            Storage::disk('public')->delete($customer->image);
        }

        $image = $request->file('image');
        $imgName = $image->store('path', 'public');

        $customer->update([
            'name' => $request->name,
            'image' => $imgName,
            'address' => $request->address
        ]);

        return response()->json($customer, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Customer $customer)
    {
        $customer->delete();
        return ['msg'=>'successfully deleted record'];
    }
}
