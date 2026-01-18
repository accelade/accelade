<?php

declare(strict_types=1);

namespace Accelade\Http\Controllers;

use Accelade\Icons\BladeIconsRegistry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class IconsController extends Controller
{
    public function __construct(
        protected BladeIconsRegistry $registry
    ) {}

    /**
     * Get all available icon sets summary.
     */
    public function sets(): JsonResponse
    {
        return response()->json([
            'sets' => $this->registry->getSetsSummary(),
        ]);
    }

    /**
     * Get icons from a specific set with pagination.
     */
    public function icons(Request $request, string $set): JsonResponse
    {
        $offset = (int) $request->get('offset', 0);
        $limit = min((int) $request->get('limit', 50), 100);
        $search = $request->get('search');

        $result = $this->registry->getIcons($set, $offset, $limit, $search);

        // Add SVG content for each icon
        foreach ($result['icons'] as &$icon) {
            $icon['svg'] = $this->registry->getSvg($icon['fullName'], 'w-full h-full');
        }

        return response()->json($result);
    }

    /**
     * Search icons across all sets.
     */
    public function search(Request $request): JsonResponse
    {
        $query = $request->get('q', '');
        $sets = $request->get('sets') ? explode(',', $request->get('sets')) : null;
        $offset = (int) $request->get('offset', 0);
        $limit = min((int) $request->get('limit', 50), 100);

        if (strlen($query) < 2) {
            return response()->json([
                'icons' => [],
                'total' => 0,
                'hasMore' => false,
            ]);
        }

        $result = $this->registry->searchIcons($query, $sets, $offset, $limit);

        // Add SVG content for each icon
        foreach ($result['icons'] as &$icon) {
            $icon['svg'] = $this->registry->getSvg($icon['fullName'], 'w-full h-full');
        }

        return response()->json($result);
    }

    /**
     * Get SVG content for a specific icon.
     */
    public function svg(Request $request, string $icon): JsonResponse
    {
        $class = $request->get('class', '');
        $svg = $this->registry->getSvg($icon, $class);

        if ($svg === null) {
            return response()->json(['error' => 'Icon not found'], 404);
        }

        return response()->json(['svg' => $svg]);
    }
}
