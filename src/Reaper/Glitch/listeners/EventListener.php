<?php

namespace Reaper\Glitch\listeners;

use pocketmine\block\Air;
use pocketmine\event\entity\EntityTeleportEvent;
use pocketmine\event\EventPriority;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\math\AxisAlignedBB;
use pocketmine\network\mcpe\protocol\MoveActorAbsolutePacket;
use pocketmine\network\mcpe\protocol\PlayerAuthInputPacket;
use pocketmine\player\Player;
use Reaper\Glitch\libs\SenseiTarzan\ExtraEvent\Class\EventAttribute;
use Reaper\Glitch\session\SessionManager;

class EventListener
{
    /**
     * @param DataPacketReceiveEvent $event
     * @return void
     */
    #[EventAttribute(EventPriority::NORMAL)]
    public function handle(DataPacketReceiveEvent $event): void
    {
        $packet = $event->getPacket();
        $player = ($origin = $event->getOrigin())->getPlayer();

        if(!$player instanceof Player) return;
        if($packet instanceof PlayerAuthInputPacket) {
            $session = SessionManager::getInstance()->getSession($player);
            $lastPosition = $session->lastPosition;
            if(is_null($lastPosition)) {
                $session->lastPosition = $packet->getPosition()->subtract(0, 1.62, 0);
                return;
            }

            $newPosition = $packet->getPosition();

            if(!$player->isConnected()) return;
            if(!$player->isSurvival()) return;
            if($newPosition->distance($lastPosition) < 0.1) return;

            $bb = new AxisAlignedBB($newPosition->x - 0.25, ($newPosition->y - 1.62), $newPosition->z - 0.25, $newPosition->x + 0.25, $newPosition->y, $newPosition->z + 0.25);
            $fuckingForEach = $player->getWorld()->getBlockCollisionBoxes($bb);
            if(count($fuckingForEach) >= 1) {
                $lastTeleportTicks = $session->lastTeleportTicks;
                if(!is_null($session->safePosition) && $lastTeleportTicks > 20 && !$player->isSwimming() && !$player->isGliding()) {
                    $session->getPlayer()->getNetworkSession()->sendDataPacket(MoveActorAbsolutePacket::create(
                        $player->getId(),
                        $session->safePosition,
                        $session->getPlayer()->getLocation()->getPitch(),
                        $session->getPlayer()->getLocation()->getYaw(),
                        $session->getPlayer()->getLocation()->getYaw(),
                        MoveActorAbsolutePacket::FLAG_FORCE_MOVE_LOCAL_ENTITY
                    ));
                }
            } else $session->safePosition = $newPosition;
            $session->lastTeleportTicks++;
            $session->lastPosition = $newPosition;
        }
    }

    /**
     * @param EntityTeleportEvent $event
     * @return void
     */
    #[EventAttribute(EventPriority::NORMAL)]
    public function handleTeleport(EntityTeleportEvent $event): void
    {
        $entity = $event->getEntity();
        if($entity instanceof Player) {
            $session = SessionManager::getInstance()->getSession($entity);
            $session->lastTeleportTicks = 0;
            $session->safePosition = $event->getTo()->asVector3();
        }
    }

    /**
     * @param PlayerLoginEvent $event
     * @return void
     */
    #[EventAttribute(EventPriority::NORMAL)]
    public function handleLogin(PlayerLoginEvent $event): void
    {
        SessionManager::getInstance()->addSession($event->getPlayer());
    }

    /**
     * @param PlayerQuitEvent $event
     * @return void
     */
    #[EventAttribute(EventPriority::NORMAL)]
    public function handleQuit(PlayerQuitEvent $event): void
    {
        SessionManager::getInstance()->removeSession($event->getPlayer());
    }
}