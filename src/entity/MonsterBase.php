<?php

declare(strict_types=1);

namespace Lyrica0954\Monsters\entity;

use Lyrica0954\Monsters\entity\source\ContinuousDamageEvent;
use Lyrica0954\Monsters\entity\source\InboundDamageModifierEvent;
use Lyrica0954\Monsters\entity\state\StateManager;
use Lyrica0954\SmartEntity\entity\LivingBase;
use Lyrica0954\SmartEntity\entity\walking\FightingEntity;
use pocketmine\entity\Living;

interface MonsterBase {

	/**
	 * Get the value of states
	 *
	 * @return StateManager
	 */
	public function getStates(): StateManager;

	/**
	 * Get the value of continuousDamages
	 *
	 * @return array
	 */
	public function getContinuousDamages(): array;

	/**
	 * Get the value of inboundDamageModifiers
	 *
	 * @return array
	 */
	public function getInboundDamageModifiers(): array;

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

	public function applyContinuousDamage(ContinuousDamageEvent $source): void;
}
