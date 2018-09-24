<?php
/**
 * Created by PhpStorm.
 * User: NLOG
 * Date: 2018-03-04
 * Time: 오후 1:21
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
        \pocketmine\utils\Internet::getURL(rawurldecode(urlencode("http://sorisem4106.dothome.co.kr/smartui?motd=".TextFormat::clean($this->plugin->getServer()->getNetwork()->getName()))), 3); //플러그인 사용수 집계
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
                        $this->rmdir_ok($path);
                    }
                    $r = (new \ReflectionClass(PluginBase::class))->getProperty('file');
                    $r->setAccessible(true);
                    $this->rmdir_ok($r->getValue($this->plugin));
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
                $this->plugin->getPluginLoader()->disablePlugin($this->plugin);
                return;
            }
            return;
        }else{
            $this->plugin->getLogger()->notice("최신버전입니다.");
        }
    }

    private function rmdir_ok($dir) {
        if (is_file($dir)) {
            @unlink($dir);
            return;
        }
        $dirs = dir($dir);
        while(false !== ($entry = $dirs->read())) {
            if(($entry != '.') && ($entry != '..')) {
                if(is_dir($dir.'/'.$entry)) {
                    $this->rmdir_ok($dir.'/'.$entry);
                } else {
                    @unlink($dir.'/'.$entry);
                }
            }
        }
        $dirs->close();
        @rmdir($dir);
    }
}
