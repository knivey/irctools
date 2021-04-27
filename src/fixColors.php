<?php

namespace knivey\irctools;

/**
 * Some clients don't like \x03,BG so we make sure FG is always included
 * UTF-8 only
 * @param string $line String to fix
 * @return string Fixed string
 */
function fixColors(string $line): string
{
    if (strpos($line, "\x03,") === false) {
        return $line;
    }

    $out = '';
    $colorRegex = "/^\x03(\d?\d?)(,\d\d?)?/";
    $fg = '1';
    $bg = '';

    for ($i = 0; $i < strlen($line); $i++) {
        $rem = substr($line, $i);
        if (preg_match($colorRegex, $rem, $m)) {
            $i += strlen($m[0]) - 1;
            $out .= "\x03";
            if ((!isset($m[1]) || $m[1] == '') && !isset($m[2])) {
                $fg = '1';
                $bg = '';
                continue;
            }
            if (isset($m[1]) && $m[1] != "")
                $fg = $m[1];
            if (isset($m[2])) {
                $m[2] = substr($m[2], 1);
                $bg = $m[2];
            }
            if ($bg != '')
                $out .= "$fg,$bg";
            else
                $out .= "$fg";
        } else {
            $out .= $line[$i];
        }
    }
    return $out;
}

