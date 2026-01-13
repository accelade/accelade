<?php

declare(strict_types=1);

namespace Accelade\Http\Controllers;

use Accelade\Bridge\BridgeManager;
use Accelade\Bridge\BridgeResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

/**
 * Handles bridge component AJAX requests.
 *
 * Endpoints:
 * - POST /accelade/bridge/call - Call a component method
 * - POST /accelade/bridge/sync - Sync property changes
 */
class BridgeController extends Controller
{
    public function __construct(
        protected BridgeManager $manager
    ) {}

    /**
     * Call a method on a bridge component.
     */
    public function call(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'state' => ['required', 'string'],
            'method' => ['required', 'string'],
            'args' => ['array'],
        ]);

        // Decode the state payload
        $payload = $this->manager->decodePayload($validated['state']);

        if (! $payload) {
            return response()->json(
                BridgeResponse::error('Invalid bridge state.')->toArray(),
                400
            );
        }

        // Create the component instance
        $component = $this->manager->createInstance($payload);

        if (! $component) {
            return response()->json(
                BridgeResponse::error('Failed to create component instance.')->toArray(),
                400
            );
        }

        // Call the method using BridgeManager
        $method = $validated['method'];
        $args = $validated['args'] ?? [];

        $response = $this->manager->callMethod($component, $method, $args);

        // Get updated props
        $props = $this->manager->getProps($component);

        // Update the state payload with new props
        $newPayload = $this->manager->createPayload(
            $payload['id'],
            $payload['class'],
            $props
        );

        $result = $response->toArray();
        $result['state'] = $newPayload;
        $result['props'] = $props;

        return response()->json($result);
    }

    /**
     * Sync property changes from the frontend.
     */
    public function sync(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'state' => ['required', 'string'],
            'props' => ['required', 'array'],
        ]);

        // Decode the state payload
        $payload = $this->manager->decodePayload($validated['state']);

        if (! $payload) {
            return response()->json(
                BridgeResponse::error('Invalid bridge state.')->toArray(),
                400
            );
        }

        // Create the component instance
        $component = $this->manager->createInstance($payload);

        if (! $component) {
            return response()->json(
                BridgeResponse::error('Failed to create component instance.')->toArray(),
                400
            );
        }

        // Update the properties using BridgeManager
        $this->manager->updateProps($component, $validated['props']);

        // Get updated props
        $props = $this->manager->getProps($component);

        // Create new state payload
        $newPayload = $this->manager->createPayload(
            $payload['id'],
            $payload['class'],
            $props
        );

        return response()->json([
            'success' => true,
            'props' => $props,
            'state' => $newPayload,
        ]);
    }
}
