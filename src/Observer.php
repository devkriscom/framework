<?php
declare (strict_types = 1);

namespace Nusantara;

use Nusantara\Contract\Container;
use Nusantara\Contract\Kernel\Observer as ObserverInterface;
use Nusantara\Contract\Kernel\ObserverExtension;
use Nusantara\Blueprint\Manager;
use Nusantara\Exception\InvalidModuleException;
use Nusantara\Component\Collection\ArrayCollection;
use Nusantara\Component\Collection\Configuration;
use Nusantara\Traits\ContainerAwareTrait;
use Nusantara\Component\Metadata\Reflection;


class Observer implements ObserverInterface {

    use ContainerAwareTrait;

    protected $messages = array();

    protected $observers = [];
    
    protected $sortedObservers = [];

    public function __construct(Container $container)
    {
        $this->container = $container;
    }
    
    public function addObserver($observer, $priority = 100)
    {
        $observer = $this->ensureObserver($observer);

        if(is_string($observer))
        {
            $observer = new $observer();
        }

        $this->observers[$observer::name()][$priority][] = $observer;
        $this->clearSortedObservers($observer::name());
        return $this;
    }
    
    public function removeObserver($message, $observer)
    {
        $this->clearSortedObservers($message);
        $observers = $this->hasObservers($message)
        ? $this->observers[$message]
        : [];
        $filter = function ($registered) use ($observer) {
            return ! $registered->isObserver($observer);
        };
        foreach ($observers as $priority => $collection) {
            $observers[$priority] = array_filter($collection, $filter);
        }
        $this->observers[$message] = $observers;
        return $this;
    }
    
    public function removeAllObservers($message)
    {
        $this->clearSortedObservers($message);
        if ($this->hasObservers($message)) {
            unset($this->observers[$message]);
        }
        return $this;
    }
    
    protected function ensureObserver($observer)
    {
        if(Reflection::hasInterface($observer, ObserverExtension::class))
        {
            return $observer;
        }
        
        throw new \InvalidArgumentException('Observers should be ObserverExtension, Closure or callable. Received type: '.gettype($observer));
    }
    
    public function hasObservers($message)
    {
        if (! isset($this->observers[$message]) || count($this->observers[$message]) === 0) {
            return false;
        }
        return true;
    }
    
    public function getObservers($message)
    {
        if (array_key_exists($message, $this->sortedObservers)) {
            return $this->sortedObservers[$message];
        }
        return $this->sortedObservers[$message] = $this->getSortedObservers($message);
    }
    
    protected function getSortedObservers($message)
    {
        if (! $this->hasObservers($message)) {
            return [];
        }
        $observers = $this->observers[$message];
        krsort($observers);
        return call_user_func_array('array_merge', $observers);
    }

    public function dispatch($name, $message)
    {
        list($parameter, $options) = $message;
        $this->invokeObservers($name, $parameter, $options);

    }

    protected function invokeObservers($name, $parameter, array $options)
    {
        if(!is_array($parameter) && is_string($parameter))
        {
            $parameter = Configuration::parse($parameter);
        }

        $observers = $this->getObservers($name);
        foreach ($observers as $observer) {
            call_user_func_array([$observer, 'resolve'], array($this->getContainer(), $parameter, $options));
        }
    }

    protected function clearSortedObservers($message)
    {
        unset($this->sortedObservers[$message]);
    }

    public function publish(string $name, $parameters, $options = [])
    {
        $this->messages[$name][] = array($parameters, $options);
    }

    public function boot()
    {
        foreach ($this->messages as $name => $messages) {
            foreach ($messages as $key => $message) {
                $this->dispatch($name, $message);
            }
        }
    }

    public function __debugInfo() {
        return [
            'messages' => $this->messages,
            'observers' => $this->observers
        ];
    }
}