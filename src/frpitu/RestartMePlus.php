<?php

declare(strict_types=1);

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

namespace frpitu;

use pocketmine\plugin\PluginBase;
use pocketmine\Player;
use frpitu\tasks\CountdownTask;
use frpitu\config\RestartMeConfig;

final class RestartMePlus extends PluginBase
{
	/** @var RestartMeConfig */
	private $config;

	const RESTART_START = 0;
	const RESTART_COUNTDOWN = 1;
	const RESTART_STOP = 2;

	/** @var int */
	private $state = null;

	/** @var int */
	private $countdown = 0;

	/** @var int */
	private $delay = 0;

	/** @var int */
	private $time = 0;

	/** @var int */
	private $restartTime = 0;

	/** @var int */
	private $broadcastTime = 60;

	/**
	 * @inheritDoc
	 */
	public function onEnable()
	{
		$this->config = new RestartMeConfig($this);
		$this->config->initFile();

		if ($this->restartEnabled()) {
			$this->getServer()->getScheduler()->scheduleRepeatingTask(new CountdownTask($this), 20);
		}
	}

	/**
	 * @return RestartMeConfig
	 */
	public function getConfigManager()
	{
		return $this->config;
	}

	/**
	 * @return array
	 */
	public function getConfigData()
	{
		return $this->config->getConfigData()->getAll() ?? [];
	}

	/**
	 * @return int|float
	 */
	public function getConfigCountdown()
	{
		return $this->getConfigData()['restart-time'];
	}

	/**
	 * @return bool
	 */
	public function restartEnabled()
	{
		return (bool)($this->getConfigData()['restart-enabled'] ?? true);
	}

	/**
	 * @return int
	 */
	public function getCountdown()
	{
		return $this->countdown;
	}

	/**
	 * @return int
	 */
	public function getState()
	{
		return $this->state;
	}

	/**
	 * @param string $msg
	 */
	public function sendMessage($msg)
	{
		$type = $this->getConfigData()['restart-msgType'];
		foreach ($this->getServer()->getOnlinePlayers() as $player) {
			switch ($type) {
				case 0:
					$player->sendMessage($msg);
					break;
				case 1:
					$player->sendPopup($msg);
					break;
				case 2:
					$player->sendTip($msg);
					break;
			}
		}
	}

	protected function start()
	{
        $this->state = self::RESTART_START;
		if ($this->broadcastTime > 0) {
			$this->broadcastTime--;
		} else {
			$this->broadcastTime = 60;

			$msg = str_replace(
				'{TIME}',
				gmdate('H:i:s', $this->restartTime),
				$this->getConfigData()['restart-broadcastTimeMsg']
			);
			$this->getLogger()->notice($msg);
			$this->sendMessage($msg);
		}

		if ($this->restartTime < 11) {
			$this->getLogger()->notice('State ' . $this->state);
			$this->state++;
		}
	}

	protected function stoping()
	{
		$msg = str_replace(
			'{TIME}',
			$this->restartTime,
			$this->getConfigData()['restart-serverIsStoppingMsg']
		);
		$this->sendMessage($msg);

		if ($this->restartTime < 1) {
			$this->state++;
		}
	}

	protected function stop()
	{
		foreach ($this->getServer()->getOnlinePlayers() as $player) {
			$player->kick($this->getConfigData()['restart-kickMsg'], false);
		}
		$this->getServer()->shutdown();
		$this->state = self::RESTART_START;
	}

	public function tickRestart()
	{
		$this->countdown = $this->getConfigCountdown() * 60;
		$this->restartTime = $this->countdown - $this->time;

		switch ($this->state) {
			case 0:
				$this->start();
				break;
			case 1:
				$this->stoping();
				break;
			case 2:
				$this->stop();
				break;
		}

		$this->time++;
		//$this->getLogger()->notice("{$this->restartTime}");
	}
}
