<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 13.11.2018
 * Time: 13:43
 */

namespace Mastercoding\Group;

use pocketmine\entity\Human;
use pocketmine\entity\Skin;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\utils\MainLogger;
use raklib\protocol\OpenConnectionReply1;

class EventListener implements Listener {
    public $pl;
    public $perms;
    public $nicks;
    public $skins;

    public function __construct(Group $pl)
    {
        $this->pl = $pl;
        @mkdir(Group::getInstance()->getDataFolder() . "/skins/");
    }

    public function onConfig() : Config {
        $config = new Config(Group::getInstance()->getDataFolder() . "players.json", Config::JSON);
        $config->reload();
        return $config;
    }

    public function registerPlayer(Player $p){
        $n = $p->getName();

        $config = $this->onConfig();

        if (!$config->exists($n)){
            $this->pl->getLogger()->info("§eNeuer Spieler in die DatenBank STATS registriert §2{$n}");
            $config->set($n, [
               "Rang" => "Spieler",
               "Nickname" => 'n',
               "BedWarsRang" => 'n'
            ]);
            $config->save();
        }

        #Server::getInstance()->getAsyncPool()->submitTask(new mysqlTask("INSERT INTO crang (Name, Rang, Nickname, BedWarsRang) VALUES ('$n', 'Spieler', 'n', 'n')"));
    }

    public function onSkinConfig(string $name){
        $c = new Config(Group::getInstance()->getDataFolder() . "/skins/" . "{$name}.json", Config::JSON);
        $c->reload();
        return $c;
    }


    public function onLogin(PlayerLoginEvent $ev){
        $p = $ev->getPlayer();
        $n = $p->getName();

        Group::getInstance()->skin[$n] = $p->getSkin();

        $n = $p->getName();
        #Server::getInstance()->getAsyncPool()->submitTask(new mysqlTask("UPDATE crang SET BedWarsRang = 'n' WHERE Name = '$n'"));

        $this->registerPlayer($p);
        $this->updateNickTool();
    }

    public function onJoin(PlayerJoinEvent $ev){
        $p = $ev->getPlayer();

        $n = $p->getName();
        $c = $this->onSkinConfig($n);

        $skindata = base64_encode($p->getSkin()->getSkinData());
        $c->set("Skin", $skindata);
        $c->save();

        $this->onLoadPlayer($p);
    }

    public function onChat(PlayerChatEvent $ev)
    {
        $p = $ev->getPlayer();
        $msg = $ev->getMessage();
        if (Server::getInstance()->getPort() == 2000) {
            if ($p->getLevel()->getFolderName() === Server::getInstance()->getDefaultLevel()->getFolderName()) {
                if ($this->getBedWarsRang($p) == "n") {
                    if ($this->isNicked($p)) {
                        if ($this->getBedWarsRang($p) == "n") {
                            $rang = "Spieler";
                            $nick = $this->getNick($p);
                            $c = $this->getRangConfig($rang);

                            $format = $c["format"];

                            $format = str_replace("{clan}", '', $format);
                            $format = str_replace("{display_name}", $nick, $format);
                            $format = str_replace("{msg}", $msg, $format);
                            $ev->setFormat($format);
                        } else {
                            $rang = $this->getBedWarsRang($p);
                            $nick = $this->getNick($p);
                            $c = $this->getRangConfig($rang);

                            $format = $c["format"];

                            $format = str_replace("{clan}", '', $format);
                            $format = str_replace("{display_name}", $nick, $format);
                            $format = str_replace("{msg}", $msg, $format);
                            $ev->setCancelled();


                            foreach ($p->getLevel()->getPlayers() as $player){
                                $player->sendMessage($format);
                            }
                        }
                    } else {
                        $n = $p->getName();
                        $rang = $this->getRang($p);
                        $c = $this->getRangConfig($rang);
                        $format = $c["format"];

                        if (Server::getInstance()->getPluginManager()->getPlugin("Clan") !== null) {
                            #$clan = Group::$clan->getClan($n);
                            #$k = Group::$clan->getKuerzel($n, $clan);

                            $format = str_replace("{clan}", "", $format);
                            $format = str_replace("{display_name}", $n, $format);
                            $format = str_replace("{msg}", $msg, $format);
                        }else{

                            $format = str_replace("{clan}", "", $format);
                            $format = str_replace("{display_name}", $n, $format);
                            $format = str_replace("{msg}", $msg, $format);
                        }
                        $ev->setCancelled();

                        foreach ($p->getLevel()->getPlayers() as $player){
                            $player->sendMessage($format);
                        }
                    }
                }
            }
        }else{
            if ($this->getBedWarsRang($p) == "n") {
                if ($this->isNicked($p)) {
                    if ($this->getBedWarsRang($p) == "n") {
                        $rang = "Spieler";
                        $nick = $this->getNick($p);
                        $c = $this->getRangConfig($rang);

                        $format = $c["format"];

                        $format = str_replace("{clan}", '', $format);
                        $format = str_replace("{display_name}", $nick, $format);
                        $format = str_replace("{msg}", $msg, $format);
                        $ev->setFormat($format);
                    } else {
                        $rang = $this->getBedWarsRang($p);
                        $nick = $this->getNick($p);
                        $c = $this->getRangConfig($rang);

                        $format = $c["format"];

                        $format = str_replace("{clan}", '', $format);
                        $format = str_replace("{display_name}", $nick, $format);
                        $format = str_replace("{msg}", $msg, $format);
                        $ev->setFormat($format);
                    }
                } else {
                    $n = $p->getName();
                    $rang = $this->getRang($p);
                    $c = $this->getRangConfig($rang);
                    $format = $c["format"];

                    if (Server::getInstance()->getPluginManager()->getPlugin("Clan") !== null) {
                        #$clan = Group::$clan->getClan($n);
                        #$k = Group::$clan->getKuerzel($n, $clan);

                        $format = str_replace("{clan}", "", $format);
                        $format = str_replace("{display_name}", $n, $format);
                        $format = str_replace("{msg}", $msg, $format);
                        $ev->setFormat($format);
                    }else{
                        $format = str_replace("{clan}", "", $format);
                        $format = str_replace("{display_name}", $n, $format);
                        $format = str_replace("{msg}", $msg, $format);
                        $ev->setFormat($format);
                    }
                }
            }
        }
    }

    public function getRang(Player $p){
        $n = $p->getName();

        $config = $this->onConfig();

        return $config->getNested($n . ".Rang");
    }

    public function getBedWarsRang(Player $p)
    {
        $n = $p->getName();

        $config = $this->onConfig();
        if ($config->exists($n)) {
            return $config->getNested($n . ".BedWarsRang");
        }
    }

    public function setRang(Player $p, $rang){
        $n = $p->getName();

        $config = $this->onConfig();
        if ($config->exists($n)){
           $config->setNested($n . ".Rang", $rang);
           $config->save();
        }
    }

    public function onConfigRang(){
        $c = new Config($this->pl->getDataFolder() . "Rang.json", Config::JSON);
        $c->reload();
        return $c;
    }

    public function getRangConfig(string $rang){
        $c = new Config($this->pl->getDataFolder() . "Rang.json", Config::JSON);
        $c->reload();
        $c = $c->get($rang);
        return $c;
    }

    public function getNick(Player $p)
    {
        $n = $p->getName();

        $config = $this->onConfig();
        if ($config->exists($n)) {
            return $config->getNested($n . ".Nickname");
        }
    }

    public function setNick(Player $p, string $nick){
        $n = $p->getName();

        if ($nick !== 'n') {
            $skin = $this->skins[mt_rand(0, count($this->skins) - 1)];
            if ($skin instanceof Skin){
                $pskin = Group::getInstance()->skin[$n];
                if ($pskin instanceof Skin) {
                    if ($skin->getSkinData() !== $pskin->getSkinData()) {
                        $p->setSkin($skin);

                        $config = $this->onConfig();
                        if($config->exists($n)){
                            $config->setNested($n . ".Nickname", $nick);
                            $config->save();
                        }

                    } else {
                        $this->setNick($p, $nick);
                    }
                }
            }
        }else{
            $config = $this->onConfig();
            if($config->exists($n)){
                $config->setNested($n . ".Nickname", $nick);
                $config->save();
            }
            $p->setSkin(Group::getInstance()->skin[$n]);
        }

        $this->onLoadPlayer($p);
    }


    public function updateNickTool(){
        $c = new Config($this->pl->getDataFolder() . "nicks.json", Config::JSON);
        $c->reload();

        foreach ($c->getAll() as $nick => $value){
          $this->nicks[] =  $nick;
        }

        if (!empty($this->nicks)) {
            shuffle($this->nicks);
        }


        $path = Group::getInstance()->getDataFolder() . "/skins/";
        $t = @scandir($path);
        for($i=0; $i <= count($t); $i++){
            if($i != 0 && $i != 1){
                if($i < count($t)){
                    $m = new Config($path . $t[$i], Config::YAML);
                    $this->skins[] = new Skin("Standart_Custom", base64_decode($m->get("Skin")));
                    if (!empty($this->skins)) {
                        shuffle($this->skins);
                    }
                }
            }
        }
    }

    public function isNicked(Player $p){
        $nick = $this->getNick($p);
        if ($nick !== 'n' and $p->hasPermission("nick.perm")){
            return true;
        }else{
            return false;
        }
    }

    public function updatePerms(Player $p){
        $n = $p->getName();
        $rang = $this->getRang($p);
        if(isset($this->perms[$n])){
            $p->removeAttachment($this->perms[$n]);
        }

        $perms = $p->addAttachment($this->pl);
        $perms->clearPermissions();

        $c = $this->getRangConfig($rang);
        if (isset($c["perms"])) {
            foreach ($c["perms"] as $perm) {
                $perms->setPermission($perm, true);
            }
        }
    }

    public function updateScreen(Player $p){
        if ($this->isNicked($p)){
            if ($this->getBedWarsRang($p) == "n") {
                $nick = $this->getNick($p);
                $rang = "Spieler";
                $cc = $this->getRangConfig($rang);
                $nametag = $cc["nametag"];
                $nametag = str_replace("{clan}", "", $nametag);
                $nametag = str_replace("{display_name}", $nick, $nametag);
                $p->setNameTag($nametag);
                $p->setDisplayName($nick);
            }else{
                $nick = $this->getNick($p);
                $rang = $this->getBedWarsRang($p);
                $cc = $this->getRangConfig($rang);
                $nametag = $cc["nametag"];
                $nametag = str_replace("{clan}", "", $nametag);
                $nametag = str_replace("{display_name}", $nick, $nametag);
                $p->setNameTag($nametag);
                $p->setDisplayName($nick);
            }
        }else{
            $n = $p->getName();
            if ($this->getBedWarsRang($p) == "n") {
                $rang = $this->getRang($p);

                $cc = $this->getRangConfig($rang);
                $nametag = $cc["nametag"];
                $nametag = str_replace("{clan}", "", $nametag);
                $nametag = str_replace("{display_name}", $n, $nametag);
                $p->setNameTag($nametag);
                $p->setDisplayName($n);
            }else{
                $rang = $this->getBedWarsRang($p);

                $cc = $this->getRangConfig($rang);
                $nametag = $cc["nametag"];
                $nametag = str_replace("{clan}", "", $nametag);
                $nametag = str_replace("{display_name}", $n, $nametag);
                $p->setNameTag($nametag);
                $p->setDisplayName($n);
            }
        }
    }

    public function onLoadPlayer(Player $p){
        $this->updatePerms($p);
        $this->updateScreen($p);
    }

}