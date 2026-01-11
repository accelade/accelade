<?php

declare(strict_types=1);

namespace Accelade\Notification;

use JsonSerializable;

/**
 * Notification with Filament-compatible fluent API.
 */
class Notification implements JsonSerializable
{
    public string $id;

    public string $title = '';

    public string $body = '';

    public string $status = 'success';

    public string $icon = '';

    public string $iconColor = '';

    public string $color = '';

    public string $position = 'top-right';

    public int $duration = 5000;

    public bool $persistent = false;

    public array $actions = [];

    public function __construct(?string $title = null)
    {
        $this->id = uniqid('notif-');
        if ($title !== null) {
            $this->title = $title;
        }
    }

    public static function make(?string $title = null): self
    {
        return new self($title);
    }

    public function title(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function body(string $body): self
    {
        $this->body = $body;

        return $this;
    }

    /** @deprecated Use body() instead */
    public function message(string $message): self
    {
        return $this->body($message);
    }

    public function icon(string $icon): self
    {
        $this->icon = $icon;

        return $this;
    }

    public function iconColor(string $color): self
    {
        $this->iconColor = $color;

        return $this;
    }

    public function color(string $color): self
    {
        $this->color = $color;

        return $this;
    }

    public function status(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function success(): self
    {
        $this->status = 'success';

        return $this;
    }

    public function info(): self
    {
        $this->status = 'info';

        return $this;
    }

    public function warning(): self
    {
        $this->status = 'warning';

        return $this;
    }

    public function danger(): self
    {
        $this->status = 'danger';

        return $this;
    }

    public function position(string $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function duration(int $milliseconds): self
    {
        $this->duration = $milliseconds;
        $this->persistent = false;

        return $this;
    }

    public function seconds(int $seconds): self
    {
        return $this->duration($seconds * 1000);
    }

    /** @deprecated Use seconds() instead */
    public function autoDismiss(int $seconds): self
    {
        return $this->seconds($seconds);
    }

    public function persistent(): self
    {
        $this->persistent = true;
        $this->duration = 0;

        return $this;
    }

    public function actions(array $actions): self
    {
        $this->actions = $actions;

        return $this;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function send(): self
    {
        app('accelade.notify')->push($this);

        return $this;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'body' => $this->body,
            'status' => $this->status,
            'icon' => $this->icon,
            'iconColor' => $this->iconColor,
            'color' => $this->color,
            'position' => $this->position,
            'duration' => $this->persistent ? 0 : $this->duration,
            'persistent' => $this->persistent,
            'actions' => $this->actions,
        ];
    }
}
