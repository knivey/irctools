<?php
namespace knivey\irctools;

/* general functions in here */


/**
 * needed because some mircart files are UTF-16LE
 */
function loadartfile(string $file): array {
    $cont = file_get_contents($file);
    //php apparently sucked at its detection so just checking this manually
    if(strlen($cont) > 1 && ($cont[0] == "\xFF" && $cont[1] == "\xFE")) {
        //UTF-16LE is best bet then fallback to the auto
        if(mb_check_encoding($cont, "UTF-16LE")) {
            $cont = mb_convert_encoding($cont, "UTF-8", "UTF-16LE");
        } else {
            $cont = mb_convert_encoding($cont, "UTF-8");
        }
    }
    $cont = str_replace("\r", "\n", $cont);
    return array_filter(explode("\n", $cont));
}

function stripcodes(string $text, $color = true, $reset = true): string {
    $text = str_replace("\x02", "", $text);
    $text = str_replace("\x1D", "", $text);
    $text = str_replace("\x1F", "", $text);
    $text = str_replace("\x1E", "", $text);
    $text = str_replace("\x11", "", $text);
    $text = str_replace("\x16", "", $text);
    if($reset)
        $text = str_replace("\x0F", "", $text);
    if(!$color)
        return $text;
    $colorRegex = "/\x03(\d?\d?)(,\d\d?)?/";
    return preg_replace($colorRegex, '', $text);
}

/**
 * Generate an IRC progress bar using chr(22) codes
 * $n is fill amount, $d is the total size
 * @param number $n
 * @param number $d
 * @return string
 */
function bar_meter($n, $d) {
    $out = chr(22);
    if($d < $n) {
        return "Error: bar overfull";
    }
    if($d < 10) {
        return "Error: bar too small";
    }
    $text = (int)(($n / $d) * 100) . '%';
    $textpos = (($d / 2) - ((strlen($text)) / 2));
    for($i = 0; $i < $d; $i++) {
        if($i < $textpos || $i > ($textpos + strlen($text))) {
            $out .= ' ';
        } else {
            $out .= $text[(int)($i - $textpos)];
        }
        if($i == $n) {
            $out .= chr(22);
        }
    }
    return $out;
}


/**
 * Check if $string matches $mask using ? and * wildcards
 * @param string $mask
 * @param string $string
 * @param bool $ignoreCase
 * @return bool
 */
function pmatch($mask, $string, $ignoreCase = TRUE) {
    $expr = preg_replace_callback ('/[\\\\^$.[\\]|()?*+{}\\-\\/]/', function ($matches) {
        switch ($matches [0]) {
            case '*' :
                return '.*';
            case '?' :
                return '.';
            default :
                return '\\' . $matches [0];
        }
    }, $mask);

    $expr = '/' . $expr . '/';
    if ($ignoreCase) {
        $expr .= 'i';
    }

    return (bool) preg_match($expr, $string);
}

