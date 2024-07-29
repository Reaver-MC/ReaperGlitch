<?php

namespace Reaper\Glitch\session;

use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\world\Position;

class Session
{
    public ?Vector3 $safePosition = null;
    public int $lastTeleportTicks = -1;
    public ?Vector3 $lastPosition = null;

    public function __construct(protected Player $player)
    {
    }

    /**
     * @return Player
     */
    public function getPlayer(): Player
    {
        return $this->player;
    }
}