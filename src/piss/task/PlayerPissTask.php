<?php

declare(strict_types=1);

namespace piss\task;

use piss\Loader;
use piss\utils\Timer;
use pocketmine\Player;
use pocketmine\scheduler\Task;

class PlayerPissTask extends Task {

    private Timer $timer;

    public function __construct(
        private Player $player,
        private Loader $loader
    ) {
        $this->timer = new Timer($this->loader->getConfiguration()->getConfigData()['pissCount']);
    }

    public function onRun(int $currentTick): void {
        if ($this->player == null) $this->getHandler()->cancel();
        $this->loader->getPissManager()->processPiss($this->player);

        if ($this->timer->isComplete()) {
            $this->getHandler()->cancel();
        }
    }
}