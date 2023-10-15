<?php

/**
 * Отладочная функция, которая выводит массив или переменную
 * @param $var mixed
 * @param $isArray bool
 * @return void
 */
function debug(mixed $var, bool $isArray = true): void{
    echo "<pre>";
    if($isArray){
        print_r($var);
    } else {
        var_dump($isArray);
    }
    echo "</pre>";
}