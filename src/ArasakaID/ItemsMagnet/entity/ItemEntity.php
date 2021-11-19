<?php

namespace ArasakaID\ItemsMagnet\entity;

use pocketmine\entity\Human;
use pocketmine\Player;

class ItemEntity extends \pocketmine\entity\object\ItemEntity {

    public const MAX_TARGET_DISTANCE = 8.0;

    /** @var Player */
    private $targetPlayer = null;
    private $lookForTargetTime = 0;

    public function setTargetPlayer(?Player $player){
        $this->targetPlayer = $player;
    }

    public function getTargetPlayer(): ?Player
    {
        return $this->targetPlayer;
    }

    public function entityBaseTick(int $tickDiff = 1): bool
    {
        $hasUpdate = parent::entityBaseTick($tickDiff);

        $currentTarget = $this->getTargetPlayer();
        if($currentTarget !== null and (!$currentTarget->isAlive() or $currentTarget->distanceSquared($this) > self::MAX_TARGET_DISTANCE ** 2)){
            $currentTarget = null;
        }

        if($this->lookForTargetTime >= 20){
            if($currentTarget === null){
                $newTarget = $this->level->getNearestEntity($this, self::MAX_TARGET_DISTANCE, Human::class);

                if($newTarget instanceof Human and !($newTarget instanceof Player and $newTarget->isSpectator())){
                    $currentTarget = $newTarget;
                }
            }

            $this->lookForTargetTime = 0;
        }else{
            $this->lookForTargetTime += $tickDiff;
        }

        $this->setTargetPlayer($currentTarget);

        if($currentTarget !== null){
            $vector = $currentTarget->add(0, $currentTarget->getEyeHeight() / 2)->subtract($this)->divide(self::MAX_TARGET_DISTANCE);

            $distance = $vector->lengthSquared();
            if($distance < 1){
                $diff = $vector->normalize()->multiply(0.2 * (1 - sqrt($distance)) ** 2);

                $this->motion->x += $diff->x;
                $this->motion->y += $diff->y;
                $this->motion->z += $diff->z;
            }
        }
        return $hasUpdate;
    }

}