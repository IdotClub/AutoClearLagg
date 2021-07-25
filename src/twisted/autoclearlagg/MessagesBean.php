<?php


namespace twisted\autoclearlagg;


class MessagesBean {
	private string $time_left;
	private string $entities_cleared;

	public function getEntitiesClearedFormat() : string {
		return $this->entities_cleared;
	}

	public function getTimeLeftFormat() : string {
		return $this->time_left;
	}
}