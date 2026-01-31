<?php

if (!function_exists('formatNumber')) {
    /**
     * Formatea un número con separador de miles (.) y decimales (,)
     * Formato: 1.500.000,00
     */
    function formatNumber($number, $decimals = 2)
    {
        if ($number === null) {
            return '0' . ($decimals > 0 ? ',' . str_repeat('0', $decimals) : '');
        }

        return number_format($number, $decimals, ',', '.');
    }
}

if (!function_exists('formatCurrency')) {
    /**
     * Formatea un número como moneda
     * Formato: $1.500.000,00
     */
    function formatCurrency($number, $decimals = 2)
    {
        return '$' . formatNumber($number, $decimals);
    }
}
