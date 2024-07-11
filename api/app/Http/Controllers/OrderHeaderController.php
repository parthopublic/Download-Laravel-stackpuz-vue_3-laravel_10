<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Util;
use App\Models\OrderHeader;

class OrderHeaderController extends Controller {

    public function index()
    {
        $size = request()->input('size') ? request()->input('size') : 10;
        $sort = request()->input('sort') ? request()->input('sort') : 'orderheader.id';
        $sortDirection = request()->input('sort') ? (request()->input('desc') ? 'desc' : 'asc') : 'asc';
        $column = request()->input('sc');
        $query = OrderHeader::query()
            ->leftjoin('customer', 'orderheader.customer_id', 'customer.id')
            ->select('orderheader.id', 'customer.name as customer_name', 'orderheader.order_date')
            ->orderBy($sort, $sortDirection);
        if (Util::IsInvalidSearch($query->getQuery()->columns, $column)) {
            abort(403);
        }
        if (request()->input('sw')) {
            $search = request()->input('sw');
            $operator = Util::getOperator(request()->input('so'));
            if ($column == 'orderheader.order_date') {
                $search = Util::formatDateStr($search, 'date');
            }
            if ($operator == 'like') {
                $search = '%'.$search.'%';
            }
            $query->where($column, $operator, $search);
        }
        $orderHeaders = $query->paginate($size);
        return ['orderHeaders' => $orderHeaders->items(), 'last' => $orderHeaders->lastPage()];
    }

    public function create()
    {
        $customers = DB::table('customer')
            ->select('customer.id', 'customer.name')
            ->get();
        return ['customers' => $customers];
    }

    public function store()
    {
        $this->validate(request(), [
            'customer_id' => 'required',
            'order_date' => 'required'
        ]);
        OrderHeader::create([
            'customer_id' => request()->input('customer_id'),
            'order_date' => Util::getDate(request()->input('order_date'))
        ]);
    }

    public function show($id)
    {
        $orderHeader = OrderHeader::query()
            ->leftjoin('customer', 'orderheader.customer_id', 'customer.id')
            ->select('orderheader.id', 'customer.name as customer_name', 'orderheader.order_date')
            ->where('orderheader.id', $id)
            ->first();
        $orderHeaderOrderDetails = DB::table('orderheader')
            ->join('orderdetail', 'orderheader.id', 'orderdetail.order_id')
            ->join('product', 'orderdetail.product_id', 'product.id')
            ->select('orderdetail.no', 'product.name as product_name', 'orderdetail.qty', 'orderdetail.order_id')
            ->where('orderheader.id', $id)
            ->get();
        return ['orderHeader' => $orderHeader, 'orderHeaderOrderDetails' => $orderHeaderOrderDetails];
    }

    public function edit($id)
    {
        $orderHeader = OrderHeader::query()
            ->select('orderheader.id', 'orderheader.customer_id', 'orderheader.order_date')
            ->where('orderheader.id', $id)
            ->first();
        $customers = DB::table('customer')
            ->select('customer.id', 'customer.name')
            ->get();
        return ['orderHeader' => $orderHeader, 'customers' => $customers];
    }

    public function update($id)
    {
        $this->validate(request(), [
            'customer_id' => 'required',
            'order_date' => 'required'
        ]);
        OrderHeader::find($id)->update([
            'customer_id' => request()->input('customer_id'),
            'order_date' => Util::getDate(request()->input('order_date'))
        ]);
    }

    public function destroy($id)
    {
        OrderHeader::find($id)->delete();
    }
}