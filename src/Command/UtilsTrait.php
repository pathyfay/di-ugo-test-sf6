<?php

namespace App\Command;

use DateTime;

trait UtilsTrait
{
    /**
     * @param string $dateString
     * @param string $format
     * @return DateTime|null
     */
    function parseDateToDateTime(string $dateString, string $format = 'j/n/Y'): ?DateTime
    {
        $currentYear = date('Y');
        $dateString = trim($dateString);

        if (preg_match('/^\d{1,2}\/\d{1,2}$/', $dateString)) {
            $dateString .= '/' . $currentYear;
            return DateTime::createFromFormat($format, $dateString);
        }

        if (preg_match('/^\d{1,2}\/\d{1,2}\/\d{4}$/', $dateString)) {
            return DateTime::createFromFormat($format, $dateString);
        }

        if (preg_match('/^\d{1,2}-\d{1,2}$/', $dateString)) {
            $dateString .= '-' . $currentYear;
            return DateTime::createFromFormat($format, $dateString);
        }

        if (preg_match('/^\d{1,2}-\d{1,2}-\d{4}$/', $dateString)) {
            return DateTime::createFromFormat($format, $dateString);
        }

        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateString)) {
            $format = "Y-m-d";
            return DateTime::createFromFormat($format, $dateString);
        }

        if (preg_match('/^\d{4}\/\d{2}\/\d{2}$/', $dateString)) {
            $format = "Y-m-d";
            return DateTime::createFromFormat($format, $dateString);
        }

        if (preg_match('/^\d{1,2}-(janv|févr|mars|avr|mai|juin|juil|août|sept|oct|nov|déc)$/ui', $dateString, $matches)) {
            $dateString .= '-' . $currentYear;
            return DateTime::createFromFormat('j-M-Y', $dateString);
        }

        if (preg_match('/^\d{1,2}-(janv|févr|mars|avr|mai|juin|juil|août|sept|oct|nov|déc)-\d{4}$/ui', $dateString)) {
            return DateTime::createFromFormat('j-M-Y', $dateString);
        }

        return null;
    }

}