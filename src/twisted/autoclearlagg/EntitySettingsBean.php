<?php


namespace twisted\autoclearlagg;


class EntitySettingsBean {
	private bool $items;
	private bool $mobs;
	private bool $xp_orbs;
	/** @var string[] */
	private array $exempt;

	public function isItemsEnabled() : bool {
		return $this->items;
	}

	public function isMobsEnabled() : bool {
		return $this->mobs;
	}

	public function isXpOrbsEnabled() : bool {
		return $this->xp_orbs;
	}

	public function getExemptList() : array {
		return $this->exempt;
	}
}