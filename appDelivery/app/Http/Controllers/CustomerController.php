<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storages;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Models\Customer;
use App\Repositories\CustomerRepository;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

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
     * 
     *  * @OA\Get(
     *     path="/api/customer",
     *     tags={"Customer"},
     *     summary="Pesquisar todos os clientes",
     *     @OA\Response(
     *         response=200,
     *         description="Mostrar todas os Clientes com pesquisa personalizada"
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="Ha ocurrido un error."
     *     )
     * ) 
     */
    public function index(Request $request)
    {

        $customerRepository = new CustomerRepository($this->customer);

        if ($request->has('attributesOrder')) {
            $attributesOrder = 'order:id,' . $request->attributesOrder;
            $customerRepository->selectAtributtesRegistersRelationship($attributesOrder);
        } else {
            $customerRepository->selectAtributtesRegistersRelationship('order');
        }

        if ($request->has('filter')) {
            $customerRepository->filter(request('filter'));
        }


        if ($request->has('attributes')) {
            $attributes = explode(',', $request->get('attributes'));
            $customerRepository->selectAttributes($attributes);
        }

        return response()->json($customerRepository->getResult(), 200);

        // //return response()->json($this->customer->with('order')->get(), 200);
        // //all() obj + get Collection
        // //get() modificar a consulta -> Collection
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreCustomerRequest  $request
     * @return \Illuminate\Http\Response
     *@OA\Post(
     *     path="/api/customer",
     *     tags={"Customer"},
     *     summary="Cadastra Clientes",
     *     @OA\Response(
     *         response=200,
     *         description="Cadastra Clientes"
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="Ha ocurrido un error."
     *     )
     * ) 
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
     * 
     *  * @OA\Get(
     *     path="/api/customer/{id}",
     *     tags={"Customer"},
     *     summary="Busca Apenas um cliente",
     *     @OA\Response(
     *         response=200,
     *         description="Busca Apenas um cliente"
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="Ha ocurrido un error."
     *     )
     * ) 
     */
    public function show($id)
    {
        $customer = $this->customer->with('order')->find($id);
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
     * @OA\Post(
     *     path="/api/customer/{id}",
     *     tags={"Customer"},
     *     summary="Atualiza Clientes",
     *     @OA\Response(
     *         response=200,
     *         description="Atualiza Clientes"
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="Ha ocurrido un error."
     *     )
     * ) 
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

        //Add dados vindo da Request in customer
        $customer->fill($request->All());
        $customer->image = $imgName;

        //Update and Save ID
        $customer->save();

        return response()->json($customer, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\id $id
     * @return \Illuminate\Http\Response
     *@OA\Delete(
     *     path="/api/customer",
     *     tags={"Customer"},
     *     summary="Deleta Clientes",
     *     @OA\Response(
     *         response=200,
     *         description="Deleta Clientes"
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="Ha ocurrido un error."
     *     )
     * ) 
     */
    public function destroy($id)
    {
        $customer = $this->customer->find($id);
        if ($customer === null) {
            return response()->json(['erro' => 'Não existe'], 404);
        }

        Storage::disk('public')->delete($customer->image);

        $customer->delete();
        return response()->json(['msg' => 'successfully deleted record'], 200);
    }
}
