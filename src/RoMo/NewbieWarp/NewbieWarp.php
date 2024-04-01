<?php

declare(strict_types=1);

namespace RoMo\NewbieWarp;

use pocketmine\event\EventPriority;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\plugin\PluginBase;
use RoMo\WarpCore\warp\Warp;
use RoMo\WarpCore\warp\WarpFactory;
use Symfony\Component\Filesystem\Path;

class NewbieWarp extends PluginBase{

    /** @var Warp|null */
    private ?Warp $warp = null;

    protected function onEnable() : void{
        $this->saveResource("warpName.txt");
        $path = Path::join($this->getDataFolder(), "warpName.txt");

        if(is_file($path)){
            $warpName = trim(file_get_contents($path));
            $this->warp = WarpFactory::getInstance()->getWarp($warpName);

            if($this->warp === null){
                $this->getLogger()->error("{$warpName} 워프를 찾을 수 없습니다.");
            }
        }

        $this->getServer()->getPluginManager()->registerEvent(PlayerJoinEvent::class, function(PlayerJoinEvent $event) : void{
            if($this->warp === null){
                return;
            }
            $player = $event->getPlayer();
            if(!$player->hasPlayedBefore()){
                $this->warp->teleport($player);
            }
        }, EventPriority::LOWEST, $this);
        // 입장 환영 메세지와 겹치지 않게 하기 위해 LOWEST를 사용
    }
}