<?php
if (!function_exists('currency_options')) {
    function currency_options(): array
    {
        return [
            'NIO' => 'Córdobas',
            'USD' => 'Dólares',
        ];
    }
}

if (!function_exists('currency_symbol')) {
    function currency_symbol(?string $currency): string
    {
        $currency = strtoupper($currency ?? '');
        return $currency === 'USD' ? '$' : 'C$';
    }
}

if (!function_exists('currency_name')) {
    function currency_name(?string $currency): string
    {
        $options = currency_options();
        $currency = strtoupper($currency ?? '');
        return $options[$currency] ?? $options['NIO'];
    }
}

if (!function_exists('format_currency')) {
    function format_currency($amount, ?string $currency, bool $withSymbol = true): string
    {
        $numeric = is_numeric($amount) ? (float) $amount : 0.0;
        $formatted = number_format($numeric, 2);
        if (!$withSymbol) {
            return $formatted;
        }
        return currency_symbol($currency) . ' ' . $formatted;
    }
}
