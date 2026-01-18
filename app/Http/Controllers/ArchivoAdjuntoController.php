<?php

namespace App\Http\Controllers;

use App\Models\ArchivoAdjunto;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ArchivoAdjuntoController extends Controller
{
    /**
     * Download an archivo adjunto.
     */
    public function download(ArchivoAdjunto $archivo): StreamedResponse
    {
        // TODO: Verificar permisos según el documentable
        
        return Storage::disk('private')->download($archivo->ruta, $archivo->nombre_original);
    }

    /**
     * View an archivo adjunto inline (for images, PDFs, etc).
     */
    public function view(ArchivoAdjunto $archivo)
    {
        // TODO: Verificar permisos según el documentable
        
        return Storage::disk('private')->response($archivo->ruta);
    }
}
