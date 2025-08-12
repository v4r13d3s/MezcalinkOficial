<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Mezcal;
use App\Models\Region;
use App\Models\CategoriaMezcal;
use App\Models\TipoMaduracion;
use App\Models\Agave;
use Livewire\WithPagination;

class FeaturedMezcales extends Component
{
    

    public function render()
    {
        $mezcales = Mezcal::query()
            ->with(['images' => function ($query) {
                $query->orderByDesc('is_featured')->orderBy('order');
            }])
            ->with('region:id,nombre')
            ->select(['id','region_id', 'marca_id', 'tipo_maduracion_id', 'categoria_mezcal_id', 'tipo_elaboracion_id', 'maestro_id', 'palenque_id', 'nombre', 'slug', 'precio_regular', 'descripcion', 'contenido_alcohol', 'tamanio_bote', 'proveedor', 'notas_cata', 'premios', 'activo'])
            ->where('activo', true)
            ->latest('id')
            ->get();

        return view('livewire.featured-mezcales', [
            'mezcales' => $mezcales,
        ]);
    }
}
