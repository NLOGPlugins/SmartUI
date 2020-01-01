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

use ifteam\SimpleArea\database\area\AreaProvider;
use ifteam\SimpleArea\database\area\AreaSection;
use ifteam\SimpleArea\database\user\UserProperties;
use ifteam\SimpleArea\SimpleArea;
use nlog\SmartUI\FormHandlers\FormManager;
use nlog\SmartUI\FormHandlers\SmartUIForm;
use nlog\SmartUI\SmartUI;
use pocketmine\world\Position;
use pocketmine\player\Player;
use nlog\SmartUI\FormHandlers\NeedPluginInterface;
use pocketmine\network\mcpe\protocol\ModalFormRequestPacket;
use solo\swarp\SWarp;
use solo\swarp\Warp;

class IslandMoveFunction extends SmartUIForm implements NeedPluginInterface {

    const NOT_HAVE_ISLAND = 0;

    /** @var array */
    protected $islandList;

    public function __construct(SmartUI $owner, FormManager $formManager, int $formId) {
        parent::__construct($owner, $formManager, $formId);
        $this->islandList = [];
    }

    public static function getName(): string {
        return "섬 이동";
    }

    public static function getIdentifyName(): string {
        return "moveisland";
    }

    public function CompatibilityWithPlugin(): bool {
        return class_exists(SimpleArea::class, true);
    }

    public function sendPacket(Player $player) {
        $pk = new ModalFormRequestPacket();
        $formData = $this->getFormData($player);
        if ($formData === self::NOT_HAVE_ISLAND) {
            $player->sendMessage(SmartUI::$prefix . "소유한 섬이 없습니다.");
        } else {
            $pk->formData = $formData;
            $pk->formId = $this->formId;

            $player->getNetworkSession()->sendDataPacket($pk);
        }
    }

    protected function getFormData(Player $player) {
        $json = [];
        $json['type'] = 'form';
        $json['title'] = "§6- 섬 이동";
        $json['content'] = "§b§l이동할 섬의 버튼을 눌러주세요.";
        $json["buttons"] = [];
        $ids = [];
        $islands = UserProperties::getInstance()->getUserProperties($player->getName(), "island");
        if (empty($islands)) {
            return self::NOT_HAVE_ISLAND;
        }
        foreach ($islands as $id => $xyz) {
            $this->islandList[$player->getName()][] = $id;
            $ids[] = "§7> {$id}";
            $json['buttons'][] = ['text' => "§7▷ {$id}"]; //TODO: add image
        }

        return json_encode($json);
    }

    public function handleReceive(Player $player, $result) {
        if ($result === null) {
            unset($this->islandList[$player->getName()]);
            return;
        }
        if (!isset($this->islandList[$player->getName()])) {
            $this->owner->getLogger()->debug("비정상적인 응답입니다. {$player->getName()}, {$this->getName()}");
            return;
        }
        $islandId = $this->islandList[$player->getName()][$result];
        $areaSection = AreaProvider::getInstance()->getAreaToId("island", $islandId);
        $level = $this->owner->getServer()->getWorldManager()->getWorldByName('island');
        if (!$areaSection instanceof AreaSection) {
            $player->sendMessage(SmartUI::$prefix . "섬이 존재하지 않습니다.");
        } else {
            $center = $areaSection->getCenter();
            $x = $center->x;
            $z = $center->z;
            $y = $level->getHighestBlockAt($x, $z) + 2;
            $player->teleport(new Position($x, $y, $z, $level));
            $player->sendMessage(SmartUI::$prefix . "{$islandId}번 섬으로 이동하였습니다.");
        }
        unset($this->islandList[$player->getName()]);
    }

}