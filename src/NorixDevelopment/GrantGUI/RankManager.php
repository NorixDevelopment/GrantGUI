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

use _64FF00\PurePerms\PPGroup;
use _64FF00\PurePerms\PurePerms;
use IvanCraft623\RankSystem\rank\Rank;
use IvanCraft623\RankSystem\session\SessionManager;
use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\DeterministicInvMenuTransaction;
use pocketmine\item\Item;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class RankManager
{
    public function __construct()
    {
    }


    public static function checkSupport()
    {
        $pureperms = Server::getInstance()->getPluginManager()->getPlugin("PurePerms");
        $ranksystem = Server::getInstance()->getPluginManager()->getPlugin("RankSystem");

        $result = 0;
        if ($pureperms == true) {
            $result = "pureperms";
        } elseif ($ranksystem == true) {
            $result = "ranksystem";
        }

        return $result;
    }


    /**
     * @param Player $player
     * @param Player $victim
     */
    public static function sendRanksMenu(Player $player, Player $victim)
    {
        $menu = InvMenu::create(InvMenu::TYPE_DOUBLE_CHEST);
        $inv = $menu->getInventory();

        $color = [
            TextFormat::WHITE => 0,
            TextFormat::GOLD => 1,
            TextFormat::DARK_PURPLE => 2,
            TextFormat::AQUA => 3,
            TextFormat::YELLOW => 4,
            TextFormat::GREEN => 5,
            TextFormat::LIGHT_PURPLE => 6,
            TextFormat::DARK_GRAY => 7,
            TextFormat::GRAY => 8,
            TextFormat::DARK_AQUA => 9,
            TextFormat::DARK_PURPLE => 10,
            TextFormat::BLUE => 11,
            TextFormat::GOLD => 12,
            TextFormat::DARK_GREEN => 13,
            TextFormat::DARK_RED => 14,
            TextFormat::RED => 14,
            TextFormat::BLACK => 15,
        ];


        $randColor = array_rand($color);
        $rand = rand(1, 16);

        if (RankManager::checkSupport() == "ranksystem") {
            $rank = Rank::getAll();
            foreach ($rank as $ranks) {
                $wool = Item::get(Item::WOOL, $rand);
                $wool->setCustomName(TextFormat::RESET . TextFormat::BOLD . $randColor . $ranks->getName());
                $inv->addItem($wool);
                $menu->setName("GrantGUI");
                $menu->send($player);

                $menu->setListener(InvMenu::readonly(function (DeterministicInvMenuTransaction $transaction) use ($player, $victim, $ranks, $menu): void {
                    $group = TextFormat::clean($transaction->getItemClicked()->getCustomName());
                    if (!in_array($group, $ranks)) {
                        $player->sendMessage(GrantGUI::PREFIX . "That rank does not exist."); // Not really necessary but added in-case.
                        return;
                    }
                    $session = SessionManager::getInstance()->get($victim->getName());
                    if ($session->hasRank($group)) {
                        $player->sendMessage(GrantGUI::PREFIX . "This user already has this rank.");
                        return;
                    } else {
                        $rank = Rank::getByName($group);
                        $session->setRank($rank);
                        $player->sendMessage(GrantGUI::PREFIX . "Successfully set the user's rank to " . TextFormat::RESET . TextFormat::YELLOW . TextFormat::BOLD . $group);
                        return;
                    }
                }));
            }
        } elseif (RankManager::checkSupport() == "pureperms") {
            $pureperms = Server::getInstance()->getPluginManager()->getPlugin("PurePerms");
            if (!$pureperms instanceof PurePerms) {
                return;
            }
            $pureperms->reload();

            foreach (self::getGroups() as $pureranks) {

                if (!$pureranks instanceof PPGroup) {
                    return;
                }
                $wool = Item::get(Item::WOOL, $rand);
                $wool->setCustomName(TextFormat::RESET . TextFormat::BOLD . $randColor . $pureranks->getName());
                $inv->addItem($wool);
                $menu->setName("GrantGUI");
                $menu->send($player);

                $menu->setListener(InvMenu::readonly(function (DeterministicInvMenuTransaction $transaction) use ($player, $victim, $pureranks, $menu, $pureperms): void {
                    $group = TextFormat::clean($transaction->getItemClicked()->getCustomName());
                    if (!in_array($group, self::getGroups())) {
                        $player->sendMessage(GrantGUI::PREFIX . "That rank does not exist."); // Not really necessary but added in-case.
                        return;
                    }
                    $rank = $pureperms->getGroup($group);
                    $pureperms->setGroup($victim, $rank);
                    $player->sendMessage(GrantGUI::PREFIX . "Successfully set the user's rank to " . TextFormat::RESET . TextFormat::BOLD . TextFormat::YELLOW . $group);
                }));
            }
        }
    }

    public static function getGroups(): array
    {
        $pureperms = Server::getInstance()->getPluginManager()->getPlugin("PurePerms");
        return $pureperms->getGroups();
    }

}