<?php
use Illuminate\Support\Facades\Schedule;

Schedule::command('pengingat:auto')->dailyAt('08:00');
