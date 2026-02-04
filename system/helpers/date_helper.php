<?php

declare(strict_types=1);

if (!function_exists('month_add'))
{
    function month_add($date = null, $value = 0, $format = 'Y-m-d'): string
    {
        $date = is_null($date) ? date('Y-m-d') : date($date);
        $dateObject = new DateTime($date);

        if($value == 0)
            return $dateObject->format($format);

        elseif($value > 0)
            return date_add($dateObject, new DateInterval("P{$value}M"))->format($format);

        elseif($value < 0)
            return date_sub($dateObject, new DateInterval("P{$value}M"))->format($format);

        return '';
    }
}

if(!function_exists('next_month'))
{
    function next_month($date = null, $value = 1, $format = 'Y-m-d'): string
    {
        return month_add($date, $value, $format);
    }
}

if (!function_exists('previous_month'))
{
    function previous_month($date = null, $value = 1, $format = 'Y-m-d'): string
    {
        return month_add($date, -$value, $format);
    }
}

if (!function_exists('period_add'))
{
    function period_add($period, $addition = 0): string
    {
        $date = is_null($period) ? date_create() : date_create_from_format('Ym', $period);
        if($addition == 0) {
            return $date->format('Ym');
        }
        elseif($addition > 0) {
            return date_add($date, new DateInterval("P{$addition}M"))->format('Ym');
        }
        elseif($addition < 0) {
            return date_sub($date, new DateInterval("P{$addition}M"))->format('Ym');
        }
        else {
            return '';
        }
    }
}

if (!function_exists('previous_period'))
{
    function previous_period($period = null, $value = 1): string
    {
        return period_add($period, -$value);
    }
}

if (!function_exists('next_period'))
{
    function next_period($period = null, $value = 1): string
    {
        return period_add($period, $value);
    }
}