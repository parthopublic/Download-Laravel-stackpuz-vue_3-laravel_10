<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Util;
use App\Models\Brand;

class BrandController extends Controller {

    public function index()
    {
        $size = request()->input('size') ? request()->input('size') : 10;
        $sort = request()->input('sort') ? request()->input('sort') : 'brand.id';
        $sortDirection = request()->input('sort') ? (request()->input('desc') ? 'desc' : 'asc') : 'asc';
        $column = request()->input('sc');
        $query = Brand::query()
            ->select('brand.id', 'brand.name')
            ->orderBy($sort, $sortDirection);
        if (Util::IsInvalidSearch($query->getQuery()->columns, $column)) {
            abort(403);
        }
        if (request()->input('sw')) {
            $search = request()->input('sw');
            $operator = Util::getOperator(request()->input('so'));
            if ($operator == 'like') {
                $search = '%'.$search.'%';
            }
            $query->where($column, $operator, $search);
        }
        $brands = $query->paginate($size);
        return ['brands' => $brands->items(), 'last' => $brands->lastPage()];
    }

    public function create()
    {
        return [];
    }

    public function store()
    {
        $this->validate(request(), [
            'name' => 'unique:Brand,name|required|max:50'
        ]);
        Brand::create([
            'name' => request()->input('name')
        ]);
    }

    public function show($id)
    {
        $brand = Brand::query()
            ->select('brand.id', 'brand.name')
            ->where('brand.id', $id)
            ->first();
        $brandProducts = DB::table('brand')
            ->join('product', 'brand.id', 'product.brand_id')
            ->select('product.name', 'product.price', 'product.id')
            ->where('brand.id', $id)
            ->get();
        return ['brand' => $brand, 'brandProducts' => $brandProducts];
    }

    public function edit($id)
    {
        $brand = Brand::query()
            ->select('brand.id', 'brand.name')
            ->where('brand.id', $id)
            ->first();
        return ['brand' => $brand];
    }

    public function update($id)
    {
        $this->validate(request(), [
            'name' => 'required|max:50'
        ]);
        Brand::find($id)->update([
            'name' => request()->input('name')
        ]);
    }

    public function destroy($id)
    {
        Brand::find($id)->delete();
    }
}