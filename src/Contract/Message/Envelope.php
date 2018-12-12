<?php
declare (strict_types = 1);

namespace Nusantara\Contract\Message;

interface Envelope {

	public function set(string $id, $value);

	public function get(string $id, $default = null);

	public function getMessage() : Message;

	public function setMessage(Message $message);

}