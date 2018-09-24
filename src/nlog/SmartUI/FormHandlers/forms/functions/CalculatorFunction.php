<?php

namespace nlog\SmartUI\FormHandlers\forms\functions;

use nlog\SmartUI\FormHandlers\SmartUIForm;
use nlog\SmartUI\SmartUI;
use oat\beeme\Parser;
use pocketmine\entity\projectile\Throwable;
use pocketmine\Player;
use pocketmine\network\mcpe\protocol\ModalFormRequestPacket;

class CalculatorFunction extends SmartUIForm{
	
	public static function getName(): string{
		return "계산기";
	}
	
	public static function getIdentifyName(): string{
		return "calc";
	}
	
	public function sendPacket(Player $player) {
		$pk = new ModalFormRequestPacket();
		$pk->formData = $this->getFormData($player);
		$pk->formId = $this->formId;
		
		$player->dataPacket($pk);
	}
	
	protected function getFormData(Player $player) {
		$json = [];
		$json['type'] = 'custom_form';
		$json['title'] = "§6- 계산기";
		$json['content'] = [];
		$json['content'][] = ["type" => "label", "text" => "수식을 정확히 써주세요.\n더하기 : +, 빼기 : -,\n곱하기 : *, 나누기 : /\n제곱근 : √\n제곱 : ^"];
		$json['content'][] = ["type" => "input", "text" => "계산할 수식을 입력하세요.", "placeholder" => "수식을 입력하세요..."];
		
		return json_encode($json);
	}
	
	public function handleRecieve(Player $player, $result) {
		if ($result === null) {
			return;
		}
		$formula = trim($result[1]);
		$formula = str_replace(["√", "^"], ["sqrt", "**"], $formula);
		if ($formula === "") {
			$player->sendMessage(SmartUI::$prefix . "아무것도 입력하지 않았습니다.");
			return;
		}
		
		$result = (new Parser)->evaluate($formula);
		
		if (!$result) {
			$player->sendMessage(SmartUI::$prefix . "잘못된 수식입니다.");
			return;
	    }
		$player->sendMessage(SmartUI::$prefix . "결과 : {" . $formula . "} = {" . $result . "}");
	}
	
}