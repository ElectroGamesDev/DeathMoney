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
        if(!$player->getLastDamageCause() instanceof EntityDamageByEntityEvent) return;
        $playerMoney = EconomyAPI::getInstance()->myMoney($player);
        $damager = $player->getLastDamageCause()->getDamager();
        if (!$damager instanceof Player)
        {
            $this->naturalMoneyLoss($player, $playerMoney);
            return;
        }
        if ($this->getConfig()->get("Type") == "all"){
            if ($this->getConfig()->get("KillerGainMoney"))
            {
                $damager->sendMessage("§aYou have killed " . $player->getName() . " and stole $" . $playerMoney);
                EconomyAPI::getInstance()->addMoney($damager, $playerMoney);
            }
            $player->sendMessage("§aYou have died and lost $" . $playerMoney);
            EconomyAPI::getInstance()->reduceMoney($player, $playerMoney);
        }
        if ($this->getConfig()->get("Type") == "half"){
            if ($this->getConfig()->get("KillerGainMoney"))
            {
                $damager->sendMessage("§aYou have killed " . $player->getName() . " and stole $" . $playerMoney / 2);
                EconomyAPI::getInstance()->addMoney($damager, $playerMoney / 2);
            }
            $player->sendMessage("§aYou have died and lost $" . $playerMoney / 2);
            EconomyAPI::getInstance()->reduceMoney($player, $playerMoney / 2);
        }
        if ($this->getConfig()->get("Type") == "amount"){
            if ($this->getConfig()->get("KillerGainMoney"))
            {
                $damager->sendMessage("§aYou have killed " . $player->getName() . " and stole $" . (double)$this->getConfig()->get("Money-Loss"));
                EconomyAPI::getInstance()->addMoney($damager, (double)$this->getConfig()->get("Money-Loss"));
            }
            $player->sendMessage("§aYou have died and lost $" . (double)$this->getConfig()->get("Money-Loss"));
            EconomyAPI::getInstance()->reduceMoney($player, (double)$this->getConfig()->get("Money-Loss"));
        }
        if ($this->getConfig()->get("Type") == "percent"){
            if ($this->getConfig()->get("KillerGainMoney"))
            {
                $damager->sendMessage("§aYou have killed " . $player->getName() . " and stole $" . ((double)$this->getConfig()->get("Money-Loss") / 100) * $playerMoney);
                EconomyAPI::getInstance()->addMoney($damager, ((double)$this->getConfig()->get("Money-Loss") / 100) * $playerMoney);
            }
            $player->sendMessage("§aYou have died and lost $" . ((double)$this->getConfig()->get("Money-Loss") / 100) * $playerMoney);
            EconomyAPI::getInstance()->reduceMoney($player, ((double)$this->getConfig()->get("Money-Loss") / 100) * $playerMoney);
        }
    }

    public function naturalMoneyLoss($player, $playerMoney)
    {
        if (!$this->getConfig()->get("LoseMoneyNaturally")) return;
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
