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

namespace NorixDevelopment\GrantGUI;



use muqsit\invmenu\InvMenuHandler;
use NorixDevelopment\GrantGUI\commands\GrantCommand;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class GrantGUI extends PluginBase
{

    public const PREFIX = TextFormat::YELLOW . TextFormat::BOLD . "> " . TextFormat::RESET . TextFormat::GRAY;

    public function onEnable(): void
    {
        if (!InvMenuHandler::isRegistered()) {
            InvMenuHandler::register($this);
        }
        $this->checkSoftDepends();
        Server::getInstance()->getCommandMap()->register("GrantGUI", new GrantCommand());
    }

    public function checkSoftDepends() //This must be here.
    {
        $pureperms = Server::getInstance()->getPluginManager()->getPlugin("PurePerms");
        $ranksystem = Server::getInstance()->getPluginManager()->getPlugin("RankSystem");

        if ($pureperms == true and $ranksystem == true) {
            $this->getLogger()->notice("PurePerms and RankSystem Detected. GrantGUI cannot support both at the same time. Please remove one of the plugins.");
            Server::getInstance()->getPluginManager()->disablePlugin($this);
            return;
        } elseif ($pureperms == false and $ranksystem == false) {
            $this->getLogger()->notice("PurePerms or RankSystem not Detected. GrantGUI will stay disabled until one of the plugins are added. ");
            Server::getInstance()->getPluginManager()->disablePlugin($this);
            return;
        }
    }

}