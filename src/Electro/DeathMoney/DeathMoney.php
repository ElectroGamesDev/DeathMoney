<?php

namespace Electro\DeathMoney;

use onebone\economyapi\EconomyAPI;

use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;

class DeathMoney extends PluginBase implements Listener{

    public function onEnable() : void
    {
        $this->saveDefaultConfig();
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    public function onDeath(PlayerDeathEvent $event) : void
    {
        $player = $event->getPlayer();
        $playerMoney = EconomyAPI::getInstance()->myMoney($player);
        if(!$player->getLastDamageCause() instanceof EntityDamageByEntityEvent) return;

        $damager = $player->getLastDamageCause()->getDamager();
        if (!$damager instanceof Player) return;
        if ($this->getConfig()->get("Type") == "all"){
            $player->sendMessage("§aYou have died and lost $" . $playerMoney);
            $damager->sendMessage("§aYou have killed " . $player->getName() . " and stole $" . $playerMoney);
            EconomyAPI::getInstance()->addMoney($damager, $playerMoney);
            EconomyAPI::getInstance()->reduceMoney($player, $playerMoney);
        }
        if ($this->getConfig()->get("Type") == "half"){
            $player->sendMessage("§aYou have died and lost $" . $playerMoney / 2);
            $damager->sendMessage("§aYou have killed " . $player->getName() . " and stole $" . $playerMoney / 2);
            EconomyAPI::getInstance()->addMoney($damager, $playerMoney / 2);
            EconomyAPI::getInstance()->reduceMoney($player, $playerMoney / 2);
        }
        if ($this->getConfig()->get("Type") == "amount"){
            $player->sendMessage("§aYou have died and lost $" . (double)$this->getConfig()->get("Money-Loss"));
            $damager->sendMessage("§aYou have killed " . $player->getName() . " and stole $" . (double)$this->getConfig()->get("Money-Loss"));
            EconomyAPI::getInstance()->addMoney($damager, (double)$this->getConfig()->get("Money-Loss"));
            EconomyAPI::getInstance()->reduceMoney($player, (double)$this->getConfig()->get("Money-Loss"));
        }
        if ($this->getConfig()->get("Type") == "percent"){
            $player->sendMessage("§aYou have died and lost $" . ((double)$this->getConfig()->get("Money-Loss") / 100) * $playerMoney);
            $damager->sendMessage("§aYou have killed " . $player->getName() . " and stole $" . ((double)$this->getConfig()->get("Money-Loss") / 100) * $playerMoney);
            EconomyAPI::getInstance()->addMoney($damager, ((double)$this->getConfig()->get("Money-Loss") / 100) * $playerMoney);
            EconomyAPI::getInstance()->reduceMoney($player, ((double)$this->getConfig()->get("Money-Loss") / 100) * $playerMoney);
        }
    }
}
