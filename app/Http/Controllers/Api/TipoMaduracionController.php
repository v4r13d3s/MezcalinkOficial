<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TipoMaduracion;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Str;

class TipoMaduracionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $tiposMaduracion = TipoMaduracion::with(['logo', 'gallery', 'mezcals'])
                ->where('activo', true)
                ->orderBy('orden')
                ->orderBy('nombre')
                ->get();

            if ($tiposMaduracion->isEmpty()) {
                $data = [
                    'message' => 'No hay tipos de maduración registrados.',
                    'status' => 404,
                    'data' => []
                ];
                return response()->json($data, 404);
            }

            $data = [
                'message' => 'Lista de tipos de maduración obtenida correctamente.',
                'status' => 200,
                'data' => [
                    'tipos_maduracion' => $tiposMaduracion
                ]
            ];
            return response()->json($data, 200);

        } catch (Exception $e) {
            $data = [
                'message' => 'Error al obtener los tipos de maduración.',
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
            // Detectar si es un array de tipos de maduración o un solo tipo
            $input = $request->all();
            $isMultiple = isset($input[0]) && is_array($input[0]);

            if ($isMultiple) {
                return $this->storeMultiple($request);
            } else {
                return $this->storeSingle($request);
            }

        } catch (Exception $e) {
            $data = [
                'message' => 'Error al crear el(los) tipo(s) de maduración.',
                'status' => 500,
                'data' => []
            ];
            return response()->json($data, 500);
        }
    }

    /**
     * Store a single tipo de maduración
     */
    private function storeSingle(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:35',
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

        $tipoMaduracion = TipoMaduracion::create([
            'nombre' => $request->nombre,
            'slug' => $slug,
            'descripcion' => $request->descripcion,
            'activo' => $request->activo ?? true,
            'orden' => $request->orden ?? 0
        ]);

        $data = [
            'message' => 'Tipo de maduración creado correctamente.',
            'status' => 201,
            'data' => [
                'tipo_maduracion' => $tipoMaduracion
            ]
        ];

        return response()->json($data, 201);
    }

    /**
     * Store multiple tipos de maduración
     */
    private function storeMultiple(Request $request)
    {
        $validator = Validator::make($request->all(), [
            '*.nombre' => 'required|string|max:35',
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

        DB::beginTransaction();
        
        try {
            $tiposMaduracion = [];
            foreach ($request->all() as $tipoData) {
                $tipoMaduracion = TipoMaduracion::create([
                    'nombre' => $tipoData['nombre'],
                    'slug' => Str::slug($tipoData['nombre']),
                    'descripcion' => $tipoData['descripcion'] ?? null,
                    'activo' => $tipoData['activo'] ?? true,
                    'orden' => $tipoData['orden'] ?? 0
                ]);

                $tiposMaduracion[] = $tipoMaduracion;
            }

            DB::commit();

            $data = [
                'message' => 'Tipos de maduración creados correctamente.',
                'status' => 201,
                'data' => [
                    'tipos_maduracion' => $tiposMaduracion
                ]
            ];

            return response()->json($data, 201);

        } catch (Exception $e) {
            DB::rollBack();
            $data = [
                'message' => 'Error al crear los tipos de maduración.',
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
            $tipoMaduracion = TipoMaduracion::with(['logo', 'gallery', 'mezcals'])
                ->find($id);

            if (!$tipoMaduracion) {
                $data = [
                    'message' => 'Tipo de maduración no encontrado.',
                    'status' => 404,
                    'data' => []
                ];
                return response()->json($data, 404);
            }

            $data = [
                'message' => 'Tipo de maduración obtenido correctamente.',
                'status' => 200,
                'data' => [
                    'tipo_maduracion' => $tipoMaduracion
                ]
            ];

            return response()->json($data, 200);

        } catch (Exception $e) {
            $data = [
                'message' => 'Error al obtener el tipo de maduración.',
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
            $tipoMaduracion = TipoMaduracion::find($id);

            if (!$tipoMaduracion) {
                $data = [
                    'message' => 'Tipo de maduración no encontrado.',
                    'status' => 404,
                    'data' => []
                ];
                return response()->json($data, 404);
            }

            $validator = Validator::make($request->all(), [
                'nombre' => 'required|string|max:35',
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

            $tipoMaduracion->update([
                'nombre' => $request->nombre,
                'slug' => Str::slug($request->nombre),
                'descripcion' => $request->descripcion,
                'activo' => $request->activo,
                'orden' => $request->orden
            ]);

            $data = [
                'message' => 'Tipo de maduración actualizado correctamente.',
                'status' => 200,
                'data' => [
                    'tipo_maduracion' => $tipoMaduracion
                ]
            ];

            return response()->json($data, 200);

        } catch (Exception $e) {
            $data = [
                'message' => 'Error al actualizar el tipo de maduración.',
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
            $tipoMaduracion = TipoMaduracion::withCount(['mezcals'])->find($id);

            if (!$tipoMaduracion) {
                $data = [
                    'message' => 'Tipo de maduración no encontrado.',
                    'status' => 404,
                    'data' => []
                ];
                return response()->json($data, 404);
            }

            // Verificar si tiene mezcales asociados
            if ($tipoMaduracion->mezcals_count > 0) {
                $data = [
                    'message' => 'No se puede eliminar el tipo de maduración porque tiene mezcales asociados.',
                    'status' => 409,
                    'data' => []
                ];
                return response()->json($data, 409);
            }

            $tipoMaduracion->delete();

            $data = [
                'message' => 'Tipo de maduración eliminado correctamente.',
                'status' => 200,
                'data' => []
            ];

            return response()->json($data, 200);

        } catch (Exception $e) {
            $data = [
                'message' => 'Error al eliminar el tipo de maduración.',
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
            $tipoMaduracion = TipoMaduracion::find($id);

            if (!$tipoMaduracion) {
                $data = [
                    'message' => 'Tipo de maduración no encontrado.',
                    'status' => 404,
                    'data' => []
                ];
                return response()->json($data, 404);
            }

            $rules = [];
            
            if ($request->has('nombre')) {
                $rules['nombre'] = 'string|max:35';
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

            $tipoMaduracion->update($updateData);

            $data = [
                'message' => 'Tipo de maduración actualizado correctamente.',
                'status' => 200,
                'data' => [
                    'tipo_maduracion' => $tipoMaduracion
                ]
            ];

            return response()->json($data, 200);

        } catch (Exception $e) {
            $data = [
                'message' => 'Error al actualizar el tipo de maduración.',
                'status' => 500,
                'data' => []
            ];
            return response()->json($data, 500);
        }
    }
}
