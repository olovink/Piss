<?php

declare(strict_types=1);

namespace piss\command;

use piss\Loader;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;

class PissCommand extends Command {

    public function __construct(private /** readonly */ Loader $loader) {
        parent::__construct("piss");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool {
        if (!$sender instanceof Player) return false;

        $configData = $this->loader->getConfiguration()->getConfigData();

        if (!$sender->hasPermission($configData['pissPermission'])) {
            $sender->sendMessage($configData['pissNoPermissionMessage']);
            return false;
        }

        $this->loader->getPissManager()->piss($sender);
        return true;
    }
}