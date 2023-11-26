<?php
use Carbon\Carbon;

if (!function_exists('formatDate')) {
    function formatDate($date)
    {
        if ($date !== null) {
            return Carbon::parse($date)->format('d-m-Y H:i');
        } else {
           
            return null; 
        }
    }
}