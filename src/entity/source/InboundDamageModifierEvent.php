<?php

declare(strict_types=1);

namespace Lyrica0954\Monsters\entity\source;

use pocketmine\entity\Entity;

class InboundDamageModifierEvent extends EntityDamageEffectEvent {

	protected ?Entity $damager;

	protected InboundDamageModifier $modifier;


	public function __construct(
		?Entity               $damager,
		Entity                $entity,
		int                   $cause,
		InboundDamageModifier $modifier,
	) {
		parent::__construct($entity, $cause, 0);
		$this->damager = $damager;
		$this->modifier = $modifier;
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
	 * Get the value of modifier
	 *
	 * @return InboundDamageModifier
	 */
	public function getDamageModifier(): InboundDamageModifier {
		return $this->modifier;
	}
}
