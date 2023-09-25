<?php

declare(strict_types=1);

namespace Lyrica0954\Monsters\search;

use Closure;
use pocketmine\entity\Entity;

class EntitySearchOption {

	protected array $exludeSet;

	/**
	 * @param bool $includeDead
	 * @param string $entityType
	 * @param int|null $max
	 * @param int[] $exclude entity runtime id array
	 */
	public function __construct(
		public bool     $includeDead = false,
		public string   $entityType = Entity::class,
		public ?int     $max = null,
		protected array $exclude = [],
		public ?Closure $filter = null,
		public ?Closure $processor = null
	) {
		$this->setExclude($exclude);
	}

	public function setExclude(array $exclude): void {
		$this->exclude = $exclude;
		$this->exludeSet = array_flip($exclude);
	}

	public static function includeDead(bool $v): self {
		$i = self::default();
		$i->includeDead = $v;

		return $i;
	}

	public static function default(): self {
		return new self();
	}

	public static function entityType(string $v): self {
		$i = self::default();
		$i->entityType = $v;

		return $i;
	}

	public static function filter(Closure $filter): self {
		$i = self::default();
		$i->filter = $filter;

		return $i;
	}

	public static function processor(Closure $caster): self {
		$i = self::default();
		$i->processor = $caster;

		return $i;
	}

	public static function max(int $v): self {
		$i = self::default();

		$i->max = $v;

		return $i;
	}

	/**
	 * @param int[] $v
	 *
	 * @return self
	 */
	public static function exclude(array $v): self {
		$i = self::default();
		$i->setExclude($v);

		return $i;
	}

	public function process(Entity $entity): mixed {
		return $this->processor === null ? $entity : ($this->processor)($entity);
	}

	public function getExclude(): array {
		return $this->exclude;
	}

	public function isExcluded(int $runtimeId): bool {
		return isset($this->exludeSet[$runtimeId]); // in_array is weight
	}
}
