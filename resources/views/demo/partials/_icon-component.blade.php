{{-- Icon Component Section --}}
@props(['prefix' => 'a'])

<section class="rounded-xl p-6 mb-6 border border-[var(--docs-border)]" style="background: var(--docs-bg-alt);">
    <div class="flex items-center gap-3 mb-2">
        <span class="w-2.5 h-2.5 bg-purple-500 rounded-full"></span>
        <h3 class="text-lg font-semibold" style="color: var(--docs-text);">Icon Component</h3>
    </div>
    <p class="text-sm mb-6" style="color: var(--docs-text-muted);">
        Render icons from any installed Blade Icons package with automatic fallback support. Supports multiple sizes and custom styling.
    </p>

    {{-- Icon Sizes Demo --}}
    <div class="rounded-xl p-4 border border-blue-500/30 mb-6" style="background: rgba(59, 130, 246, 0.1);">
        <h4 class="font-medium mb-4 flex items-center gap-2" style="color: var(--docs-text);">
            <span class="text-xs px-2 py-1 bg-blue-500/20 text-blue-500 rounded">Sizes</span>
            Icon Size Variants
        </h4>

        <div class="flex flex-wrap items-end gap-6 mb-4">
            <div class="flex flex-col items-center gap-2">
                <x-accelade::icon name="heroicon-o-star" size="xs" class="text-yellow-500" />
                <span class="text-xs" style="color: var(--docs-text-muted);">xs</span>
            </div>
            <div class="flex flex-col items-center gap-2">
                <x-accelade::icon name="heroicon-o-star" size="sm" class="text-yellow-500" />
                <span class="text-xs" style="color: var(--docs-text-muted);">sm</span>
            </div>
            <div class="flex flex-col items-center gap-2">
                <x-accelade::icon name="heroicon-o-star" size="md" class="text-yellow-500" />
                <span class="text-xs" style="color: var(--docs-text-muted);">md</span>
            </div>
            <div class="flex flex-col items-center gap-2">
                <x-accelade::icon name="heroicon-o-star" size="base" class="text-yellow-500" />
                <span class="text-xs" style="color: var(--docs-text-muted);">base</span>
            </div>
            <div class="flex flex-col items-center gap-2">
                <x-accelade::icon name="heroicon-o-star" size="lg" class="text-yellow-500" />
                <span class="text-xs" style="color: var(--docs-text-muted);">lg</span>
            </div>
            <div class="flex flex-col items-center gap-2">
                <x-accelade::icon name="heroicon-o-star" size="xl" class="text-yellow-500" />
                <span class="text-xs" style="color: var(--docs-text-muted);">xl</span>
            </div>
            <div class="flex flex-col items-center gap-2">
                <x-accelade::icon name="heroicon-o-star" size="2xl" class="text-yellow-500" />
                <span class="text-xs" style="color: var(--docs-text-muted);">2xl</span>
            </div>
            <div class="flex flex-col items-center gap-2">
                <x-accelade::icon name="heroicon-o-star" size="3xl" class="text-yellow-500" />
                <span class="text-xs" style="color: var(--docs-text-muted);">3xl</span>
            </div>
            <div class="flex flex-col items-center gap-2">
                <x-accelade::icon name="heroicon-o-star" size="4xl" class="text-yellow-500" />
                <span class="text-xs" style="color: var(--docs-text-muted);">4xl</span>
            </div>
        </div>

        <p class="text-xs" style="color: var(--docs-text-muted);">
            Sizes: xs (12px), sm (16px), md (20px), base (24px), lg (28px), xl (32px), 2xl (40px), 3xl (48px), 4xl (64px)
        </p>
    </div>

    {{-- Icon Variants Demo --}}
    <div class="rounded-xl p-4 border border-emerald-500/30 mb-6" style="background: rgba(16, 185, 129, 0.1);">
        <h4 class="font-medium mb-4 flex items-center gap-2" style="color: var(--docs-text);">
            <span class="text-xs px-2 py-1 bg-emerald-500/20 text-emerald-500 rounded">Variants</span>
            Heroicons Variants
        </h4>

        <div class="grid grid-cols-3 gap-4 mb-4">
            <div class="flex flex-col items-center gap-2 p-4 rounded-lg" style="background: var(--docs-bg);">
                <x-accelade::icon name="heroicon-o-heart" size="2xl" class="text-red-500" />
                <span class="text-xs font-medium" style="color: var(--docs-text);">Outline</span>
                <code class="text-xs px-1 py-0.5 rounded" style="background: var(--docs-bg-alt); color: var(--docs-text-muted);">heroicon-o-*</code>
            </div>
            <div class="flex flex-col items-center gap-2 p-4 rounded-lg" style="background: var(--docs-bg);">
                <x-accelade::icon name="heroicon-s-heart" size="2xl" class="text-red-500" />
                <span class="text-xs font-medium" style="color: var(--docs-text);">Solid</span>
                <code class="text-xs px-1 py-0.5 rounded" style="background: var(--docs-bg-alt); color: var(--docs-text-muted);">heroicon-s-*</code>
            </div>
            <div class="flex flex-col items-center gap-2 p-4 rounded-lg" style="background: var(--docs-bg);">
                <x-accelade::icon name="heroicon-m-heart" size="2xl" class="text-red-500" />
                <span class="text-xs font-medium" style="color: var(--docs-text);">Mini</span>
                <code class="text-xs px-1 py-0.5 rounded" style="background: var(--docs-bg-alt); color: var(--docs-text-muted);">heroicon-m-*</code>
            </div>
        </div>
    </div>

    {{-- Colors Demo --}}
    <div class="rounded-xl p-4 border border-pink-500/30 mb-6" style="background: rgba(236, 72, 153, 0.1);">
        <h4 class="font-medium mb-4 flex items-center gap-2" style="color: var(--docs-text);">
            <span class="text-xs px-2 py-1 bg-pink-500/20 text-pink-500 rounded">Colors</span>
            Styling with Tailwind
        </h4>

        <div class="flex flex-wrap gap-4 mb-4">
            <x-accelade::icon name="heroicon-o-bell" size="xl" class="text-gray-500" />
            <x-accelade::icon name="heroicon-o-bell" size="xl" class="text-red-500" />
            <x-accelade::icon name="heroicon-o-bell" size="xl" class="text-orange-500" />
            <x-accelade::icon name="heroicon-o-bell" size="xl" class="text-yellow-500" />
            <x-accelade::icon name="heroicon-o-bell" size="xl" class="text-green-500" />
            <x-accelade::icon name="heroicon-o-bell" size="xl" class="text-teal-500" />
            <x-accelade::icon name="heroicon-o-bell" size="xl" class="text-blue-500" />
            <x-accelade::icon name="heroicon-o-bell" size="xl" class="text-indigo-500" />
            <x-accelade::icon name="heroicon-o-bell" size="xl" class="text-purple-500" />
            <x-accelade::icon name="heroicon-o-bell" size="xl" class="text-pink-500" />
        </div>

        <p class="text-xs" style="color: var(--docs-text-muted);">
            Apply any Tailwind color class directly to the icon component.
        </p>
    </div>

    {{-- Fallback Demo --}}
    <div class="rounded-xl p-4 border border-amber-500/30 mb-6" style="background: rgba(245, 158, 11, 0.1);">
        <h4 class="font-medium mb-4 flex items-center gap-2" style="color: var(--docs-text);">
            <span class="text-xs px-2 py-1 bg-amber-500/20 text-amber-500 rounded">Fallback</span>
            Graceful Degradation
        </h4>

        <div class="grid grid-cols-2 gap-4 mb-4">
            <div class="flex items-center gap-3 p-4 rounded-lg" style="background: var(--docs-bg);">
                <x-accelade::icon name="non-existent-icon" size="xl" />
                <div>
                    <span class="text-sm font-medium" style="color: var(--docs-text);">Default Fallback</span>
                    <p class="text-xs" style="color: var(--docs-text-muted);">Shows question mark when icon not found</p>
                </div>
            </div>
            <div class="flex items-center gap-3 p-4 rounded-lg" style="background: var(--docs-bg);">
                <x-accelade::icon name="non-existent-icon" fallback="heroicon-o-exclamation-circle" size="xl" class="text-amber-500" />
                <div>
                    <span class="text-sm font-medium" style="color: var(--docs-text);">Custom Fallback</span>
                    <p class="text-xs" style="color: var(--docs-text-muted);">Uses specified fallback icon</p>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3 p-4 rounded-lg" style="background: var(--docs-bg);">
            <span class="w-8 h-8 flex items-center justify-center border-2 border-dashed border-gray-300 dark:border-gray-600 rounded">
                <x-accelade::icon name="non-existent-icon" :showFallback="false" size="lg" />
            </span>
            <div>
                <span class="text-sm font-medium" style="color: var(--docs-text);">No Fallback</span>
                <p class="text-xs" style="color: var(--docs-text-muted);">Renders nothing when <code>:showFallback="false"</code></p>
            </div>
        </div>
    </div>

    {{-- Common Icons Gallery --}}
    <div class="rounded-xl p-4 border border-cyan-500/30 mb-6" style="background: rgba(6, 182, 212, 0.1);">
        <h4 class="font-medium mb-4 flex items-center gap-2" style="color: var(--docs-text);">
            <span class="text-xs px-2 py-1 bg-cyan-500/20 text-cyan-500 rounded">Gallery</span>
            Common Icons
        </h4>

        <div class="grid grid-cols-6 sm:grid-cols-8 md:grid-cols-10 gap-3">
            @php
                $commonIcons = [
                    'heroicon-o-home', 'heroicon-o-user', 'heroicon-o-cog-6-tooth', 'heroicon-o-bell',
                    'heroicon-o-envelope', 'heroicon-o-magnifying-glass', 'heroicon-o-plus', 'heroicon-o-minus',
                    'heroicon-o-x-mark', 'heroicon-o-check', 'heroicon-o-arrow-left', 'heroicon-o-arrow-right',
                    'heroicon-o-chevron-down', 'heroicon-o-chevron-up', 'heroicon-o-calendar', 'heroicon-o-clock',
                    'heroicon-o-document', 'heroicon-o-folder', 'heroicon-o-photo', 'heroicon-o-trash',
                    'heroicon-o-pencil', 'heroicon-o-eye', 'heroicon-o-eye-slash', 'heroicon-o-link',
                    'heroicon-o-clipboard', 'heroicon-o-share', 'heroicon-o-heart', 'heroicon-o-star',
                    'heroicon-o-lock-closed', 'heroicon-o-lock-open',
                ];
            @endphp
            @foreach($commonIcons as $icon)
                <div class="flex flex-col items-center gap-1 p-2 rounded-lg hover:bg-[var(--docs-bg)] transition-colors cursor-pointer group" title="{{ $icon }}">
                    <x-accelade::icon :name="$icon" size="lg" class="text-[var(--docs-text-muted)] group-hover:text-[var(--docs-accent)]" />
                </div>
            @endforeach
        </div>
    </div>

    {{-- Code Examples --}}
    <div class="space-y-4">
        <h4 class="font-medium" style="color: var(--docs-text);">Usage Examples</h4>

        <x-accelade::code-block language="blade" title="Basic Usage">
&#123;&#123;-- Basic icon --&#125;&#125;
&lt;x-accelade::icon name="heroicon-o-star" /&gt;

&#123;&#123;-- With size --&#125;&#125;
&lt;x-accelade::icon name="heroicon-o-star" size="xl" /&gt;

&#123;&#123;-- With color --&#125;&#125;
&lt;x-accelade::icon name="heroicon-o-star" size="lg" class="text-yellow-500" /&gt;

&#123;&#123;-- With fallback icon --&#125;&#125;
&lt;x-accelade::icon name="custom-icon" fallback="heroicon-o-question-mark-circle" /&gt;

&#123;&#123;-- Hide fallback (render nothing if not found) --&#125;&#125;
&lt;x-accelade::icon name="maybe-exists" :showFallback="false" /&gt;
        </x-accelade::code-block>

        <x-accelade::code-block language="php" title="Available Sizes">
// Size options with their Tailwind classes
'xs'   =&gt; 'w-3 h-3',   // 12px
'sm'   =&gt; 'w-4 h-4',   // 16px
'md'   =&gt; 'w-5 h-5',   // 20px
'base' =&gt; 'w-6 h-6',   // 24px (default)
'lg'   =&gt; 'w-7 h-7',   // 28px
'xl'   =&gt; 'w-8 h-8',   // 32px
'2xl'  =&gt; 'w-10 h-10', // 40px
'3xl'  =&gt; 'w-12 h-12', // 48px
'4xl'  =&gt; 'w-16 h-16', // 64px
        </x-accelade::code-block>
    </div>

    {{-- Installed Packages Info --}}
    <div class="mt-6 pt-6 border-t border-[var(--docs-border)]">
        <h4 class="font-medium mb-4" style="color: var(--docs-text);">Supported Icon Packages</h4>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="rounded-lg p-3" style="background: var(--docs-bg);">
                <h5 class="text-sm font-medium mb-2" style="color: var(--docs-text);">Heroicons</h5>
                <code class="text-xs" style="color: var(--docs-text-muted);">blade-ui-kit/blade-heroicons</code>
                <p class="text-xs mt-2" style="color: var(--docs-text-muted);">
                    Prefixes: heroicon-o-*, heroicon-s-*, heroicon-m-*
                </p>
            </div>
            <div class="rounded-lg p-3" style="background: var(--docs-bg);">
                <h5 class="text-sm font-medium mb-2" style="color: var(--docs-text);">Boxicons</h5>
                <code class="text-xs" style="color: var(--docs-text-muted);">mallardduck/blade-boxicons</code>
                <p class="text-xs mt-2" style="color: var(--docs-text-muted);">
                    Prefixes: bx-*, bxs-*, bxl-*
                </p>
            </div>
            <div class="rounded-lg p-3" style="background: var(--docs-bg);">
                <h5 class="text-sm font-medium mb-2" style="color: var(--docs-text);">Font Awesome</h5>
                <code class="text-xs" style="color: var(--docs-text-muted);">owenvoke/blade-fontawesome</code>
                <p class="text-xs mt-2" style="color: var(--docs-text-muted);">
                    Prefixes: fas-*, far-*, fab-*
                </p>
            </div>
        </div>
    </div>
</section>
