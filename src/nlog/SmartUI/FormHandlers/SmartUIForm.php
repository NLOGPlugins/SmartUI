<?php

/**
 * Copyright (C) 2017-2020   NLOG (엔로그)
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

namespace nlog\SmartUI\FormHandlers;

use nlog\SmartUI\SmartUI;
use pocketmine\player\Player;

abstract class SmartUIForm {

    /** @var SmartUI */
    protected $owner;

    /** @var FormManager */
    protected $FormManager;

    /** @var int */
    public $formId;

    public function __construct(SmartUI $owner, FormManager $formManager, int $formId) {
        $this->owner = $owner;
        $this->FormManager = $formManager;
        $this->formId = $formId;
    }

    public final function getFormId(): int {
        return $this->formId;
    }

    abstract public static function getName(): string;

    abstract public static function getIdentifyName(): string;

    abstract protected function getFormData(Player $player);

    abstract public function sendPacket(Player $player);

    abstract public function handleReceive(Player $player, $result);

}