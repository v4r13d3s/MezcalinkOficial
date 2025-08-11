<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CategoriaMision;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class CategoriaMisionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $categorias = CategoriaMision::with(['logo', 'gallery'])
                ->orderBy('orden')
                ->get();

            if ($categorias->isEmpty()) {
                $data = [
                    'message' => 'No hay categorías de misiones registradas.',
                    'status' => 404,
                    'data' => []
                ];
                return response()->json($data, 404);
            }

            $data = [
                'message' => 'Lista de categorías de misiones obtenida correctamente.',
                'status' => 200,
                'data' => [
                    'categorias' => $categorias
                ]
            ];
            return response()->json($data, 200);

        } catch (Exception $e) {
            $data = [
                'message' => 'Error al obtener las categorías de misiones.',
                'status' => 500,
                'data' => []
            ];
            return response()->json($data, 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Detectar si es un array de categorías o una sola categoría
            $input = $request->all();
            $isMultiple = isset($input[0]) && is_array($input[0]);

            if ($isMultiple) {
                return $this->storeMultiple($request);
            } else {
                return $this->storeSingle($request);
            }

        } catch (Exception $e) {
            $data = [
                'message' => 'Error interno del servidor.',
                'status' => 500,
                'data' => []
            ];
            return response()->json($data, 500);
        }
    }

    /**
     * Store a single categoria
     */
    private function storeSingle(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:50',
            'descripcion' => 'nullable|string',
            'orden' => 'integer',
            'activa' => 'boolean',
            'visible' => 'boolean',
            'metadatos' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            $data = [
                'message' => 'Error en la validación de los datos.',
                'errors' => $validator->errors(),
                'status' => 422,
                'data' => []
            ];
            return response()->json($data, 422);
        }

        // Generar el slug a partir del nombre
        $slug = Str::slug($request->nombre);

        $categoria = CategoriaMision::create([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'slug' => $slug,
            'orden' => $request->orden ?? 0,
            'activa' => $request->activa ?? true,
            'visible' => $request->visible ?? true,
            'metadatos' => $request->metadatos
        ]);

        $data = [
            'message' => 'Categoría de misión creada correctamente.',
            'status' => 201,
            'data' => [
                'categoria' => $categoria
            ]
        ];

        return response()->json($data, 201);
    }

    /**
     * Store multiple categorias
     */
    private function storeMultiple(Request $request)
    {
        $validator = Validator::make($request->all(), [
            '*.nombre' => 'required|string|max:50',
            '*.descripcion' => 'nullable|string',
            '*.orden' => 'integer',
            '*.activa' => 'boolean',
            '*.visible' => 'boolean',
            '*.metadatos' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            $data = [
                'message' => 'Error en la validación de los datos.',
                'errors' => $validator->errors(),
                'status' => 422,
                'data' => []
            ];
            return response()->json($data, 422);
        }

        DB::beginTransaction();
        
        try {
            $categorias = [];
            foreach ($request->all() as $categoriaData) {
                $categorias[] = CategoriaMision::create([
                    'nombre' => $categoriaData['nombre'],
                    'descripcion' => $categoriaData['descripcion'] ?? null,
                    'slug' => Str::slug($categoriaData['nombre']),
                    'orden' => $categoriaData['orden'] ?? 0,
                    'activa' => $categoriaData['activa'] ?? true,
                    'visible' => $categoriaData['visible'] ?? true,
                    'metadatos' => $categoriaData['metadatos'] ?? null
                ]);
            }

            DB::commit();

            $data = [
                'message' => count($categorias) . ' categorías de misiones creadas correctamente.',
                'status' => 201,
                'data' => [
                    'categorias' => $categorias
                ]
            ];

            return response()->json($data, 201);

        } catch (Exception $e) {
            DB::rollBack();
            $data = [
                'message' => 'Error al crear las categorías de misiones.',
                'status' => 500,
                'data' => []
            ];
            return response()->json($data, 500);
        }
    }
    
}
