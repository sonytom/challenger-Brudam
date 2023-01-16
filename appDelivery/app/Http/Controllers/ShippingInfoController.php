<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreShippingInfoRequest;
use App\Http\Requests\UpdateShippingInfoRequest;
use App\Models\ShippingInfo;

class ShippingInfoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreShippingInfoRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreShippingInfoRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ShippingInfo  $shippingInfo
     * @return \Illuminate\Http\Response
     */
    public function show(ShippingInfo $shippingInfo)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateShippingInfoRequest  $request
     * @param  \App\Models\ShippingInfo  $shippingInfo
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateShippingInfoRequest $request, ShippingInfo $shippingInfo)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ShippingInfo  $shippingInfo
     * @return \Illuminate\Http\Response
     */
    public function destroy(ShippingInfo $shippingInfo)
    {
        //
    }
}
