<?php

/**
 * Copyright (C) 2017-2020   NLOG (엔로그)
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

namespace nlog\SmartUI\FormHandlers\forms\functions;

use nlog\SmartUI\FormHandlers\SmartUIForm;
use nlog\SmartUI\SmartUI;
use pocketmine\player\Player;
use pocketmine\world\Position;

class SpawnFunction extends SmartUIForm {

    public static function getName(): string {
        return "스폰";
    }

    public static function getIdentifyName(): string {
        return "spawn";
    }

    public function sendPacket(Player $player) {
        $dlevel = $this->owner->getServer()->getWorldManager()->getDefaultWorld();
        $pos = new Position($dlevel->getSafeSpawn()->x, $dlevel->getSafeSpawn()->y, $dlevel->getSafeSpawn()->z, $dlevel);
        $player->teleport($pos);
        $player->sendMessage(SmartUI::$prefix . "스폰으로 이동하였습니다.");
    }

    protected function getFormData(Player $player) {
        //Not need
    }

    public function handleReceive(Player $player, $result) {
        //Not need
    }

}