<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductFilterRequest;
use App\Models\Product;
use App\Services\FilterService;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function __construct(private readonly FilterService $filterService) {}

    public function index(ProductFilterRequest $request): JsonResponse
    {
        $products = $this->filterService->filter(new Product, $request->validated())
            ->orderBy($request->input('sort_by', 'id'), $request->input('sort_direction', 'asc'));;;

        return response()->json($products->paginate($request->input('per_page', 40)));
    }
}
