<?php

declare(strict_types=1);

namespace piss;

use piss\entity\PissEntity;
use pocketmine\block\BlockIds;
use pocketmine\entity\Entity;
use pocketmine\entity\object\ItemEntity;
use pocketmine\item\ItemFactory;
use pocketmine\Player;
use pocketmine\utils\Utils;

class PissManager {

    private array $playerData = [];

    public function __construct(
        private /** readonly */ Loader $loader
    ) {}

    public function piss(Player $player): void{
        $configData = $this->loader->getConfiguration()->getConfigData();

        if (!isset($this->playerData[$playerName = $player->getLowerCaseName()])) {
            $this->playerData[$playerName]['coolDown'] = 0;
        }

        if ($this->getTimeElapsed($playerName) <= $this->loader->getConfiguration()->getConfigData()['pissCoolDown']) {
            $player->sendMessage(sprintf($configData['pissCoolDownMessage'], $this->getTimeRemaining($playerName)));
            return;
        }
        $this->playerData[$playerName]['coolDown'] = time();

        $player->sendMessage($configData['pissUseMessage']);
        $this->loader->startPissTask($player);
    }

    public function processPiss(Player $player): void{
        if (!$player->spawned || !$player->isAlive()) {
            return;
        }
        $motion = $player->getDirectionVector()->multiply(0.175);
        $motion->y += 0.2;

        $item = ItemFactory::get(BlockIds::CONCRETE, 4); // yellow concrete

        $itemTag = $item->nbtSerialize();
        $itemTag->setName("ItemPiss");

        $pos = $player->getPosition();
        $pos->y += 0.1;

        $nbt = Entity::createBaseNBT($pos, $motion, Utils::getRandomFloat() * 360, 0);
        $nbt->setTag($itemTag);

        $itemEntity = Entity::createEntity("PissEntity", $player->getLevel(), $nbt);

        if ($itemEntity instanceof PissEntity) {
            $itemEntity->spawnToAll();
        }
    }

    public function getCoolDown(string $playerName): int{
        return $this->playerData[strtolower($playerName)]['coolDown'];
    }

    public function getTimeRemaining(string $playerName): int{
        return $this->loader->getConfiguration()->getConfigData()['pissCoolDown'] - $this->getTimeElapsed($playerName);
    }

    public function getTimeElapsed(string $playerName): int{
        return time() - $this->getCoolDown($playerName);
    }
}