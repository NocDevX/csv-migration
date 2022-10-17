<?php

function dump($arg)
{
    if (is_array($arg)) {
        foreach ($arg as $key => $val) {
            if (is_array($val)) {
                dump($val);
            } else {
                echo "<pre> {$key} => {$val} </pre>";
            }
        }
    }

    if (is_string($arg)) {
        echo "<pre>{$arg}</pre>";
    }

    if (!is_string($arg) && !is_array($arg)) {
        var_dump($arg);
    }
}
