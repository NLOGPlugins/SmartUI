<?php

/**
 * Copyright (C) 2017-2019   NLOG (엔로그)
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

namespace nlog\SmartUI\FormHandlers\forms;

use nlog\SmartUI\FormHandlers\SmartUIForm;
use pocketmine\Player;
use pocketmine\network\mcpe\protocol\ModalFormRequestPacket;

class MainMenu extends SmartUIForm {

    public static function getIdentifyName(): string {
        return "main";
    }

    public static function getName(): string {
        return "메인 메뉴";
    }

    public function sendPacket(Player $player) {
        $pk = new ModalFormRequestPacket();
        $pk->formId = $this->formId;
        $pk->formData = $this->getFormData($player);

        $player->sendDataPacket($pk);
    }

    protected function getFormData(Player $player) {
        $json = [];
        $json['type'] = 'modal';
        $json['title'] = "§6- 메인 메뉴";
        $json['content'] = $this->owner->getSettings()->getMessage($player);
        $json["button1"] = "≫ 메뉴 오픈 ≪";
        $json["button2"] = "≫ 창 닫기 ≪";

        return json_encode($json);
    }

    public function handleReceive(Player $player, $result) {
        if ($result) {
            $this->FormManager->getListMenuForm()->sendPacket($player);
        }
    }

}