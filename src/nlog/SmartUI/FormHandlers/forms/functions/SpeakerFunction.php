<?php

namespace nlog\SmartUI\FormHandlers\forms\functions;

use nlog\SmartUI\FormHandlers\FormManager;
use nlog\SmartUI\FormHandlers\SmartUIForm;
use nlog\SmartUI\SmartUI;
use pocketmine\Player;
use pocketmine\network\mcpe\protocol\ModalFormRequestPacket;
use onebone\economyapi\EconomyAPI;

class SpeakerFunction extends SmartUIForm{
	
	/** @var int */
	private $limitStrlen;
	
	/** @var int */
	private $money;
	
	public function __construct(SmartUI $owner, FormManager $formManager, int $formId) {
		parent::__construct($owner, $formManager, $formId);
		
		$this->limitStrlen = intval($this->owner->getSettings()->getSetting(self::getIdentifyName(), 'limit-message')) ?? 50;
		$this->limitStrlen = $this->limitStrlen < 1 ? 50 : $this->limitStrlen;
		$this->money = intval($this->owner->getSettings()->getSetting(self::getIdentifyName(), 'need-money')) ?? 1000;
		$this->money = $this->money < 1 ? 1000 : $this->money;
	}
	
	public static function getName(): string{
		return "확성기";
	}
	
	public static function getIdentifyName(): string{
		return "speaker";
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
		$json['title'] = "§6- 확성기";
		$json['content'] = [];
		$json['content'][] = ["type" => "label", "text" => "최대 {$this->limitStrlen}글자까지 입력할 수 있습니다.\n확성기 한번당 {$this->money}원이 필요합니다."];
		$json['content'][] = ["type" => "input", "text" => "확성기로 알릴 내용을 입력하세요.", "placeholder" => "메세지를 입력하세요..."];
		
		return json_encode($json);
	}
	
	public function handleRecieve(Player $player, $result) {
		if ($result === null) {
			return;
		}
		$message = trim($result[1]);
		if ($message === "") {
			$player->sendMessage(SmartUI::$prefix . "아무것도 입력하지 않았습니다.");
			return;
		}
		if (mb_strlen($message, 'utf8') > $this->limitStrlen) {
			$player->sendMessage(SmartUI::$prefix . "{$this->limitStrlen}글자를 초과하였습니다.");
			return;
		}
		if (EconomyAPI::getInstance()->myMoney($player) < $this->money) {
			$player->sendMessage(SmartUI::$prefix . "돈이 부족합니다.");
			return;
		}
		EconomyAPI::getInstance()->reduceMoney($player, $this->money);
		$this->owner->getServer()->broadcastMessage("\n§c§l[확성기] §r§7{$player->getName()} > §r{$message}");
	}
	
}