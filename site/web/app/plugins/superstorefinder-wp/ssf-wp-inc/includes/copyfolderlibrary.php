<?php

function ssf_wp_copyr($source, $dest)
{

    if (is_link($source)) {
        return symlink(readlink($source), $dest);
    }


    if (is_file($source)) {
        return copy($source, $dest);
    }


    if (!is_dir($dest)) {
        mkdir($dest, 0755);
    }


    $dir = dir($source);
    while (false !== $entry = $dir->read()) {
        // Skip pointers
        if ($entry == '.' || $entry == '..') {
            continue;
        }


        ssf_wp_copyr("$source/$entry", "$dest/$entry");
    }


    $dir->close();
    return true;
}
?>