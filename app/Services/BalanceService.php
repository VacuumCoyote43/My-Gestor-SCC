<?php

namespace App\Services;

use App\Models\User;
use App\Models\Factura;
use App\Models\Pago;
use App\Models\CargoJugador;
use Carbon\Carbon;

class BalanceService
{
    /**
     * Get monthly balance for a proveedor.
     * Returns total emitted vs total collected (validated payments).
     *
     * @param int $proveedorId
     * @param int|null $year
     * @param int|null $month
     * @return array
     */
    public function getProveedorMonthlyBalance(int $proveedorId, ?int $year = null, ?int $month = null): array
    {
        $year = $year ?? now()->year;
        $month = $month ?? now()->month;

        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = Carbon::create($year, $month, 1)->endOfMonth();

        // Total facturas emitidas en el mes
        $totalEmitido = Factura::where('emisor_type', 'App\\Models\\Proveedor')
            ->where('emisor_id', $proveedorId)
            ->whereBetween('fecha_factura', [$startDate, $endDate])
            ->whereIn('estado', [
                Factura::ESTADO_PENDING_PAYMENT,
                Factura::ESTADO_PAYMENT_REGISTERED,
                Factura::ESTADO_PAID,
            ])
            ->sum('total');

        // Total pagos validados en el mes (de facturas emitidas)
        $facturaIds = Factura::where('emisor_type', 'App\\Models\\Proveedor')
            ->where('emisor_id', $proveedorId)
            ->pluck('id');

        $totalCobrado = Pago::whereIn('factura_id', $facturaIds)
            ->where('estado_pago', Pago::ESTADO_VALIDADO)
            ->whereBetween('validated_at', [$startDate, $endDate])
            ->sum('importe');

        // Pendiente de cobro (todas las facturas no pagadas completamente)
        $pendienteCobro = Factura::where('emisor_type', 'App\\Models\\Proveedor')
            ->where('emisor_id', $proveedorId)
            ->whereIn('estado', [
                Factura::ESTADO_PENDING_PAYMENT,
                Factura::ESTADO_PAYMENT_REGISTERED,
            ])
            ->sum('total');

        // Restar lo ya pagado de las facturas pendientes
        $pagosPendientes = Pago::whereIn('factura_id', function($query) use ($proveedorId) {
            $query->select('id')
                ->from('facturas')
                ->where('emisor_type', 'App\\Models\\Proveedor')
                ->where('emisor_id', $proveedorId)
                ->whereIn('estado', [
                    Factura::ESTADO_PENDING_PAYMENT,
                    Factura::ESTADO_PAYMENT_REGISTERED,
                ]);
        })
        ->where('estado_pago', Pago::ESTADO_VALIDADO)
        ->sum('importe');

        $pendienteCobro -= $pagosPendientes;

        return [
            'periodo' => [
                'year' => $year,
                'month' => $month,
                'mes_nombre' => Carbon::create($year, $month, 1)->locale('es')->monthName,
            ],
            'total_emitido' => round($totalEmitido, 2),
            'total_cobrado' => round($totalCobrado, 2),
            'pendiente_cobro' => round($pendienteCobro, 2),
            'balance' => round($totalCobrado - $totalEmitido, 2),
        ];
    }

    /**
     * Get monthly balance for a gestor club.
     * Returns total paid to proveedores and total charges to jugadores.
     *
     * @param int $clubId
     * @param int|null $year
     * @param int|null $month
     * @return array
     */
    public function getClubMonthlyBalance(int $clubId, ?int $year = null, ?int $month = null): array
    {
        $year = $year ?? now()->year;
        $month = $month ?? now()->month;

        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = Carbon::create($year, $month, 1)->endOfMonth();

        // Total pagado a proveedores (pagos validados de facturas recibidas)
        $facturaIds = Factura::where('receptor_type', 'App\\Models\\Club')
            ->where('receptor_id', $clubId)
            ->pluck('id');

        $totalPagadoProveedores = Pago::whereIn('factura_id', $facturaIds)
            ->where('estado_pago', Pago::ESTADO_VALIDADO)
            ->whereBetween('validated_at', [$startDate, $endDate])
            ->sum('importe');

        // Total cargos emitidos a jugadores en el mes
        $totalCargosEmitidos = CargoJugador::where('club_id', $clubId)
            ->whereBetween('fecha_emision', [$startDate, $endDate])
            ->whereIn('estado', [
                CargoJugador::ESTADO_PENDING_PAYMENT,
                CargoJugador::ESTADO_PAYMENT_REGISTERED,
                CargoJugador::ESTADO_PAID,
            ])
            ->sum('total');

        // Total cobrado de jugadores (pagos validados)
        $cargoIds = CargoJugador::where('club_id', $clubId)->pluck('id');

        $totalCobradoJugadores = Pago::whereIn('cargo_jugador_id', $cargoIds)
            ->where('estado_pago', Pago::ESTADO_VALIDADO)
            ->whereBetween('validated_at', [$startDate, $endDate])
            ->sum('importe');

        // Pendiente de cobro de jugadores
        $pendienteCobroJugadores = CargoJugador::where('club_id', $clubId)
            ->whereIn('estado', [
                CargoJugador::ESTADO_PENDING_PAYMENT,
                CargoJugador::ESTADO_PAYMENT_REGISTERED,
            ])
            ->sum('total');

        $pagosCargos = Pago::whereIn('cargo_jugador_id', $cargoIds)
            ->where('estado_pago', Pago::ESTADO_VALIDADO)
            ->sum('importe');

        $pendienteCobroJugadores -= $pagosCargos;

        return [
            'periodo' => [
                'year' => $year,
                'month' => $month,
                'mes_nombre' => Carbon::create($year, $month, 1)->locale('es')->monthName,
            ],
            'total_pagado_proveedores' => round($totalPagadoProveedores, 2),
            'total_cargos_emitidos' => round($totalCargosEmitidos, 2),
            'total_cobrado_jugadores' => round($totalCobradoJugadores, 2),
            'pendiente_cobro_jugadores' => round($pendienteCobroJugadores, 2),
            'balance' => round($totalCobradoJugadores - $totalPagadoProveedores, 2),
        ];
    }

    /**
     * Get total debt for a jugador (all unpaid or partially paid cargos).
     *
     * @param int $jugadorId
     * @return array
     */
    public function getJugadorDeuda(int $jugadorId): array
    {
        $cargos = CargoJugador::where('user_id_jugador', $jugadorId)
            ->whereIn('estado', [
                CargoJugador::ESTADO_PENDING_PAYMENT,
                CargoJugador::ESTADO_PAYMENT_REGISTERED,
            ])
            ->with(['pagos' => function($query) {
                $query->where('estado_pago', Pago::ESTADO_VALIDADO);
            }])
            ->get();

        $totalDeuda = 0;
        $detallesCargos = [];

        foreach ($cargos as $cargo) {
            $totalPagado = $cargo->pagos->sum('importe');
            $pendiente = $cargo->total - $totalPagado;

            if ($pendiente > 0) {
                $totalDeuda += $pendiente;
                $detallesCargos[] = [
                    'cargo_id' => $cargo->id,
                    'club' => $cargo->club->nombre,
                    'total' => $cargo->total,
                    'pagado' => $totalPagado,
                    'pendiente' => $pendiente,
                    'fecha_emision' => $cargo->fecha_emision->format('d/m/Y'),
                    'fecha_vencimiento' => $cargo->fecha_vencimiento?->format('d/m/Y'),
                ];
            }
        }

        return [
            'total_deuda' => round($totalDeuda, 2),
            'cantidad_cargos_pendientes' => count($detallesCargos),
            'detalles' => $detallesCargos,
        ];
    }

    /**
     * Get admin dashboard statistics (no amounts, only counters).
     *
     * @return array
     */
    public function getAdminStats(): array
    {
        return [
            'total_usuarios' => User::count(),
            'total_proveedores' => \App\Models\Proveedor::count(),
            'total_clubes' => \App\Models\Club::count(),
            'incidencias_abiertas' => \App\Models\Incidencia::whereIn('estado', [
                \App\Models\Incidencia::ESTADO_ABIERTA,
                \App\Models\Incidencia::ESTADO_EN_PROGRESO,
            ])->count(),
            'facturas_por_estado' => [
                'draft' => Factura::where('estado', Factura::ESTADO_DRAFT)->count(),
                'pending_payment' => Factura::where('estado', Factura::ESTADO_PENDING_PAYMENT)->count(),
                'payment_registered' => Factura::where('estado', Factura::ESTADO_PAYMENT_REGISTERED)->count(),
                'paid' => Factura::where('estado', Factura::ESTADO_PAID)->count(),
                'cancelled' => Factura::where('estado', Factura::ESTADO_CANCELLED)->count(),
            ],
        ];
    }
}
