<?php

namespace Azuriom\Plugin\Tebexrewards\Services;

use Azuriom\Plugin\Tebexrewards\Models\Transaction;

class GoalProgressService
{
    /**
     * @return array{
     *   enabled:bool,
     *   currency:string,
     *   target:float,
     *   current:float,
     *   total:float,
     *   percent:int,
     *   reached:bool,
     *   message_reached:string,
     *   color_start:string,
     *   color_mid:string,
     *   color_end:string
     * }
     */
    public function get(): array {
        $enabled = (bool) setting('tebexrewards.goal.enabled', true);

        $currency = (string) setting('tebexrewards.goal.currency', '€');
        $targetBase = (float) setting('tebexrewards.goal.target', 3000);
        $targetBase = max(0.0, $targetBase);

        $resetEnabled = (bool) setting('tebexrewards.goal.reset_enabled', false);
        $increment = (float) setting('tebexrewards.goal.reset_increment', 0);
        $increment = max(0.0, $increment);

        $total = (float) Transaction::totalDonationsAllTime();

        [$target, $current] = $this->computeCycle($total, $targetBase, $resetEnabled, $increment);

        $percent = $target > 0 ? (int) floor(min(100, ($current / $target) * 100)) : 0;
        $reached = $target > 0 && $current >= $target;

        return [
            'enabled' => $enabled,
            'currency' => $currency,
            'target' => $target,
            'current' => $current,
            'total' => $total,
            'percent' => $percent,
            'reached' => $reached,
            'message_reached' => (string) setting(
                'tebexrewards.goal.message_reached',
                trans('tebexrewards::messages.defaults.goal_message_reached')
            ),
            'color_start' => (string) setting('tebexrewards.goal.color_start', '#00c853'),
            'color_mid' => (string) setting('tebexrewards.goal.color_mid', '#ffd600'),
            'color_end' => (string) setting('tebexrewards.goal.color_end', '#ff3d00'),
        ];
    }

    /**
     * @return array{0:float,1:float} target,current
     */
    private function computeCycle(float $total, float $targetBase, bool $resetEnabled, float $increment): array {

        if ($targetBase <= 0) {
            return [0.0, 0.0];
        }

        if (! $resetEnabled) {
            return [$targetBase, min($total, $targetBase)];
        }

        if ($increment <= 0) {
            $current = fmod($total, $targetBase);
            $current = $current === 0.0 && $total > 0 ? $targetBase : $current;

            return [$targetBase, min($current, $targetBase)];
        }

        $remaining = $total;
        $target = $targetBase;

        while ($remaining >= $target && $target > 0) {
            $remaining -= $target;
            $target += $increment;
            if ($target > 100000000) {
                break;
            }
        }

        return [$target, min($remaining, $target)];
    }
}

