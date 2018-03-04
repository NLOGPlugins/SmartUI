<?php

namespace nlog\SmartUI\FormHandlers\forms\functions;

use nlog\SmartUI\FormHandlers\SmartUIForm;
use nlog\SmartUI\SmartUI;
use nlog\SmartUI\util\Utils;
use pocketmine\Player;
use nlog\SmartUI\FormHandlers\NeedPluginInterface;
use pocketmine\network\mcpe\protocol\ModalFormRequestPacket;
use onebone\economyapi\EconomyAPI;
use pocketmine\utils\Config;

class SendMoneyFunction extends SmartUIForm implements NeedPluginInterface{
	
	public static function getName(): string{
		return "돈 보내기";
	}
	
	public static function getIdentifyName(): string{
		return "sendmoney";
	}
	
	public function CompatibilityWithPlugin(): bool {
		return class_exists(EconomyAPI::class, true);
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
		$json['title'] = "§6- 돈 보내기";
		$json['content'] = [];
		$json['content'][] = ["type" => "input", "text" => "수취인을 입력하세요.", "placeholder" => "이름을 입력하세요..."];
		$json['content'][] = ["type" => "input", "text" => "보낼만큼의 돈 수량을 입력하세요", "placeholder" => "돈을 입력하세요..."];
		
		return json_encode($json);
	}
	
	public function handleRecieve(Player $player, $result) {
		if ($result === null) {
			return;
		}
		$name = trim($result[0]);
		$money = trim($result[1]);
		
		$economy = EconomyAPI::getInstance();
		if (!$economy->accountExists($name)) {
			$player->sendMessage(SmartUI::$prefix . "{$name}님은 서버에 접속한 적이 없습니다.");
			return;
		}
		if (!is_numeric($money) || $money < 1) {
			$player->sendMessage(SmartUI::$prefix . "{$money}는 정수가 아닙니다.");
			return;
		}
		$money = floor($money);
		if ($economy->myMoney($player) < $money) {
			$player->sendMessage(SmartUI::$prefix . "내가 가진 돈보다 작습니다.");
			return;
		}
		$this->sendMoneyLogger($player, $money, $name);
		$orgin = Utils::koreanWonFormat($economy->myMoney($player));
		$economy->reduceMoney($player, $money);
		$economy->addMoney($name, $money);
		$left = Utils::koreanWonFormat($economy->myMoney($player));
		$money = Utils::koreanWonFormat($money);
		$player->sendMessage(SmartUI::$prefix . "성공적으로 돈을 보냈습니다. 원래 돈 : {$orgin}, 보낸 돈 : {$money}, 남은 돈 ; {$left}");
		if ($recieve = $this->owner->getServer()->getPlayerExact($name) instanceof Player) {
			$recieve->sendMessage(SmartUI::$prefix . "{$player->getName()}님이 당신에게 {$money}을 보냈습니다.");
		}
	}
	
	public function sendMoneyLogger(Player $player, int $money, string $recipments) {
		$recipments = strtolower($recipments);
		@mkdir($this->owner->getDataFolder() . "money/", 0777, true);
		$conf = new Config($this->owner->getDataFolder() . "money/" . $recipments . ".json", Config::JSON);
		$all = $conf->getAll();
		$all = array_values($all);
		$all[] = ['name' => $player->getName(), 'time' => time(), 'money' => $money];
		$conf->setAll($all);
		$conf->save();
	}
	
}