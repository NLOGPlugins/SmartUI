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

use nlog\SmartUI\FormHandlers\FormManager;
use nlog\SmartUI\FormHandlers\SmartUIForm;
use nlog\SmartUI\SmartUI;
use nlog\SmartUI\util\Utils;
use pocketmine\Player;
use nlog\SmartUI\FormHandlers\NeedPluginInterface;
use pocketmine\network\mcpe\protocol\ModalFormRequestPacket;
use onebone\economyapi\EconomyAPI;
use pocketmine\utils\Config;

class TellFunction extends SmartUIForm {
	
	public static function getName(): string{
		return "귓속말";
	}
	
	public static function getIdentifyName(): string{
		return "tell";
	}

	/** @var array */
	private $recip;

	public function __construct(SmartUI $owner, FormManager $formManager, int $formId) {
        parent::__construct($owner, $formManager, $formId);
        $this->recip = [];
    }

    public function sendPacket(Player $player) {
		$pk = new ModalFormRequestPacket();
		$pk->formData = $this->getFormData($player);
		$pk->formId = $this->formId;
		
		$player->sendDataPacket($pk);
	}
	
	protected function getFormData(Player $player) {
		$json = [];
		$json['type'] = 'custom_form';
		$json['title'] = "§6- 귓속말";
		$json['content'] = [];
		if (isset($this->recip[$player->getName()])) {
            $json['content'][] = ["type" => "input", "text" => "수취인을 입력하세요.", "placeholder" => "이름을 입력하세요...", "default" => $this->recip[$player->getName()]];
        }else{
            $json['content'][] = ["type" => "input", "text" => "수취인을 입력하세요.", "placeholder" => "이름을 입력하세요..."];
        }
		$json['content'][] = ["type" => "input", "text" => "보낼 메세지를 입력하세요", "placeholder" => "메세지를 입력하세요..."];
		$json['content'][] = ["type" => "toggle", "text" => "닉네임 유지 (다음 번에는 입력한 닉네임으로 입력됩니다.)", "default" => true];
		
		return json_encode($json);
	}
	
	public function handleReceive(Player $player, $result) {
		if ($result === null) {
			return;
		}
		$name = trim($result[0]);
		$message = trim($result[1]);
		$nickname = $result[2];

		if (!$this->owner->getServer()->getPlayerExact($name) instanceof Player) {
			$player->sendMessage(SmartUI::$prefix . "{$name}님은 온라인이 아닙니다.");
			return;
		}
		if ($message === "") {
			$player->sendMessage(SmartUI::$prefix . "공백입니다.");
			return;
		}
		if (isset($this->recip[$player->getName()])) {
		    unset($this->recip[$player->getName()]);
        }
        if ($nickname) {
            $this->recip[$player->getName()] = $name;
        }
        $player->sendMessage(SmartUI::$prefix . "{$name}님께 메세지를 보냈습니다.");
        $this->owner->getServer()->getPlayerExact($name)->sendMessage("§7{$player->getName()}: {$message}");
        foreach ($this->owner->getServer()->getOnlinePlayers() as $player) {
            if (strcasecmp($name, $player->getName()) === 0 || strcasecmp($player->getName(), $player->getName()) === 0) {
                continue;
            }
            if ($player->isOp()) {
                $player->sendMessage("§7[{$player->getName()} => [{$this->owner->getServer()->getPlayerExact($name)->getName()}] : {$message}"); //TODO: 로그 설정
            }
        }
	}
}