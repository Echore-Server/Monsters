<?php

declare(strict_types=1);

namespace Lyrica0954\Monsters\entity;

use Closure;
use Lyrica0954\Monsters\entity\state\StateManager;
use pocketmine\entity\Living;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\scheduler\TaskScheduler;
use pocketmine\utils\ObjectSet;

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

	/**
	 * @return ObjectSet<Closure(EntityDamageEvent): void>
	 */
	public function getAttackListeners(): ObjectSet;

	/**
	 * @return ObjectSet<Closure(EntityDamageEvent): void>
	 */
	public function getPreAttackListeners(): ObjectSet;

	public function getTaskScheduler(): TaskScheduler;
}
