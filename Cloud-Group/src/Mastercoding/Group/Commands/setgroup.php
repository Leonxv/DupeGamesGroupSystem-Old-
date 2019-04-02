<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 13.11.2018
 * Time: 15:07
 */

namespace Mastercoding\Group\Commands;

use Mastercoding\Group\Group;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\event\Listener;
use pocketmine\plugin\Plugin;
use pocketmine\Server;

class setgroup extends PluginCommand implements Listener
{
    public $pl;

    public function __construct(Group $pl)
    {
        $this->pl = $pl;
        parent::__construct("setgroup", $pl);
        $this->setDescription("§6setgroup");
        $this->setPermission("setgroup.perm");

    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if ($this->testPermission($sender)) {
            if (!empty($args[0])) {
                $config = Group::getInstance()->getEventListener()->onConfig();
                if (!empty($args[1])) {
                    $rangconfig = Group::getInstance()->getEventListener()->onConfigRang();
                    if ($rangconfig->exists($args[1])) {
                        if (Server::getInstance()->getPlayer($args[0]) !== null) {
                            $player = Server::getInstance()->getPlayer($args[0]);
                            if ($config->exists($player->getName())) {
                                $config->setNested($player->getName() . ".Rang", $args[1]);
                                $config->save();
                                $sender->sendMessage("§7Der Rang von §2{$player->getName()}§7 wurde auf §e{$args[1]}§7 gesetzt.");
                                $player->sendMessage("§7Dein Rang wurde auf §e{$args[1]}§7gesetzt");
                                Group::getInstance()->getEventListener()->onLoadPlayer($player);
                            } else {
                                $sender->sendMessage("§7Dieser Spieler ist nich im Verzeichnis");
                            }
                        } else {
                            if ($config->exists($args[0])) {
                                $config->setNested($args[0] . ".Rang", $args[1]);
                                $config->save();
                                $sender->sendMessage("§7Der Rang von §2{$args[0]}§7 wurde auf §e{$args[1]}§7 gesetzt.");
                            } else {
                                $sender->sendMessage("§7Dieser Spieler ist nich im Verzeichnis");
                            }
                        }
                    } else {
                        $sender->sendMessage("§7Diesen Rang gibt es nicht.");
                    }
                } else {
                    $sender->sendMessage("§2/setgroup §aname rang");
                }
            } else {
                $sender->sendMessage("§2/setgroup §aname rang");
            }
        }
    }
}