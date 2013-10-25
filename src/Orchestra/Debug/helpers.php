<?php namespace Orchestra\Debug;


/**
 * Replace a given value in the string sequentially with an array.
 *
 * @param  string  $search
 * @param  array  $replace
 * @param  string  $subject
 * @return string
 */
function str_replace_array($search, array $replace, $subject)
{
    foreach ($replace as $value) {
        $subject = preg_replace('/'.$search.'/', $value, $subject, 1);
    }

    return $subject;
}
