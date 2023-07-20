<?php

declare(strict_types=1);

namespace Lyrica0954\Monsters\search\shape;

interface IShape {

	/**
	 * @return float
	 *
	 * Google/DeepL: interior
	 * other: internal
	 */
	public function getSumOfInteriorAngles(): float;
}
