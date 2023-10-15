<?php

/**
 * Основной класс темы, основанный на анти-паттерне Singleton
 */
class SpaceCoreTheme
{

    /**
     * @var mixed|null
     */
    protected static mixed $instance = null;
    /**
     * @var mixed
     */
    public static mixed $settings;


    /**
     * Получаем настройки темы
     */
    private function __construct()
    {
        self::$settings = get_field("system", "options");
    }

    /**
     * Возвращает класс темы, если он объявлен
     * @return object
     */
    public static function getInstance():object
    {
        if (is_null(self::$instance)) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    /**
     * Возвращает класс темы, если он объявлен
     * @return object|null
     */
    public function init():object
    {
        return self::$instance;
    }

}