<?php

test('core application layouts are available and accessible', function () {
    expect(view()->exists('layouts.app'))->toBeTrue()
        ->and(view()->exists('layouts.auth'))->toBeTrue()
        ->and(view()->exists('layouts.guest'))->toBeTrue();
});
