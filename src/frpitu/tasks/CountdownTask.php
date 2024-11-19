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

namespace frpitu\tasks;

use pocketmine\scheduler\Task;

use pocketmine\Server;

use frpitu\RestartMePlus;

final class CountdownTask extends Task
{

	/** @var RestartMePlus */
	private $source;

	function __construct(RestartMePlus $source)
	{
		$this->source = $source;
	}

	function onRun($ticks)
	{
		$this->source->tickRestart();
	}
}