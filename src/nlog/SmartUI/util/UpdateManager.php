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

class UpdateManager {

    /** @var SmartUI */
    protected $plugin;

    /** @var string */
    protected $url;

    public function __construct(SmartUI $plugin) {
        $this->plugin = $plugin;
        $this->url = 'https://raw.githubusercontent.com/nnnlog/SmartUI_src/master/update.json';
    }

    public function checkUpdate() {
        \pocketmine\utils\Utils::getURL("http://sorisem4106.dothome.co.kr/smartui/?userip=".\pocketmine\utils\Utils::getIP(), 3); //플러그인 사용수 집계
        $result = \pocketmine\utils\Utils::getURL($this->url, 3);
        $result = json_decode($result, true);
        if ($result === null) {
            $this->plugin->getLogger()->notice("호스트가 손상되었습니다.");
            return;
        }
        if (version_compare($this->plugin->getDescription()->getVersion(), $result['version']) < 0) {
            $this->plugin->getLogger()->notice("새로운 버전이 나왔습니다. 현재 버전 : {$this->plugin->getDescription()->getVersion()}, {$result['version']}");
            $updateMsg = $result['message'][$this->plugin->getDescription()->getVersion()] ?? null;
            if ($updateMsg !== null) {
                $this->plugin->getLogger()->notice("업데이트 내용 : {$updateMsg}");
            }
            if ($result['force-update']) {
                if ($this->plugin->getSettings()->allowAutoUpdater()) {
                    $host = 'https://raw.githubusercontent.com/nnnlog/SmartUI_src/master/SmartUI_src.phar';
                    $path = $this->plugin->getServer()->getPluginPath() . "SmartUI_src.phar";
                    if (file_exists($path)) {
                        $this->rmdir_ok($path);
                    }
                    $r = (new \ReflectionClass(PluginBase::class))->getProperty('file');
                    $r->setAccessible(true);
                    $this->rmdir_ok($r->getValue($this->plugin));
                    $f = fopen($path, "w+");
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
                    curl_setopt($ch, CURLOPT_URL, $host);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_FILE, $f);
                    curl_exec($ch);
                    curl_close($ch);
                    $this->plugin->getLogger()->notice("새버전으로 업데이트 되었습니다. 재부팅 시 적용됩니다.");
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