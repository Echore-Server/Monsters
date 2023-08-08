<?php

declare(strict_types=1);

namespace Lyrica0954\Monsters\entity\state\impl;

use Echore\Stargazer\ModifiableValue;
use Echore\Stargazer\Modifier;
use Echore\Stargazer\Stargazer;
use Lyrica0954\Monsters\entity\state\DurationState;
use pocketmine\entity\Living;

class ModifyStargazerValueState extends DurationState {

	private function __construct(
		Living $entity, int $duration, protected ModifiableValue $value, protected Modifier $modifier, protected bool $toModifiedValue
	) {
		parent::__construct($entity, $duration);
	}

	public static function maxHealth(Living $entity, int $duration, Modifier $modifier, bool $toModifiedValue = false): self {
		return new self($entity, $duration, Stargazer::get($entity)->getMaxHealth(), $modifier, $toModifiedValue);
	}

	public static function movementSpeed(Living $entity, int $duration, Modifier $modifier, bool $toModifiedValue = false): self {
		return new self($entity, $duration, Stargazer::get($entity)->getMovementSpeed(), $modifier, $toModifiedValue);
	}

	public static function attackDamage(Living $entity, int $duration, Modifier $modifier, bool $toModifiedValue = false): self {
		return new self($entity, $duration, Stargazer::get($entity)->getAttackDamage(), $modifier, $toModifiedValue);
	}

	/**
	 * @return ModifiableValue
	 */
	public function getValue(): ModifiableValue {
		return $this->value;
	}


	public function onRemove(): void {
		$this->active = false;

		if ($this->toModifiedValue) {
			$this->value->removeModifiedValue($this->modifier);
		} else {
			$this->value->remove($this->modifier);
		}
	}

	public function onApply(): void {
		parent::onApply();

		$this->active = true;

		if ($this->toModifiedValue) {
			$this->value->applyModifiedValue($this->modifier);
		} else {
			$this->value->apply($this->modifier);
		}
	}
}
