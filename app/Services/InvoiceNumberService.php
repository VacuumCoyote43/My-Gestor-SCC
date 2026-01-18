<?php

namespace App\Services;

use App\Models\Factura;
use App\Models\Proveedor;

class InvoiceNumberService
{
    /**
     * Generate the next invoice number for a proveedor and serie.
     * Format: SERIE-NNNNNN (e.g., FAC-000001)
     *
     * @param int $proveedorId
     * @param string $serie
     * @return string
     */
    public function nextNumberForProveedor(int $proveedorId, string $serie = 'FAC'): string
    {
        // Obtener el último número de factura para este proveedor y serie
        $lastFactura = Factura::where('emisor_type', 'App\\Models\\Proveedor')
            ->where('emisor_id', $proveedorId)
            ->where('serie', $serie)
            ->orderBy('numero', 'desc')
            ->first();

        if (!$lastFactura) {
            // Primera factura de esta serie
            return $serie . '-000001';
        }

        // Extraer el número de la última factura
        $parts = explode('-', $lastFactura->numero);
        $lastNumber = isset($parts[1]) ? intval($parts[1]) : 0;

        // Incrementar y formatear
        $nextNumber = $lastNumber + 1;
        return $serie . '-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Validate if an invoice number is unique for a proveedor.
     *
     * @param int $proveedorId
     * @param string $numero
     * @param int|null $excludeFacturaId
     * @return bool
     */
    public function isNumberUnique(int $proveedorId, string $numero, ?int $excludeFacturaId = null): bool
    {
        $query = Factura::where('emisor_type', 'App\\Models\\Proveedor')
            ->where('emisor_id', $proveedorId)
            ->where('numero', $numero);

        if ($excludeFacturaId) {
            $query->where('id', '!=', $excludeFacturaId);
        }

        return $query->count() === 0;
    }

    /**
     * Generate a serie code based on current year.
     * Format: FAC-YYYY (e.g., FAC-2026)
     *
     * @return string
     */
    public function generateSerieForCurrentYear(): string
    {
        return 'FAC-' . date('Y');
    }

    /**
     * Parse invoice number to extract serie and sequential number.
     *
     * @param string $numero
     * @return array ['serie' => string, 'sequential' => int]
     */
    public function parseInvoiceNumber(string $numero): array
    {
        $parts = explode('-', $numero);
        
        return [
            'serie' => $parts[0] ?? '',
            'sequential' => isset($parts[1]) ? intval($parts[1]) : 0,
        ];
    }
}
