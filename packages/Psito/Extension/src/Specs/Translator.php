<?php

namespace Psito\Extension\Specs;

class Translator
{
    /**
     * @param string $path
     * @param mixed $default
     * @return mixed
     */
    public static function translate($path, $default = false)
    {
        $string = app('translator')->get($path);

        if ($default !== false) {
            if ($string == $path) {
                return $default;
            }
        }

        return $string;
    }

    /**
     * @param string $namespace
     * @return static
     */
    public static function make($namespace)
    {
        return new static($namespace);
    }

    /**
     * @var string
     */
    protected $namespace;

    /**
     * @param string $namespace
     */
    public function __construct($namespace)
    {
        $this->namespace = $namespace;
    }

    /**
     * @param string $path
     * @param mixed $default
     * @return mixed
     */
    public function get($path, $default = false)
    {
        $value = static::translate($this->fullpath($path), $default);

        if (is_string($value)) {
            $value = $this->resolve($value);
        } elseif (is_array($value)) {
            foreach ($value as &$subValue) {
                $subValue = $this->resolve($subValue);
            }
        }

        return $value;
    }

    /**
     * @param string $string
     * @param string $default
     * @return string
     */
    public function resolve($string, $default = false)
    {
        if (strpos($string, '@') !== false) {
            list(, $vocabularyPath) = explode('@', $string, 2);

            $string = static::translate($this->fullpath('vocabulary.'.$vocabularyPath), $default);
        }

        return $string;
    }

    /**
     * @param string $path
     * @return string
     */
    private function fullpath($path)
    {
        return $this->namespace ? $this->namespace.'::'.$path : $path;
    }
}
