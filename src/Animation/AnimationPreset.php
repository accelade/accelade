<?php

declare(strict_types=1);

namespace Accelade\Animation;

/**
 * Value object representing an animation preset.
 */
readonly class AnimationPreset
{
    public function __construct(
        public string $name,
        public AnimationPhase $enter,
        public AnimationPhase $leave,
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
            'enter' => $this->enter->transition,
            'enterFrom' => $this->enter->from,
            'enterTo' => $this->enter->to,
            'leave' => $this->leave->transition,
            'leaveFrom' => $this->leave->from,
            'leaveTo' => $this->leave->to,
        ];
    }
}
