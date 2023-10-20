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



function constructTitle($titleArray):string
{
	$el = $titleArray['title']['element'];
	$title = $titleArray['title']['title'];
	$class = null;
	if ($titleArray['title']['class'] != null) {
		$class = $titleArray['title']['class'];
		$class = "class='$class'";
	}
	return "<$el $class>$title</$el>";
}