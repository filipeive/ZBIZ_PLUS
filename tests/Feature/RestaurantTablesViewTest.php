<?php

test('restaurant table view and routes are available', function () {
    expect(view()->exists('restaurant.tables.index'))->toBeTrue()
        ->and(route('restaurant.tables.index'))->toContain('/restaurant/tables');
});