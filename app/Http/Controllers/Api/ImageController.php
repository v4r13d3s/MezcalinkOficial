<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use Exception;

class ImageController extends Controller
{
    /**
     * Almacena una imagen para cualquier modelo que use la relación polymórfica.
     */
    public function store(Request $request)
    {
        $storedPath = null;
        try {
            $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
                'type' => 'required|in:logo,gallery',
                'model_type' => 'required|in:CategoriaMision,CategoriaInsignia,Insignia,Mision,Maestro,TipoMaduracion,CategoriaMezcal,TipoElaboracion,Palenque,Marca,Mezcal,Agave',
                'model_id' => 'required|integer',
                'order' => 'integer'
            ]);

            // Guardar la imagen usando el disco 'public'.
            // Esto genera rutas como 'images/xxxx.jpg' bajo storage/app/public
            $path = $request->file('image')->store('images', 'public');
            $storedPath = $path;

            // Crear el registro de la imagen
            $image = new Image([
                'path' => $path,
                'type' => $request->type,
                'order' => $request->order ?? 0
            ]);

            // Obtener el modelo al que se asociará la imagen
            $modelClass = "App\\Models\\" . $request->model_type;
            $model = $modelClass::findOrFail($request->model_id);

            // Asociar la imagen al modelo
            $model->images()->save($image);

            return response()->json([
                'message' => 'Imagen guardada correctamente.',
                'status' => 201,
                'data' => [
                    'image' => $image
                ]
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Datos inválidos.',
                'status' => 422,
                'errors' => $e->errors(),
            ], 422);
        } catch (ModelNotFoundException $e) {
            if ($storedPath) {
                Storage::disk('public')->delete($storedPath);
            }
            return response()->json([
                'message' => 'Modelo no encontrado para asociar la imagen.',
                'status' => 404,
                'data' => []
            ], 404);
        } catch (Exception $e) {
            // Limpieza del archivo si ya se guardó pero la DB falló
            if ($storedPath) {
                Storage::disk('public')->delete($storedPath);
            }
            Log::error('Error al guardar imagen', [
                'error' => $e->getMessage(),
            ]);
            return response()->json([
                'message' => 'Error al guardar la imagen.',
                'status' => 500,
                'data' => []
            ], 500);
        }
    }

    /**
     * Actualiza una imagen existente.
     */
    public function update(Request $request, $id)
    {
        try {
            $image = Image::findOrFail($id);

            $request->validate([
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'type' => 'nullable|in:logo,gallery',
                'order' => 'nullable|integer'
            ]);

            // Si se envió una nueva imagen
            if ($request->hasFile('image')) {
                // Eliminar la imagen anterior
                Storage::disk('public')->delete($image->path);
                
                // Guardar la nueva imagen
                $path = $request->file('image')->store('images', 'public');
                $image->path = $path;
            }

            // Actualizar otros campos si se enviaron
            if ($request->has('type')) {
                $image->type = $request->type;
            }
            if ($request->has('order')) {
                $image->order = $request->order;
            }

            $image->save();

            return response()->json([
                'message' => 'Imagen actualizada correctamente.',
                'status' => 200,
                'data' => [
                    'image' => $image
                ]
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Datos inválidos.',
                'status' => 422,
                'errors' => $e->errors(),
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar la imagen.',
                'status' => 500,
                'data' => []
            ], 500);
        }
    }

    /**
     * Elimina una imagen.
     */
    public function destroy($id)
    {
        try {
            $image = Image::findOrFail($id);
            
            // Eliminar el archivo físico
            Storage::disk('public')->delete($image->path);
            
            // Eliminar el registro
            $image->delete();

            return response()->json([
                'message' => 'Imagen eliminada correctamente.',
                'status' => 200,
                'data' => []
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error al eliminar la imagen.',
                'status' => 500,
                'data' => []
            ], 500);
        }
    }
}
