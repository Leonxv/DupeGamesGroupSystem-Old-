<?php

namespace Mastercoding\Group;

use Mastercoding\Group\Commands\addPerm;
use Mastercoding\Group\Commands\nick;
use Mastercoding\Group\Commands\Realname;
use Mastercoding\Group\Commands\setgroup;
use Mastercoding\Group\Commands\unnick;
use pocketmine\command\defaults\TimingsCommand;
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use Mastercoding\Group\EventListener;
use pocketmine\Server;
use pocketmine\utils\Config;

class Group extends PluginBase implements Listener {
    const prefix = "§eCloud§7-§2Group§7│";

    public static $eventlistener;
    public $skin;

    /** @var Group */
    private static $instance;

    /** Clan */

    public static $clan;

    public function onEnable()
    {
     self::$instance = $this;
     $this->getServer()->getPluginManager()->registerEvents($this, $this);
     $this->getLogger()->emergency("§2Group-System §awurde geladen");

     $this->registerCommands();
     $this->registerEvents();

     $this->getEventListener()->updateNickTool();

     self::$clan = Server::getInstance()->getPluginManager()->getPlugin("Clan");

        /*$db = new \Mastercoding\Money\db();
        $con = $db->connect();
        $result = $con->query("SELECT * FROM crang");

        $config = $this->onConfig();
        while ($row = mysqli_fetch_assoc($result)){
            var_dump($row);
            if ($row["Rang"] !== "Gast"){
                $config->set($row["Name"], [
                    "Rang" => $row["Rang"],
                    "Nickname" => 'n',
                    "BedWarsRang" => 'n'
                ]);
                $config->save();
                $this->getLogger()->info("§2player {$row["Name"]} saving with {$row["Rang"]}");
            }else{
                $config->set($row["Name"], [
                    "Rang" => "Spieler",
                    "Nickname" => 'n',
                    "BedWarsRang" => 'n'
                ]);
                $config->save();
                $this->getLogger()->info("§2player {$row["Name"]} saving with Spieler");
            }*/
            /*$config->set($n, [
                "Rang" => "Spieler",
                "Nickname" => 'n',
                "BedWarsRang" => 'n'
            ]);
            $config->save();*/

        #$con->query("INSERT IGNORE INTO geld (playername, Geld) VALUES ('{$row["Name"]}', '{$row["Money"]}')");

     #var_dump($this->getServer()->getPluginManager()->getPermissions());

    }

    public function onConfig() : Config {
        $config = new Config(Group::getInstance()->getDataFolder() . "players.json", Config::JSON);
        $config->reload();
        return $config;
    }

    public function onDisable()
    {
     $this->getLogger()->emergency("§4Group-System §cwurde endladen");
    }

    public function registerCommands(){
        $this->getServer()->getCommandMap()->register("setgroup", new setgroup($this));
        $this->getServer()->getCommandMap()->register("nick", new nick($this));
        $this->getServer()->getCommandMap()->register("unnick", new unnick($this));
        $this->getServer()->getCommandMap()->register("realname", new Realname($this, "realname", "Sehe Namen"));
    }

    public function registerEvents(){
        $eventlistener = new EventListener($this);
        $this->getServer()->getPluginManager()->registerEvents($eventlistener, $this);
        self::$eventlistener = $eventlistener;


        $group = new addPerm("group");
        $this->getServer()->getPluginManager()->registerEvents($group, $this);
        $this->getServer()->getCommandMap()->register("group", $group);


    }

    public static function getInstance() : Group {
        return self::$instance;
    }

    public function getEventListener() : EventListener {
        return self::$eventlistener;
    }



}
