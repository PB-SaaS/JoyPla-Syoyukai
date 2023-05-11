<?php

namespace JoyPla\Service\Functions;

class FunctionService
{
    public static function arrayToString($array, $key = ',')
    {
        $string = '';
        foreach ($array as $subArray) {
            $string .= implode($key, $subArray) . "\n";
        }

        return $string;
    }

    public static function encodeBase64($string)
    {
        return base64_encode($string);
    }

    public static function calculateTotalWithTax(
        $price = 0,
        $count = 0,
        $taxrate = 0
    ) {
        // Convert price, quantity, and tax rate to integers
        $priceInt = round($price * 100);
        $countInt = round($count * 100);
        $taxRateInt = round($taxrate);

        // Calculate subtotal and tax amount
        $itemTotalInt = ($priceInt * $countInt) / 100;
        $taxAmountInt = ($itemTotalInt * $taxRateInt) / 100;

        // Add subtotal and tax amount, then convert the result back to a decimal and return
        return ($itemTotalInt + $taxAmountInt) / 100 ?: 0;
    }

    public static function arrayToTsvBase64(array $data)
    {
        $tsv = self::arrayToString($data, "\t");
        return self::encodeBase64($tsv);
    }

    public static function arrayToCsvBase64(array $data)
    {
        foreach ($data as $key => $d) {
            $data[$key] = array_map(function ($field) {
                // Escape any double quotes within the field
                $escapedField = str_replace('"', '""', $field);
                // Enclose the field in double quotes
                return '"' . $escapedField . '"';
            }, $d);
        }

        $tsv = self::arrayToString($data, ',');
        return self::encodeBase64($tsv);
    }
}
