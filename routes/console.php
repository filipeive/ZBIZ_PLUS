<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// Comando de exemplo que vem por default
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Agendador Automático de Sincronização Híbrida (Local -> Nuvem)
Schedule::command('zbiz:sync-push')
    ->everyFiveMinutes()
    ->withoutOverlapping()
    ->runInBackground();

