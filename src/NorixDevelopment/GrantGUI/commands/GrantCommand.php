<?php

/*
 *   $$\   $$\                     $$\                 $$$$$$$\                                $$\                                                        $$\
 *   $$$\  $$ |                    \__|                $$  __$$\                               $$ |                                                       $$ |
 *   $$$$\ $$ | $$$$$$\   $$$$$$\  $$\ $$\   $$\       $$ |  $$ | $$$$$$\ $$\    $$\  $$$$$$\  $$ | $$$$$$\   $$$$$$\  $$$$$$\$$$$\   $$$$$$\  $$$$$$$\ $$$$$$\
 *   $$ $$\$$ |$$  __$$\ $$  __$$\ $$ |\$$\ $$  |      $$ |  $$ |$$  __$$\\$$\  $$  |$$  __$$\ $$ |$$  __$$\ $$  __$$\ $$  _$$  _$$\ $$  __$$\ $$  __$$\\_$$  _|
 *   $$ \$$$$ |$$ /  $$ |$$ |  \__|$$ | \$$$$  /       $$ |  $$ |$$$$$$$$ |\$$\$$  / $$$$$$$$ |$$ |$$ /  $$ |$$ /  $$ |$$ / $$ / $$ |$$$$$$$$ |$$ |  $$ | $$ |
 *   $$ |\$$$ |$$ |  $$ |$$ |      $$ | $$  $$<        $$ |  $$ |$$   ____| \$$$  /  $$   ____|$$ |$$ |  $$ |$$ |  $$ |$$ | $$ | $$ |$$   ____|$$ |  $$ | $$ |$$\
 *   $$ | \$$ |\$$$$$$  |$$ |      $$ |$$  /\$$\       $$$$$$$  |\$$$$$$$\   \$  /   \$$$$$$$\ $$ |\$$$$$$  |$$$$$$$  |$$ | $$ | $$ |\$$$$$$$\ $$ |  $$ | \$$$$  |
 *   \__|  \__| \______/ \__|      \__|\__/  \__|      \_______/  \_______|   \_/     \_______|\__| \______/ $$  ____/ \__| \__| \__| \_______|\__|  \__|  \____/
 *                                                                                                           $$ |
 *                                                                                                           $$ |
 *                                                                                                           \__|
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author Norix Development
 * @powered by Norix Industries
 * @link https://bit.ly/NorixDiscord
*/

namespace NorixDevelopment\GrantGUI\commands;


use NorixDevelopment\GrantGUI\GrantGUI;
use NorixDevelopment\GrantGUI\RankManager;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\Player;
use pocketmine\Server;

class GrantCommand extends PluginCommand
{

    public function __construct(GrantGUI $owner)
    {
        parent::__construct("grant", $owner);
        $this->setPermission("grantgui.command.use");
        $this->setDescription("Access GrantGUI");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if (!$sender instanceof Player) {
            $sender->sendMessage("This command does not support console executions.");
            return;
        }

        if (!isset($args[0])) {
            $sender->sendMessage(GrantGUI::PREFIX . "Usage: /grant <player>");
            return;
        }

        $target = Server::getInstance()->getPlayer($args[0]);
        if (!$target instanceof Player) {
            return;
        }

        if (!$target->isOnline()) {
            $target = Server::getInstance()->getOfflinePlayer($args[0]);
            if ($target == null) {
                $sender->sendMessage(GrantGUI::PREFIX . "This user does not exist.");
                return;
            }
            RankManager::sendRanksMenu($sender, $target);
            return;
        } else {
            RankManager::sendRanksMenu($sender, $target);
            return;
        }
    }

}