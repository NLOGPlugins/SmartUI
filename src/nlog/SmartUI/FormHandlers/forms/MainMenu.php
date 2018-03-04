<?php

namespace nlog\SmartUI\FormHandlers\forms;

use nlog\SmartUI\FormHandlers\SmartUIForm;
use pocketmine\Player;
use pocketmine\network\mcpe\protocol\ModalFormRequestPacket;

class MainMenu extends SmartUIForm {
	
	public static function getIdentifyName(): string
	{
		return "main";
	}
	
	public static function getName(): string{
		return "메인 메뉴";
	}
	
	public function sendPacket(Player $player) {
		$pk = new ModalFormRequestPacket();
		$pk->formId = $this->formId;
		$pk->formData = $this->getFormData($player);
		
		$player->dataPacket($pk);
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
	
	public function handleRecieve(Player $player, $result) {
		if ($result) {
			$this->FormManager->getListMenuForm()->sendPacket($player);
		}
	}
	
}