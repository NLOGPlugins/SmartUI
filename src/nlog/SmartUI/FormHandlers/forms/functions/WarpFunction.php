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
use pocketmine\player\Player;
use nlog\SmartUI\FormHandlers\NeedPluginInterface;
use pocketmine\network\mcpe\protocol\ModalFormRequestPacket;
use solo\swarp\SWarp;
use solo\swarp\Warp;
use solo\swarp\WarpException;

class WarpFunction extends SmartUIForm implements NeedPluginInterface {

    /** @var array */
    protected $warpList;

    public function __construct(SmartUI $owner, FormManager $formManager, int $formId) {
        parent::__construct($owner, $formManager, $formId);
        $this->warpList = [];
    }

    public static function getName(): string {
        return "워프";
    }

    public static function getIdentifyName(): string {
        return "warp";
    }

    public function CompatibilityWithPlugin(): bool {
        return class_exists(SWarp::class, true);
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
        $json['title'] = "§6- 워프";
        $json['content'] = "§b§l워프할 곳의 버튼을 눌러주세요.";
        $json["buttons"] = [];
        $name = [];
        foreach (SWarp::getInstance()->getAllWarp() as $warp) {
            if (!$player->hasPermission($warp->getPermission())) {
                continue;
            }
            $name[] = $warp->getName();
            $json['buttons'][] = ['text' => "§7▷ {$warp->getName()}"]; //TODO: add image
        }
        $this->warpList[$player->getName()] = $name;

        return json_encode($json);
    }

    public function handleReceive(Player $player, $result) {
        if ($result === null) {
            return;
        }
        if (!isset($this->warpList[$player->getName()])) {
            $this->owner->getLogger()->debug("비정상적인 응답입니다. {$player->getName()}, {$this->getName()}");
            return;
        }
        $warpname = $this->warpList[$player->getName()][$result];
        $warp = SWarp::getInstance()->getWarp($warpname);
        if (!$warp instanceof Warp) {
            $player->sendMessage(SmartUI::$prefix . "{$warpname} 워프가 존재하지 않습니다.");
        } else {
            $player->sendMessage(SmartUI::$prefix . "{$warpname} 워프로 이동하였습니다.");
            try {
                if (!$player->hasPermission($warp->getPermission())) {
                    throw new WarpException("워프하기 위한 권한이 부족합니다.");
                }
                $warp->warp($player);
            } catch (WarpException $e) {
                $player->sendMessage(SmartUI::$prefix . $e->getMessage());
            }
        }
        unset($this->warpList[$player->getName()]);
    }

}