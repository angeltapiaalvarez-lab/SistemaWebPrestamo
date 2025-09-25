<?php

declare(strict_types=1);

if (!function_exists('calcularCuotaFrancesa')) {
    /**
     * Calcula la cuota fija utilizando el método francés de amortización.
     */
    function calcularCuotaFrancesa(float $principal, float $tasaPeriodo, int $cuotas, int $precision = 2): float
    {
        if ($principal <= 0 || $cuotas <= 0) {
            return 0.0;
        }

        if ($tasaPeriodo <= 0) {
            return round($principal / $cuotas, $precision);
        }

        $factor = pow(1 + $tasaPeriodo, $cuotas);
        $pago = $principal * $tasaPeriodo * $factor / ($factor - 1);

        return round($pago, $precision);
    }
}

if (!function_exists('generarTablaAmortizacionFrancesa')) {
    /**
     * Genera el desglose de cuotas con el método francés de amortización.
     *
     * @return array{pago: float, tabla: array<int, array<string, float|int>>}
     */
    function generarTablaAmortizacionFrancesa(float $principal, float $tasaPeriodo, int $cuotas, int $precision = 2): array
    {
        $principal = max($principal, 0.0);
        $cuotas = max($cuotas, 0);

        if ($principal <= 0 || $cuotas === 0) {
            return [
                'pago' => 0.0,
                'tabla' => [],
            ];
        }

        $pago = ($tasaPeriodo > 0)
            ? $principal * $tasaPeriodo / (1 - pow(1 + $tasaPeriodo, -$cuotas))
            : $principal / $cuotas;

        $pagoRedondeado = round($pago, $precision);
        $saldo = $principal;
        $tabla = [];
        $capitalAcumulado = 0.0;

        for ($numero = 1; $numero <= $cuotas; $numero++) {
            $interes = ($tasaPeriodo > 0) ? $saldo * $tasaPeriodo : 0.0;
            $capital = $pago - $interes;

            $interesRedondeado = round($interes, $precision);
            $capitalRedondeado = round($pagoRedondeado - $interesRedondeado, $precision);

            if ($capitalRedondeado < 0) {
                $capitalRedondeado = 0.0;
            }

            if ($capitalRedondeado > $saldo) {
                $capitalRedondeado = $saldo;
            }

            if ($numero === $cuotas) {
                $capitalRedondeado = round($principal - $capitalAcumulado, $precision);
                if ($capitalRedondeado < 0) {
                    $capitalRedondeado = 0.0;
                }
                $interesRedondeado = round($pagoRedondeado - $capitalRedondeado, $precision);
                $pagoCuota = round($capitalRedondeado + $interesRedondeado, $precision);
                $saldoRestante = 0.0;
            } else {
                $pagoCuota = $pagoRedondeado;
                $saldoRestante = round(max($saldo - $capitalRedondeado, 0.0), $precision);
            }

            $capitalAcumulado += $capitalRedondeado;
            $saldo = max($saldo - $capitalRedondeado, 0.0);

            $tabla[] = [
                'cuota' => $numero,
                'pago' => $pagoCuota,
                'interes' => $interesRedondeado,
                'capital' => $capitalRedondeado,
                'saldo' => $saldoRestante,
            ];
        }

        return [
            'pago' => $pagoRedondeado,
            'tabla' => $tabla,
        ];
    }
}
