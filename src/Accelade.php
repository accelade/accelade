<?php

declare(strict_types=1);

namespace Accelade;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Str;

class Accelade
{
    protected Application $app;

    protected array $componentStack = [];

    protected int $componentCounter = 0;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Current framework override (set via setFramework method)
     */
    protected ?string $frameworkOverride = null;

    /**
     * Set the framework to use for the current request.
     * This overrides the config value.
     */
    public function setFramework(string $framework): self
    {
        $this->frameworkOverride = $framework;

        return $this;
    }

    /**
     * Get the current framework.
     */
    public function getFramework(): string
    {
        return $this->frameworkOverride ?? config('accelade.framework', 'vanilla');
    }

    /**
     * Generate the script tags for Accelade.
     */
    public function scripts(): string
    {
        $framework = $this->getFramework();
        $assetMode = config('accelade.asset_mode', 'route');
        $syncDebounce = config('accelade.sync_debounce', 300);

        // Inline the JavaScript - use unified bundle (includes all frameworks)
        $jsPath = __DIR__.'/../dist/accelade.js';

        // Fallback to framework-specific bundle if unified doesn't exist
        if (! file_exists($jsPath)) {
            $jsPath = __DIR__."/../dist/accelade-{$framework}.js";
        }
        if (! file_exists($jsPath)) {
            $jsPath = __DIR__.'/../dist/accelade-vanilla.js';
        }

        $inlineJs = file_exists($jsPath) ? file_get_contents($jsPath) : "console.error('Accelade JS not found at: ' + '{$jsPath}');";

        // Get progress config
        $progressConfig = config('accelade.progress', []);
        $progressJson = json_encode($progressConfig);

        return <<<HTML
<script>
    window.AcceladeConfig = {
        framework: '{$framework}',
        syncDebounce: {$syncDebounce},
        csrfToken: document.querySelector('meta[name=\"csrf-token\"]')?.content || '',
        updateUrl: '/accelade/update',
        batchUpdateUrl: '/accelade/batch-update',
        progress: {$progressJson}
    };
</script>
<script>
{$inlineJs}
</script>
HTML;
    }

    /**
     * Generate any style tags for Accelade.
     */
    public function styles(): string
    {
        return <<<'HTML'
<style>
    /* ========================================
       Accelade Progress Bar (Inertia.js style)
       ======================================== */
    #accelade-progress {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        z-index: 99999;
        pointer-events: none;
        background: transparent;
        overflow: hidden;
    }

    #accelade-progress .bar {
        position: absolute;
        top: 0;
        left: 0;
        height: 100%;
        width: 0%;
        background: linear-gradient(90deg, #6366f1, #8b5cf6, #a855f7);
        box-shadow: 0 0 10px rgba(99, 102, 241, 0.7), 0 0 5px rgba(139, 92, 246, 0.5);
        transition: width 0.2s ease-out;
        border-radius: 0 2px 2px 0;
    }

    #accelade-progress .bar.indeterminate {
        width: 100% !important;
        animation: accelade-progress-indeterminate 1.5s ease-in-out infinite;
        background: linear-gradient(90deg,
            transparent 0%,
            #6366f1 20%,
            #8b5cf6 50%,
            #a855f7 80%,
            transparent 100%
        );
        background-size: 200% 100%;
    }

    @keyframes accelade-progress-indeterminate {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }

    #accelade-progress .spinner {
        position: fixed;
        top: 15px;
        right: 15px;
        width: 18px;
        height: 18px;
        border: 2px solid transparent;
        border-top-color: #6366f1;
        border-left-color: #8b5cf6;
        border-radius: 50%;
        animation: accelade-spinner 0.6s linear infinite;
        opacity: 0;
        transition: opacity 0.2s ease;
    }

    #accelade-progress .spinner.visible {
        opacity: 1;
    }

    @keyframes accelade-spinner {
        to { transform: rotate(360deg); }
    }

    /* ========================================
       Accelade Component Styles
       ======================================== */
    [data-accelade] {
        display: block;
    }

    /* Cloak: hide uninitialized components */
    [data-accelade-cloak],
    [a-cloak],
    [v-cloak] {
        opacity: 0 !important;
        pointer-events: none !important;
    }

    /* ========================================
       Page Transitions
       ======================================== */
    [data-accelade-page] {
        transition: opacity 0.15s ease-out, transform 0.15s ease-out;
    }

    [data-accelade-page].accelade-leaving {
        opacity: 0;
        transform: translateY(-8px);
    }

    [data-accelade-page].accelade-entering {
        opacity: 0;
        transform: translateY(8px);
    }

    /* Loading state during SPA navigation */
    .accelade-navigating {
        cursor: progress;
    }

    .accelade-navigating [data-accelade-page] {
        pointer-events: none;
    }

    /* ========================================
       Component Reveal Animation
       ======================================== */
    [data-accelade].accelade-ready {
        animation: accelade-reveal 0.25s cubic-bezier(0.4, 0, 0.2, 1) forwards;
    }

    @keyframes accelade-reveal {
        from {
            opacity: 0;
            transform: translateY(8px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Staggered reveal for multiple components */
    [data-accelade].accelade-ready:nth-child(1) { animation-delay: 0ms; }
    [data-accelade].accelade-ready:nth-child(2) { animation-delay: 50ms; }
    [data-accelade].accelade-ready:nth-child(3) { animation-delay: 100ms; }
    [data-accelade].accelade-ready:nth-child(4) { animation-delay: 150ms; }
    [data-accelade].accelade-ready:nth-child(5) { animation-delay: 200ms; }

    /* ========================================
       Accelade Notifications (Filament-style)
       CSS Variables for customization
       ======================================== */
    :root {
        --accelade-notif-width: 24rem;
        --accelade-notif-radius: 0.75rem;
        --accelade-notif-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
        --accelade-notif-bg: #fff;
        --accelade-notif-border: 1px solid rgb(229 231 235);
        --accelade-notif-title-color: #111827;
        --accelade-notif-body-color: #6b7280;
        --accelade-notif-success-icon: #10b981;
        --accelade-notif-success-bg: #ecfdf5;
        --accelade-notif-info-icon: #3b82f6;
        --accelade-notif-info-bg: #eff6ff;
        --accelade-notif-warning-icon: #f59e0b;
        --accelade-notif-warning-bg: #fffbeb;
        --accelade-notif-danger-icon: #ef4444;
        --accelade-notif-danger-bg: #fef2f2;
    }

    .accelade-notifications {
        position: fixed;
        z-index: 99999;
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        padding: 1rem;
        width: var(--accelade-notif-width);
        max-width: calc(100vw - 2rem);
        pointer-events: none;
    }

    .accelade-notifications-top-right { top: 0; right: 0; }
    .accelade-notifications-top-left { top: 0; left: 0; }
    .accelade-notifications-top-center { top: 0; left: 50%; transform: translateX(-50%); }
    .accelade-notifications-bottom-right { bottom: 0; right: 0; flex-direction: column-reverse; }
    .accelade-notifications-bottom-left { bottom: 0; left: 0; flex-direction: column-reverse; }
    .accelade-notifications-bottom-center { bottom: 0; left: 50%; transform: translateX(-50%); flex-direction: column-reverse; }

    .accelade-notif {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        padding: 1rem;
        background: var(--accelade-notif-bg);
        border: var(--accelade-notif-border);
        border-radius: var(--accelade-notif-radius);
        box-shadow: var(--accelade-notif-shadow);
        pointer-events: auto;
        opacity: 0;
        transform: translateY(-0.5rem);
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .accelade-notif-show { opacity: 1; transform: translateY(0); }
    .accelade-notif-hide { opacity: 0; transform: translateY(-0.5rem); }

    .accelade-notif-icon {
        flex-shrink: 0;
        width: 2rem;
        height: 2rem;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
    }
    .accelade-notif-icon svg { width: 1.25rem; height: 1.25rem; }

    .accelade-notif-success .accelade-notif-icon { background: var(--accelade-notif-success-bg); color: var(--accelade-notif-success-icon); }
    .accelade-notif-info .accelade-notif-icon { background: var(--accelade-notif-info-bg); color: var(--accelade-notif-info-icon); }
    .accelade-notif-warning .accelade-notif-icon { background: var(--accelade-notif-warning-bg); color: var(--accelade-notif-warning-icon); }
    .accelade-notif-danger .accelade-notif-icon { background: var(--accelade-notif-danger-bg); color: var(--accelade-notif-danger-icon); }

    .accelade-notif-content { flex: 1; min-width: 0; }
    .accelade-notif-title { font-weight: 600; font-size: 0.875rem; color: var(--accelade-notif-title-color); line-height: 1.25rem; }
    .accelade-notif-body { font-size: 0.875rem; color: var(--accelade-notif-body-color); margin-top: 0.25rem; line-height: 1.25rem; }

    .accelade-notif-actions { display: flex; gap: 0.5rem; margin-top: 0.75rem; flex-wrap: wrap; }
    .accelade-notif-action {
        display: inline-flex;
        align-items: center;
        padding: 0.375rem 0.75rem;
        font-size: 0.75rem;
        font-weight: 600;
        color: #4f46e5;
        background: transparent;
        border: none;
        border-radius: 0.375rem;
        cursor: pointer;
        transition: background 0.15s;
        text-decoration: none;
    }
    .accelade-notif-action:hover { background: #f3f4f6; }

    .accelade-notif-close {
        flex-shrink: 0;
        width: 1.5rem;
        height: 1.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        border: none;
        background: transparent;
        cursor: pointer;
        color: #9ca3af;
        border-radius: 0.375rem;
        transition: all 0.15s;
        margin: -0.25rem -0.25rem -0.25rem 0;
    }
    .accelade-notif-close:hover { background: #f3f4f6; color: #6b7280; }
    .accelade-notif-close svg { width: 1rem; height: 1rem; }
</style>
HTML;
    }

    /**
     * Start an inline reactive component.
     */
    public function startComponent(array $state = [], string|array|null $sync = null): void
    {
        $this->componentCounter++;
        $componentId = $this->generateComponentId();

        // Parse sync properties
        $syncProperties = [];
        if ($sync !== null) {
            $syncProperties = is_array($sync) ? $sync : explode(',', $sync);
            $syncProperties = array_filter(array_map('trim', $syncProperties));
        }

        $this->componentStack[] = [
            'id' => $componentId,
            'state' => $state,
            'sync' => $syncProperties,
        ];

        ob_start();
    }

    /**
     * End an inline reactive component and return the rendered output.
     */
    public function endComponent(): string
    {
        $content = ob_get_clean();
        $component = array_pop($this->componentStack);

        return $this->wrapComponent($component, $content);
    }

    /**
     * Get the current component's state (for use in views).
     */
    public function getCurrentState(): array
    {
        if (empty($this->componentStack)) {
            return [];
        }

        return end($this->componentStack)['state'] ?? [];
    }

    /**
     * Start a component from a compiled tag.
     */
    public function startComponentFromTag(string $name, array $attributes = []): void
    {
        $this->componentCounter++;
        $componentId = $this->generateComponentId($name);

        // Extract sync properties
        $syncProperties = [];
        if (isset($attributes['sync'])) {
            $syncProperties = is_array($attributes['sync'])
                ? $attributes['sync']
                : explode(',', $attributes['sync']);
            unset($attributes['sync']);
        }

        // Separate bound attributes from regular ones
        $state = [];
        $props = [];
        foreach ($attributes as $key => $value) {
            if (Str::startsWith($key, 'bind:')) {
                $stateKey = Str::after($key, 'bind:');
                $state[$stateKey] = $value;

                // Normalize common patterns: initial-X becomes X
                if (Str::startsWith($stateKey, 'initial-')) {
                    $normalizedKey = Str::after($stateKey, 'initial-');
                    $state[$normalizedKey] = $value;
                }
            } elseif (Str::startsWith($key, 'on:')) {
                // Event handlers are passed to JS
                $props[$key] = $value;
            } else {
                $props[$key] = $value;
            }
        }

        $this->componentStack[] = [
            'id' => $componentId,
            'name' => $name,
            'state' => $state,
            'props' => $props,
            'sync' => $syncProperties,
        ];

        ob_start();
    }

    /**
     * End a tag-based component and return the rendered output.
     */
    public function endComponentFromTag(): string
    {
        $content = ob_get_clean();
        $component = array_pop($this->componentStack);

        return $this->wrapComponent($component, $content);
    }

    /**
     * Wrap component content with Accelade data attributes.
     */
    protected function wrapComponent(array $component, string $content): string
    {
        $id = $component['id'];
        $state = json_encode($component['state'] ?? []);
        $sync = implode(',', $component['sync'] ?? []);
        $props = json_encode($component['props'] ?? []);

        return <<<HTML
<div data-accelade
     data-accelade-cloak
     data-accelade-id="{$id}"
     data-accelade-state='{$state}'
     data-accelade-sync="{$sync}"
     data-accelade-props='{$props}'>
    {$content}
</div>
HTML;
    }

    /**
     * Generate a unique component ID.
     */
    protected function generateComponentId(?string $name = null): string
    {
        $base = $name ?? 'accelade';

        return $base.'-'.Str::random(8);
    }

    /**
     * Get the configured framework.
     */
    public function framework(): string
    {
        return config('accelade.framework', 'vue');
    }
}
