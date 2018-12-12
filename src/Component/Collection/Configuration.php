<?php
declare (strict_types = 1);

namespace Nusantara\Component\Collection;

use Symfony\Component\Yaml\Yaml as SymfonyYaml;

class Configuration
{
    public static function parse($path, array $keys = ['config', 'parameter'])
    {
        $result = [];
        if(false !== $path = realpath($path))
        {
            $result = self::isFile($path) ? self::loadFile($path) : self::loadDir($path, $keys);
        }
        return $result;
    }

    public static function load($path, array $keys = ['config', 'parameter'])
    {
        return self::replace(self::parse($path, $keys));
    }


    public static function replace(array $collection = [])
    {
        $replacer = new Collection($collection);

        return new Collection(self::array_map_recrusive(function($item) use($replacer) {
            
            if(!is_string($item))
            {
                return $item;
            }

            preg_match_all('/%(.*)%/', $item, $matches);

            list($orig, $key) = $matches;

            if(count($key) > 0)
            {
                if(null !== $value = $replacer->get($key[0]))
                {
                    return preg_replace('/%('.$key[0].')%/', $value, $item);
                } else {
                    return $item;
                }

            } else {
                return $item;
            }

        }, $collection));

    }

    public static function array_map_recrusive($callback, $input) {
        $output = array();
        foreach ($input as $key => $data) {
            if (is_array($data)) {
                $output[$key] = self::array_map_recrusive($callback, $data);
            } else {
                $output[$key] = $callback($data);
            }
        }
        return $output;
    }

    public static function loadDir($path, $keys)
    {
        $dirs = self::scanFiles($path);
        asort($dirs);
        return self::loadFileRecrusive($dirs, $keys);
    }

    public static function loadFileRecrusive($files, array $keys = [])
    {
        $config = array();
        array_walk($files, function($file, $mainKey) use (&$config, $keys) {
            if(is_array($file))
            {
                $config[$mainKey] = self::loadFileRecrusive($file);
            } else {
                if(array_key_exists($mainKey, array_flip($keys)))
                {
                    $config = array_merge($config, self::loadFile($file));
                } else {
                    $config[$mainKey] = self::loadFile($file);
                }

            }
        });

        return $config;
    }


    public static function loadFile(string $file, $flags = 0)
    {
        try {

            if(self::last(strtolower($file), '.yml') || self::last(strtolower($file), '.yaml')) {
                return self::parseYaml($file, $flags);
            } elseif (self::last(strtolower($file), '.xml'))
            {
                return self::parseXml($file, $flags);
            } elseif (self::last(strtolower($file), '.php')) {
                return self::parsePhp($file, $flags);
            }

            throw new \Exception(sprintf("%s need to be 'yml, xml, php'  file"));

        } catch (\Exception $exception) {
            throw new Exception();
        }
    }

    public static function parseYaml($contents)
    {
        if (!is_string($contents)) {
            throw new InvalidFileException(self::context . ' does not return a valid array');
        }

        if (is_file($contents) && is_readable($contents)) {
            $dir = realpath(dirname($contents));
            $contents = file_get_contents($contents);
            $contents = strtr($contents, array(
                '___DIR___' => '__DIR__',
                '__DIR__' => $dir,
            ));
        }
        return SymfonyYaml::parse($contents);
    }


    public static function parsePhp($file, $flags)
    {
        $contents = include $file;

        if (gettype($contents) != 'array') {
            $contents = [];
        }

        return $contents;
    }

    public static function parseXml($file, $flags)
    {
        $parsed = @simplexml_load_file($file);

        if (! $parsed) {
            throw new InvalidFileException('Unable to parse invalid XML file at ' . self::context);
        }

        return json_decode(json_encode($parsed), true);
    }

    public static function last($haystack, $needles)
    {
        foreach ((array) $needles as $needle) {
            if (substr($haystack, -strlen($needle)) === (string) $needle) {
                return true;
            }
        }
        return false;
    }

    public static function scanFiles($dir)
    {
        $files = array_filter(scandir($dir), function($item) use ($dir) {
            if ($item != "." && $item != "..") {
                return true;
            }
        });

        $result = array();
        array_walk($files, function($item) use ($dir, &$result) {
            $path = $dir.DIRECTORY_SEPARATOR.$item;

            if(is_dir($path))
            {
                $result[self::makeKey($path)] = self::scanFiles($path);

            } else {

                $result[self::makeKey($path)] = $path;

            }
        });

        return $result;
    }

    public static function makeKey($key)
    {
        return is_string($key) && file_exists(trim($key)) ? preg_replace('/\.[^.]+$/', '', basename($key)): $key;
    }

    public static function isFile($path)
    {
        return is_string($path) && file_exists($path) && is_file($path);
    }
}
