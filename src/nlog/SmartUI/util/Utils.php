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

namespace nlog\SmartUI\util;

class Utils {

    public static function koreanWonFormat(int $money): string {
        $elements = [];
        if ($money >= 1000000000000) {
            $elements[] = floor($money / 1000000000000) . "조";
            $money %= 1000000000000;
        }
        if ($money >= 100000000) {
            $elements[] = floor($money / 100000000) . "억";
            $money %= 100000000;
        }
        if ($money >= 10000) {
            $elements[] = floor($money / 10000) . "만";
            $money %= 10000;
        }
        if (count($elements) == 0 || $money > 0) {
            $elements[] = $money;
        }
        return implode(" ", $elements) . "원";
    }

}
