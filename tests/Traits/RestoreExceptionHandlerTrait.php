<?php

namespace App\Tests\Traits;

trait RestoreExceptionHandlerTrait
{
    /**
     * Restore previous exception handler.
     */
    protected function restoreExceptionHandler(): void
    {
        while (true) {
            $previousHandler = set_exception_handler(static fn() => null);

            restore_exception_handler();

            if ($previousHandler === null) {
                break;
            }

            restore_exception_handler();
        }
    }
}
