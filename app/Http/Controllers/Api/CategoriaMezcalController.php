<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CategoriaMezcal;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Str;

class CategoriaMezcalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $categoriasMezcal = CategoriaMezcal::with(['logo', 'gallery', 'mezcals'])
                ->where('activo', true)
                ->orderBy('orden')
                ->orderBy('nombre')
                ->get();

            if ($categoriasMezcal->isEmpty()) {
                $data = [
                    'message' => 'No hay categorías de mezcal registradas.',
                    'status' => 404,
                    'data' => []
                ];
                return response()->json($data, 404);
            }

            $data = [
                'message' => 'Lista de categorías de mezcal obtenida correctamente.',
                'status' => 200,
                'data' => [
                    'categorias_mezcal' => $categoriasMezcal
                ]
            ];
            return response()->json($data, 200);

        } catch (Exception $e) {
            $data = [
                'message' => 'Error al obtener las categorías de mezcal.',
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
                'message' => 'Error al crear la(s) categoría(s) de mezcal.',
                'status' => 500,
                'data' => []
            ];
            return response()->json($data, 500);
        }
    }

    /**
     * Store a single categoría de mezcal
     */
    private function storeSingle(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:35|unique:categoria_mezcal,nombre',
            'descripcion' => 'nullable|string',
            'activo' => 'boolean',
            'orden' => 'integer|min:0'
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

        $categoriaMezcal = CategoriaMezcal::create([
            'nombre' => $request->nombre,
            'slug' => $slug,
            'descripcion' => $request->descripcion,
            'activo' => $request->activo ?? true,
            'orden' => $request->orden ?? 0
        ]);

        $data = [
            'message' => 'Categoría de mezcal creada correctamente.',
            'status' => 201,
            'data' => [
                'categoria_mezcal' => $categoriaMezcal
            ]
        ];

        return response()->json($data, 201);
    }

    /**
     * Store multiple categorías de mezcal
     */
    private function storeMultiple(Request $request)
    {
        $validator = Validator::make($request->all(), [
            '*.nombre' => 'required|string|max:35|unique:categoria_mezcal,nombre',
            '*.descripcion' => 'nullable|string',
            '*.activo' => 'boolean',
            '*.orden' => 'integer|min:0'
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

        // Validar que no haya nombres duplicados en el array
        $nombres = collect($request->all())->pluck('nombre');
        if ($nombres->count() !== $nombres->unique()->count()) {
            $data = [
                'message' => 'Error en la validación de los datos.',
                'errors' => ['nombre' => ['No se permiten nombres duplicados en el mismo lote.']],
                'status' => 422,
                'data' => []
            ];
            return response()->json($data, 422);
        }

        // Validar que los nombres no existan ya en la base de datos
        $nombresExistentes = CategoriaMezcal::whereIn('nombre', $nombres)->pluck('nombre');
        if ($nombresExistentes->count() > 0) {
            $data = [
                'message' => 'Error en la validación de los datos.',
                'errors' => ['nombre' => ['Los siguientes nombres ya existen: ' . $nombresExistentes->implode(', ')]],
                'status' => 422,
                'data' => []
            ];
            return response()->json($data, 422);
        }

        DB::beginTransaction();
        
        try {
            $categoriasMezcal = [];
            foreach ($request->all() as $categoriaData) {
                $categoriaMezcal = CategoriaMezcal::create([
                    'nombre' => $categoriaData['nombre'],
                    'slug' => Str::slug($categoriaData['nombre']),
                    'descripcion' => $categoriaData['descripcion'] ?? null,
                    'activo' => $categoriaData['activo'] ?? true,
                    'orden' => $categoriaData['orden'] ?? 0
                ]);

                $categoriasMezcal[] = $categoriaMezcal;
            }

            DB::commit();

            $data = [
                'message' => 'Categorías de mezcal creadas correctamente.',
                'status' => 201,
                'data' => [
                    'categorias_mezcal' => $categoriasMezcal
                ]
            ];

            return response()->json($data, 201);

        } catch (Exception $e) {
            DB::rollBack();
            $data = [
                'message' => 'Error al crear las categorías de mezcal.',
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
            $categoriaMezcal = CategoriaMezcal::with(['logo', 'gallery', 'mezcals'])
                ->find($id);

            if (!$categoriaMezcal) {
                $data = [
                    'message' => 'Categoría de mezcal no encontrada.',
                    'status' => 404,
                    'data' => []
                ];
                return response()->json($data, 404);
            }

            $data = [
                'message' => 'Categoría de mezcal obtenida correctamente.',
                'status' => 200,
                'data' => [
                    'categoria_mezcal' => $categoriaMezcal
                ]
            ];

            return response()->json($data, 200);

        } catch (Exception $e) {
            $data = [
                'message' => 'Error al obtener la categoría de mezcal.',
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
            $categoriaMezcal = CategoriaMezcal::find($id);

            if (!$categoriaMezcal) {
                $data = [
                    'message' => 'Categoría de mezcal no encontrada.',
                    'status' => 404,
                    'data' => []
                ];
                return response()->json($data, 404);
            }

            $validator = Validator::make($request->all(), [
                'nombre' => 'required|string|max:35|unique:categoria_mezcal,nombre,' . $id,
                'descripcion' => 'nullable|string',
                'activo' => 'boolean',
                'orden' => 'integer|min:0'
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

            $categoriaMezcal->update([
                'nombre' => $request->nombre,
                'slug' => Str::slug($request->nombre),
                'descripcion' => $request->descripcion,
                'activo' => $request->activo,
                'orden' => $request->orden
            ]);

            $data = [
                'message' => 'Categoría de mezcal actualizada correctamente.',
                'status' => 200,
                'data' => [
                    'categoria_mezcal' => $categoriaMezcal
                ]
            ];

            return response()->json($data, 200);

        } catch (Exception $e) {
            $data = [
                'message' => 'Error al actualizar la categoría de mezcal.',
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
            $categoriaMezcal = CategoriaMezcal::withCount(['mezcals'])->find($id);

            if (!$categoriaMezcal) {
                $data = [
                    'message' => 'Categoría de mezcal no encontrada.',
                    'status' => 404,
                    'data' => []
                ];
                return response()->json($data, 404);
            }

            // Verificar si tiene mezcales asociados
            if ($categoriaMezcal->mezcals_count > 0) {
                $data = [
                    'message' => 'No se puede eliminar la categoría de mezcal porque tiene mezcales asociados.',
                    'status' => 409,
                    'data' => []
                ];
                return response()->json($data, 409);
            }

            $categoriaMezcal->delete();

            $data = [
                'message' => 'Categoría de mezcal eliminada correctamente.',
                'status' => 200,
                'data' => []
            ];

            return response()->json($data, 200);

        } catch (Exception $e) {
            $data = [
                'message' => 'Error al eliminar la categoría de mezcal.',
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
            $categoriaMezcal = CategoriaMezcal::find($id);

            if (!$categoriaMezcal) {
                $data = [
                    'message' => 'Categoría de mezcal no encontrada.',
                    'status' => 404,
                    'data' => []
                ];
                return response()->json($data, 404);
            }

            $rules = [];
            
            if ($request->has('nombre')) {
                $rules['nombre'] = 'string|max:35|unique:categoria_mezcal,nombre,' . $id;
            }
            if ($request->has('descripcion')) {
                $rules['descripcion'] = 'nullable|string';
            }
            if ($request->has('activo')) {
                $rules['activo'] = 'boolean';
            }
            if ($request->has('orden')) {
                $rules['orden'] = 'integer|min:0';
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

            $updateData = $request->only(array_keys($rules));
            
            if (isset($updateData['nombre'])) {
                $updateData['slug'] = Str::slug($updateData['nombre']);
            }

            $categoriaMezcal->update($updateData);

            $data = [
                'message' => 'Categoría de mezcal actualizada correctamente.',
                'status' => 200,
                'data' => [
                    'categoria_mezcal' => $categoriaMezcal
                ]
            ];

            return response()->json($data, 200);

        } catch (Exception $e) {
            $data = [
                'message' => 'Error al actualizar la categoría de mezcal.',
                'status' => 500,
                'data' => []
            ];
            return response()->json($data, 500);
        }
    }
}
