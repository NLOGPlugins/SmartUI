<?php

namespace nlog\SmartUI\FormHandlers\forms\functions;

use nlog\SmartUI\FormHandlers\SmartUIForm;
use nlog\SmartUI\SmartUI;
use pocketmine\Player;
use pocketmine\network\mcpe\protocol\ModalFormRequestPacket;

class RecieveMoneyFunction extends SmartUIForm{
	
	const error_no_recieve = 0;
	const error_crash_file = 1;
	
	public static function getName(): string{
		return "받은 돈 보기";
	}
	
	public static function getIdentifyName(): string{
		return "recievemoney";
	}
	
	public function sendPacket(Player $player) {
		$pk = new ModalFormRequestPacket();
		$formData = $this->getFormData($player);
		if ($formData === self::error_no_recieve) {
			$player->sendMessage(SmartUI::$prefix . "당신은 돈을 받은 적이 없습니다.");
		}elseif ($formData === self::error_crash_file) {
			$player->sendMessage(SmartUI::$prefix . "데이터가 손상되어 로그를 보여 줄 수 없습니다.");
			@unlink($this->owner->getDataFolder() . "money/" . $player->getName() . ".json");
		}else{
			$pk->formData = $formData;
			$pk->formId = $this->formId;
			
			$player->dataPacket($pk);
		}
	}
	
	protected function getFormData(Player $player) {
		if (!file_exists($this->owner->getDataFolder() . "money/" . $player->getName() . ".json")) {
			return self::error_no_recieve;
		}elseif (!is_array(json_decode(file_get_contents($this->owner->getDataFolder() . "money/" . $player->getName() . ".json"), true))) {
			return self::error_crash_file;
		}
		$str = "";
		$file = json_decode(file_get_contents($this->owner->getDataFolder() . "money/" . $player->getName() . ".json"), true);
		foreach ($file as $index => $info) {
			$date = date("Y-m-d G:i", $info['time']);
			$str .= "[{$date}] {$info['name']} > {$info['money']}";
		}
		if ($str === "") {
			return self::error_crash_file;
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