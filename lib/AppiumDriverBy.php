<?php

namespace Facebook\WebDriver;

/**
 * The basic 8 mechanisms supported by webdriver to locate a web element.
 * ie. 'class name', 'css selector', 'id', 'name', 'link text',
 *     'partial link text', 'tag name' and 'xpath'.
 *
 * @see WebDriver::findElement, WebDriverElement::findElement
 */
class AppiumDriverBy extends WebDriverBy
{
    public static $baseId = '';

    public static function setBaseId($id)
    {
        static::$baseId = $id;
    }

    public static function accessibilityId($id)
    {
        return new static('accessibility id', $id);
    }

    public static function text($text)
    {
        return new static('-android uiautomator', 'new UiSelector().text("' . $text . '")');
    }


    /**
     * Locates elements whose class name contains the search value; compound class
     * names are not permitted.
     *
     * @param string $class_name
     * @return static
     */
    public static function className($class_name)
    {
        return new static('class name', $class_name);
    }

    /**
     * Locates elements whose ID attribute matches the search value.
     *
     * @param string $id
     * @return static
     */
    public static function id($id)
    {
        return new static('id', static::$baseId . $id);
    }

    /**
     * Locates elements whose NAME attribute matches the search value.
     *
     * @param string $name
     * @return static
     */
    public static function name($name)
    {
        return new static('name', $name);
    }


    /**
     * Locates elements matching an XPath expression.
     *
     * @param string $xpath
     * @return static
     */
    public static function xpath($xpath)
    {
        return new static('xpath', $xpath);
    }
}
