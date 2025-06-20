<?php
namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Seller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
class AdminController extends Controller
{
   public function __construct()
{
    $this->middleware(['auth:sanctum', 'ability:role:admin']);
}
    public function createSeller(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'mobile_no' => 'required|string|unique:sellers,mobile_no',
            'country' => 'required|string',
            'state' => 'required|string',
            'skills' => 'required|array|min:1',
            'password' => 'required|string|min:6|confirmed',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        try {
            DB::beginTransaction();
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'seller',
            ]);
            Seller::create([
                'user_id' => $user->id,
                'mobile_no' => $request->mobile_no,
                'country' => $request->country,
                'state' => $request->state,
                'skills' => $request->skills,
            ]);
            DB::commit();
            return response()->json(['message' => 'Seller created successfully'], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to create seller', 'error' => $e->getMessage()], 500);
        }
    }
    public function listSellers(Request $request): JsonResponse
    {
        $perPage = $request->query('per_page', 10);
        $sellers = Seller::with('user')->paginate($perPage);
        return response()->json([
            'data' => $sellers->items(),
            'pagination' => [
                'total' => $sellers->total(),
                'per_page' => $sellers->perPage(),
                'current_page' => $sellers->currentPage(),
                'last_page' => $sellers->lastPage(),
            ],
        ], 200);
    }
}
