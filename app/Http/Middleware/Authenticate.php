<?php
namespace App\Http\Middleware;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
class Authenticate extends Middleware
{
    protected function redirectTo(Request $request): ?string
    {
        return null; // No redirects for API
    }

    protected function unauthenticated($request, array $guards)
    {
        abort(response()->json(['message' => 'Unauthorized'], 401));
    }
}
