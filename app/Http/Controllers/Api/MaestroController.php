<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Maestro;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Str;

class MaestroController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $maestros = Maestro::with(['logo', 'gallery', 'region', 'palenque', 'mezcals'])
                ->orderBy('nombre')
                ->get();

            if ($maestros->isEmpty()) {
                $data = [
                    'message' => 'No hay maestros registrados.',
                    'status' => 404,
                    'data' => []
                ];
                return response()->json($data, 404);
            }

            $data = [
                'message' => 'Lista de maestros obtenida correctamente.',
                'status' => 200,
                'data' => [
                    'maestros' => $maestros
                ]
            ];
            return response()->json($data, 200);

        } catch (Exception $e) {
            $data = [
                'message' => 'Error al obtener los maestros.',
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
            // Detectar si es un array de maestros o un solo maestro
            $input = $request->all();
            $isMultiple = isset($input[0]) && is_array($input[0]);

            if ($isMultiple) {
                return $this->storeMultiple($request);
            } else {
                return $this->storeSingle($request);
            }

        } catch (Exception $e) {
            $data = [
                'message' => 'Error al crear el(los) maestro(s).',
                'status' => 500,
                'data' => []
            ];
            return response()->json($data, 500);
        }
    }

    /**
     * Store a single maestro
     */
    private function storeSingle(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'region_id' => 'nullable|exists:regions,id',
            'nombre' => 'required|string|max:30',
            'fecha_nacimiento' => 'nullable|date',
            'genero' => 'required|in:Masculino,Femenino',
            'nacionalidad' => 'nullable|string|max:30',
            'telefono' => 'nullable|digits_between:7,15',
            'correo' => 'nullable|email|unique:maestros,correo',
            'anios_experiencia' => 'nullable|integer|min:0|max:99',
            'biografia' => 'nullable|string'
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

        $maestro = Maestro::create([
            'region_id' => $request->region_id,
            'nombre' => $request->nombre,
            'slug' => $slug,
            'fecha_nacimiento' => $request->fecha_nacimiento,
            'genero' => $request->genero,
            'nacionalidad' => $request->nacionalidad,
            'telefono' => $request->telefono,
            'correo' => $request->correo,
            'anios_experiencia' => $request->anios_experiencia,
            'biografia' => $request->biografia
        ]);

        $data = [
            'message' => 'Maestro creado correctamente.',
            'status' => 201,
            'data' => [
                'maestro' => $maestro->load(['region'])
            ]
        ];

        return response()->json($data, 201);
    }

    /**
     * Store multiple maestros
     */
    private function storeMultiple(Request $request)
    {
        $validator = Validator::make($request->all(), [
            '*.region_id' => 'nullable|exists:regions,id',
            '*.nombre' => 'required|string|max:30',
            '*.fecha_nacimiento' => 'nullable|date',
            '*.genero' => 'required|in:Masculino,Femenino',
            '*.nacionalidad' => 'nullable|string|max:30',
            '*.telefono' => 'nullable|digits_between:7,15',
            '*.correo' => 'nullable|email',
            '*.anios_experiencia' => 'nullable|integer|min:0|max:99',
            '*.biografia' => 'nullable|string'
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
            $correosExistentes = Maestro::whereIn('correo', $correos)->pluck('correo');
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
            $maestros = [];
            foreach ($request->all() as $maestroData) {
                $maestro = Maestro::create([
                    'region_id' => $maestroData['region_id'] ?? null,
                    'nombre' => $maestroData['nombre'],
                    'slug' => Str::slug($maestroData['nombre']),
                    'fecha_nacimiento' => $maestroData['fecha_nacimiento'] ?? null,
                    'genero' => $maestroData['genero'],
                    'nacionalidad' => $maestroData['nacionalidad'] ?? null,
                    'telefono' => $maestroData['telefono'] ?? null,
                    'correo' => $maestroData['correo'] ?? null,
                    'anios_experiencia' => $maestroData['anios_experiencia'] ?? null,
                    'biografia' => $maestroData['biografia'] ?? null
                ]);

                $maestros[] = $maestro;
            }

            DB::commit();

            $data = [
                'message' => 'Maestros creados correctamente.',
                'status' => 201,
                'data' => [
                    'maestros' => $maestros
                ]
            ];

            return response()->json($data, 201);

        } catch (Exception $e) {
            DB::rollBack();
            $data = [
                'message' => 'Error al crear los maestros.',
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
            $maestro = Maestro::with(['logo', 'gallery', 'region', 'palenque', 'mezcals'])
                ->find($id);

            if (!$maestro) {
                $data = [
                    'message' => 'Maestro no encontrado.',
                    'status' => 404,
                    'data' => []
                ];
                return response()->json($data, 404);
            }

            $data = [
                'message' => 'Maestro obtenido correctamente.',
                'status' => 200,
                'data' => [
                    'maestro' => $maestro
                ]
            ];

            return response()->json($data, 200);

        } catch (Exception $e) {
            $data = [
                'message' => 'Error al obtener el maestro.',
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
            $maestro = Maestro::find($id);

            if (!$maestro) {
                $data = [
                    'message' => 'Maestro no encontrado.',
                    'status' => 404,
                    'data' => []
                ];
                return response()->json($data, 404);
            }

            $validator = Validator::make($request->all(), [
                'region_id' => 'nullable|exists:regions,id',
                'nombre' => 'required|string|max:30',
                'fecha_nacimiento' => 'nullable|date',
                'genero' => 'required|in:Masculino,Femenino',
                'nacionalidad' => 'nullable|string|max:30',
                'telefono' => 'nullable|digits_between:7,15',
                'correo' => 'nullable|email|unique:maestros,correo,' . $id,
                'anios_experiencia' => 'nullable|integer|min:0|max:99',
                'biografia' => 'nullable|string'
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

            $maestro->update([
                'region_id' => $request->region_id,
                'nombre' => $request->nombre,
                'slug' => Str::slug($request->nombre),
                'fecha_nacimiento' => $request->fecha_nacimiento,
                'genero' => $request->genero,
                'nacionalidad' => $request->nacionalidad,
                'telefono' => $request->telefono,
                'correo' => $request->correo,
                'anios_experiencia' => $request->anios_experiencia,
                'biografia' => $request->biografia
            ]);

            $data = [
                'message' => 'Maestro actualizado correctamente.',
                'status' => 200,
                'data' => [
                    'maestro' => $maestro->load(['region'])
                ]
            ];

            return response()->json($data, 200);

        } catch (Exception $e) {
            $data = [
                'message' => 'Error al actualizar el maestro.',
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
            $maestro = Maestro::withCount(['palenque', 'mezcals'])->find($id);

            if (!$maestro) {
                $data = [
                    'message' => 'Maestro no encontrado.',
                    'status' => 404,
                    'data' => []
                ];
                return response()->json($data, 404);
            }

            // Verificar si tiene palenques o mezcales asociados
            if ($maestro->palenque_count > 0 || $maestro->mezcals_count > 0) {
                $data = [
                    'message' => 'No se puede eliminar el maestro porque tiene palenques o mezcales asociados.',
                    'status' => 409,
                    'data' => []
                ];
                return response()->json($data, 409);
            }

            $maestro->delete();

            $data = [
                'message' => 'Maestro eliminado correctamente.',
                'status' => 200,
                'data' => []
            ];

            return response()->json($data, 200);

        } catch (Exception $e) {
            $data = [
                'message' => 'Error al eliminar el maestro.',
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
            $maestro = Maestro::find($id);

            if (!$maestro) {
                $data = [
                    'message' => 'Maestro no encontrado.',
                    'status' => 404,
                    'data' => []
                ];
                return response()->json($data, 404);
            }

            $rules = [];
            
            if ($request->has('region_id')) {
                $rules['region_id'] = 'nullable|exists:regions,id';
            }
            if ($request->has('nombre')) {
                $rules['nombre'] = 'string|max:30';
            }
            if ($request->has('fecha_nacimiento')) {
                $rules['fecha_nacimiento'] = 'nullable|date';
            }
            if ($request->has('genero')) {
                $rules['genero'] = 'in:Masculino,Femenino';
            }
            if ($request->has('nacionalidad')) {
                $rules['nacionalidad'] = 'nullable|string|max:30';
            }
            if ($request->has('telefono')) {
                $rules['telefono'] = 'nullable|digits_between:7,15';
            }
            if ($request->has('correo')) {
                $rules['correo'] = 'nullable|email|unique:maestros,correo,' . $id;
            }
            if ($request->has('anios_experiencia')) {
                $rules['anios_experiencia'] = 'nullable|integer|min:0|max:99';
            }
            if ($request->has('biografia')) {
                $rules['biografia'] = 'nullable|string';
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

            $maestro->update($updateData);

            $data = [
                'message' => 'Maestro actualizado correctamente.',
                'status' => 200,
                'data' => [
                    'maestro' => $maestro->load(['region'])
                ]
            ];

            return response()->json($data, 200);

        } catch (Exception $e) {
            $data = [
                'message' => 'Error al actualizar el maestro.',
                'status' => 500,
                'data' => []
            ];
            return response()->json($data, 500);
        }
    }
}
