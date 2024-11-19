<?php

declare (strict_types = 1);

/*

.########..####.########.##.....##
.##.....##..##.....##....##.....##
.##.....##..##.....##....##.....##
.########...##.....##....##.....##
.##.........##.....##....##.....##
.##.........##.....##....##.....##
.##........####....##.....#######.

COPYRIGHT LUIZ GULHERME S. MUNHOZ (frpitu), ALL RIGHTS RESERVED


_________________________________________________

Twitter: @mechamopitu
Discord: frpitu#1085 | frpitu
_________________________________________________

*/

namespace frpitu\config;

use pocketmine\utils\Config;

use frpitu\RestartMePlus;

final class RestartMeConfig
{

	/** @var RestartMePlus */
	private $source;

	/** @var Config */
	private $config;

	function __construct(RestartMePlus $source)
	{
		$this->source = $source;
	}

	public function initFile()
	{
		$datapath = $this->source->getDataFolder() . 'config.yml';

		if (!file_exists($datapath))
		{
			$this->source->saveResource('config.yml');
		}

		$this->config = new Config($datapath, Config::YAML);
	}

	public function getConfigData() : Config
	{
		return $this->config;
	}
}