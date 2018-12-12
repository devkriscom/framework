<?php
declare (strict_types = 1);

namespace Nusantara\Contract\Service;

use Nusantara\Contract\Message\Event;
interface Listener 
{
	/**
     * Handle an event.
     *
     * @param EventInterface $event
     *
     * @return void
     */
    public function handle(Event $event);

    /**
     * Check whether the listener is the given parameter.
     *
     * @param mixed $listener
     *
     * @return bool
     */
    public function isListener($listener);
	
}