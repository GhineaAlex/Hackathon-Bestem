<?php

namespace engine;

if (!defined('ALLOW'))
    exit;

class functions {
    
    static function __protected_string($string) {
        return str_replace("}", "", str_replace("{", "", filter_var(htmlspecialchars($string, ENT_QUOTES, 'UTF-8'), FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH)));
    }
    
    static function __protected_string_header($string, $type) {
        return str_replace("}", "", str_replace("{", "", filter_var(htmlspecialchars(strip_tags(filter_input($type, $string)), ENT_QUOTES, 'UTF-8'), FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH)));
    }
    
    static function __protected_header_bbcode($string, $type) {
        return self::bbcode_to_html(strip_tags(filter_input($type, $string)));
    }
    
    static function __remove_square_bracket($string) {
        $string = substr($string, strpos($string, '[') + strlen('['));
        return substr($string, 0, strpos($string, ']'));
        ;
    }
    
    static function __redirect($path) {
        header("Location: " . URL . $path);
    }
    
    static function __date_transalte($data) {
        if (!is_numeric($data))
            return "None";
        return ($data == 0) ? "None" : gmdate("H:i:s | d/m/Y", $data);
    }
    
    static function __array_sort($array, $on, $order = SORT_ASC) {
        $new_array      = array();
        $sortable_array = array();
        
        if (count($array) > 0) {
            foreach ($array as $k => $v) {
                if (is_array($v)) {
                    foreach ($v as $k2 => $v2) {
                        if ($k2 == $on) {
                            $sortable_array[$k] = $v2;
                        }
                    }
                } else {
                    $sortable_array[$k] = $v;
                }
            }
            
            switch ($order) {
                case SORT_ASC:
                    asort($sortable_array);
                    break;
                case SORT_DESC:
                    arsort($sortable_array);
                    break;
            }
            
            foreach ($sortable_array as $k => $v) {
                $new_array[$k] = $array[$k];
            }
        }
        return $new_array;
    }
    
    static function str_replace_first($from, $to, $subject) {
        $from = '/' . preg_quote($from, '/') . '/';
        return preg_replace($from, $to, $subject, 1);
    }
    
    static function __unique_multidim_array($array, $key) {
        $temp_array = array();
        $i          = 0;
        $key_array  = array();
        
        foreach ($array as $val) {
            if (!in_array($val[$key], $key_array)) {
                $key_array[$i]  = $val[$key];
                $temp_array[$i] = $val;
            }
            $i++;
        }
        return $temp_array;
    }
    
    static function js_str($s) {
        return '"' . addcslashes($s, "\0..\37\"\\") . '"';
    }
    
    static function js_array($array) {
        $temp = array_map('js_str', $array);
        return '[' . implode(',', $temp) . ']';
    }
    
}