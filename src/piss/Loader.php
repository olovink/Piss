<?php

declare(strict_types=1);

namespace piss;

use piss\command\PissCommand;
use piss\entity\PissEntity;
use piss\task\PlayerPissTask;
use pocketmine\entity\Entity;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;

/**
 * @author smkzaza
 */
class Loader extends PluginBase {

    private Configuration $configuration;
    private PissManager $pissManager;

    private static self $instance;

    public function onEnable(): void{
        self::$instance = $this;

        $this->configuration = new Configuration($this);
        $this->getServer()->getCommandMap()->register("", new PissCommand(self::$instance));
        $this->pissManager = new PissManager($this);

        Entity::registerEntity(PissEntity::class);
    }

    public function startPissTask(Player $player): void{
        $this->getScheduler()->scheduleRepeatingTask(new PlayerPissTask($player, self::$instance), 3);
    }

    public function onDisable(): void{}

    public static function getInstance(): self{
        return self::$instance;
    }

    public function getPissManager(): PissManager{
        return $this->pissManager;
    }

    public function getConfiguration(): Configuration{
        return $this->configuration;
    }
}