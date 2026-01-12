<?php

declare(strict_types=1);

namespace Accelade\Animation;

/**
 * Value object representing an animation preset.
 */
class AnimationPreset
{
    public function __construct(
        public readonly string $name,
        public readonly string $enter,
        public readonly string $enterFrom,
        public readonly string $enterTo,
        public readonly string $leave,
        public readonly string $leaveFrom,
        public readonly string $leaveTo,
    ) {}

    /**
     * Convert to array for JSON serialization.
     *
     * @return array<string, string>
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'enter' => $this->enter,
            'enterFrom' => $this->enterFrom,
            'enterTo' => $this->enterTo,
            'leave' => $this->leave,
            'leaveFrom' => $this->leaveFrom,
            'leaveTo' => $this->leaveTo,
        ];
    }
}
