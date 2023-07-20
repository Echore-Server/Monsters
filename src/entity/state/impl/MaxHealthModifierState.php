<?php

namespace Lyrica0954\Monsters\entity\state\impl;

use Echore\Stargazer\Modifier;
use Echore\Stargazer\Stargazer;
use Lyrica0954\Monsters\entity\state\DurationState;
use pocketmine\entity\Living;

class MaxHealthModifierState extends DurationState {

	public function __construct(Living $entity, int $duration, protected Modifier $modifier) {
		parent::__construct($entity, $duration);
	}

	public function onRemove(): void {
		Stargazer::get($this->entity)->getMaxHealth()->remove($this->modifier);
	}

	public function onApply(): void {
		parent::onApply();

		Stargazer::get($this->entity)->getMaxHealth()->apply($this->modifier);
	}
}
