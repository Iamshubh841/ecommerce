<?php
namespace App\Http\Controllers;
use App\Models\Product;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
class SellerController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum', 'ability:role:seller']);
    }

    public function addProduct(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'brands' => 'required|array|min:1',
            'brands.*.name' => 'required|string|max:255',
            'brands.*.detail' => 'required|string',
            'brands.*.image' => 'required|string',
            'brands.*.price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        if (!auth()->user()->seller) {
            return response()->json(['message' => 'User is not associated with a seller account'], 403);
        }

        try {
            DB::beginTransaction();

            $product = Product::create([
                'seller_id' => auth()->user()->seller->id,
                'name' => $request->name,
                'description' => $request->description,
            ]);

            foreach ($request->brands as $brand) {
                Brand::create([
                    'product_id' => $product->id,
                    'name' => $brand['name'],
                    'detail' => $brand['detail'],
                    'image' => $brand['image'],
                    'price' => $brand['price'],
                ]);
            }

            DB::commit();
            return response()->json(['message' => 'Product added successfully'], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to add product', 'error' => $e->getMessage()], 500);
        }
    }

    public function listProducts(Request $request): JsonResponse
    {
        if (!auth()->user()->seller) {
            return response()->json(['message' => 'User is not associated with a seller account'], 403);
        }

        $perPage = $request->query('per_page', 10);
        $products = Product::with('brands')
            ->where('seller_id', auth()->user()->seller->id)
            ->paginate($perPage);

        return response()->json([
            'data' => $products->items(),
            'pagination' => [
                'total' => $products->total(),
                'per_page' => $products->perPage(),
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
            ],
        ], 200);
    }

    public function deleteProduct(Request $request, $id): JsonResponse
    {
        if (!auth()->user()->seller) {
            return response()->json(['message' => 'User is not associated with a seller account'], 403);
        }

        $validator = Validator::make(['id' => $id], [
            'id' => 'required|integer|exists:products,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $product = Product::where('id', $id)
            ->where('seller_id', auth()->user()->seller->id)
            ->first();

        if (!$product) {
            return response()->json(['message' => 'Product not found or not owned by this seller'], 404);
        }

        try {
            DB::beginTransaction();
            $product->brands()->delete(); // Delete associated brands
            $product->delete();
            DB::commit();
            return response()->json(['message' => 'Product deleted successfully'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to delete product', 'error' => $e->getMessage()], 500);
        }
    }
}
