<?php

namespace Electro\DeathMoney;

use onebone\economyapi\EconomyAPI;

use pocketmine\event\entity\EntityDeathEvent;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\utils\Config;

class DeathMoney extends PluginBase implements Listener{

    public $player;

    public function onEnable()
    {
        $this->saveDefaultConfig();
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    public function onDeath(EntityDeathEvent $event)
    {
        $player = $event->getEntity();
        if (!$player instanceof Player){
            return true;
        }
        $playerMoney = EconomyAPI::getInstance()->myMoney($player);
        if ($this->getConfig()->get("Type") == "all"){
            $player->sendMessage("§aYou have died and lost $" . $playerMoney);
            EconomyAPI::getInstance()->reduceMoney($player, $playerMoney);
        }
        if ($this->getConfig()->get("Type") == "half"){
            $player->sendMessage("§aYou have died and lost $" . $playerMoney / 2);
            EconomyAPI::getInstance()->reduceMoney($player, $playerMoney / 2);
        }
        if ($this->getConfig()->get("Type") == "amount"){
            $player->sendMessage("§aYou have died and lost $" . (double)$this->getConfig()->get("Money-Loss"));
            EconomyAPI::getInstance()->reduceMoney($player, (double)$this->getConfig()->get("Money-Loss"));
        }
        if ($this->getConfig()->get("Type") == "percent"){
            $player->sendMessage("§aYou have died and lost $" . ((double)$this->getConfig()->get("Money-Loss") / 100) * $playerMoney);
            EconomyAPI::getInstance()->reduceMoney($player, ((double)$this->getConfig()->get("Money-Loss") / 100) * $playerMoney);
        }
    }



}