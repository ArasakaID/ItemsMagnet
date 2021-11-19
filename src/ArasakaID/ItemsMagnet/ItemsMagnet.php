<?php

namespace ArasakaID\ItemsMagnet;

use ArasakaID\ItemsMagnet\entity\ItemEntity;
use pocketmine\entity\Entity;
use pocketmine\plugin\PluginBase;

class ItemsMagnet extends PluginBase {

    public function onEnable()
    {
        Entity::registerEntity(ItemEntity::class, true, ['Item', 'minecraft:item']);
    }

}