<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Models\Order;

class OrderController extends Controller
{

    protected $order;
    // TypeHitting Objeto
    //injeção
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $orders = $this->order->all();
        return $orders;
    }

    /**
     * Store a newly created resource in storage.
     * 
     * @param  \App\Http\Requests\StoreOrderRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreOrderRequest $request)
    {
        $request->validate($this->order->rules(null));

        $order = $this->order->create([
            'customers_id' => $request->customers_id,
            'dateDelivery' => $request->dateDelivery,
            'taxSend' => $request->taxSend,
            'fragile' => $request->fragile
        ]);

        return response()->json($order, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $order = $this->order->find($id);
        if ($order === null) {
            return response()->json(['erro' => 'Não existe'], 404);
        }
        return response()->json($order, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateOrderRequest  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateOrderRequest $request, $id)
    {
        $order = $this->order->find($id);

        if ($order === null) {
            return response()->json(['erro' => 'Não existe'], 404);
        }

        if (request()->method() === 'PATCH') {
            $rulesDynamics = array();

            foreach ($order->rules($id) as $input => $rule) {

                if (array_key_exists($input, $request->all())) {
                    $rulesDynamics[$input] = $rule;
                }
            }
            $request->validate($rulesDynamics);
        } else {
            $request->validate($this->order->rules($id));
        }


        $order->update([
            'customers_id' => $request->customers_id,
            'dateDelivery' => $request->dateDelivery,
            'taxSend' => $request->taxSend,
            'fragile' => $request->fragile
        ]);

        return response()->json($order, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $order = $this->order->find($id);
        if ($order === null) {
            return response()->json(['erro' => 'Não existe'], 404);
        }

        $order->delete();
        return response()->json(['msg' => 'successfully deleted record'], 200);
    }
}
