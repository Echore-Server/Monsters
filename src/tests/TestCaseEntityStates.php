<?php

declare(strict_types=1);

namespace Lyrica0954\Monsters\tests;

use Echore\PMRuntimeUnit\TestCaseParticipant;
use Lyrica0954\Monsters\entity\MonsterBase;
use Lyrica0954\Monsters\entity\MonsterTrait;
use pocketmine\entity\EntitySizeInfo;
use pocketmine\entity\Living;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class TestCaseEntityStates extends TestCaseStates {

	public function cleanup(): void {
		$this->monster->getEntity()->kill();
	}

	public function setUpWithParticipant(TestCaseParticipant $participant): void {
		$entity = new class($participant->getPlayer()->getLocation()) extends Living implements MonsterBase {
			use MonsterTrait;

			protected function initEntity(CompoundTag $nbt): void {
				parent::initEntity($nbt);
				$this->initMonster();
			}

			protected function getInitialSizeInfo(): EntitySizeInfo {
				return new EntitySizeInfo(1.8, 0.6);
			}

			public static function getNetworkTypeId(): string {
				return EntityIds::ZOMBIE;
			}

			public function getName(): string {
				return "TestCase Dummy";
			}
		};
		$entity->spawnToAll();
		$this->monster = $entity;
	}
}
