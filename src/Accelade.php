<?php

declare(strict_types=1);

namespace Accelade;

use Accelade\Broadcasting\EventResponse;
use Accelade\Exceptions\ExceptionHandler;
use Accelade\Support\SharedData;
use Closure;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Exceptions\Handler;
use Illuminate\Support\Str;

class Accelade
{
    protected Application $app;

    protected array $componentStack = [];

    protected int $componentCounter = 0;

    protected SharedData $sharedData;

    /**
     * Injected scripts from other packages.
     *
     * @var array<string, string|Closure>
     */
    protected array $injectedScripts = [];

    /**
     * Injected styles from other packages.
     *
     * @var array<string, string|Closure>
     */
    protected array $injectedStyles = [];

    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->sharedData = new SharedData;
    }

    /**
     * Current framework override (set via setFramework method)
     */
    protected ?string $frameworkOverride = null;

    /**
     * Register a script to be included with @acceladeScripts.
     * Allows other packages to inject their JavaScript.
     *
     * @param  string  $key  Unique key to prevent duplicates
     * @param  string|Closure  $script  Script content or closure that returns script
     */
    public function registerScript(string $key, string|Closure $script): self
    {
        $this->injectedScripts[$key] = $script;

        return $this;
    }

    /**
     * Register a style to be included with @acceladeStyles.
     * Allows other packages to inject their CSS.
     *
     * @param  string  $key  Unique key to prevent duplicates
     * @param  string|Closure  $style  Style content or closure that returns style
     */
    public function registerStyle(string $key, string|Closure $style): self
    {
        $this->injectedStyles[$key] = $style;

        return $this;
    }

    /**
     * Get all injected scripts rendered as HTML.
     */
    protected function renderInjectedScripts(): string
    {
        $output = '';
        foreach ($this->injectedScripts as $script) {
            $content = $script instanceof Closure ? $script() : $script;
            $output .= $content."\n";
        }

        return $output;
    }

    /**
     * Get all injected styles rendered as HTML.
     */
    protected function renderInjectedStyles(): string
    {
        $output = '';
        foreach ($this->injectedStyles as $style) {
            $content = $style instanceof Closure ? $style() : $style;
            $output .= $content."\n";
        }

        return $output;
    }

    /**
     * Share data globally across the application.
     * Data will be available in the frontend via state.shared.
     *
     * @param  array<string, mixed>|string  $key
     */
    public function share(array|string $key, mixed $value = null): self
    {
        $this->sharedData->share($key, $value);

        return $this;
    }

    /**
     * Get a shared value by key.
     */
    public function getShared(string $key, mixed $default = null): mixed
    {
        return $this->sharedData->get($key, $default);
    }

    /**
     * Get all shared data.
     *
     * @return array<string, mixed>
     */
    public function allShared(): array
    {
        return $this->sharedData->all();
    }

    /**
     * Get the SharedData instance.
     */
    public function shared(): SharedData
    {
        return $this->sharedData;
    }

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

        // Get navigation config
        $navigationConfig = config('accelade.navigation', []);
        $navigationJson = json_encode($navigationConfig);

        // Get shared data
        $sharedData = $this->allShared();
        $sharedJson = json_encode($sharedData, JSON_THROW_ON_ERROR);

        // Get error handling config
        $errorConfig = config('accelade.errors', []);
        $errorsJson = json_encode([
            'suppressErrors' => $errorConfig['suppress_errors'] ?? false,
            'showToasts' => $errorConfig['show_toasts'] ?? true,
            'logErrors' => $errorConfig['log_errors'] ?? true,
            'debug' => $errorConfig['debug'] ?? config('app.debug', false),
        ]);

        $injectedScripts = $this->renderInjectedScripts();

        return <<<HTML
<script>
    window.AcceladeConfig = {
        framework: '{$framework}',
        syncDebounce: {$syncDebounce},
        csrfToken: document.querySelector('meta[name=\"csrf-token\"]')?.content || '',
        updateUrl: '/accelade/update',
        batchUpdateUrl: '/accelade/batch-update',
        progress: {$progressJson},
        navigation: {$navigationJson},
        shared: {$sharedJson},
        errors: {$errorsJson}
    };
</script>
<script>
{$inlineJs}
</script>
{$injectedScripts}
HTML;
    }

    /**
     * Generate any style tags for Accelade.
     */
    public function styles(): string
    {
        $styles = <<<'HTML'
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
        inset-inline-end: 15px;
        width: 18px;
        height: 18px;
        border: 2px solid transparent;
        border-top-color: #6366f1;
        border-inline-start-color: #8b5cf6;
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
        --accelade-notif-action-color: #4f46e5;
        --accelade-notif-action-hover-bg: #f3f4f6;
        --accelade-notif-close-color: #9ca3af;
        --accelade-notif-close-hover-bg: #f3f4f6;
        --accelade-notif-close-hover-color: #6b7280;
    }

    /* Dark mode notifications */
    .dark, [data-theme="dark"] {
        --accelade-notif-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.3), 0 8px 10px -6px rgb(0 0 0 / 0.2);
        --accelade-notif-bg: #1e293b;
        --accelade-notif-border: 1px solid rgb(51 65 85);
        --accelade-notif-title-color: #f1f5f9;
        --accelade-notif-body-color: #94a3b8;
        --accelade-notif-success-icon: #34d399;
        --accelade-notif-success-bg: rgba(16, 185, 129, 0.15);
        --accelade-notif-info-icon: #60a5fa;
        --accelade-notif-info-bg: rgba(59, 130, 246, 0.15);
        --accelade-notif-warning-icon: #fbbf24;
        --accelade-notif-warning-bg: rgba(245, 158, 11, 0.15);
        --accelade-notif-danger-icon: #f87171;
        --accelade-notif-danger-bg: rgba(239, 68, 68, 0.15);
        --accelade-notif-action-color: #818cf8;
        --accelade-notif-action-hover-bg: #334155;
        --accelade-notif-close-color: #64748b;
        --accelade-notif-close-hover-bg: #334155;
        --accelade-notif-close-hover-color: #94a3b8;
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

    .accelade-notifications-top-right { top: 0; inset-inline-end: 0; }
    .accelade-notifications-top-left { top: 0; inset-inline-start: 0; }
    .accelade-notifications-top-center { top: 0; left: 50%; transform: translateX(-50%); }
    .accelade-notifications-bottom-right { bottom: 0; inset-inline-end: 0; flex-direction: column-reverse; }
    .accelade-notifications-bottom-left { bottom: 0; inset-inline-start: 0; flex-direction: column-reverse; }
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
        color: var(--accelade-notif-action-color);
        background: transparent;
        border: none;
        border-radius: 0.375rem;
        cursor: pointer;
        transition: background 0.15s;
        text-decoration: none;
    }
    .accelade-notif-action:hover { background: var(--accelade-notif-action-hover-bg); }

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
        color: var(--accelade-notif-close-color);
        border-radius: 0.375rem;
        transition: all 0.15s;
        margin-block: -0.25rem;
        margin-inline: 0 -0.25rem;
    }
    .accelade-notif-close:hover { background: var(--accelade-notif-close-hover-bg); color: var(--accelade-notif-close-hover-color); }
    .accelade-notif-close svg { width: 1rem; height: 1rem; }

    /* Title-only notification: vertically center all elements */
    .accelade-notif-title-only { align-items: center; }

    /* ========================================
       Accelade Show/Hide Transitions
       ======================================== */
    [a-show],
    [v-show],
    [s-show],
    [ng-show],
    [data-state-show] {
        transition: opacity 0.1s cubic-bezier(0.4, 0, 0.2, 1), transform 0.1s cubic-bezier(0.4, 0, 0.2, 1);
        transform-origin: top center;
    }

    .accelade-hiding {
        opacity: 0 !important;
        transform: scale(0.98);
        pointer-events: none;
    }

    .accelade-visible {
        opacity: 1;
        transform: scale(1);
    }

    /* ========================================
       Animation Preset Utility Classes
       (Tailwind-compatible for animation presets)
       ======================================== */

    /* Transitions */
    .transition { transition-property: color, background-color, border-color, text-decoration-color, fill, stroke, opacity, box-shadow, transform, filter, backdrop-filter; transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1); transition-duration: 150ms; }
    .transition-opacity { transition-property: opacity; transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1); transition-duration: 150ms; }
    .transition-all { transition-property: all; transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1); transition-duration: 150ms; }
    .transition-transform { transition-property: transform; transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1); transition-duration: 150ms; }

    /* Durations */
    .duration-100 { transition-duration: 100ms; }
    .duration-150 { transition-duration: 150ms; }
    .duration-200 { transition-duration: 200ms; }
    .duration-300 { transition-duration: 300ms; }
    .duration-500 { transition-duration: 500ms; }

    /* Easing */
    .ease-in { transition-timing-function: cubic-bezier(0.4, 0, 1, 1); }
    .ease-out { transition-timing-function: cubic-bezier(0, 0, 0.2, 1); }
    .ease-in-out { transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1); }

    /* Opacity */
    .opacity-0 { opacity: 0; }
    .opacity-100 { opacity: 1; }

    /* Scale */
    .scale-0 { transform: scale(0); }
    .scale-95 { transform: scale(0.95); }
    .scale-100 { transform: scale(1); }

    /* Translate X */
    .translate-x-0 { transform: translateX(0); }
    .translate-x-full { transform: translateX(100%); }
    .-translate-x-full { transform: translateX(-100%); }

    /* Translate Y */
    .translate-y-0 { transform: translateY(0); }
    .translate-y-full { transform: translateY(100%); }
    .-translate-y-full { transform: translateY(-100%); }

    /* Combined transforms (for animations that use multiple) */
    .opacity-0.scale-95 { opacity: 0; transform: scale(0.95); }
    .opacity-100.scale-100 { opacity: 1; transform: scale(1); }
    .opacity-0.scale-0 { opacity: 0; transform: scale(0); }
    .opacity-0.-translate-x-full { opacity: 0; transform: translateX(-100%); }
    .opacity-100.translate-x-0 { opacity: 1; transform: translateX(0); }
    .opacity-0.translate-x-full { opacity: 0; transform: translateX(100%); }
    .opacity-0.translate-y-full { opacity: 0; transform: translateY(100%); }
    .opacity-100.translate-y-0 { opacity: 1; transform: translateY(0); }
    .opacity-0.-translate-y-full { opacity: 0; transform: translateY(-100%); }

    /* ========================================
       Accelade Lazy Loading
       ======================================== */
    .accelade-lazy-wrapper {
        position: relative;
    }

    .accelade-lazy-placeholder {
        transition: opacity 0.2s ease-out;
    }

    .accelade-lazy-placeholder.accelade-lazy-hiding {
        opacity: 0;
    }

    .accelade-lazy-content {
        opacity: 0;
        transition: opacity 0.3s ease-out;
    }

    .accelade-lazy-content.accelade-lazy-visible {
        opacity: 1;
    }

    /* Default spinner */
    .accelade-lazy-loading {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem;
    }

    .accelade-lazy-spinner {
        width: 24px;
        height: 24px;
        border: 2px solid #e5e7eb;
        border-top-color: #6366f1;
        border-radius: 50%;
        animation: accelade-lazy-spin 0.8s linear infinite;
    }

    @keyframes accelade-lazy-spin {
        to { transform: rotate(360deg); }
    }

    /* ========================================
       Shimmer / Skeleton Loader
       ======================================== */
    .accelade-shimmer-container {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        padding: 0.25rem 0;
    }

    .accelade-shimmer-container.accelade-shimmer-rounded {
        border-radius: 0.5rem;
        overflow: hidden;
    }

    .accelade-shimmer-container.accelade-shimmer-circle {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        padding: 0;
        overflow: hidden;
    }

    .accelade-shimmer-line {
        height: 1rem;
        background: linear-gradient(
            90deg,
            #f0f0f0 0%,
            #e0e0e0 20%,
            #f0f0f0 40%,
            #f0f0f0 100%
        );
        background-size: 200% 100%;
        animation: accelade-shimmer 1.5s ease-in-out infinite;
        border-radius: 0.25rem;
    }

    .accelade-shimmer-line-short {
        width: 60%;
    }

    .accelade-shimmer-circle-inner {
        width: 100%;
        height: 100%;
        background: linear-gradient(
            90deg,
            #f0f0f0 0%,
            #e0e0e0 20%,
            #f0f0f0 40%,
            #f0f0f0 100%
        );
        background-size: 200% 100%;
        animation: accelade-shimmer 1.5s ease-in-out infinite;
    }

    @keyframes accelade-shimmer {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }

    /* Shimmer variants - dark mode auto-detected */
    .dark .accelade-shimmer-line,
    .dark .accelade-shimmer-circle-inner,
    .dark .accelade-shimmer-inline,
    [data-theme="dark"] .accelade-shimmer-line,
    [data-theme="dark"] .accelade-shimmer-circle-inner,
    [data-theme="dark"] .accelade-shimmer-inline,
    .accelade-shimmer-dark .accelade-shimmer-line,
    .accelade-shimmer-dark .accelade-shimmer-circle-inner {
        background: linear-gradient(
            90deg,
            #374151 0%,
            #4b5563 20%,
            #374151 40%,
            #374151 100%
        );
        background-size: 200% 100%;
    }

    /* Card shimmer preset */
    .accelade-shimmer-card {
        padding: 1rem;
        background: #fff;
        border-radius: 0.5rem;
        border: 1px solid #e5e7eb;
    }
    .dark .accelade-shimmer-card,
    [data-theme="dark"] .accelade-shimmer-card {
        background: #1e293b;
        border-color: #334155;
    }

    .accelade-shimmer-card .accelade-shimmer-line:first-child {
        height: 1.5rem;
        width: 40%;
        margin-bottom: 0.5rem;
    }

    /* Image shimmer preset */
    .accelade-shimmer-image {
        aspect-ratio: 16/9;
        border-radius: 0.5rem;
    }

    .accelade-shimmer-image .accelade-shimmer-line {
        height: 100%;
        border-radius: 0.5rem;
    }

    /* Avatar shimmer preset */
    .accelade-shimmer-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .accelade-shimmer-avatar .accelade-shimmer-line {
        height: 100%;
        border-radius: 50%;
    }

    /* Inline shimmer (for text) */
    .accelade-shimmer-inline {
        display: inline-block;
        height: 1em;
        width: 100px;
        vertical-align: middle;
        border-radius: 0.25rem;
        background: linear-gradient(
            90deg,
            #f0f0f0 0%,
            #e0e0e0 20%,
            #f0f0f0 40%,
            #f0f0f0 100%
        );
        background-size: 200% 100%;
        animation: accelade-shimmer 1.5s ease-in-out infinite;
    }

    /* ========================================
       ApexCharts Dark Mode Support
       ======================================== */
    .dark .apexcharts-menu,
    [data-theme="dark"] .apexcharts-menu {
        background: #1e293b !important;
        border: 1px solid #334155 !important;
        box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.3), 0 4px 6px -4px rgb(0 0 0 / 0.2) !important;
    }

    .dark .apexcharts-menu-item,
    [data-theme="dark"] .apexcharts-menu-item {
        color: #e2e8f0 !important;
    }

    .dark .apexcharts-menu-item:hover,
    [data-theme="dark"] .apexcharts-menu-item:hover {
        background: #334155 !important;
    }

    .dark .apexcharts-toolbar .apexcharts-menu-icon svg,
    .dark .apexcharts-toolbar .apexcharts-pan-icon svg,
    .dark .apexcharts-toolbar .apexcharts-reset-icon svg,
    .dark .apexcharts-toolbar .apexcharts-selection-icon svg,
    .dark .apexcharts-toolbar .apexcharts-zoom-icon svg,
    .dark .apexcharts-toolbar .apexcharts-zoomin-icon svg,
    .dark .apexcharts-toolbar .apexcharts-zoomout-icon svg,
    [data-theme="dark"] .apexcharts-toolbar .apexcharts-menu-icon svg,
    [data-theme="dark"] .apexcharts-toolbar .apexcharts-pan-icon svg,
    [data-theme="dark"] .apexcharts-toolbar .apexcharts-reset-icon svg,
    [data-theme="dark"] .apexcharts-toolbar .apexcharts-selection-icon svg,
    [data-theme="dark"] .apexcharts-toolbar .apexcharts-zoom-icon svg,
    [data-theme="dark"] .apexcharts-toolbar .apexcharts-zoomin-icon svg,
    [data-theme="dark"] .apexcharts-toolbar .apexcharts-zoomout-icon svg {
        fill: #94a3b8 !important;
    }

    .dark .apexcharts-toolbar .apexcharts-menu-icon:hover svg,
    .dark .apexcharts-toolbar .apexcharts-pan-icon:hover svg,
    .dark .apexcharts-toolbar .apexcharts-reset-icon:hover svg,
    .dark .apexcharts-toolbar .apexcharts-selection-icon:hover svg,
    .dark .apexcharts-toolbar .apexcharts-zoom-icon:hover svg,
    .dark .apexcharts-toolbar .apexcharts-zoomin-icon:hover svg,
    .dark .apexcharts-toolbar .apexcharts-zoomout-icon:hover svg,
    [data-theme="dark"] .apexcharts-toolbar .apexcharts-menu-icon:hover svg,
    [data-theme="dark"] .apexcharts-toolbar .apexcharts-pan-icon:hover svg,
    [data-theme="dark"] .apexcharts-toolbar .apexcharts-reset-icon:hover svg,
    [data-theme="dark"] .apexcharts-toolbar .apexcharts-selection-icon:hover svg,
    [data-theme="dark"] .apexcharts-toolbar .apexcharts-zoom-icon:hover svg,
    [data-theme="dark"] .apexcharts-toolbar .apexcharts-zoomin-icon:hover svg,
    [data-theme="dark"] .apexcharts-toolbar .apexcharts-zoomout-icon:hover svg {
        fill: #e2e8f0 !important;
    }

    .dark .apexcharts-tooltip,
    [data-theme="dark"] .apexcharts-tooltip {
        background: #1e293b !important;
        border: 1px solid #334155 !important;
        color: #e2e8f0 !important;
    }

    .dark .apexcharts-tooltip-title,
    [data-theme="dark"] .apexcharts-tooltip-title {
        background: #0f172a !important;
        border-bottom: 1px solid #334155 !important;
        color: #f1f5f9 !important;
    }

    .dark .apexcharts-tooltip-text-y-label,
    .dark .apexcharts-tooltip-text-y-value,
    [data-theme="dark"] .apexcharts-tooltip-text-y-label,
    [data-theme="dark"] .apexcharts-tooltip-text-y-value {
        color: #e2e8f0 !important;
    }

    .dark .apexcharts-xaxistooltip,
    [data-theme="dark"] .apexcharts-xaxistooltip {
        background: #1e293b !important;
        border: 1px solid #334155 !important;
        color: #e2e8f0 !important;
    }

    .dark .apexcharts-xaxistooltip-bottom:before,
    [data-theme="dark"] .apexcharts-xaxistooltip-bottom:before {
        border-bottom-color: #334155 !important;
    }

    .dark .apexcharts-xaxistooltip-bottom:after,
    [data-theme="dark"] .apexcharts-xaxistooltip-bottom:after {
        border-bottom-color: #1e293b !important;
    }

    .dark .apexcharts-yaxistooltip,
    [data-theme="dark"] .apexcharts-yaxistooltip {
        background: #1e293b !important;
        border: 1px solid #334155 !important;
        color: #e2e8f0 !important;
    }

    .dark .apexcharts-legend-text,
    [data-theme="dark"] .apexcharts-legend-text {
        color: #94a3b8 !important;
    }

    .dark .apexcharts-text,
    .dark .apexcharts-xaxis-label,
    .dark .apexcharts-yaxis-label,
    [data-theme="dark"] .apexcharts-text,
    [data-theme="dark"] .apexcharts-xaxis-label,
    [data-theme="dark"] .apexcharts-yaxis-label {
        fill: #94a3b8 !important;
    }

    .dark .apexcharts-gridline,
    [data-theme="dark"] .apexcharts-gridline {
        stroke: #334155 !important;
    }

    .dark .apexcharts-radar-series polygon,
    [data-theme="dark"] .apexcharts-radar-series polygon {
        stroke: #475569 !important;
    }

    .dark .apexcharts-radar-series line,
    [data-theme="dark"] .apexcharts-radar-series line {
        stroke: #475569 !important;
    }

    /* ========================================
       Event Calendar Light/Dark Mode
       ======================================== */
    /* Light mode - ensure proper colors */
    .ec {
        --ec-bg-color: #ffffff;
        --ec-text-color: #1f2937;
        --ec-border-color: #e5e7eb;
        --ec-today-bg-color: rgba(59, 130, 246, 0.08);
        --ec-highlight-color: rgba(59, 130, 246, 0.05);
        --ec-list-day-bg-color: #f8fafc;
    }

    /* Dark mode calendar - match docs-bg (#0f172a) */
    .dark .ec,
    [data-theme="dark"] .ec {
        --ec-bg-color: #0f172a;
        --ec-text-color: #f1f5f9;
        --ec-border-color: #334155;
        --ec-today-bg-color: rgba(59, 130, 246, 0.15);
        --ec-highlight-color: rgba(59, 130, 246, 0.1);
        --ec-list-day-bg-color: #1e293b;
    }

    /* Calendar buttons - Light mode */
    .ec .ec-button {
        background-color: #ffffff;
        border: 1px solid #d1d5db;
        color: #374151;
        font-weight: 500;
        font-size: 0.875rem;
        padding: 0.5rem 0.875rem;
        border-radius: 0.375rem;
        transition: all 0.15s ease;
    }

    .ec .ec-button:hover:not(:disabled) {
        background-color: #f9fafb;
        border-color: #9ca3af;
    }

    .ec .ec-button.ec-active,
    .ec .ec-button:active:not(:disabled) {
        background-color: #3b82f6;
        border-color: #3b82f6;
        color: #ffffff;
    }

    .ec .ec-button:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    /* Calendar buttons - Dark mode */
    .dark .ec .ec-button,
    [data-theme="dark"] .ec .ec-button {
        background-color: #1e293b;
        border-color: #475569;
        color: #e2e8f0;
    }

    .dark .ec .ec-button:hover:not(:disabled),
    [data-theme="dark"] .ec .ec-button:hover:not(:disabled) {
        background-color: #334155;
        border-color: #64748b;
    }

    .dark .ec .ec-button.ec-active,
    .dark .ec .ec-button:active:not(:disabled),
    [data-theme="dark"] .ec .ec-button.ec-active,
    [data-theme="dark"] .ec .ec-button:active:not(:disabled) {
        background-color: #3b82f6;
        border-color: #3b82f6;
        color: #ffffff;
    }

    /* Button group styling */
    .ec .ec-button-group {
        display: inline-flex;
        border-radius: 0.375rem;
        overflow: hidden;
    }

    .ec .ec-button-group .ec-button {
        border-radius: 0;
        margin-left: -1px;
    }

    .ec .ec-button-group .ec-button:first-child {
        border-radius: 0.375rem 0 0 0.375rem;
        margin-left: 0;
    }

    .ec .ec-button-group .ec-button:last-child {
        border-radius: 0 0.375rem 0.375rem 0;
    }

    /* Calendar container padding */
    [data-accelade-calendar] {
        padding: 0.5rem;
    }

    /* Calendar toolbar spacing */
    .ec .ec-toolbar {
        padding: 0.5rem 0;
        margin-bottom: 0.75rem;
    }

    /* Calendar title styling */
    .ec .ec-title {
        font-weight: 600;
        font-size: 1.125rem;
        color: var(--ec-text-color);
    }

    /* Calendar day headers */
    .ec .ec-day-head {
        font-weight: 500;
        font-size: 0.875rem;
        color: var(--ec-text-color);
        opacity: 0.7;
    }

    /* Calendar grid borders */
    .ec .ec-header,
    .ec .ec-body,
    .ec .ec-day {
        border-color: var(--ec-border-color);
    }
</style>
HTML;

        // Include the bundled CSS (contains calendar and other component styles)
        $cssPath = __DIR__.'/../dist/accelade.css';
        if (file_exists($cssPath)) {
            $bundledCss = file_get_contents($cssPath);
            $styles .= "\n<style>\n/* Accelade Bundled Styles */\n{$bundledCss}\n</style>";
        }

        // Append injected styles from other packages
        return $styles.$this->renderInjectedStyles();
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

    /**
     * Create a redirect action for broadcast events.
     *
     * When this payload is broadcast, the client will redirect to the given URL.
     *
     * @param  string  $url  The URL to redirect to
     */
    public static function redirectOnEvent(string $url): EventResponse
    {
        return EventResponse::redirect($url);
    }

    /**
     * Create a redirect to route action for broadcast events.
     *
     * @param  string  $route  The route name
     * @param  array  $parameters  Route parameters
     */
    public static function redirectToRouteOnEvent(string $route, array $parameters = []): EventResponse
    {
        return EventResponse::redirectToRoute($route, $parameters);
    }

    /**
     * Create a refresh action for broadcast events.
     *
     * When this payload is broadcast, the client will refresh the page.
     */
    public static function refreshOnEvent(): EventResponse
    {
        return EventResponse::refresh();
    }

    /**
     * Create a toast notification action for broadcast events.
     *
     * When this payload is broadcast, the client will show a toast notification.
     *
     * @param  string  $message  The toast message
     * @param  string  $type  The toast type (success, info, warning, danger)
     */
    public static function toastOnEvent(string $message, string $type = 'info'): EventResponse
    {
        return EventResponse::toast($message, $type);
    }

    /**
     * Create a success toast action for broadcast events.
     */
    public static function successOnEvent(string $message): EventResponse
    {
        return EventResponse::success($message);
    }

    /**
     * Create an info toast action for broadcast events.
     */
    public static function infoOnEvent(string $message): EventResponse
    {
        return EventResponse::info($message);
    }

    /**
     * Create a warning toast action for broadcast events.
     */
    public static function warningOnEvent(string $message): EventResponse
    {
        return EventResponse::warning($message);
    }

    /**
     * Create a danger toast action for broadcast events.
     */
    public static function dangerOnEvent(string $message): EventResponse
    {
        return EventResponse::danger($message);
    }

    /**
     * Create an exception handler for Accelade requests.
     *
     * Register this in your app/Exceptions/Handler.php:
     *
     * ```php
     * use Accelade\Facades\Accelade;
     *
     * public function register(): void
     * {
     *     $this->renderable(Accelade::exceptionHandler($this));
     * }
     * ```
     *
     * With custom handler for specific exceptions:
     *
     * ```php
     * $this->renderable(Accelade::exceptionHandler($this, function ($e, $request) {
     *     if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpException) {
     *         if ($e->getStatusCode() === 419) {
     *             return redirect()->route('login', ['reason' => 'timeout']);
     *         }
     *     }
     * }));
     * ```
     *
     * @param  Handler  $handler  The Laravel exception handler
     * @param  Closure|null  $customHandler  Optional custom handler for specific exceptions
     */
    public static function exceptionHandler(Handler $handler, ?Closure $customHandler = null): Closure
    {
        return ExceptionHandler::handle($handler, $customHandler);
    }
}
