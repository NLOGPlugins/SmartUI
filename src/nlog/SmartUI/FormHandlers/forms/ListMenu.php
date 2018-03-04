<?php

namespace nlog\SmartUI\FormHandlers\forms;

use nlog\SmartUI\FormHandlers\SmartUIForm;
use pocketmine\Player;
use pocketmine\network\mcpe\protocol\ModalFormRequestPacket;

class ListMenu extends SmartUIForm{
	
	public static function getIdentifyName(): string
	{
		return "list";
	}
	
	public static function getName(): string{
		return "목록";
	}
	
	public function sendPacket(Player $player) {
		$pk = new ModalFormRequestPacket();
		$pk->formId = $this->formId;
		$pk->formData = $this->getFormData($player);
		
		$player->dataPacket($pk);
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
	
	public function handleRecieve(Player $player, $result) {
		if ($result === null) {
			return;
		}
		$func = array_values($this->owner->getFormManager()->getFunctions());
		$func[$result]->sendPacket($player);
	}
	
}