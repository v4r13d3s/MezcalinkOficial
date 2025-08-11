<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CategoriaInsignia;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Str;

class CategoriaInsigniaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $categorias = CategoriaInsignia::with(['logo', 'gallery'])
                ->orderBy('orden')
                ->get();

            if ($categorias->isEmpty()) {
                $data = [
                    'message' => 'No hay categorías de insignias registradas.',
                    'status' => 404,
                    'data' => []
                ];
                return response()->json($data, 404);
            }

            $data = [
                'message' => 'Lista de categorías de insignias obtenida correctamente.',
                'status' => 200,
                'data' => [
                    'categorias' => $categorias
                ]
            ];
            return response()->json($data, 200);

        } catch (Exception $e) {
            $data = [
                'message' => 'Error al obtener las categorías de insignias.',
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
            'visible' => 'boolean'
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

        $categoria = CategoriaInsignia::create([
            'nombre' => $request->nombre,
            'descripcion' => $request->descripcion,
            'slug' => $slug,
            'orden' => $request->orden ?? 0,
            'activa' => $request->activa ?? true,
            'visible' => $request->visible ?? true
        ]);

        $data = [
            'message' => 'Categoría de insignia creada correctamente.',
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
            '*.visible' => 'boolean'
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
                $categorias[] = CategoriaInsignia::create([
                    'nombre' => $categoriaData['nombre'],
                    'descripcion' => $categoriaData['descripcion'] ?? null,
                    'slug' => Str::slug($categoriaData['nombre']),
                    'orden' => $categoriaData['orden'] ?? 0,
                    'activa' => $categoriaData['activa'] ?? true,
                    'visible' => $categoriaData['visible'] ?? true
                ]);
            }

            DB::commit();

            $data = [
                'message' => count($categorias) . ' categorías de insignias creadas correctamente.',
                'status' => 201,
                'data' => [
                    'categorias' => $categorias
                ]
            ];

            return response()->json($data, 201);

        } catch (Exception $e) {
            DB::rollBack();
            $data = [
                'message' => 'Error al crear las categorías de insignias.',
                'status' => 500,
                'data' => []
            ];
            return response()->json($data, 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        if (!is_numeric($id)) {
            $data = [
                'message' => 'ID inválido.',
                'status' => 400,
                'data' => []
            ];
            return response()->json($data, 400);
        }

        try {
            $categoria = CategoriaInsignia::with(['logo', 'gallery', 'insignias'])->find($id);

            if (!$categoria) {
                $data = [
                    'message' => 'Categoría de insignia no encontrada.',
                    'status' => 404,
                    'data' => []
                ];
                return response()->json($data, 404);
            }

            $data = [
                'message' => 'Categoría de insignia obtenida correctamente.',
                'status' => 200,
                'data' => [
                    'categoria' => $categoria
                ]
            ];

            return response()->json($data, 200);

        } catch (Exception $e) {
            $data = [
                'message' => 'Error al obtener la categoría de insignia.',
                'status' => 500,
                'data' => []
            ];
            return response()->json($data, 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        if (!is_numeric($id)) {
            $data = [
                'message' => 'ID inválido.',
                'status' => 400,
                'data' => []
            ];
            return response()->json($data, 400);
        }

        try {
            $categoria = CategoriaInsignia::find($id);

            if (!$categoria) {
                $data = [
                    'message' => 'Categoría de insignia no encontrada.',
                    'status' => 404,
                    'data' => []
                ];
                return response()->json($data, 404);
            }

            $validator = Validator::make($request->all(), [
                'nombre' => 'required|string|max:50',
                'descripcion' => 'nullable|string',
                'orden' => 'integer',
                'activa' => 'boolean',
                'visible' => 'boolean'
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

            $categoria->nombre = $request->nombre;
            $categoria->descripcion = $request->descripcion;
            $categoria->slug = Str::slug($request->nombre);
            $categoria->orden = $request->orden ?? $categoria->orden;
            $categoria->activa = $request->activa ?? $categoria->activa;
            $categoria->visible = $request->visible ?? $categoria->visible;
            
            $categoria->save();

            $data = [
                'message' => 'Categoría de insignia actualizada correctamente.',
                'status' => 200,
                'data' => [
                    'categoria' => $categoria
                ]
            ];

            return response()->json($data, 200);

        } catch (Exception $e) {
            $data = [
                'message' => 'Error al actualizar la categoría de insignia.',
                'status' => 500,
                'data' => []
            ];
            return response()->json($data, 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        if (!is_numeric($id)) {
            $data = [
                'message' => 'ID inválido.',
                'status' => 400,
                'data' => []
            ];
            return response()->json($data, 400);
        }

        try {
            $categoria = CategoriaInsignia::find($id);

            if (!$categoria) {
                $data = [
                    'message' => 'Categoría de insignia no encontrada.',
                    'status' => 404,
                    'data' => []
                ];
                return response()->json($data, 404);
            }

            // Verificar si tiene insignias asociadas
            if ($categoria->insignias()->count() > 0) {
                $data = [
                    'message' => 'No se puede eliminar la categoría porque tiene insignias asociadas.',
                    'status' => 409,
                    'data' => []
                ];
                return response()->json($data, 409);
            }

            $categoria->delete();

            $data = [
                'message' => 'Categoría de insignia eliminada correctamente.',
                'status' => 200,
                'data' => []
            ];

            return response()->json($data, 200);

        } catch (Exception $e) {
            $data = [
                'message' => 'Error al eliminar la categoría de insignia.',
                'status' => 500,
                'data' => []
            ];
            return response()->json($data, 500);
        }
    }

    /**
     * Update partially the specified resource in storage.
     */
    public function updatePartial(Request $request, string $id)
    {
        if (!is_numeric($id)) {
            $data = [
                'message' => 'ID inválido.',
                'status' => 400,
                'data' => []
            ];
            return response()->json($data, 400);
        }

        try {
            $categoria = CategoriaInsignia::find($id);

            if (!$categoria) {
                $data = [
                    'message' => 'Categoría de insignia no encontrada.',
                    'status' => 404,
                    'data' => []
                ];
                return response()->json($data, 404);
            }

            $rules = [];
            if ($request->has('nombre')) {
                $rules['nombre'] = 'string|max:50';
            }
            if ($request->has('descripcion')) {
                $rules['descripcion'] = 'nullable|string';
            }
            if ($request->has('orden')) {
                $rules['orden'] = 'integer';
            }
            if ($request->has('activa')) {
                $rules['activa'] = 'boolean';
            }
            if ($request->has('visible')) {
                $rules['visible'] = 'boolean';
            }

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $data = [
                    'message' => 'Error en la validación de los datos.',
                    'errors' => $validator->errors(),
                    'status' => 422,
                    'data' => []
                ];
                return response()->json($data, 422);
            }

            if ($request->has('nombre')) {
                $categoria->nombre = $request->nombre;
                $categoria->slug = Str::slug($request->nombre);
            }
            if ($request->has('descripcion')) {
                $categoria->descripcion = $request->descripcion;
            }
            if ($request->has('orden')) {
                $categoria->orden = $request->orden;
            }
            if ($request->has('activa')) {
                $categoria->activa = $request->activa;
            }
            if ($request->has('visible')) {
                $categoria->visible = $request->visible;
            }

            $categoria->save();

            $data = [
                'message' => 'Categoría de insignia actualizada correctamente.',
                'status' => 200,
                'data' => [
                    'categoria' => $categoria
                ]
            ];

            return response()->json($data, 200);

        } catch (Exception $e) {
            $data = [
                'message' => 'Error al actualizar la categoría de insignia.',
                'status' => 500,
                'data' => []
            ];
            return response()->json($data, 500);
        }
    }
}
