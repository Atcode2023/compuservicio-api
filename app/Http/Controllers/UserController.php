<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\PaginateRequest;
use App\Http\Resources\Users\CustomerResource;

class UserController extends Controller
{
    public function index(Request $request)
    {
        // Paginar los usuarios con sus relaciones
        $users = User::with(['services', 'servicesTech'])
                    ->paginate($request->input('per_page', 10)); // Puedes ajustar el número predeterminado de elementos por página

        return response()->json($users);
    }

    public function search(Request $request)
    {
        // Validar el request para asegurar que se recibe al menos uno de los campos de búsqueda
        $request->validate([
            'name' => 'nullable|string|max:255',
            'ci' => 'nullable|string|max:255'
        ]);

        // Construir la consulta basada en los parámetros recibidos
        $query = User::with(['services', 'servicesTech']);

        if ($request->has('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        if ($request->has('ci')) {
            $query->where('ci', 'like', '%' . $request->ci . '%');
        }

        // Ejecutar la consulta y obtener los resultados paginados
        $users = $query->paginate($request->input('per_page', 10));

        if ($users->isEmpty()) {
            return response()->json(['message' => 'No se han encontrado usuarios que coincidan con los criterios de búsqueda.'], 404);
        }

        return response()->json($users);
    }

}
