<?php

declare(strict_types=1);

namespace Accelade\Http\Controllers;

use Accelade\Support\HybridReactivity;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class AcceladeController extends Controller
{
    /**
     * Serve the Accelade JavaScript file.
     */
    public function script(): Response
    {
        $framework = config('accelade.framework', 'vanilla');

        // Try framework-specific first, then fall back to vanilla
        $path = __DIR__."/../../../dist/accelade-{$framework}.js";

        if (! file_exists($path)) {
            $path = __DIR__.'/../../../dist/accelade-vanilla.js';
        }

        if (! file_exists($path)) {
            $content = "console.error('Accelade: No JS file found');";

            return response($content, 200, [
                'Content-Type' => 'application/javascript',
            ]);
        }

        return response(file_get_contents($path), 200, [
            'Content-Type' => 'application/javascript',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
        ]);
    }

    /**
     * Handle a single state update from the client.
     */
    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'component' => 'required|string',
            'property' => 'required|string',
            'value' => 'nullable',
        ]);

        $reactivity = new HybridReactivity($validated['component']);
        $reactivity->set($validated['property'], $validated['value']);

        return response()->json([
            'success' => true,
            'component' => $validated['component'],
            'property' => $validated['property'],
            'value' => $reactivity->get($validated['property']),
        ]);
    }

    /**
     * Handle batch state updates from the client.
     */
    public function batchUpdate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'component' => 'required|string',
            'updates' => 'required|array',
            'updates.*.property' => 'required|string',
            'updates.*.value' => 'nullable',
        ]);

        $reactivity = new HybridReactivity($validated['component']);
        $results = [];

        foreach ($validated['updates'] as $update) {
            $reactivity->set($update['property'], $update['value']);
            $results[$update['property']] = $reactivity->get($update['property']);
        }

        return response()->json([
            'success' => true,
            'component' => $validated['component'],
            'state' => $results,
        ]);
    }
}
