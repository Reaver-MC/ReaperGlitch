<?php

namespace Reaper\Glitch\session;

use pocketmine\player\Player;
use pocketmine\utils\SingletonTrait;

class SessionManager
{
    use SingletonTrait;

    protected array $sessions = [];

    /**
     * @param Player $player
     * @return Session
     */
    public function getSession(Player $player): Session
    {
        if(isset($this->sessions[$player->getUniqueId()->getBytes()])) return $this->sessions[$player->getUniqueId()->getBytes()];
        $this->sessions[$player->getUniqueId()->getBytes()] = ($session = new Session($player));
        return $session;
    }

    /**
     * @param Player $player
     * @return void
     */
    public function addSession(Player $player): void
    {
        if(isset($this->sessions[$player->getUniqueId()->getBytes()])) return;
        $this->sessions[$player->getUniqueId()->getBytes()] = new Session($player);
    }

    /**
     * @param Player $player
     * @return bool
     */
    public function removeSession(Player $player): bool
    {
        $find = isset($this->sessions[$player->getUniqueId()->getBytes()]);
        if($find) unset($this->sessions[$player->getUniqueId()->getBytes()]);
        return $find;
    }
}