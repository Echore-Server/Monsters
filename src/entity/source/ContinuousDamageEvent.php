<?php

declare(strict_types=1);

namespace Lyrica0954\Monsters\entity\source;

use Lyrica0954\Monsters\entity\MonsterDamageCause;
use pocketmine\entity\Entity;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;

class ContinuousDamageEvent extends EntityDamageEffectEvent {

	protected ?Entity $damager;

	protected ContinuousDamage $continuousDamage;

	/**
	 * @param float[] $modifiers
	 */
	public function __construct(
		?Entity          $damager,
		Entity           $entity,
		int              $cause,
		ContinuousDamage $damage
	) {
		parent::__construct($entity, $cause);
		$this->entity = $entity;
		$this->damager = $damager;
		$this->continuousDamage = $damage;
	}

	/**
	 * Get the value of damager
	 *
	 * @return Entity
	 */
	public function getDamager(): Entity {
		return $this->damager;
	}

	/**
	 * Get the value of continuousDamage
	 *
	 * @return ContinuousDamage
	 */
	public function getContinuousDamage(): ContinuousDamage {
		return $this->continuousDamage;
	}

	public function source(): EntityDamageEvent {
		if ($this->damager !== null) {
			return new EntityDamageByEntityEvent($this->damager, $this->entity, MonsterDamageCause::CONTINUOUS, $this->continuousDamage->getDamage(), [], 0.0);
		}

		return new EntityDamageEvent($this->entity, MonsterDamageCause::CONTINUOUS, $this->continuousDamage->getDamage());
	}
}
