<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Repositories\OrderRepository;

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
    public function index(Request $request)
    {
        $orderRepository = new OrderRepository($this->order);

        if ($request->has('attributesCustomer')) {
            $attributesCustomer = 'customer:id,'.$request->attributesCustomer;
            $orderRepository -> selectAtributtesRegistersRelationship($attributesCustomer);
        } else {
            $orderRepository -> selectAtributtesRegistersRelationship('customer');
        }

        if ($request->has('filter')) {
           $orderRepository -> filter(request('filter'));
        }

        if ($request->has('attributes')) {
            $attributes = explode(',', $request->get('attributes'));
            $orderRepository-> selectAttributes($attributes);
        } 

        return response() -> json($orderRepository -> getResult(),200);
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
            'customer_id' => $request->customers_id,
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
        $order = $this->order->with('customer')->find($id);

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

        $order->fill($request->All());
        $order->save();

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
