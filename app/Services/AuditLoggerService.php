<?php

namespace App\Services;

use App\Models\Modificacion;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AuditLoggerService
{
    /**
     * Log a modification to the audit trail.
     *
     * @param Model $model El modelo que se está modificando
     * @param string $accion La acción realizada (created, updated, deleted, emitted, etc.)
     * @param array $cambios Los cambios realizados (antes/después o datos relevantes)
     * @param string|null $comentario Comentario adicional opcional
     * @return Modificacion
     */
    public function log(Model $model, string $accion, array $cambios = [], ?string $comentario = null): Modificacion
    {
        return Modificacion::create([
            'modifiable_type' => get_class($model),
            'modifiable_id' => $model->id,
            'accion' => $accion,
            'cambios' => $cambios,
            'comentario' => $comentario,
            'created_by' => Auth::id(),
        ]);
    }

    /**
     * Log creation of a model.
     */
    public function logCreated(Model $model, ?string $comentario = null): Modificacion
    {
        return $this->log($model, Modificacion::ACCION_CREATED, [
            'created' => $model->toArray(),
        ], $comentario);
    }

    /**
     * Log update of a model.
     */
    public function logUpdated(Model $model, array $original, ?string $comentario = null): Modificacion
    {
        $changes = [];
        foreach ($model->getDirty() as $key => $newValue) {
            $changes[$key] = [
                'before' => $original[$key] ?? null,
                'after' => $newValue,
            ];
        }

        return $this->log($model, Modificacion::ACCION_UPDATED, $changes, $comentario);
    }

    /**
     * Log deletion of a model.
     */
    public function logDeleted(Model $model, ?string $comentario = null): Modificacion
    {
        return $this->log($model, Modificacion::ACCION_DELETED, [
            'deleted' => $model->toArray(),
        ], $comentario);
    }

    /**
     * Log emission of a factura or cargo.
     */
    public function logEmitted(Model $model, ?string $comentario = null): Modificacion
    {
        return $this->log($model, Modificacion::ACCION_EMITTED, [
            'estado_anterior' => 'draft',
            'estado_nuevo' => 'pending_payment',
            'fecha_emision' => now()->toDateTimeString(),
        ], $comentario);
    }

    /**
     * Log payment registration.
     */
    public function logPaymentRegistered(Model $model, float $importe, ?string $comentario = null): Modificacion
    {
        return $this->log($model, Modificacion::ACCION_PAYMENT_REGISTERED, [
            'importe' => $importe,
            'fecha_registro' => now()->toDateTimeString(),
        ], $comentario);
    }

    /**
     * Log payment validation.
     */
    public function logValidated(Model $model, ?string $comentario = null): Modificacion
    {
        return $this->log($model, Modificacion::ACCION_VALIDATED, [
            'validated_at' => now()->toDateTimeString(),
            'validated_by' => Auth::id(),
        ], $comentario);
    }

    /**
     * Log payment rejection.
     */
    public function logRejected(Model $model, ?string $comentario = null): Modificacion
    {
        return $this->log($model, Modificacion::ACCION_REJECTED, [
            'rejected_at' => now()->toDateTimeString(),
            'rejected_by' => Auth::id(),
        ], $comentario);
    }

    /**
     * Log status change.
     */
    public function logStatusChanged(Model $model, string $estadoAnterior, string $estadoNuevo, ?string $comentario = null): Modificacion
    {
        return $this->log($model, Modificacion::ACCION_STATUS_CHANGED, [
            'estado_anterior' => $estadoAnterior,
            'estado_nuevo' => $estadoNuevo,
            'fecha_cambio' => now()->toDateTimeString(),
        ], $comentario);
    }
}
