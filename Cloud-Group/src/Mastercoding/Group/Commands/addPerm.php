<?php
/**
 * Created by PhpStorm.
 * User: chr1s
 * Date: 18.01.2019
 * Time: 13:28
 */

namespace Mastercoding\Group\Commands;

use Mastercoding\Group\Group;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\ModalFormRequestPacket;
use pocketmine\network\mcpe\protocol\ModalFormResponsePacket;
use pocketmine\Player;
use pocketmine\utils\Config;

class addPerm extends Command implements Listener {

    public $player;

    public const Prefix = "§9Perm-Manager§7│";


    public function __construct(string $name, string $description = '', string $usageMessage = null, $aliases = [])
    {
        parent::__construct($name, $description, $usageMessage, $aliases);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if ($sender->hasPermission("group.perm")){
            if ($sender instanceof Player){
                $this->openGroupManager($sender);
            }
        }
    }

    public function openGroupManager(Player $player){
        $fdata = [];

        $fdata['title'] = "ManagePerm";
        $fdata['buttons'] = [];
        $fdata['content'] = '';
        $fdata['type'] = "form";

        #$fdata['content'][] = ["type" => "input", "text" => '§cSonstiges', "placeholder" => 'Sonstigen Grund', 'default' => ''];

        $fdata["buttons"][] = ["text" => "§7Gruppe erstellen"];
        $fdata["buttons"][] = ["text" => "§7Gruppe verwalten"];

        $pk = new ModalFormRequestPacket();
        $pk->formId = 600;
        $pk->formData = json_encode($fdata);

        $player->sendDataPacket($pk);
    }

    public function addGroup(Player $player){
        $fdata = [];

        $fdata['title'] = "§7Gruppe Hinzufügen";
        $fdata['buttons'] = [];
        $fdata['content'] = [];
        $fdata['type'] = "custom_form";

        $fdata['content'][] = ["type" => "input", "text" => "§7Gruppen Name", "placeholder" => 'z.B. Dev', 'default' => ''];
        $fdata["content"][] = ["type" => "input", "text" => "§7Format", "placeholder" => "§9Developer§8| §7{display_name}§7: §f{msg}", "default" => "§fDeveloper§8| §f{display_name}§7: §f{msg}"];
        $fdata["content"][] = ["type" => "input", "text" => "§7Nametag", "placeholder" => "§9{display_name}", "default" => "§f{display_name}"];

        $pk = new ModalFormRequestPacket();
        $pk->formId = 601;
        $pk->formData = json_encode($fdata);

        $player->sendDataPacket($pk);
    }

    public function getGroup(Player $player){
        $fdata = [];

        $fdata['title'] = "Groups";
        $fdata['buttons'] = [];
        $fdata['content'] = "";
        $fdata['type'] = "form";

        unset($this->player[$player->getName()]["Groups"]);

        $config = Group::getInstance()->getEventListener()->onConfigRang();
        foreach ($config->getAll() as $name => $info){
            $fdata["buttons"][] = ["text" => $name];
            $this->player[$player->getName()]["Groups"][] = $name;
        }

        $pk = new ModalFormRequestPacket();
        $pk->formId = 602;
        $pk->formData = json_encode($fdata);

        $player->sendDataPacket($pk);
    }

    public function editGroup(Player $player, string $group){
        $fdata = [];

        $fdata['title'] = "Grupppe $group";
        $fdata['buttons'] = [];
        $fdata['content'] = "";
        $fdata['type'] = "form";

        $this->player[$player->getName()]["Group"] = $group;


        $fdata["buttons"][] = ["text" => "§7Bearbeiten Style"];
        $fdata["buttons"][] = ["text" => "§7Rechte löschen"];
        $fdata["buttons"][] = ["text" => "§7Rechte hinzufügen"];
        $fdata["buttons"][] = ["text" => "§4Gruppe löschen"];

        $pk = new ModalFormRequestPacket();
        $pk->formId = 603;
        $pk->formData = json_encode($fdata);

        $player->sendDataPacket($pk);
    }

    public function editGroupStyle(Player $player, string $group){
        $fdata = [];

        $fdata['title'] = "§7Gruppen Style bearbeiten";
        $fdata['buttons'] = [];
        $fdata['content'] = [];
        $fdata['type'] = "custom_form";

        $config = Group::getInstance()->getEventListener()->onConfigRang();
        if ($config->exists($group)) {
            $fdata['content'][] = ["type" => "input", "text" => "§7Gruppen Name", "placeholder" => '', 'default' => $group];
            $fdata["content"][] = ["type" => "input", "text" => "§7Format", "placeholder" => "§9Developer§8| §7{display_name}§7: §f{msg}", "default" => $config->get($group)["format"]];
            $fdata["content"][] = ["type" => "input", "text" => "§7Nametag", "placeholder" => "", "default" => $config->get($group)["nametag"]];
        }

        $pk = new ModalFormRequestPacket();
        $pk->formId = 605;
        $pk->formData = json_encode($fdata);

        $player->sendDataPacket($pk);
    }

    public function addPerm(Player $player){
        $fdata = [];

        $fdata['title'] = "§7Gruppen Permission geben";
        $fdata['buttons'] = [];
        $fdata['content'] = [];
        $fdata['type'] = "custom_form";

        $fdata['content'][] = ["type" => "input", "text" => "§7Gruppen Name", "placeholder" => 'z.B. test.perm'];

        $pk = new ModalFormRequestPacket();
        $pk->formId = 606;
        $pk->formData = json_encode($fdata);

        $player->sendDataPacket($pk);
    }

    public function deletePerm(Player $player, string $group)
    {
        $fdata = [];

        $fdata['title'] = "ManagePerm";
        $fdata['buttons'] = [];
        $fdata['content'] = '';
        $fdata['type'] = 'form';

        unset($this->player[$player->getName()]["Rang"]);

        $this->player[$player->getName()]["Rang"] = $group;

        $config = Group::getInstance()->getEventListener()->onConfigRang();
        if ($config->exists($group)){
            foreach ($config->get($group)["perms"] as $key => $info){
                $fdata["buttons"][] = ["text" => $info];
            }
        }

        $pk = new ModalFormRequestPacket();
        $pk->formId = 607;
        $pk->formData = json_encode($fdata);

        $player->sendDataPacket($pk);
    }

    public function onData(DataPacketReceiveEvent $event){
        $pk = $event->getPacket();
        if ($pk instanceof ModalFormResponsePacket){
            $player = $event->getPlayer();
            $name = $player->getName();
            $data = json_decode($pk->formData);
            $id = $pk->formId;

            switch ($id){
                case 600:
                    if ($player->hasPermission("group.perm")) {
                        if ($data !== null) {
                            switch ($data) {
                                case 0:
                                    $this->addGroup($player);
                                    break;
                                case 1:
                                    $this->getGroup($player);
                                    break;
                            }
                        }
                    }
                    break;
                case 601:
                    if ($player->hasPermission("group.perm")) {
                        if ($data !== null) {
                            if (!empty($data[0])) {
                                if (!empty($data[1])) {
                                    if (!empty($data[2])) {
                                        $config = Group::getInstance()->getEventListener()->onConfigRang();
                                        if (!$config->exists($data[0])) {
                                            $config->set($data[0], [
                                                "format" => $data[1],
                                                "nametag" => $data[2],
                                                "perms" => []
                                            ]);
                                            $config->save();
                                            $player->sendMessage(Group::prefix . "§2Du hast die Guppe §e{$data[0]} §2erstellt");
                                            $this->player[$name]["Group"] = $data[0];
                                            $this->addPerm($player);
                                        } else {
                                            $player->sendMessage(Group::prefix . "§7Diese Gruppe gibt es bereits");
                                        }
                                    }
                                }
                            }
                        }
                    }
                    break;
                case 602:
                    if ($player->hasPermission("group.perm")) {
                        if ($data !== null) {
                            $button = $this->player[$name]["Groups"][$data];
                            $this->editGroup($player, $button);
                        }
                    }
                    break;
                case 603:
                    if ($player->hasPermission("group.perm")) {
                        if ($data !== null) {
                            switch ($data) {
                                case 0:
                                    $this->editGroupStyle($player, $this->player[$name]["Group"]);
                                    break;
                                case 1:
                                    $this->deletePerm($player, $this->player[$name]["Group"]);
                                    break;
                                case 2:
                                    $this->addPerm($player);
                                    break;
                                case 3:
                                    $player->sendMessage("Kommt!");
                                    break;
                            }
                        }
                    }
                    break;
                case 605:
                    if ($player->hasPermission("group.perm")) {
                        if ($data !== null) {
                            if (!empty($data[0])) {
                                if (!empty($data[1])) {
                                    if (!empty($data[2])) {
                                        $config = Group::getInstance()->getEventListener()->onConfigRang();

                                        $oldgroup = $this->player[$name]["Group"];

                                        if ($data[0] !== $oldgroup) {
                                            $config->set($data[0], $config->get($oldgroup));
                                            $config->remove($oldgroup);
                                            $config->save();
                                        }else{
                                            $config->set($data[0], $config->get($oldgroup));
                                            $config->save();
                                        }

                                        $player->sendMessage(Group::prefix . "§2Du hast die Guppe §e{$data[0]} §2geupdatet");
                                        $this->player[$name]["Group"] = $data[0];
                                    }
                                }
                            }
                        }
                    }
                    break;
                case 606:
                    if ($player->hasPermission("group.perm")) {
                        if ($data !== null) {
                            $group = $this->player[$name]["Group"];
                            if (!empty($data[0])) {
                                $config = Group::getInstance()->getEventListener()->onConfigRang();
                                $array = $config->get($group)["perms"];

                                $array[] = $data[0];

                                $config->setNested($group . ".perms", $array);
                                $config->save();

                                $player->sendMessage(Group::prefix . "§7Du hast die Berechtigung §e{$data[0]}§7 der Gruppe §2{$group}§7 hinzugefügt.");
                            }
                        }
                    }
                    break;
                case 607:
                    if ($player->hasPermission("group.perm")) {
                        if ($data !== null) {
                            $rang = $this->player[$name]["Group"];
                            var_dump($rang);
                            $config = Group::getInstance()->getEventListener()->onConfigRang();
                            if ($config->exists($rang)) {
                                $array = $config->get($rang)["perms"];
                                $new = [];
                                foreach ($array as $perms => $info) {
                                    if ($perms !== $data) {
                                        $new[] = $info;
                                    } else {

                                    }
                                }


                                $config->setNested($rang . ".perms", $new);
                                $config->save();

                            }
                        }
                    }
                    break;
            }
        }
    }
}