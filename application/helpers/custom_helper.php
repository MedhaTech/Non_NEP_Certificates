<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('convert_number_to_words')) {
    function convert_number_to_words($number) {
        // Array of words for numbers 0 to 19
        $words = array('zero', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine', 'ten', 'eleven', 'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen', 'seventeen', 'eighteen', 'nineteen');
        
        // Array of words for tens
        $words_tens = array('', '', 'twenty', 'thirty', 'forty', 'fifty', 'sixty', 'seventy', 'eighty', 'ninety');
        
        // Array of scale units for Indian numbering system
        $words_scale = array('', 'thousand', 'lakh');

        // Start conversion
        if ($number < 20) {
            return ucfirst($words[$number]);
        } elseif ($number < 100) {
            return ucfirst($words_tens[(int)($number / 10)] . (($number % 10 != 0) ? ' ' . $words[$number % 10] : ''));
        } elseif ($number < 1000) {
            return ucfirst($words[(int)($number / 100)] . ' hundred' . (($number % 100 != 0) ? ' ' . convert_number_to_words($number % 100) : ''));
        } elseif ($number < 100000) {
            return ucfirst(convert_number_to_words((int)($number / 1000)) . ' ' . $words_scale[1] . (($number % 1000 != 0) ? ' ' . convert_number_to_words($number % 1000) : ''));
        } elseif ($number < 10000000) {
            return ucfirst(convert_number_to_words((int)($number / 100000)) . ' ' . $words_scale[2] . (($number % 100000 != 0) ? ' ' . convert_number_to_words($number % 100000) : ''));
        }
    }
}
