<?php

namespace Lyrica0954\Monsters\entity\state\impl;

use Echore\Stargazer\Modifier;
use Echore\Stargazer\Stargazer;
use Lyrica0954\Monsters\entity\state\DurationState;
use pocketmine\entity\Living;

class SpeedModifierState extends DurationState {

	public function __construct(Living $entity, int $duration, protected Modifier $modifier) {
		parent::__construct($entity, $duration);
	}

	public function onRemove(): void {
		$this->active = false;

		Stargazer::get($this->entity)->getMovementSpeed()->remove($this->modifier);
	}

	public function onApply(): void {
		parent::onApply();

		$this->active = true;

		Stargazer::get($this->entity)->getMovementSpeed()->apply($this->modifier);
	}
}
