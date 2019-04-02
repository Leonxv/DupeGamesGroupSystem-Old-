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
use pocketmine\Player;
use pocketmine\plugin\Plugin;

class nick extends PluginCommand implements Listener {
    public $pl;

    public function __construct(Group $pl)
    {
        $this->pl = $pl;
        parent::__construct("nick", $pl);
        $this->setDescription("§6nick");

    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if ($sender instanceof Player) {
            if ($sender->hasPermission("nick.perm") and !isset($args[0])) {
                $nick = Group::getInstance()->getEventListener()->nicks[mt_rand(0, count(Group::getInstance()->getEventListener()->nicks) - 1)];
                $sender->sendMessage(Group::prefix . "§3Dein nick ist nun §e{$nick}");
                Group::getInstance()->getEventListener()->setNick($sender, $nick);
            } elseif ($sender->hasPermission("premium.nick.perm") and isset($args[0])) {
                if (!isset($args[0])) {
                    $sender->sendMessage("§7------ §e Nick-help §7-----\n§3/nick §2name");
                } else {
                    $nick = $args[0];
                    $sender->sendMessage(Group::prefix . "§3Dein nick ist nun §e{$nick}");
                    Group::getInstance()->getEventListener()->setNick($sender, $nick);
                }
            } else {
                $sender->sendMessage(Group::prefix . "§4Du brauchst mindestens Premium.\n§l§eShop§7: §3dupegames.de/shop §eKommt");
            }
        }
    }
}