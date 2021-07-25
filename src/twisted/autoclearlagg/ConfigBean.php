<?php


namespace twisted\autoclearlagg;


class ConfigBean {
	private int $seconds;
	private EntitySettingsBean $clear;
	private MessagesBean $messages;
	private array $times;

	public function getMessages() : MessagesBean {
		return $this->messages;
	}

	public function getClearSettings() : EntitySettingsBean {
		return $this->clear;
	}

	public function getDisplayTimes() : array {
		return $this->times;
	}

	public function getSeconds() : int {
		return $this->seconds;
	}
}