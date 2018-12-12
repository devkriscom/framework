<?php
declare (strict_types = 1);

namespace Nusantara;

use Nusantara\Component\Collection\Collection;
use Nusantara\Component\Collection\Configuration;

class Parameter extends Collection
{
    public function set($key, $value)
    {
        $items = &$this->items;
        foreach (explode('.', $key) as $k) {
            $items = &$items[$k];
        }
        $items = $value;

        return true;
    }

    public function get($key, $default = null)
    {
        $items = $this->items;
        foreach (explode('.', $key) as $k) {
            if (! isset($items[$k])) {
                return $default;
            }
            $items = $items[$k];
        }
        return $items;
    }


    public function has($key)
    {
        $items = $this->items;

        foreach (explode('.', $key) as $k) {
            if (! isset($items[$k])) {
                return false;
            }
            $items = $items[$k];
        }

        return true;
    }

    public function load($path, $prefix = null, $override = true)
    {
        $newConfig = Configuration::load($path)->toArray();
        $this->items = array_replace_recursive($this->items, $newConfig);
        return $this;
    }

}
