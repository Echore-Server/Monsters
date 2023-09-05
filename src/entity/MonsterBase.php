<?php

declare(strict_types=1);

namespace Lyrica0954\Monsters\entity;

use Lyrica0954\Monsters\entity\state\StateManager;
use pocketmine\entity\Living;

interface MonsterBase {

	/**
	 * Get the value of states
	 *
	 * @return StateManager
	 */
	public function getStates(): StateManager;

	/**
	 * Get the value of motioner
	 *
	 * @return Motioner
	 */
	public function motion(): Motioner;

	/**
	 * @return Living
	 */
	public function getEntity(): Living;
}
