<?php

declare(strict_types=1);

namespace piss\entity;

use pocketmine\block\Water;
use pocketmine\entity\Entity;
use pocketmine\item\Item;
use pocketmine\math\AxisAlignedBB;
use pocketmine\network\mcpe\protocol\AddItemActorPacket;
use pocketmine\Player;
use pocketmine\timings\Timings;
use UnexpectedValueException;

class PissEntity extends Entity {
    public const NETWORK_ID = self::ITEM;

    /** @var Item */
    protected Item $item;

    public $width = 0.1;
    public $height = 0.1;
    protected $baseOffset = 0.1;

    protected $gravity = 0.03;
    protected $drag = 0.02;

    protected function initEntity() : void
    {
        parent::initEntity();
        $this->setImmobile(true);
        $itemTag = $this->namedtag->getCompoundTag("ItemPiss");
        if ($itemTag === null) {
            throw new UnexpectedValueException("Invalid " . get_class($this) . " entity: expected \"Item\" NBT tag not found");
        }

        $this->item = Item::nbtDeserialize($itemTag);
        if ($this->item->isNull()) {
            throw new UnexpectedValueException("Item for " . get_class($this) . " is invalid");
        }
    }

    public function entityBaseTick(int $tickDiff = 1) : bool
    {
        if ($this->closed) {
            return false;
        }
        Timings::$itemEntityBaseTick->startTiming();
        try {
            $hasUpdate = parent::entityBaseTick($tickDiff);

            if (!$this->isFlaggedForDespawn()) {
                if ($this->onGround) {
                    $this->flagForDespawn();
                    $hasUpdate = true;
                }
            }

            return $hasUpdate;
        } finally {
            Timings::$itemEntityBaseTick->stopTiming();
        }
    }

    protected function tryChangeMovement() : void
    {
        $this->checkObstruction($this->x, $this->y, $this->z);
        parent::tryChangeMovement();
    }

    protected function applyDragBeforeGravity() : bool
    {
        return true;
    }

    protected function applyGravity() : void
    {
        if ($this->level->getBlockAt($this->getFloorX(), $this->getFloorY(), $this->getFloorZ()) instanceof Water) {
            $bb = $this->getBoundingBox();
            $waterCount = 0;

            for ($j = 0; $j < 5; ++$j) {
                $d1 = $bb->minY + ($bb->maxY - $bb->minY) * $j / 5 + 0.4;
                $d3 = $bb->minY + ($bb->maxY - $bb->minY) * ($j + 1) / 5 + 1;

                $bb2 = new AxisAlignedBB($bb->minX, $d1, $bb->minZ, $bb->maxX, $d3, $bb->maxZ);

                if ($this->level->isLiquidInBoundingBox($bb2, new Water())) {
                    $waterCount += 0.2;
                }
            }

            if ($waterCount > 0) {
                $this->motion->y += 0.002 * ($waterCount * 2 - 1);
            } else {
                $this->motion->y -= $this->gravity;
            }
        } else {
            $this->motion->y -= $this->gravity;
        }
    }

    public function getItem() : Item {
        return $this->item;
    }

    public function canCollideWith(Entity $entity) : bool {
        return false;
    }

    protected function sendSpawnPacket(Player $player) : void {
        $pk = new AddItemActorPacket();
        $pk->entityRuntimeId = $this->getId();
        $pk->position = $this->asVector3();
        $pk->motion = $this->getMotion();
        $pk->item = $this->getItem();
        $pk->metadata = $this->propertyManager->getAll();

        $player->dataPacket($pk);
    }

    public function onCollideWithPlayer(Player $player) : void {}
}