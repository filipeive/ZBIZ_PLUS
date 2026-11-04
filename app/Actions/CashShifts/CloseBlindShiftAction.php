<?php

namespace App\Actions\CashShifts;

use App\Models\CashShift;
use Illuminate\Support\Facades\DB;

class CloseBlindShiftAction
{
    /**
     * Executa o Fecho Cego de Caixa de forma atómica e idempotente (com lockForUpdate).
     *
     * @return array{already_closed: bool, shift: CashShift}
     */
    public function execute(CashShift $shift, float $actualCash, ?string $notes = null): array
    {
        return DB::transaction(function () use ($shift, $actualCash, $notes): array {
            /** @var CashShift $lockedShift */
            $lockedShift = CashShift::withoutGlobalScopes()
                ->where('tenant_id', $shift->tenant_id)
                ->whereKey($shift->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedShift->status === 'closed') {
                return [
                    'already_closed' => true,
                    'shift'          => $lockedShift,
                ];
            }

            $expectedCash = (float) $lockedShift->expected_cash;
            $declaredCash = round($actualCash, 2);
            $diff = round($declaredCash - $expectedCash, 2);

            $mergedNotes = !empty($notes)
                ? ($lockedShift->notes ? $lockedShift->notes . ' | Fecho: ' . $notes : $notes)
                : $lockedShift->notes;

            $lockedShift->update([
                'closed_at'              => now(),
                'closing_balance_system' => $expectedCash,
                'closing_balance_actual' => $declaredCash,
                'difference'             => $diff,
                'status'                 => 'closed',
                'notes'                  => $mergedNotes,
            ]);

            return [
                'already_closed' => false,
                'shift'          => $lockedShift,
            ];
        });
    }
}
