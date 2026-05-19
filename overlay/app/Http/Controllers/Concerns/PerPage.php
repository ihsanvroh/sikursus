<?php
namespace App\Http\Controllers\Concerns;

trait PerPage {
    protected function perPage(int $default = 25, array $allowed = [10,25,50,100]): int {
        $pp = (int) request('per_page', $default);
        return in_array($pp, $allowed, true) ? $pp : $default;
    }
}
