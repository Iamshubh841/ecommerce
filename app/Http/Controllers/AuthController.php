<?php
namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
class AuthController extends Controller
{
    public function adminLogin(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $user = User::where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password) || $user->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $token = $user->createToken('api-token', ['role:admin'])->plainTextToken;
        return response()->json([
            'access_token' => $token,
            'role' => $user->role,
        ], 200);
    }
    public function sellerLogin(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $user = User::where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password) || $user->role !== 'seller') {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $token = $user->createToken('api-token', ['role:seller'])->plainTextToken;
        return response()->json([
            'access_token' => $token,
            'role' => $user->role,
        ], 200);
    }
}
