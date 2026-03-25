<?php

namespace Tests\Traits;

use Laravel\Sanctum\Sanctum;

trait ActsAsSanctumOnce
{
    protected bool $sanctumOneShot = false;
    protected ?string $sanctumOneShotGuard = null;

    /**
     * Authenticate the given user for the next HTTP request only.
     */
    public function actingAsOnce($user, array $abilities = ['*'], string $guard = 'sanctum')
    {
        Sanctum::actingAs($user, $abilities, $guard);
        $this->sanctumOneShot = true;
        $this->sanctumOneShotGuard = $guard;
        return $this;
    }

    /**
     * Clear one-shot auth state (called from TestCase::call after request).
     */
    public function clearSanctumOneShot(): void
    {
        if ($this->sanctumOneShot) {
            try {
                if ($this->sanctumOneShotGuard) {
                    app('auth')->guard($this->sanctumOneShotGuard)->setUser(null);
                }
            } catch (\Throwable $_) {
                // ignore
            }
            $this->sanctumOneShot = false;
            $this->sanctumOneShotGuard = null;
        }
    }
}
