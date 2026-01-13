<?php

declare(strict_types=1);

namespace Accelade\Animation;

/**
 * Value object representing a single animation phase (enter or leave).
 */
readonly class AnimationPhase
{
    public function __construct(
        public string $transition,
        public string $from,
        public string $to,
    ) {}

    /**
     * Convert to array for JSON serialization.
     *
     * @return array<string, string>
     */
    public function toArray(): array
    {
        return [
            'transition' => $this->transition,
            'from' => $this->from,
            'to' => $this->to,
        ];
    }
}
