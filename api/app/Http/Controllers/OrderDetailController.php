<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Util;
use App\Models\OrderDetail;

class OrderDetailController extends Controller {

    public function create()
    {
        $products = DB::table('product')
            ->select('product.id', 'product.name')
            ->get();
        return ['products' => $products];
    }

    public function store()
    {
        $this->validate(request(), [
            'order_id' => 'required',
            'no' => 'required',
            'product_id' => 'required',
            'qty' => 'required'
        ]);
        $this->validate(request(), [ 'order_id' => Rule::unique('orderdetail', 'order_id')->where('order_id', request()->input('order_id'))->where('no', request()->input('no')) ]);
        OrderDetail::create([
            'order_id' => request()->input('order_id'),
            'no' => request()->input('no'),
            'product_id' => request()->input('product_id'),
            'qty' => request()->input('qty')
        ]);
    }

    public function edit($order_id, $no)
    {
        $orderDetail = OrderDetail::query()
            ->select('orderdetail.order_id', 'orderdetail.no', 'orderdetail.product_id', 'orderdetail.qty')
            ->where('orderdetail.order_id', $order_id)
            ->where('orderdetail.no', $no)
            ->first();
        $products = DB::table('product')
            ->select('product.id', 'product.name')
            ->get();
        return ['orderDetail' => $orderDetail, 'products' => $products];
    }

    public function update($order_id, $no)
    {
        $this->validate(request(), [
            'order_id' => 'required',
            'no' => 'required',
            'product_id' => 'required',
            'qty' => 'required'
        ]);
        DB::table('orderdetail')
            ->where('orderdetail.order_id', $order_id)
            ->where('orderdetail.no', $no)
            ->update([
            'order_id' => request()->input('order_id'),
            'no' => request()->input('no'),
            'product_id' => request()->input('product_id'),
            'qty' => request()->input('qty')
        ]);
    }

    public function destroy($order_id, $no)
    {
        DB::table('orderdetail')
            ->where('orderdetail.order_id', $order_id)
            ->where('orderdetail.no', $no)
            ->delete();
    }
}