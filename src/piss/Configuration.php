<?php

declare(strict_types=1);

namespace piss;

use JetBrains\PhpStorm\ArrayShape;
use pocketmine\utils\Config;

class Configuration {

    private array $configData;

    public function __construct(Loader $loader) {
        $config = new Config(
            $loader->getDataFolder() . "config.yml",
            Config::YAML,
            [
                "pissCount" => 50,
                "pissCoolDown" => 30,
                "pissPermission" => "piss.command",
                "pissCoolDownMessage" => "Вы сможете поссать через %s секунд.",
                "pissNoPermissionMessage" => "Вы не можете использовать эту команду.",
                "pissUseMessage" => "Вы поссали."
            ]
        );

        $this->configData = $config->getAll();
    }

    #[ArrayShape(
        [
            'pissCount' => "int",
            'pissCoolDown' => "int",
            'pissPermission' => "string",
            'pissCoolDownMessage' => "string",
            'pissNoPermissionMessage' => "string",
            'pissUseMessage' => "string"
        ]
    )]
    public function getConfigData(): array{
        return $this->configData;
    }
}