<?php
declare (strict_types = 1);

namespace Nusantara\Contract\Message;

use Nusantara\Contract\Message;

interface Event extends Message {

	/**
     * Stop event propagation.
     *
     * @return $this
     */
    public function stopPropagation();

    /**
     * Check whether propagation was stopped.
     *
     * @return bool
     */
    public function isPropagationStopped();

    /**
     * Get the event name.
     *
     * @return string
     */
    public function getName();

}