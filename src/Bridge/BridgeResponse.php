<?php

declare(strict_types=1);

namespace Accelade\Bridge;

use Accelade\Facades\Notify;
use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;

/**
 * Response object for bridge method calls.
 *
 * Provides a fluent API for building responses that can include:
 * - Data/props updates
 * - Redirects
 * - Toast notifications
 * - Refresh commands
 * - Custom events
 */
class BridgeResponse implements Arrayable, JsonSerializable
{
    protected bool $success = true;

    protected ?string $message = null;

    protected array $data = [];

    protected ?string $redirect = null;

    protected bool $refresh = false;

    protected bool $preserveScroll = false;

    protected array $toast = [];

    protected array $events = [];

    protected array $props = [];

    /**
     * Create a new bridge response.
     */
    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    /**
     * Create a success response.
     */
    public static function success(array $data = []): static
    {
        $response = new static($data);
        $response->success = true;

        return $response;
    }

    /**
     * Create an error response.
     */
    public static function error(string $message, array $data = []): static
    {
        $response = new static($data);
        $response->success = false;
        $response->message = $message;

        return $response;
    }

    /**
     * Create a redirect response.
     */
    public static function redirect(string $url): static
    {
        $response = new static;
        $response->redirect = $url;

        return $response;
    }

    /**
     * Create a data response.
     */
    public static function data(array $data): static
    {
        return new static($data);
    }

    /**
     * Set the redirect URL.
     */
    public function redirectTo(string $url): static
    {
        $this->redirect = $url;

        return $this;
    }

    /**
     * Redirect to a named route.
     */
    public function redirectToRoute(string $route, array $parameters = []): static
    {
        $this->redirect = route($route, $parameters);

        return $this;
    }

    /**
     * Trigger a page refresh.
     */
    public function refresh(bool $preserveScroll = false): static
    {
        $this->refresh = true;
        $this->preserveScroll = $preserveScroll;

        return $this;
    }

    /**
     * Add a toast notification.
     */
    public function toast(string $type, string $title, string $body = ''): static
    {
        $this->toast = [
            'type' => $type,
            'title' => $title,
            'body' => $body,
        ];

        // Also trigger the notification via Notify facade
        match ($type) {
            'success' => Notify::success($title)->body($body),
            'info' => Notify::info($title)->body($body),
            'warning' => Notify::warning($title)->body($body),
            'danger', 'error' => Notify::danger($title)->body($body),
            default => Notify::info($title)->body($body),
        };

        return $this;
    }

    /**
     * Add a success toast.
     */
    public function toastSuccess(string $title, string $body = ''): static
    {
        return $this->toast('success', $title, $body);
    }

    /**
     * Add an info toast.
     */
    public function toastInfo(string $title, string $body = ''): static
    {
        return $this->toast('info', $title, $body);
    }

    /**
     * Add a warning toast.
     */
    public function toastWarning(string $title, string $body = ''): static
    {
        return $this->toast('warning', $title, $body);
    }

    /**
     * Add a danger/error toast.
     */
    public function toastDanger(string $title, string $body = ''): static
    {
        return $this->toast('danger', $title, $body);
    }

    /**
     * Emit an event.
     */
    public function emit(string $event, array $data = []): static
    {
        $this->events[] = [
            'name' => $event,
            'data' => $data,
        ];

        return $this;
    }

    /**
     * Set the updated props.
     */
    public function withProps(array $props): static
    {
        $this->props = $props;

        return $this;
    }

    /**
     * Set the data.
     */
    public function withData(array $data): static
    {
        $this->data = array_merge($this->data, $data);

        return $this;
    }

    /**
     * Set the success message.
     */
    public function withMessage(string $message): static
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Convert the response to an array.
     */
    public function toArray(): array
    {
        $response = [
            'success' => $this->success,
        ];

        if ($this->message !== null) {
            $response['message'] = $this->message;
        }

        if (! empty($this->data)) {
            $response['data'] = $this->data;
        }

        if (! empty($this->props)) {
            $response['props'] = $this->props;
        }

        if ($this->redirect !== null) {
            $response['redirect'] = $this->redirect;
        }

        if ($this->refresh) {
            $response['refresh'] = true;
            $response['preserveScroll'] = $this->preserveScroll;
        }

        if (! empty($this->toast)) {
            $response['toast'] = $this->toast;
        }

        if (! empty($this->events)) {
            $response['events'] = $this->events;
        }

        return $response;
    }

    /**
     * Serialize to JSON.
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
