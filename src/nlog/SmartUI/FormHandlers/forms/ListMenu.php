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

class ListMenu extends SmartUIForm {

    public static function getIdentifyName(): string {
        return "list";
    }

    public static function getName(): string {
        return "목록";
    }

    public function sendPacket(Player $player) {
        $pk = new ModalFormRequestPacket();
        $pk->formId = $this->formId;
        $pk->formData = $this->getFormData($player);

        $player->sendDataPacket($pk);
    }

    protected function getFormData(Player $player) {
        $json = [];
        $json['type'] = 'form';
        $json['title'] = "§c원하시는 기능을 선택하세요.";
        $json['content'] = "";
        $json["buttons"] = [];
        foreach (array_values($this->FormManager->getFunctions()) as $function) {
            $json['buttons'][] = ['text' => "§c< " . $function->getName() . " >"]; //TODO: add image
        }

        return json_encode($json);
    }

    public function handleReceive(Player $player, $result) {
        if ($result === null) {
            return;
        }
        $func = array_values($this->owner->getFormManager()->getFunctions());
        $func[$result]->sendPacket($player);
    }

}