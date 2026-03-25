<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
        use CreatesApplication, \Tests\Traits\ActsAsSanctumOnce;

    /**
     * When tests call the persistent actingAs(..., 'sanctum'), mark it so
     * the pre-request cleanup doesn't wipe the authentication immediately.
     *
     * We override and forward to the parent's `actingAs` implementation.
     */
    protected bool $sanctumPersistentAuth = false;

    public function actingAs($user, $guard = null)
    {
        try {
            $this->sanctumPersistentAuth = ($guard === 'sanctum' || ($guard === null && config('auth.defaults.guard') === 'sanctum'));
        } catch (\Throwable $_) {
            $this->sanctumPersistentAuth = false;
        }

        return parent::actingAs($user, $guard);
    }

    public function call($method, $uri, $parameters = [], $cookies = [], $files = [], $server = [], $content = null)
    {
        // Pre-request: ensure no leftover authenticated user from previous tests
        if (app()->runningUnitTests()) {
            try {
                    // but preserve the one-shot Sanctum auth if the test just called `actingAsOnce()`
                    if ((property_exists($this, 'sanctumOneShot') && $this->sanctumOneShot) || (property_exists($this, 'sanctumPersistentAuth') && $this->sanctumPersistentAuth)) {
                        // a one-shot or persistent auth was set for this upcoming request; skip pre-clear
                    } else {
                $guards = array_keys(config('auth.guards', []));
                $guards[] = 'sanctum';

                foreach (array_unique($guards) as $g) {
                    try {
                        app('auth')->guard($g)->setUser(null);
                    } catch (\Throwable $_) {
                        // ignore guards that don't exist
                    }
                }

                try {
                    auth()->setUser(null);
                } catch (\Throwable $_) {
                    // ignore
                }

                try {
                    app('auth')->shouldUse(config('auth.defaults.guard'));
                } catch (\Throwable $_) {
                    // ignore
                }

                try {
                    if (! (property_exists($this, 'sanctumPersistentAuth') && $this->sanctumPersistentAuth)) {
                        app()->forgetInstance('auth');
                    }
                } catch (\Throwable $_) {
                    // ignore
                }

                // clear any one-shot actingAs state before the request
                try {
                    $this->clearSanctumOneShot();
                } catch (\Throwable $_) {
                    // ignore
                }
                // keep persistent auth marker until the test instance ends
                    }
            } catch (\Throwable $e) {
                // ignore
            }
        }

        $response = parent::call($method, $uri, $parameters, $cookies, $files, $server, $content);

        // Post-request: clear auth state unless a persistent sanctum auth was set by the test
        if (app()->runningUnitTests()) {
            try {
                $hasOneShot = property_exists($this, 'sanctumOneShot') && $this->sanctumOneShot;
                $hasPersistent = property_exists($this, 'sanctumPersistentAuth') && $this->sanctumPersistentAuth;

                // If persistent auth was set by the test, keep it intact across requests.
                if (! $hasPersistent) {
                    $guards = array_keys(config('auth.guards', []));
                    $guards[] = 'sanctum';

                    foreach (array_unique($guards) as $g) {
                        try {
                            app('auth')->guard($g)->setUser(null);
                        } catch (\Throwable $_) {
                            // ignore guards that don't exist
                        }
                    }

                    try {
                        auth()->setUser(null);
                    } catch (\Throwable $_) {
                        // ignore
                    }

                    try {
                        app('auth')->shouldUse(config('auth.defaults.guard'));
                    } catch (\Throwable $_) {
                        // ignore
                    }

                    try {
                        app()->forgetInstance('auth');
                    } catch (\Throwable $_) {
                        // ignore
                    }
                }

                // Always clear any one-shot actingAs state (it's scoped to the last request)
                try {
                    $this->clearSanctumOneShot();
                } catch (\Throwable $_) {
                    // ignore
                }
            } catch (\Throwable $e) {
                // ignore
            }
        }

        return $response;
    }
}
