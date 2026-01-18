<?php

declare(strict_types=1);

use Accelade\Icons\BladeIconsRegistry;
use BladeUI\Icons\Factory as BladeIconsFactory;
use Illuminate\Filesystem\Filesystem;

beforeEach(function () {
    $this->factory = app(BladeIconsFactory::class);
    $this->filesystem = app(Filesystem::class);
    $this->registry = new BladeIconsRegistry($this->factory, $this->filesystem);
});

describe('BladeIconsRegistry', function () {
    it('can be instantiated', function () {
        expect($this->registry)->toBeInstanceOf(BladeIconsRegistry::class);
    });

    it('returns an array of icon sets', function () {
        $sets = $this->registry->getSets();

        expect($sets)->toBeArray();
    });

    it('each set has required keys', function () {
        $sets = $this->registry->getSetsSummary();

        if (empty($sets)) {
            $this->markTestSkipped('No Blade Icons sets installed');
        }

        foreach ($sets as $set) {
            expect($set)->toHaveKeys(['name', 'prefix', 'count']);
            expect($set['name'])->toBeString();
            expect($set['prefix'])->toBeString();
            expect($set['count'])->toBeInt();
        }
    });

    it('returns icons with pagination', function () {
        $sets = $this->registry->getSetsSummary();

        if (empty($sets)) {
            $this->markTestSkipped('No Blade Icons sets installed');
        }

        $firstSet = $sets[0]['name'];
        $result = $this->registry->getIcons($firstSet, 0, 10);

        expect($result)->toHaveKeys(['icons', 'total', 'hasMore']);
        expect($result['icons'])->toBeArray();
        expect($result['total'])->toBeInt();
        expect($result['hasMore'])->toBeBool();
    });

    it('respects offset and limit', function () {
        $sets = $this->registry->getSetsSummary();

        if (empty($sets)) {
            $this->markTestSkipped('No Blade Icons sets installed');
        }

        $firstSet = $sets[0]['name'];
        $result1 = $this->registry->getIcons($firstSet, 0, 5);
        $result2 = $this->registry->getIcons($firstSet, 5, 5);

        expect($result1['icons'])->toHaveCount(min(5, $result1['total']));

        if ($result1['total'] > 5) {
            expect($result2['icons'])->not->toBe($result1['icons']);
        }
    });

    it('returns hasMore correctly', function () {
        $sets = $this->registry->getSetsSummary();

        if (empty($sets)) {
            $this->markTestSkipped('No Blade Icons sets installed');
        }

        $firstSet = $sets[0]['name'];
        $result = $this->registry->getIcons($firstSet, 0, 10);

        if ($result['total'] > 10) {
            expect($result['hasMore'])->toBeTrue();
        } else {
            expect($result['hasMore'])->toBeFalse();
        }
    });

    it('can search icons by name', function () {
        $sets = $this->registry->getSetsSummary();

        if (empty($sets)) {
            $this->markTestSkipped('No Blade Icons sets installed');
        }

        $firstSet = $sets[0]['name'];
        $result = $this->registry->searchIcons('arrow', [$firstSet], 0, 50);

        expect($result)->toHaveKeys(['icons', 'total', 'hasMore']);

        // All returned icons should contain 'arrow' in the name
        foreach ($result['icons'] as $icon) {
            expect(strtolower($icon['name']))->toContain('arrow');
        }
    });

    it('returns empty array for non-existent set', function () {
        $result = $this->registry->getIcons('non-existent-set', 0, 10);

        expect($result['icons'])->toBeArray()->toBeEmpty();
        expect($result['total'])->toBe(0);
        expect($result['hasMore'])->toBeFalse();
    });

    it('icons have name and set keys', function () {
        $sets = $this->registry->getSetsSummary();

        if (empty($sets)) {
            $this->markTestSkipped('No Blade Icons sets installed');
        }

        $firstSet = $sets[0]['name'];
        $result = $this->registry->getIcons($firstSet, 0, 5);

        foreach ($result['icons'] as $icon) {
            expect($icon)->toHaveKeys(['name', 'fullName', 'set', 'prefix']);
            expect($icon['name'])->toBeString();
            expect($icon['fullName'])->toBeString();
        }
    });

    it('can get SVG for specific icon', function () {
        $sets = $this->registry->getSetsSummary();

        if (empty($sets)) {
            $this->markTestSkipped('No Blade Icons sets installed');
        }

        $firstSet = $sets[0]['name'];
        $result = $this->registry->getIcons($firstSet, 0, 1);

        if (empty($result['icons'])) {
            $this->markTestSkipped('No icons in first set');
        }

        $iconName = $result['icons'][0]['fullName'];
        $svg = $this->registry->getSvg($iconName);

        expect($svg)->toBeString()->toContain('<svg');
    });

    it('returns null for non-existent icon', function () {
        $svg = $this->registry->getSvg('non-existent-set', 'non-existent-icon');

        expect($svg)->toBeNull();
    });
});
