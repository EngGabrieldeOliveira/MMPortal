<?php

namespace App\Observers;

use App\Services\AuditService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class AuditableObserver
{
    private array $original = [];

    public function __construct(private readonly AuditService $audit) {}

    public function created(Model $model): void
    {
        $this->audit->record($model, 'created', [], $this->withoutTimestamps($model->getAttributes()));
    }

    public function updating(Model $model): void
    {
        $this->original[spl_object_id($model)] = $model->getRawOriginal();
    }

    public function updated(Model $model): void
    {
        $changes = $this->withoutTimestamps($model->getChanges());
        if ($changes === []) {
            return;
        }
        $original = $this->original[spl_object_id($model)] ?? [];
        $this->audit->record($model, 'updated', Arr::only($original, array_keys($changes)), $changes);
        unset($this->original[spl_object_id($model)]);
    }

    public function deleted(Model $model): void
    {
        $action = method_exists($model, 'isForceDeleting') && $model->isForceDeleting() ? 'force_deleted' : 'soft_deleted';
        $this->audit->record($model, $action, $this->withoutTimestamps($model->getOriginal()), ['deleted_at' => $model->getAttribute('deleted_at')]);
    }

    public function restored(Model $model): void
    {
        $this->audit->record($model, 'restored', ['deleted_at' => $model->getOriginal('deleted_at')], ['deleted_at' => null]);
    }

    private function withoutTimestamps(array $values): array
    {
        return Arr::except($values, ['created_at', 'updated_at']);
    }
}
