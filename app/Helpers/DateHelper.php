<?php

namespace App\Helpers;

use Carbon\Carbon;

class DateHelper
{
    /**
     * Calcula a data de entrega considerando dias úteis (excluindo finais de semana)
     * 
     * @param Carbon $startDate Data inicial
     * @param int $businessDays Número de dias úteis
     * @return Carbon Data de entrega
     */
    public static function calculateDeliveryDate(Carbon $startDate, int $businessDays = 15): Carbon
    {
        $date = $startDate->copy();
        $addedDays = 0;

        while ($addedDays < $businessDays) {
            $date->addDay();
            
            // Pular finais de semana (sábado = 6, domingo = 0)
            if ($date->dayOfWeek !== Carbon::SATURDAY && $date->dayOfWeek !== Carbon::SUNDAY) {
                $addedDays++;
            }
        }

        return $date;
    }

    /**
     * Verifica se uma data é dia útil
     * 
     * @param Carbon $date
     * @return bool
     */
    public static function isBusinessDay(Carbon $date): bool
    {
        return $date->dayOfWeek !== Carbon::SATURDAY && $date->dayOfWeek !== Carbon::SUNDAY;
    }

    /**
     * Conta quantos dias úteis existem entre duas datas
     * 
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return int
     */
    public static function countBusinessDays(Carbon $startDate, Carbon $endDate): int
    {
        $days = 0;
        $current = $startDate->copy();

        while ($current->lte($endDate)) {
            if (self::isBusinessDay($current)) {
                $days++;
            }
            $current->addDay();
        }

        return $days;
    }
}
