<?php

if (!function_exists('ctr_e')) {
    function ctr_e(mixed $value): string { return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8'); }
    function ctr_money(mixed $cents): string { return number_format(((int) $cents) / 100, 2, ',', '.'); }
    function ctr_date(mixed $value): string { return empty($value) ? '-' : ctr_e(date('d/m/Y H:i', strtotime((string) $value))); }
    function ctr_status(mixed $status): string {
        $classes = ['Draft' => 'secondary', 'Active' => 'success', 'Superseded' => 'info', 'Expired' => 'warning', 'Canceled' => 'danger'];
        $status = (string) $status;
        return '<span class="badge badge-' . ($classes[$status] ?? 'secondary') . '">' . ctr_e($status) . '</span>';
    }
}
