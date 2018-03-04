<?php

namespace nlog\SmartUI\FormHandlers\forms\functions;

use nlog\SmartUI\FormHandlers\NeedPluginInterface;
use nlog\SmartUI\FormHandlers\SmartUIForm;
use nlog\SmartUI\SmartUI;
use onebone\economyapi\EconomyAPI;
use pocketmine\Player;
use pocketmine\network\mcpe\protocol\ModalFormRequestPacket;

class ShowMoneyInfoFunction extends SmartUIForm implements NeedPluginInterface {

    public function CompatibilityWithPlugin(): bool {
        return class_exists(EconomyAPI::class, true);
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

        $player->dataPacket($pk);
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
        $json['title'] = "- 받은 돈 보기";
        $json['content'] = $str;
        $json["button1"] = "≫ 메뉴로 돌아가기 ≪";
        $json["button2"] = "≫ 창 닫기 ≪";

        return json_encode($json);
    }

    public function handleRecieve(Player $player, $result) {
        if ($result) {
            $this->FormManager->getListMenuForm()->sendPacket($player);
        }
    }
}