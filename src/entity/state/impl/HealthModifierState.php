<?php

namespace Lyrica0954\Monsters\entity\state\impl;

use Echore\Stargazer\Modifier;
use Echore\Stargazer\Stargazer;
use Lyrica0954\Monsters\entity\state\DurationState;
use pocketmine\entity\Living;

class HealthModifierState extends DurationState {

	public function __construct(Living $entity, int $duration, protected Modifier $modifier) {
		parent::__construct($entity, $duration);
	}

	public function onRemove(): void {
		Stargazer::get($this->entity)->getHealth()->remove($this->modifier);
	}

	public function onApply(): void {
		parent::onApply();

		Stargazer::get($this->entity)->getHealth()->apply($this->modifier);
	}
}
