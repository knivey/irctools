<?php

namespace knivey\irctools;


/**
 * Colors text in a diagonal rainbow shifting colors each line
 * UTF-8 only
 */
function diagRainbow(string $input, $blockSize = null, int $dir = 1): string
{
    $text = explode("\n", $input);
    $out = "";
    //Colors 52 - 63 seems really nice and in proper order
    $startColor = 52;
    foreach ($text as $line) {
        if (mb_strlen($line) < 1)
            continue;
        $bsize = $blockSize ?? (ceil(mb_strlen($line) / 11));
        //perhaps should be exception
        if ($bsize < 1)
            $bsize = 1;
        $blocks = mb_str_split($line, $bsize);
        $curColor = $startColor;
        foreach ($blocks as $block) {
            //Don't need this CURRENTLY but maybe will used colors 1-16 in future? nice to have
            $color = str_pad($curColor, 2, '0', STR_PAD_LEFT);
            $out .= "\x03{$color}{$block}";
            $curColor++;
            if ($curColor > 63)
                $curColor = 52;
        }
        $out .= "\n";
        if ($dir >= 0) {
            $startColor++;
            if ($startColor > 63)
                $startColor = 52;
        } else {
            $startColor--;
            if ($startColor < 52)
                $startColor = 63;
        }
    }
    return $out;
}

