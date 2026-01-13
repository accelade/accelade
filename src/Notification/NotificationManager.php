<?php

declare(strict_types=1);

namespace Accelade\Notification;

use Closure;
use Illuminate\Session\Store as SessionStore;
use Illuminate\Support\Collection;

/**
 * Manages notifications with session persistence.
 */
class NotificationManager
{
    protected const SESSION_KEY = 'accelade.notifications';

    protected Collection $notifications;

    protected bool $loadedFromSession = false;

    protected ?Closure $defaultCallback = null;

    protected string $defaultPosition = 'top-right';

    protected int $defaultDuration = 5000;

    public function __construct()
    {
        $this->notifications = collect();
    }

    /**
     * Get the session store, lazily resolved.
     */
    protected function getSession(): ?SessionStore
    {
        if (app()->bound('session.store')) {
            return app('session.store');
        }

        return null;
    }

    /**
     * @deprecated Use lazy session resolution instead
     */
    public function setSession(SessionStore $session): void
    {
        // No longer needed - session is resolved lazily
        $this->ensureLoadedFromSession();
    }

    /**
     * Ensure notifications are loaded from session (once per request).
     */
    protected function ensureLoadedFromSession(): void
    {
        if ($this->loadedFromSession) {
            return;
        }

        $session = $this->getSession();
        if ($session?->has(self::SESSION_KEY)) {
            $data = $session->get(self::SESSION_KEY, []);
            $this->notifications = collect($data)->map(
                fn ($d) => $this->hydrate($d)
            );
        }

        $this->loadedFromSession = true;
    }

    public function setDefault(Closure $callback): void
    {
        $this->defaultCallback = $callback;
    }

    public function defaultPosition(string $position): self
    {
        $this->defaultPosition = $position;

        return $this;
    }

    public function defaultDuration(int $milliseconds): self
    {
        $this->defaultDuration = $milliseconds;

        return $this;
    }

    public function make(): Notification
    {
        $notification = Notification::make();
        $notification->position = $this->defaultPosition;
        $notification->duration = $this->defaultDuration;

        if ($this->defaultCallback) {
            ($this->defaultCallback)($notification);
        }

        return $notification;
    }

    public function title(string $title): Notification
    {
        return $this->make()->title($title);
    }

    public function success(string $title): Notification
    {
        $notification = $this->title($title)->success();
        $notification->setManager($this);

        return $notification;
    }

    public function info(string $title): Notification
    {
        $notification = $this->title($title)->info();
        $notification->setManager($this);

        return $notification;
    }

    public function warning(string $title): Notification
    {
        $notification = $this->title($title)->warning();
        $notification->setManager($this);

        return $notification;
    }

    public function danger(string $title): Notification
    {
        $notification = $this->title($title)->danger();
        $notification->setManager($this);

        return $notification;
    }

    public function push(Notification $notification): void
    {
        $this->ensureLoadedFromSession();
        $this->notifications->push($notification);
        $this->saveToSession();
    }

    public function all(): Collection
    {
        $this->ensureLoadedFromSession();

        return $this->notifications;
    }

    public function flush(): Collection
    {
        $this->ensureLoadedFromSession();
        $notifications = $this->notifications;
        $this->notifications = collect();
        $this->saveToSession();

        return $notifications;
    }

    public function close(string $id): void
    {
        $this->ensureLoadedFromSession();
        $this->notifications = $this->notifications->reject(
            fn (Notification $n) => $n->getId() === $id
        );
        $this->saveToSession();
    }

    public function toArray(): array
    {
        $this->ensureLoadedFromSession();

        return $this->notifications->toArray();
    }

    protected function saveToSession(): void
    {
        $session = $this->getSession();
        $session?->put(
            self::SESSION_KEY,
            $this->notifications->map(fn ($n) => $n->jsonSerialize())->toArray()
        );
    }

    protected function hydrate(array $data): Notification
    {
        $n = new Notification($data['title'] ?? '');
        $n->id = $data['id'] ?? uniqid('notif-');
        $n->body = $data['body'] ?? $data['message'] ?? '';
        $n->status = $data['status'] ?? $data['type'] ?? 'success';
        $n->icon = $data['icon'] ?? '';
        $n->iconColor = $data['iconColor'] ?? '';
        $n->color = $data['color'] ?? '';
        $n->position = $data['position'] ?? $this->defaultPosition;
        $n->duration = $data['duration'] ?? $this->defaultDuration;
        $n->persistent = $data['persistent'] ?? false;
        $n->actions = $data['actions'] ?? [];

        return $n;
    }
}
