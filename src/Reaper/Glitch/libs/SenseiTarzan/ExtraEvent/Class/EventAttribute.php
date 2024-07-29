<?php

namespace Reaper\Glitch\libs\SenseiTarzan\ExtraEvent\Class;

use Attribute;
use pocketmine\event\EventPriority;

#[Attribute(Attribute::TARGET_METHOD)]
class EventAttribute{

	/**
	 * @param int|string $priority  Sets the priority at which this event handler will receive events.
	 * 		`@see EventPriority`
	 * @param bool $handleCancelled Cancelled events will STILL invoke this handler.
	 */

	public function __construct(protected int|string $priority = EventPriority::NORMAL, protected bool $handleCancelled = true){
		if(is_string($this->priority)){
			$this->priority = EventPriority::fromString($this->priority);
		}
	}

	public function getPriority(): int|string{
		return $this->priority;
	}


	/**
	 * @return bool
	 */
	public function isHandleCancelled() : bool{
		return $this->handleCancelled;
	}
}