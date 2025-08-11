<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Palenque;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Str;

class PalenqueController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $palenques = Palenque::with(['logo', 'gallery', 'region', 'maestro', 'mezcals'])
                ->where('activo', true)
                ->orderBy('nombre')
                ->get();

            if ($palenques->isEmpty()) {
                $data = [
                    'message' => 'No hay palenques registrados.',
                    'status' => 404,
                    'data' => []
                ];
                return response()->json($data, 404);
            }

            $data = [
                'message' => 'Lista de palenques obtenida correctamente.',
                'status' => 200,
                'data' => [
                    'palenques' => $palenques
                ]
            ];
            return response()->json($data, 200);

        } catch (Exception $e) {
            $data = [
                'message' => 'Error al obtener los palenques.',
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
            // Detectar si es un array de palenques o un solo palenque
            $input = $request->all();
            $isMultiple = isset($input[0]) && is_array($input[0]);

            if ($isMultiple) {
                return $this->storeMultiple($request);
            } else {
                return $this->storeSingle($request);
            }

        } catch (Exception $e) {
            $data = [
                'message' => 'Error al crear el(los) palenque(s).',
                'status' => 500,
                'data' => []
            ];
            return response()->json($data, 500);
        }
    }

    /**
     * Store a single palenque
     */
    private function storeSingle(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'region_id' => 'nullable|exists:regions,id',
            'maestro_id' => 'nullable|exists:maestros,id',
            'nombre' => 'required|string|max:40',
            'descripcion' => 'required|string',
            'historia' => 'nullable|string',
            'telefono' => 'nullable|digits_between:7,15',
            'correo' => 'nullable|email|unique:palenques,correo',
            'direccion' => 'nullable|string|max:100',
            'redes_sociales' => 'nullable|string|max:255',
            'fecha_fundacion' => 'nullable|date',
            'capacidad_produccion' => 'nullable|string|max:100',
            'activo' => 'boolean'
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

        $palenque = Palenque::create([
            'region_id' => $request->region_id,
            'maestro_id' => $request->maestro_id,
            'nombre' => $request->nombre,
            'slug' => $slug,
            'descripcion' => $request->descripcion,
            'historia' => $request->historia,
            'telefono' => $request->telefono,
            'correo' => $request->correo,
            'direccion' => $request->direccion,
            'redes_sociales' => $request->redes_sociales,
            'fecha_fundacion' => $request->fecha_fundacion,
            'capacidad_produccion' => $request->capacidad_produccion,
            'activo' => $request->activo ?? true
        ]);

        $data = [
            'message' => 'Palenque creado correctamente.',
            'status' => 201,
            'data' => [
                'palenque' => $palenque->load(['region', 'maestro'])
            ]
        ];

        return response()->json($data, 201);
    }

    /**
     * Store multiple palenques
     */
    private function storeMultiple(Request $request)
    {
        $validator = Validator::make($request->all(), [
            '*.region_id' => 'nullable|exists:regions,id',
            '*.maestro_id' => 'nullable|exists:maestros,id',
            '*.nombre' => 'required|string|max:40',
            '*.descripcion' => 'required|string',
            '*.historia' => 'nullable|string',
            '*.telefono' => 'nullable|digits_between:7,15',
            '*.correo' => 'nullable|email',
            '*.direccion' => 'nullable|string|max:100',
            '*.redes_sociales' => 'nullable|string|max:255',
            '*.fecha_fundacion' => 'nullable|date',
            '*.capacidad_produccion' => 'nullable|string|max:100',
            '*.activo' => 'boolean'
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

        // Validar que no haya correos duplicados en el array
        $correos = collect($request->all())->pluck('correo')->filter();
        if ($correos->count() !== $correos->unique()->count()) {
            $data = [
                'message' => 'Error en la validación de los datos.',
                'errors' => ['correo' => ['No se permiten correos duplicados en el mismo lote.']],
                'status' => 422,
                'data' => []
            ];
            return response()->json($data, 422);
        }

        // Validar que los correos no existan ya en la base de datos
        if ($correos->count() > 0) {
            $correosExistentes = Palenque::whereIn('correo', $correos)->pluck('correo');
            if ($correosExistentes->count() > 0) {
                $data = [
                    'message' => 'Error en la validación de los datos.',
                    'errors' => ['correo' => ['Los siguientes correos ya existen: ' . $correosExistentes->implode(', ')]],
                    'status' => 422,
                    'data' => []
                ];
                return response()->json($data, 422);
            }
        }

        DB::beginTransaction();
        
        try {
            $palenques = [];
            foreach ($request->all() as $palenqueData) {
                $palenque = Palenque::create([
                    'region_id' => $palenqueData['region_id'] ?? null,
                    'maestro_id' => $palenqueData['maestro_id'] ?? null,
                    'nombre' => $palenqueData['nombre'],
                    'slug' => Str::slug($palenqueData['nombre']),
                    'descripcion' => $palenqueData['descripcion'],
                    'historia' => $palenqueData['historia'] ?? null,
                    'telefono' => $palenqueData['telefono'] ?? null,
                    'correo' => $palenqueData['correo'] ?? null,
                    'direccion' => $palenqueData['direccion'] ?? null,
                    'redes_sociales' => $palenqueData['redes_sociales'] ?? null,
                    'fecha_fundacion' => $palenqueData['fecha_fundacion'] ?? null,
                    'capacidad_produccion' => $palenqueData['capacidad_produccion'] ?? null,
                    'activo' => $palenqueData['activo'] ?? true
                ]);

                $palenques[] = $palenque;
            }

            DB::commit();

            $data = [
                'message' => 'Palenques creados correctamente.',
                'status' => 201,
                'data' => [
                    'palenques' => $palenques
                ]
            ];

            return response()->json($data, 201);

        } catch (Exception $e) {
            DB::rollBack();
            $data = [
                'message' => 'Error al crear los palenques.',
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
            $palenque = Palenque::with(['logo', 'gallery', 'region', 'maestro', 'mezcals'])
                ->find($id);

            if (!$palenque) {
                $data = [
                    'message' => 'Palenque no encontrado.',
                    'status' => 404,
                    'data' => []
                ];
                return response()->json($data, 404);
            }

            $data = [
                'message' => 'Palenque obtenido correctamente.',
                'status' => 200,
                'data' => [
                    'palenque' => $palenque
                ]
            ];

            return response()->json($data, 200);

        } catch (Exception $e) {
            $data = [
                'message' => 'Error al obtener el palenque.',
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
            $palenque = Palenque::find($id);

            if (!$palenque) {
                $data = [
                    'message' => 'Palenque no encontrado.',
                    'status' => 404,
                    'data' => []
                ];
                return response()->json($data, 404);
            }

            $validator = Validator::make($request->all(), [
                'region_id' => 'nullable|exists:regions,id',
                'maestro_id' => 'nullable|exists:maestros,id',
                'nombre' => 'required|string|max:40',
                'descripcion' => 'required|string',
                'historia' => 'nullable|string',
                'telefono' => 'nullable|digits_between:7,15',
                'correo' => 'nullable|email|unique:palenques,correo,' . $id,
                'direccion' => 'nullable|string|max:100',
                'redes_sociales' => 'nullable|string|max:255',
                'fecha_fundacion' => 'nullable|date',
                'capacidad_produccion' => 'nullable|string|max:100',
                'activo' => 'boolean'
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

            $palenque->update([
                'region_id' => $request->region_id,
                'maestro_id' => $request->maestro_id,
                'nombre' => $request->nombre,
                'slug' => Str::slug($request->nombre),
                'descripcion' => $request->descripcion,
                'historia' => $request->historia,
                'telefono' => $request->telefono,
                'correo' => $request->correo,
                'direccion' => $request->direccion,
                'redes_sociales' => $request->redes_sociales,
                'fecha_fundacion' => $request->fecha_fundacion,
                'capacidad_produccion' => $request->capacidad_produccion,
                'activo' => $request->activo
            ]);

            $data = [
                'message' => 'Palenque actualizado correctamente.',
                'status' => 200,
                'data' => [
                    'palenque' => $palenque->load(['region', 'maestro'])
                ]
            ];

            return response()->json($data, 200);

        } catch (Exception $e) {
            $data = [
                'message' => 'Error al actualizar el palenque.',
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
            $palenque = Palenque::withCount(['mezcals'])->find($id);

            if (!$palenque) {
                $data = [
                    'message' => 'Palenque no encontrado.',
                    'status' => 404,
                    'data' => []
                ];
                return response()->json($data, 404);
            }

            // Verificar si tiene mezcales asociados
            if ($palenque->mezcals_count > 0) {
                $data = [
                    'message' => 'No se puede eliminar el palenque porque tiene mezcales asociados.',
                    'status' => 409,
                    'data' => []
                ];
                return response()->json($data, 409);
            }

            $palenque->delete();

            $data = [
                'message' => 'Palenque eliminado correctamente.',
                'status' => 200,
                'data' => []
            ];

            return response()->json($data, 200);

        } catch (Exception $e) {
            $data = [
                'message' => 'Error al eliminar el palenque.',
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
            $palenque = Palenque::find($id);

            if (!$palenque) {
                $data = [
                    'message' => 'Palenque no encontrado.',
                    'status' => 404,
                    'data' => []
                ];
                return response()->json($data, 404);
            }

            $rules = [];
            
            if ($request->has('region_id')) {
                $rules['region_id'] = 'nullable|exists:regions,id';
            }
            if ($request->has('maestro_id')) {
                $rules['maestro_id'] = 'nullable|exists:maestros,id';
            }
            if ($request->has('nombre')) {
                $rules['nombre'] = 'string|max:40';
            }
            if ($request->has('descripcion')) {
                $rules['descripcion'] = 'string';
            }
            if ($request->has('historia')) {
                $rules['historia'] = 'nullable|string';
            }
            if ($request->has('telefono')) {
                $rules['telefono'] = 'nullable|digits_between:7,15';
            }
            if ($request->has('correo')) {
                $rules['correo'] = 'nullable|email|unique:palenques,correo,' . $id;
            }
            if ($request->has('direccion')) {
                $rules['direccion'] = 'nullable|string|max:100';
            }
            if ($request->has('redes_sociales')) {
                $rules['redes_sociales'] = 'nullable|string|max:255';
            }
            if ($request->has('fecha_fundacion')) {
                $rules['fecha_fundacion'] = 'nullable|date';
            }
            if ($request->has('capacidad_produccion')) {
                $rules['capacidad_produccion'] = 'nullable|string|max:100';
            }
            if ($request->has('activo')) {
                $rules['activo'] = 'boolean';
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

            $palenque->update($updateData);

            $data = [
                'message' => 'Palenque actualizado correctamente.',
                'status' => 200,
                'data' => [
                    'palenque' => $palenque->load(['region', 'maestro'])
                ]
            ];

            return response()->json($data, 200);

        } catch (Exception $e) {
            $data = [
                'message' => 'Error al actualizar el palenque.',
                'status' => 500,
                'data' => []
            ];
            return response()->json($data, 500);
        }
    }
}
