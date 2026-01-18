<?php

declare(strict_types=1);

use Accelade\Icons\BladeIconsRegistry;

describe('Icons API Endpoints', function () {
    it('returns icon sets at /accelade/api/icons/sets', function () {
        $response = $this->get('/accelade/api/icons/sets');

        $response->assertOk();
        $response->assertJsonStructure([
            'sets' => [
                '*' => ['name', 'prefix', 'count'],
            ],
        ]);
    });

    it('returns icons for a set with pagination', function () {
        $registry = app(BladeIconsRegistry::class);
        $sets = $registry->getSetsSummary();

        if (empty($sets)) {
            $this->markTestSkipped('No Blade Icons sets installed');
        }

        $setName = $sets[0]['name'];

        $response = $this->get("/accelade/api/icons/{$setName}?offset=0&limit=10");

        $response->assertOk();
        $response->assertJsonStructure([
            'icons' => [
                '*' => ['name', 'fullName', 'set', 'prefix'],
            ],
            'total',
            'hasMore',
        ]);
    });

    it('supports offset parameter', function () {
        $registry = app(BladeIconsRegistry::class);
        $sets = $registry->getSetsSummary();

        if (empty($sets)) {
            $this->markTestSkipped('No Blade Icons sets installed');
        }

        $setName = $sets[0]['name'];

        $response1 = $this->get("/accelade/api/icons/{$setName}?offset=0&limit=5");
        $response2 = $this->get("/accelade/api/icons/{$setName}?offset=5&limit=5");

        $response1->assertOk();
        $response2->assertOk();

        $data1 = $response1->json('icons');
        $data2 = $response2->json('icons');

        // If there are more than 5 icons, the results should be different
        if ($response1->json('total') > 5) {
            expect($data1)->not->toBe($data2);
        }
    });

    it('supports search parameter', function () {
        $registry = app(BladeIconsRegistry::class);
        $sets = $registry->getSetsSummary();

        if (empty($sets)) {
            $this->markTestSkipped('No Blade Icons sets installed');
        }

        $setName = $sets[0]['name'];

        $response = $this->get("/accelade/api/icons/{$setName}?search=arrow&limit=50");

        $response->assertOk();

        $icons = $response->json('icons');
        foreach ($icons as $icon) {
            expect(strtolower($icon['name']))->toContain('arrow');
        }
    });

    it('searches across sets at /accelade/api/icons/search', function () {
        $response = $this->get('/accelade/api/icons/search?q=home&limit=20');

        $response->assertOk();
        $response->assertJsonStructure([
            'icons' => [
                '*' => ['name', 'fullName', 'set', 'prefix'],
            ],
            'total',
            'hasMore',
        ]);
    });

    it('limits search to specific set', function () {
        $registry = app(BladeIconsRegistry::class);
        $sets = $registry->getSetsSummary();

        if (empty($sets)) {
            $this->markTestSkipped('No Blade Icons sets installed');
        }

        $setName = $sets[0]['name'];

        $response = $this->get("/accelade/api/icons/search?q=home&set={$setName}&limit=20");

        $response->assertOk();
    });

    it('returns SVG for specific icon', function () {
        $registry = app(BladeIconsRegistry::class);
        $sets = $registry->getSetsSummary();

        if (empty($sets)) {
            $this->markTestSkipped('No Blade Icons sets installed');
        }

        $setName = $sets[0]['name'];
        $icons = $registry->getIcons($setName, 0, 1);

        if (empty($icons['icons'])) {
            $this->markTestSkipped('No icons in first set');
        }

        $iconName = $icons['icons'][0]['fullName'];

        $response = $this->get("/accelade/api/icons/svg/{$iconName}");

        $response->assertOk();
        $response->assertJsonStructure(['svg']);
        expect($response->json('svg'))->toContain('<svg');
    });

    it('returns 404 for non-existent icon', function () {
        $response = $this->get('/accelade/api/icons/svg/non-existent:icon');

        $response->assertNotFound();
    });

    it('returns empty array for non-existent set', function () {
        $response = $this->get('/accelade/api/icons/non-existent-set');

        $response->assertOk();
        $response->assertJson([
            'icons' => [],
            'total' => 0,
            'hasMore' => false,
        ]);
    });
});
