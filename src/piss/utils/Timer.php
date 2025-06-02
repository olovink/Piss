<?php

declare(strict_types=1);

namespace piss\utils;

class Timer {

    private int $time;

    public function __construct(
        private int $startTime, // Начальное время
        private int $step = 1 // Шаг
    ) {
        $this->time = $this->startTime;
    }

    public function isComplete(bool $onTick = true): bool{
        if ($onTick) {
            // Типа штоби контроллировать кагда нада атнимать тики
            $this->onTick();
        }

        if ($this->time == 0) {
            $this->time = $this->startTime;
            return true;
        }
        return false;
    }

    public function onTick(): void{
        if ($this->time > 0) {
            $this->time -= $this->step;
        }
    }

    public function getTime(): int{
        return $this->time;
    }

    public function getStartTime(): int{
        return $this->startTime;
    }
}