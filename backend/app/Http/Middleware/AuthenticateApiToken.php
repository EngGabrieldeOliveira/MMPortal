<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateApiToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        $tokenHash = $token ? hash('sha256', $token) : null;
        $registro = $tokenHash ? DB::table('api_tokens')->where('token', $tokenHash)->first() : null;

        // Mantém ativas as sessões criadas antes da tabela api_tokens existir.
        if (! $registro && $tokenHash) {
            $usuarioLegado = User::where('api_token', $tokenHash)->first();

            if ($usuarioLegado) {
                DB::table('api_tokens')->insert([
                    'user_id' => $usuarioLegado->id,
                    'token' => $tokenHash,
                    'created_at' => now(),
                    'updated_at' => now(),
                    'last_used_at' => now(),
                ]);

                $registro = DB::table('api_tokens')->where('token', $tokenHash)->first();
            }
        }

        if (! $registro) {
            return response()->json(['message' => 'Não autenticado.'], 401);
        }

        DB::table('api_tokens')->where('id', $registro->id)->update(['last_used_at' => now(), 'updated_at' => now()]);

        return $next($request);
    }
}
