<?php

namespace Reaper\Glitch;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;
use Reaper\Glitch\libs\SenseiTarzan\ExtraEvent\Component\EventLoader;
use Reaper\Glitch\listeners\EventListener;

class Loader extends PluginBase
{
    use SingletonTrait;

    /**
     * @return void
     */
    protected function onLoad(): void
    {
        self::setInstance($this);
    }

    /**
     * @return void
     */
    protected function onEnable(): void
    {
        EventLoader::loadEventWithClass($this, new EventListener());
    }
}