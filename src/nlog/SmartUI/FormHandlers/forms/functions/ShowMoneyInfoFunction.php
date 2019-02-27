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

namespace nlog\SmartUI\FormHandlers\forms\functions;

use nlog\SmartUI\FormHandlers\NeedPluginInterface;
use nlog\SmartUI\FormHandlers\SmartUIForm;
use nlog\SmartUI\SmartUI;
use onebone\economyapi\EconomyAPI;
use pocketmine\Player;
use pocketmine\network\mcpe\protocol\ModalFormRequestPacket;

class ShowMoneyInfoFunction extends SmartUIForm implements NeedPluginInterface {

    public function CompatibilityWithPlugin(): bool {
        return class_exists(EconomyAPI::class, true) &&
                method_exists(EconomyAPI::getInstance(), 'getRank') &&
                method_exists(EconomyAPI::getInstance(), 'getPlayerByRank');
    }

    public static function getName(): string {
        return "돈 정보 보기";
    }

    public static function getIdentifyName(): string {
        return "moneyinfo";
    }

    public function sendPacket(Player $player) {
        $pk = new ModalFormRequestPacket();
        $pk->formData = $this->getFormData($player);
        $pk->formId = $this->formId;

        $player->sendDataPacket($pk);
    }


    protected function getFormData(Player $player) {
        $str = "";
        $str .= "당신의 돈 : " . EconomyAPI::getInstance()->myMoney($player) . "원\n";
        $str .= "당신의 순위 : " . EconomyAPI::getInstance()->getRank($player) . "등\n";
        $str .= "돈 순위 TOP 10\n";
        for ($i = 1; $i <= 10; $i++) {
            $money = EconomyAPI::getInstance()->getPlayerByRank($i);
            if (!$money) {
                break;
            }
            $str .= "{$i}등 : {$money}\n";
        }
        $json = [];
        $json['type'] = 'modal';
        $json['title'] = "- 돈 정보 보기";
        $json['content'] = $str;
        $json["button1"] = "≫ 메뉴로 돌아가기 ≪";
        $json["button2"] = "≫ 창 닫기 ≪";

        return json_encode($json);
    }

    public function handleReceive(Player $player, $result) {
        if ($result) {
            $this->FormManager->getListMenuForm()->sendPacket($player);
        }
    }
}
