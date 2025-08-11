<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TipoElaboracion;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Str;

class TipoElaboracionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $tiposElaboracion = TipoElaboracion::with(['logo', 'gallery', 'mezcals'])
                ->where('activo', true)
                ->orderBy('orden')
                ->orderBy('nombre')
                ->get();

            if ($tiposElaboracion->isEmpty()) {
                $data = [
                    'message' => 'No hay tipos de elaboración registrados.',
                    'status' => 404,
                    'data' => []
                ];
                return response()->json($data, 404);
            }

            $data = [
                'message' => 'Lista de tipos de elaboración obtenida correctamente.',
                'status' => 200,
                'data' => [
                    'tipos_elaboracion' => $tiposElaboracion
                ]
            ];
            return response()->json($data, 200);

        } catch (Exception $e) {
            $data = [
                'message' => 'Error al obtener los tipos de elaboración.',
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
            // Detectar si es un array de tipos de elaboración o un solo tipo
            $input = $request->all();
            $isMultiple = isset($input[0]) && is_array($input[0]);

            if ($isMultiple) {
                return $this->storeMultiple($request);
            } else {
                return $this->storeSingle($request);
            }

        } catch (Exception $e) {
            $data = [
                'message' => 'Error al crear el(los) tipo(s) de elaboración.',
                'status' => 500,
                'data' => []
            ];
            return response()->json($data, 500);
        }
    }

    /**
     * Store a single tipo de elaboración
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

        $tipoElaboracion = TipoElaboracion::create([
            'nombre' => $request->nombre,
            'slug' => $slug,
            'descripcion' => $request->descripcion,
            'activo' => $request->activo ?? true,
            'orden' => $request->orden ?? 0
        ]);

        $data = [
            'message' => 'Tipo de elaboración creado correctamente.',
            'status' => 201,
            'data' => [
                'tipo_elaboracion' => $tipoElaboracion
            ]
        ];

        return response()->json($data, 201);
    }

    /**
     * Store multiple tipos de elaboración
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
            $tiposElaboracion = [];
            foreach ($request->all() as $tipoData) {
                $tipoElaboracion = TipoElaboracion::create([
                    'nombre' => $tipoData['nombre'],
                    'slug' => Str::slug($tipoData['nombre']),
                    'descripcion' => $tipoData['descripcion'] ?? null,
                    'activo' => $tipoData['activo'] ?? true,
                    'orden' => $tipoData['orden'] ?? 0
                ]);

                $tiposElaboracion[] = $tipoElaboracion;
            }

            DB::commit();

            $data = [
                'message' => 'Tipos de elaboración creados correctamente.',
                'status' => 201,
                'data' => [
                    'tipos_elaboracion' => $tiposElaboracion
                ]
            ];

            return response()->json($data, 201);

        } catch (Exception $e) {
            DB::rollBack();
            $data = [
                'message' => 'Error al crear los tipos de elaboración.',
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
            $tipoElaboracion = TipoElaboracion::with(['logo', 'gallery', 'mezcals'])
                ->find($id);

            if (!$tipoElaboracion) {
                $data = [
                    'message' => 'Tipo de elaboración no encontrado.',
                    'status' => 404,
                    'data' => []
                ];
                return response()->json($data, 404);
            }

            $data = [
                'message' => 'Tipo de elaboración obtenido correctamente.',
                'status' => 200,
                'data' => [
                    'tipo_elaboracion' => $tipoElaboracion
                ]
            ];

            return response()->json($data, 200);

        } catch (Exception $e) {
            $data = [
                'message' => 'Error al obtener el tipo de elaboración.',
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
            $tipoElaboracion = TipoElaboracion::find($id);

            if (!$tipoElaboracion) {
                $data = [
                    'message' => 'Tipo de elaboración no encontrado.',
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

            $tipoElaboracion->update([
                'nombre' => $request->nombre,
                'slug' => Str::slug($request->nombre),
                'descripcion' => $request->descripcion,
                'activo' => $request->activo,
                'orden' => $request->orden
            ]);

            $data = [
                'message' => 'Tipo de elaboración actualizado correctamente.',
                'status' => 200,
                'data' => [
                    'tipo_elaboracion' => $tipoElaboracion
                ]
            ];

            return response()->json($data, 200);

        } catch (Exception $e) {
            $data = [
                'message' => 'Error al actualizar el tipo de elaboración.',
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
            $tipoElaboracion = TipoElaboracion::withCount(['mezcals'])->find($id);

            if (!$tipoElaboracion) {
                $data = [
                    'message' => 'Tipo de elaboración no encontrado.',
                    'status' => 404,
                    'data' => []
                ];
                return response()->json($data, 404);
            }

            // Verificar si tiene mezcales asociados
            if ($tipoElaboracion->mezcals_count > 0) {
                $data = [
                    'message' => 'No se puede eliminar el tipo de elaboración porque tiene mezcales asociados.',
                    'status' => 409,
                    'data' => []
                ];
                return response()->json($data, 409);
            }

            $tipoElaboracion->delete();

            $data = [
                'message' => 'Tipo de elaboración eliminado correctamente.',
                'status' => 200,
                'data' => []
            ];

            return response()->json($data, 200);

        } catch (Exception $e) {
            $data = [
                'message' => 'Error al eliminar el tipo de elaboración.',
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
            $tipoElaboracion = TipoElaboracion::find($id);

            if (!$tipoElaboracion) {
                $data = [
                    'message' => 'Tipo de elaboración no encontrado.',
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

            $tipoElaboracion->update($updateData);

            $data = [
                'message' => 'Tipo de elaboración actualizado correctamente.',
                'status' => 200,
                'data' => [
                    'tipo_elaboracion' => $tipoElaboracion
                ]
            ];

            return response()->json($data, 200);

        } catch (Exception $e) {
            $data = [
                'message' => 'Error al actualizar el tipo de elaboración.',
                'status' => 500,
                'data' => []
            ];
            return response()->json($data, 500);
        }
    }
}
