<?php

declare(strict_types=1);

namespace Accelade\Broadcasting;

use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;

/**
 * EventResponse - Response helper for broadcast events
 *
 * Provides fluent methods to create broadcast payloads that trigger
 * client-side actions like redirect, refresh, or toast notifications.
 */
class EventResponse implements Arrayable, JsonSerializable
{
    /**
     * The action type to execute
     */
    protected string $action;

    /**
     * URL for redirect action
     */
    protected ?string $url = null;

    /**
     * Message for toast action
     */
    protected ?string $message = null;

    /**
     * Title for toast action
     */
    protected ?string $title = null;

    /**
     * Type for toast action (success, info, warning, danger)
     */
    protected string $type = 'info';

    /**
     * Additional custom data to include in the response
     */
    protected array $data = [];

    /**
     * Create a new event response
     */
    public function __construct(string $action)
    {
        $this->action = $action;
    }

    /**
     * Create a redirect response
     */
    public static function redirect(string $url): self
    {
        $response = new self('redirect');
        $response->url = $url;

        return $response;
    }

    /**
     * Create a redirect to a named route
     */
    public static function redirectToRoute(string $route, array $parameters = []): self
    {
        return self::redirect(route($route, $parameters));
    }

    /**
     * Create a refresh response
     */
    public static function refresh(): self
    {
        return new self('refresh');
    }

    /**
     * Create a toast notification response
     */
    public static function toast(string $message, string $type = 'info'): self
    {
        $response = new self('toast');
        $response->message = $message;
        $response->type = $type;

        return $response;
    }

    /**
     * Create a success toast
     */
    public static function success(string $message): self
    {
        return self::toast($message, 'success');
    }

    /**
     * Create an info toast
     */
    public static function info(string $message): self
    {
        return self::toast($message, 'info');
    }

    /**
     * Create a warning toast
     */
    public static function warning(string $message): self
    {
        return self::toast($message, 'warning');
    }

    /**
     * Create a danger toast
     */
    public static function danger(string $message): self
    {
        return self::toast($message, 'danger');
    }

    /**
     * Set the toast title
     */
    public function withTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Add custom data to the response
     */
    public function with(array $data): self
    {
        $this->data = array_merge($this->data, $data);

        return $this;
    }

    /**
     * Get the Accelade action payload
     */
    protected function getAcceladePayload(): array
    {
        $payload = ['action' => $this->action];

        if ($this->url !== null) {
            $payload['url'] = $this->url;
        }

        if ($this->message !== null) {
            $payload['message'] = $this->message;
        }

        if ($this->title !== null) {
            $payload['title'] = $this->title;
        }

        if ($this->action === 'toast') {
            $payload['type'] = $this->type;
        }

        return $payload;
    }

    /**
     * Convert to array
     */
    public function toArray(): array
    {
        return array_merge($this->data, [
            '_accelade' => $this->getAcceladePayload(),
        ]);
    }

    /**
     * Convert to JSON serializable
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
