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


use nlog\SmartUI\SmartUI;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;

class UpdateManager {

    /** @var SmartUI */
    protected $plugin;

    /** @var string */
    protected $url;

    public function __construct(SmartUI $plugin) {
        $this->plugin = $plugin;
        $this->url = 'https://raw.githubusercontent.com/nnnlog/SmartUI/master/update.json';
    }

    public function checkUpdate() {
        $result = \pocketmine\utils\Internet::getURL($this->url, 3);
        $result = json_decode($result, true);
        if ($result === null) {
            $this->plugin->getLogger()->notice("호스트가 손상되었습니다.");
            return;
        }
        if (version_compare($this->plugin->getDescription()->getVersion(), $result['version']) < 0) {
            $this->plugin->getLogger()->notice("새로운 버전이 나왔습니다. 현재 버전 : {$this->plugin->getDescription()->getVersion()}, {$result['version']}");
            $updateMsg = $result['chanelog'][$this->plugin->getDescription()->getVersion()]['message'] ?? null;
            if ($updateMsg !== null) {
                $this->plugin->getLogger()->notice("업데이트 내용 : {$updateMsg}");
            }
            if ($updateMsg = $result['chanelog'][$this->plugin->getDescription()->getVersion()]['force-update'] ?? true) {
                if ($this->plugin->getSettings()->allowAutoUpdater()) {
                    $host = 'https://raw.githubusercontent.com/nnnlog/SmartUI/master/SmartUI.phar';
                    $path = $this->plugin->getServer()->getPluginPath() . "SmartUI.phar";
                    if (file_exists($path)) {
                        \pocketmine\utils\Utils::recursiveUnlink($path);
                    }
                    $r = (new \ReflectionClass(PluginBase::class))->getProperty('file');
                    $r->setAccessible(true);
                    \pocketmine\utils\Utils::recursiveUnlink($r->getValue($this->plugin));
                    $f = fopen($path, "w+");
                    curl_setopt_array($ch = curl_init(), [
                            CURLOPT_CONNECTTIMEOUT => 5,
                            CURLOPT_URL => $host,
                            CURLOPT_SSL_VERIFYPEER => false,
                            CURLOPT_SSL_VERIFYHOST => 2,
                            CURLOPT_RETURNTRANSFER => 1,
                            CURLOPT_FILE => $f
                    ]);
                    curl_exec($ch);
                    curl_close($ch);
                    $this->plugin->getLogger()->notice("새버전으로 업데이트 되었습니다. 재부팅 시 적용됩니다.");
                    if ($this->plugin->getSettings()->rebootWhenServerRestart()) {
                        $this->plugin->getLogger()->critical("업데이트 적용을 위해 서버가 꺼집니다...");
                        $this->plugin->getServer()->shutdown();
                    }
                }
                $this->plugin->getLogger()->notice("새버전으로 업데이트가 필요합니다. 플러그인이 비활성화됩니다.");
                $this->plugin->getServer()->getPluginManager()->disablePlugin($this->plugin);
                return;
            }
            return;
        } else {
            $this->plugin->getLogger()->notice("최신버전입니다.");
        }
    }
}
