<?php
use Illuminate\Support\Facades\Schedule;

Schedule::command('pengingat:auto')->dailyAt('08:00');
Schedule::command('jadwal:reminder')->everyMinute()->between('00:01', '23:59');
