/**
 * Draggable Factory
 *
 * Creates Draggable instances for drag and drop functionality.
 * Supports sortable lists, multi-container drag, tree structures, and dropzones.
 * Features spring-based animations and visual drop indicators.
 */

import type { IStateAdapter } from '../../adapters/types';
import type {
    DraggableConfig,
    DraggableInstance,
    DraggableMethods,
    DragEventDetail,
    DraggableAxis,
    DropPosition,
    TreeNode,
    SpringState,
} from './types';

/**
 * Default draggable configuration
 */
const DEFAULT_CONFIG: Omit<DraggableConfig, 'id'> = {
    group: null,
    handle: null,
    animation: 200,
    ghostClass: 'opacity-50 scale-105',
    dragClass: 'shadow-2xl ring-2 ring-blue-500/50',
    disabled: false,
    sortable: true,
    dropzone: false,
    accepts: null,
    axis: null,
    tree: false,
    nestedContainer: '[data-draggable-children]',
    maxDepth: 0,
    indentSize: 24,
    dropIndicatorClass: 'bg-blue-500',
    nestIndicatorClass: 'ring-2 ring-blue-400 ring-inset bg-blue-50',
    nestThreshold: 30,
    springAnimation: true,
    springStiffness: 300,
    springDamping: 25,
};

/**
 * Active draggable instances
 */
const instances: Map<string, DraggableInstance> = new Map();

/**
 * Currently dragged item info (for cross-container drag)
 */
let currentDrag: {
    element: HTMLElement;
    sourceInstance: DraggableInstance;
    sourceIndex: number;
    group: string | null;
    startX: number;
    startY: number;
    parentElement: HTMLElement | null;
} | null = null;

/**
 * Active drop indicator element
 */
let dropIndicator: HTMLElement | null = null;

/**
 * Active spring animations
 */
const springAnimations: Map<HTMLElement, { id: number; state: SpringState }> = new Map();

/**
 * Parse draggable configuration from element
 */
function parseConfig(element: HTMLElement): DraggableConfig {
    const id = element.dataset.draggableId ||
        `draggable-${Date.now()}-${Math.random().toString(36).slice(2, 9)}`;

    const configAttr = element.dataset.draggableConfig;
    let parsedConfig: Partial<DraggableConfig> = {};

    if (configAttr) {
        try {
            parsedConfig = JSON.parse(configAttr);
        } catch {
            console.warn('Draggable: Invalid config JSON');
        }
    }

    return {
        id,
        group: parsedConfig.group ?? DEFAULT_CONFIG.group,
        handle: parsedConfig.handle ?? DEFAULT_CONFIG.handle,
        animation: parsedConfig.animation ?? DEFAULT_CONFIG.animation,
        ghostClass: parsedConfig.ghostClass ?? DEFAULT_CONFIG.ghostClass,
        dragClass: parsedConfig.dragClass ?? DEFAULT_CONFIG.dragClass,
        disabled: parsedConfig.disabled ?? DEFAULT_CONFIG.disabled,
        sortable: parsedConfig.sortable ?? DEFAULT_CONFIG.sortable,
        dropzone: parsedConfig.dropzone ?? DEFAULT_CONFIG.dropzone,
        accepts: parsedConfig.accepts ?? DEFAULT_CONFIG.accepts,
        axis: (parsedConfig.axis as DraggableAxis) ?? DEFAULT_CONFIG.axis,
        tree: parsedConfig.tree ?? DEFAULT_CONFIG.tree,
        nestedContainer: parsedConfig.nestedContainer ?? DEFAULT_CONFIG.nestedContainer,
        maxDepth: parsedConfig.maxDepth ?? DEFAULT_CONFIG.maxDepth,
        indentSize: parsedConfig.indentSize ?? DEFAULT_CONFIG.indentSize,
        dropIndicatorClass: parsedConfig.dropIndicatorClass ?? DEFAULT_CONFIG.dropIndicatorClass,
        nestIndicatorClass: parsedConfig.nestIndicatorClass ?? DEFAULT_CONFIG.nestIndicatorClass,
        nestThreshold: parsedConfig.nestThreshold ?? DEFAULT_CONFIG.nestThreshold,
        springAnimation: parsedConfig.springAnimation ?? DEFAULT_CONFIG.springAnimation,
        springStiffness: parsedConfig.springStiffness ?? DEFAULT_CONFIG.springStiffness,
        springDamping: parsedConfig.springDamping ?? DEFAULT_CONFIG.springDamping,
    };
}

/**
 * Get draggable items within container (non-recursive for flat lists)
 */
function getDraggableItems(element: HTMLElement, config: DraggableConfig): HTMLElement[] {
    // Direct children with data-draggable-item attribute, or all direct children if none specified
    const items = element.querySelectorAll<HTMLElement>(':scope > [data-draggable-item]');
    if (items.length > 0) {
        return Array.from(items);
    }
    // Fall back to direct children (excluding ghost elements and drop indicator)
    return Array.from(element.children).filter(
        (child): child is HTMLElement =>
            child instanceof HTMLElement &&
            !child.classList.contains('draggable-ghost') &&
            !child.classList.contains('draggable-drop-indicator')
    );
}

/**
 * Get all items recursively for tree mode
 */
function getAllTreeItems(element: HTMLElement, config: DraggableConfig): HTMLElement[] {
    const items: HTMLElement[] = [];

    function collectItems(container: HTMLElement): void {
        const directItems = getDraggableItems(container, config);
        for (const item of directItems) {
            items.push(item);
            if (config.tree) {
                const nestedContainer = item.querySelector<HTMLElement>(config.nestedContainer);
                if (nestedContainer) {
                    collectItems(nestedContainer);
                }
            }
        }
    }

    collectItems(element);
    return items;
}

/**
 * Get item depth in tree
 */
function getItemDepth(element: HTMLElement, rootContainer: HTMLElement, config: DraggableConfig): number {
    let depth = 0;
    let current = element.parentElement;

    while (current && current !== rootContainer) {
        if (current.matches(config.nestedContainer)) {
            depth++;
        }
        current = current.parentElement;
    }

    return depth;
}

/**
 * Get parent item of nested item
 */
function getParentItem(item: HTMLElement, config: DraggableConfig): HTMLElement | null {
    const nestedContainer = item.parentElement;
    if (!nestedContainer?.matches(config.nestedContainer)) {
        return null;
    }
    return nestedContainer.parentElement?.closest('[data-draggable-item]') as HTMLElement | null;
}

/**
 * Get or create nested container for an item
 */
function getOrCreateNestedContainer(item: HTMLElement, config: DraggableConfig): HTMLElement {
    let container = item.querySelector<HTMLElement>(config.nestedContainer);
    if (!container) {
        container = document.createElement('div');
        container.setAttribute('data-draggable-children', '');
        container.className = 'draggable-children pl-6 mt-1';
        item.appendChild(container);
    }
    return container;
}

/**
 * Create ghost element for drag preview/placeholder
 */
function createGhost(element: HTMLElement, config: DraggableConfig): HTMLElement {
    const ghost = element.cloneNode(true) as HTMLElement;
    ghost.classList.add('draggable-ghost');

    // Apply ghost styling
    if (config.ghostClass) {
        ghost.classList.add(...config.ghostClass.split(' '));
    }

    // Ensure ghost doesn't interfere with drag events
    ghost.style.pointerEvents = 'none';

    // Remove draggable attribute from ghost
    ghost.removeAttribute('draggable');
    ghost.removeAttribute('data-draggable-item');

    return ghost;
}

/**
 * Create drop indicator line
 */
function createDropIndicator(config: DraggableConfig): HTMLElement {
    const indicator = document.createElement('div');
    indicator.className = `draggable-drop-indicator h-0.5 rounded-full transition-all duration-150 ${config.dropIndicatorClass}`;
    indicator.style.cssText = 'pointer-events: none; position: relative; z-index: 1000;';
    return indicator;
}

/**
 * Apply drag class to element with enhanced styling
 */
function applyDragClass(element: HTMLElement, config: DraggableConfig): void {
    if (config.dragClass) {
        element.classList.add(...config.dragClass.split(' '));
    }
    element.style.zIndex = '1000';
    element.style.cursor = 'grabbing';
}

/**
 * Remove drag class from element
 */
function removeDragClass(element: HTMLElement, config: DraggableConfig): void {
    if (config.dragClass) {
        element.classList.remove(...config.dragClass.split(' '));
    }
    element.style.zIndex = '';
    element.style.cursor = '';
}

/**
 * Check if a dropzone accepts items from a group
 */
function acceptsGroup(config: DraggableConfig, sourceGroup: string | null): boolean {
    if (!config.accepts) {
        return true; // Accept all
    }

    if (!sourceGroup) {
        return false; // No group, can't match
    }

    const acceptedGroups = config.accepts.split(',').map(g => g.trim());
    return acceptedGroups.includes(sourceGroup);
}

/**
 * Get the "header" height of an item (excluding nested children)
 */
function getItemHeaderHeight(item: HTMLElement, config: DraggableConfig): number {
    const nestedContainer = item.querySelector<HTMLElement>(config.nestedContainer);
    if (nestedContainer) {
        // Calculate header height as total height minus nested container height
        const itemRect = item.getBoundingClientRect();
        const nestedRect = nestedContainer.getBoundingClientRect();
        // Header is the portion before the nested container
        return nestedRect.top - itemRect.top;
    }
    return item.getBoundingClientRect().height;
}

/**
 * Calculate drop position and index based on mouse position
 */
function calculateDropPosition(
    container: HTMLElement,
    items: HTMLElement[],
    clientX: number,
    clientY: number,
    axis: DraggableAxis,
    config: DraggableConfig
): { index: number; position: DropPosition; targetItem: HTMLElement | null; nestIntent: boolean } {
    // Check for nesting intent based on horizontal drag distance
    const nestIntent = config.tree && currentDrag
        ? clientX - currentDrag.startX > config.nestThreshold
        : false;

    // Find the deepest item under the cursor (for tree mode, prefer nested items)
    let bestMatch: { item: HTMLElement; index: number } | null = null;
    let bestDepth = -1;

    for (let i = 0; i < items.length; i++) {
        const item = items[i];
        const rect = item.getBoundingClientRect();

        // Skip the dragged element and its descendants
        if (item === currentDrag?.element) continue;
        if (currentDrag?.element && currentDrag.element.contains(item)) continue;

        // Check if mouse is within item's full bounds
        if (clientX >= rect.left && clientX <= rect.right &&
            clientY >= rect.top && clientY <= rect.bottom) {

            // Calculate depth - prefer deeper (more nested) items
            const depth = getItemDepth(item, container, config);
            if (depth > bestDepth) {
                bestDepth = depth;
                bestMatch = { item, index: i };
            }
        }
    }

    if (bestMatch) {
        const item = bestMatch.item;
        const rect = item.getBoundingClientRect();
        const headerHeight = config.tree ? getItemHeaderHeight(item, config) : rect.height;
        const relativeY = clientY - rect.top;

        // Only consider header area for position calculation in tree mode
        const effectiveHeight = Math.min(headerHeight, rect.height);

        // Tree mode: check for nesting via horizontal drag or middle zone
        if (config.tree) {
            const currentDepth = getItemDepth(item, container, config);
            const canNest = config.maxDepth === 0 || currentDepth < config.maxDepth;

            // If cursor is in header area
            if (relativeY <= effectiveHeight) {
                if (nestIntent && canNest) {
                    return { index: 0, position: 'inside', targetItem: item, nestIntent: true };
                }

                // Top 25% = before, bottom 25% = after, middle 50% = inside (if can nest)
                if (relativeY < effectiveHeight * 0.25) {
                    return { index: bestMatch.index, position: 'before', targetItem: item, nestIntent: false };
                } else if (relativeY > effectiveHeight * 0.75) {
                    // Check if item has children - if so, "after" header means before first child
                    const nestedContainer = item.querySelector<HTMLElement>(config.nestedContainer);
                    if (nestedContainer && canNest) {
                        return { index: 0, position: 'inside', targetItem: item, nestIntent: true };
                    }
                    return { index: bestMatch.index + 1, position: 'after', targetItem: item, nestIntent: false };
                } else if (canNest) {
                    return { index: 0, position: 'inside', targetItem: item, nestIntent: true };
                }
            }
            // Cursor is below header (in nested area) - this should be handled by a nested item match
            return { index: bestMatch.index + 1, position: 'after', targetItem: item, nestIntent: false };
        }

        // Non-tree mode: simple before/after based on position
        if (relativeY < effectiveHeight * 0.5) {
            return { index: bestMatch.index, position: 'before', targetItem: item, nestIntent: false };
        } else {
            return { index: bestMatch.index + 1, position: 'after', targetItem: item, nestIntent: false };
        }
    }

    // No item under cursor - check if we're before/after all items
    for (let i = 0; i < items.length; i++) {
        const item = items[i];
        if (item === currentDrag?.element) continue;

        const rect = item.getBoundingClientRect();

        if (axis === 'x') {
            if (clientX < rect.left) {
                return { index: i, position: 'before', targetItem: item, nestIntent: false };
            }
        } else {
            if (clientY < rect.top) {
                return { index: i, position: 'before', targetItem: item, nestIntent: false };
            }
        }
    }

    return { index: items.length, position: 'after', targetItem: items[items.length - 1] || null, nestIntent: false };
}

/**
 * Spring physics animation
 */
function springAnimate(
    element: HTMLElement,
    targetX: number,
    targetY: number,
    config: DraggableConfig,
    onComplete?: () => void
): void {
    // Cancel any existing animation for this element
    const existing = springAnimations.get(element);
    if (existing) {
        cancelAnimationFrame(existing.id);
    }

    const state: SpringState = {
        position: { x: 0, y: 0 },
        velocity: { x: 0, y: 0 },
        target: { x: targetX, y: targetY },
    };

    // Get initial transform if any
    const computedStyle = getComputedStyle(element);
    const matrix = new DOMMatrix(computedStyle.transform);
    state.position.x = matrix.m41 || 0;
    state.position.y = matrix.m42 || 0;

    let lastTime = performance.now();

    function animate(currentTime: number): void {
        const deltaTime = Math.min((currentTime - lastTime) / 1000, 0.05); // Cap at 50ms
        lastTime = currentTime;

        // Spring physics
        const stiffness = config.springStiffness;
        const damping = config.springDamping;

        // Calculate spring force
        const forceX = (state.target.x - state.position.x) * stiffness;
        const forceY = (state.target.y - state.position.y) * stiffness;

        // Apply damping
        const dampingForceX = -state.velocity.x * damping;
        const dampingForceY = -state.velocity.y * damping;

        // Update velocity
        state.velocity.x += (forceX + dampingForceX) * deltaTime;
        state.velocity.y += (forceY + dampingForceY) * deltaTime;

        // Update position
        state.position.x += state.velocity.x * deltaTime;
        state.position.y += state.velocity.y * deltaTime;

        // Apply transform
        element.style.transform = `translate(${state.position.x}px, ${state.position.y}px)`;

        // Check if animation is complete
        const distanceX = Math.abs(state.target.x - state.position.x);
        const distanceY = Math.abs(state.target.y - state.position.y);
        const speedX = Math.abs(state.velocity.x);
        const speedY = Math.abs(state.velocity.y);

        if (distanceX < 0.5 && distanceY < 0.5 && speedX < 0.5 && speedY < 0.5) {
            // Animation complete
            element.style.transform = state.target.x === 0 && state.target.y === 0 ? '' : `translate(${state.target.x}px, ${state.target.y}px)`;
            springAnimations.delete(element);
            onComplete?.();
            return;
        }

        const animationId = requestAnimationFrame(animate);
        springAnimations.set(element, { id: animationId, state });
    }

    const animationId = requestAnimationFrame(animate);
    springAnimations.set(element, { id: animationId, state });
}

/**
 * Animate item movement using FLIP technique with spring physics
 */
function animateMove(
    element: HTMLElement,
    fromRect: DOMRect,
    config: DraggableConfig
): void {
    // Get the new position (Last)
    const toRect = element.getBoundingClientRect();

    // Calculate the delta (Invert)
    const deltaX = fromRect.left - toRect.left;
    const deltaY = fromRect.top - toRect.top;

    if (deltaX === 0 && deltaY === 0) return;

    if (config.springAnimation) {
        // Use spring animation
        element.style.transform = `translate(${deltaX}px, ${deltaY}px)`;
        springAnimate(element, 0, 0, config, () => {
            element.style.transform = '';
        });
    } else {
        // Use CSS transition (fallback)
        element.style.transition = 'none';
        element.style.transform = `translate(${deltaX}px, ${deltaY}px)`;

        // Force reflow
        void element.offsetWidth;

        // Play
        element.style.transition = `transform ${config.animation}ms cubic-bezier(0.2, 0, 0.2, 1)`;
        element.style.transform = 'translate(0, 0)';

        const cleanup = () => {
            element.style.transition = '';
            element.style.transform = '';
            element.removeEventListener('transitionend', cleanup);
        };

        element.addEventListener('transitionend', cleanup, { once: true });
        setTimeout(cleanup, config.animation + 50);
    }
}

/**
 * Show drop indicator at position
 */
function showDropIndicator(
    position: DropPosition,
    targetItem: HTMLElement | null,
    container: HTMLElement,
    config: DraggableConfig
): void {
    if (!dropIndicator) {
        dropIndicator = createDropIndicator(config);
    }

    // Remove nesting highlight from all items
    container.querySelectorAll('.draggable-nest-highlight').forEach(el => {
        el.classList.remove('draggable-nest-highlight', ...config.nestIndicatorClass.split(' '));
    });

    if (position === 'inside' && targetItem) {
        // Highlight target for nesting
        targetItem.classList.add('draggable-nest-highlight', ...config.nestIndicatorClass.split(' '));
        dropIndicator.remove();
    } else if (targetItem) {
        // Show line indicator
        dropIndicator.remove();
        if (position === 'before') {
            targetItem.parentElement?.insertBefore(dropIndicator, targetItem);
        } else {
            targetItem.parentElement?.insertBefore(dropIndicator, targetItem.nextSibling);
        }
    }
}

/**
 * Hide drop indicator and nesting highlights
 */
function hideDropIndicator(container: HTMLElement, config: DraggableConfig): void {
    dropIndicator?.remove();
    dropIndicator = null;

    container.querySelectorAll('.draggable-nest-highlight').forEach(el => {
        el.classList.remove('draggable-nest-highlight', ...config.nestIndicatorClass.split(' '));
    });
}

/**
 * Build tree structure from DOM
 */
function buildTree(container: HTMLElement, config: DraggableConfig): TreeNode[] {
    const nodes: TreeNode[] = [];

    function buildNode(item: HTMLElement, parent: TreeNode | null, depth: number, index: number): TreeNode {
        const node: TreeNode = {
            element: item,
            parent,
            children: [],
            depth,
            index,
            collapsed: item.hasAttribute('data-collapsed'),
        };

        if (config.tree) {
            const nestedContainer = item.querySelector<HTMLElement>(config.nestedContainer);
            if (nestedContainer) {
                const childItems = getDraggableItems(nestedContainer, config);
                childItems.forEach((childItem, childIndex) => {
                    node.children.push(buildNode(childItem, node, depth + 1, childIndex));
                });
            }
        }

        return node;
    }

    const items = getDraggableItems(container, config);
    items.forEach((item, index) => {
        nodes.push(buildNode(item, null, 0, index));
    });

    return nodes;
}

/**
 * Create a Draggable instance
 */
export function createDraggable(
    componentId: string,
    element: HTMLElement,
    stateAdapter: IStateAdapter
): DraggableInstance | undefined {
    const config = parseConfig(element);

    // Initialize state
    if (stateAdapter.get('isDragging') === undefined) {
        stateAdapter.set('isDragging', false);
    }
    if (stateAdapter.get('isDragOver') === undefined) {
        stateAdapter.set('isDragOver', false);
    }
    if (stateAdapter.get('draggedItem') === undefined) {
        stateAdapter.set('draggedItem', null);
    }
    if (stateAdapter.get('draggedIndex') === undefined) {
        stateAdapter.set('draggedIndex', null);
    }
    if (stateAdapter.get('dropPosition') === undefined) {
        stateAdapter.set('dropPosition', null);
    }
    if (stateAdapter.get('isNesting') === undefined) {
        stateAdapter.set('isNesting', false);
    }

    let isEnabled = !config.disabled;
    let ghost: HTMLElement | null = null;
    let draggedElement: HTMLElement | null = null;
    let draggedIndex: number = -1;
    let cleanups: (() => void)[] = [];

    /**
     * Get items
     */
    const getItems = (): HTMLElement[] => getDraggableItems(element, config);

    /**
     * Get all items including nested
     */
    const getAllItems = (): HTMLElement[] => getAllTreeItems(element, config);

    /**
     * Get item at index
     */
    const getItem = (index: number): HTMLElement | null => {
        const items = getItems();
        return items[index] || null;
    };

    /**
     * Enable dragging
     */
    const enable = (): void => {
        isEnabled = true;
        setupDragListeners();
    };

    /**
     * Disable dragging
     */
    const disable = (): void => {
        isEnabled = false;
        cleanupDragListeners();
    };

    /**
     * Check if enabled
     */
    const isEnabledFn = (): boolean => isEnabled;

    /**
     * Move item from one index to another
     */
    const moveItem = (fromIndex: number, toIndex: number): void => {
        const items = getItems();
        if (fromIndex < 0 || fromIndex >= items.length) return;
        if (toIndex < 0 || toIndex > items.length) return;
        if (fromIndex === toIndex) return;

        const item = items[fromIndex];

        // FLIP: First - capture positions of ALL items before DOM change
        const beforeRects = new Map<HTMLElement, DOMRect>();
        items.forEach(el => {
            beforeRects.set(el, el.getBoundingClientRect());
        });

        // Remove and insert at new position
        item.remove();

        const currentItems = getItems();
        const adjustedIndex = toIndex > fromIndex ? toIndex - 1 : toIndex;
        if (adjustedIndex >= currentItems.length) {
            element.appendChild(item);
        } else {
            element.insertBefore(item, currentItems[adjustedIndex]);
        }

        // FLIP: Animate all affected items
        if (config.animation > 0) {
            const newItems = getItems();
            newItems.forEach(el => {
                const beforeRect = beforeRects.get(el);
                if (beforeRect) {
                    animateMove(el, beforeRect, config);
                }
            });
        }

        // Dispatch event
        dispatchDragEvent('sort', {
            id: config.id,
            oldIndex: fromIndex,
            newIndex: toIndex,
            item,
            from: element,
            to: element,
            group: config.group,
        });
    };

    /**
     * Nest item inside another item (tree mode)
     */
    const nestItem = (item: HTMLElement, parent: HTMLElement, index?: number): void => {
        if (!config.tree) return;

        const oldParent = getParentItem(item, config);
        const beforeRects = new Map<HTMLElement, DOMRect>();

        // Capture all positions before change
        getAllItems().forEach(el => {
            beforeRects.set(el, el.getBoundingClientRect());
        });

        // Remove from current position
        item.remove();

        // Get or create nested container in parent
        const nestedContainer = getOrCreateNestedContainer(parent, config);

        // Add to parent
        const children = getDraggableItems(nestedContainer, config);
        const targetIndex = index ?? children.length;

        if (targetIndex >= children.length) {
            nestedContainer.appendChild(item);
        } else {
            nestedContainer.insertBefore(item, children[targetIndex]);
        }

        // Re-setup listeners
        setupItemListeners(item);

        // Animate
        if (config.animation > 0) {
            getAllItems().forEach(el => {
                const beforeRect = beforeRects.get(el);
                if (beforeRect) {
                    animateMove(el, beforeRect, config);
                }
            });
        }

        // Dispatch event
        dispatchDragEvent('nest', {
            id: config.id,
            oldIndex: -1,
            newIndex: targetIndex,
            item,
            from: element,
            to: nestedContainer,
            group: config.group,
            parent,
            oldParent,
            depth: getItemDepth(item, element, config),
        });
    };

    /**
     * Unnest item to parent level
     */
    const unnestItem = (item: HTMLElement): void => {
        if (!config.tree) return;

        const parentItem = getParentItem(item, config);
        if (!parentItem) return;

        const grandparent = getParentItem(parentItem, config);
        const targetContainer = grandparent
            ? getOrCreateNestedContainer(grandparent, config)
            : element;

        const beforeRects = new Map<HTMLElement, DOMRect>();
        getAllItems().forEach(el => {
            beforeRects.set(el, el.getBoundingClientRect());
        });

        // Remove from current position
        item.remove();

        // Insert after parent
        const siblings = getDraggableItems(targetContainer, config);
        const parentIndex = siblings.indexOf(parentItem);
        const insertIndex = parentIndex + 1;

        if (insertIndex >= siblings.length) {
            targetContainer.appendChild(item);
        } else {
            targetContainer.insertBefore(item, siblings[insertIndex]);
        }

        // Re-setup listeners
        setupItemListeners(item);

        // Animate
        if (config.animation > 0) {
            getAllItems().forEach(el => {
                const beforeRect = beforeRects.get(el);
                if (beforeRect) {
                    animateMove(el, beforeRect, config);
                }
            });
        }

        dispatchDragEvent('unnest', {
            id: config.id,
            oldIndex: -1,
            newIndex: insertIndex,
            item,
            from: element,
            to: targetContainer,
            group: config.group,
            parent: grandparent,
            oldParent: parentItem,
            depth: getItemDepth(item, element, config),
        });
    };

    /**
     * Get tree structure
     */
    const getTree = (): TreeNode[] => buildTree(element, config);

    /**
     * Get item depth in tree
     */
    const getItemDepthFn = (item: HTMLElement): number => getItemDepth(item, element, config);

    /**
     * Toggle collapse state
     */
    const toggleCollapse = (item: HTMLElement): void => {
        const nestedContainer = item.querySelector<HTMLElement>(config.nestedContainer);
        if (!nestedContainer) return;

        const isCollapsedState = item.hasAttribute('data-collapsed');
        if (isCollapsedState) {
            item.removeAttribute('data-collapsed');
            nestedContainer.style.display = '';
        } else {
            item.setAttribute('data-collapsed', '');
            nestedContainer.style.display = 'none';
        }

        dispatchDragEvent('collapse', {
            id: config.id,
            oldIndex: -1,
            newIndex: -1,
            item,
            from: element,
            to: element,
            group: config.group,
        });
    };

    /**
     * Check if item is collapsed
     */
    const isCollapsed = (item: HTMLElement): boolean => {
        return item.hasAttribute('data-collapsed');
    };

    /**
     * Add item at index
     */
    const addItem = (newItem: HTMLElement, index?: number): void => {
        const items = getItems();
        const targetIndex = index ?? items.length;

        if (targetIndex >= items.length) {
            element.appendChild(newItem);
        } else {
            element.insertBefore(newItem, items[targetIndex]);
        }

        // Re-setup listeners for new item
        setupItemListeners(newItem);
    };

    /**
     * Remove item at index
     */
    const removeItem = (index: number): HTMLElement | null => {
        const items = getItems();
        if (index < 0 || index >= items.length) return null;

        const item = items[index];
        item.remove();
        return item;
    };

    /**
     * Refresh items (re-scan DOM and setup listeners)
     */
    const refresh = (): void => {
        cleanupDragListeners();
        setupDragListeners();
    };

    /**
     * Get the direct container of an item (for nested items)
     */
    const getItemContainer = (item: HTMLElement): HTMLElement => {
        const parent = item.parentElement;
        if (parent && parent.matches(config.nestedContainer)) {
            return parent;
        }
        return element;
    };

    /**
     * Get siblings of an item (items in the same container)
     */
    const getSiblings = (item: HTMLElement): HTMLElement[] => {
        const container = getItemContainer(item);
        return getDraggableItems(container, config);
    };

    /**
     * Setup drag listeners for an item
     */
    const setupItemListeners = (item: HTMLElement): void => {
        if (config.dropzone) return;

        const handleElement = config.handle
            ? item.querySelector<HTMLElement>(config.handle)
            : item;

        if (!handleElement) return;

        // Make item draggable
        item.draggable = true;
        item.setAttribute('data-draggable-item', 'true');

        // Add grab cursor on hover
        item.style.cursor = 'grab';

        const onDragStart = (e: DragEvent): void => {
            // CRITICAL: Stop propagation so parent items don't capture this drag
            e.stopPropagation();

            if (!isEnabled) {
                e.preventDefault();
                return;
            }

            draggedElement = item;
            // Get index among siblings (items in same container)
            draggedIndex = getSiblings(item).indexOf(item);

            // Set drag data
            if (e.dataTransfer) {
                e.dataTransfer.effectAllowed = 'move';
                e.dataTransfer.setData('text/plain', config.id);

                // Create custom drag image
                const dragImage = item.cloneNode(true) as HTMLElement;
                dragImage.style.cssText = 'position: absolute; top: -9999px; opacity: 0.8;';
                document.body.appendChild(dragImage);
                e.dataTransfer.setDragImage(dragImage, e.offsetX, e.offsetY);
                setTimeout(() => dragImage.remove(), 0);
            }

            // Apply drag class
            applyDragClass(item, config);

            // Update state
            stateAdapter.set('isDragging', true);
            stateAdapter.set('draggedItem', item);
            stateAdapter.set('draggedIndex', draggedIndex);

            // Store for cross-container drag
            currentDrag = {
                element: item,
                sourceInstance: instance,
                sourceIndex: draggedIndex,
                group: config.group,
                startX: e.clientX,
                startY: e.clientY,
                parentElement: getParentItem(item, config),
            };

            // Dispatch event
            dispatchDragEvent('start', {
                id: config.id,
                oldIndex: draggedIndex,
                newIndex: draggedIndex,
                item,
                from: element,
                to: element,
                group: config.group,
                depth: getItemDepthFn(item),
            });
        };

        const onDragEnd = (): void => {
            // Remove drag class
            if (draggedElement) {
                removeDragClass(draggedElement, config);
            }

            // Remove ghost
            if (ghost && ghost.parentNode) {
                ghost.parentNode.removeChild(ghost);
                ghost = null;
            }

            // Hide drop indicator
            hideDropIndicator(element, config);

            // Update state
            stateAdapter.set('isDragging', false);
            stateAdapter.set('draggedItem', null);
            stateAdapter.set('draggedIndex', null);
            stateAdapter.set('dropPosition', null);
            stateAdapter.set('isNesting', false);

            // Clear current drag
            currentDrag = null;
            draggedElement = null;
            draggedIndex = -1;

            // Dispatch event
            dispatchDragEvent('end', {
                id: config.id,
                oldIndex: -1,
                newIndex: -1,
                item,
                from: element,
                to: element,
                group: config.group,
            });
        };

        item.addEventListener('dragstart', onDragStart);
        item.addEventListener('dragend', onDragEnd);

        cleanups.push(() => {
            item.removeEventListener('dragstart', onDragStart);
            item.removeEventListener('dragend', onDragEnd);
            item.draggable = false;
            item.style.cursor = '';
        });

        // Note: Nested items are handled by getAllItems() in setupDragListeners
        // We don't recursively call setupItemListeners here to avoid duplicate listeners
    };

    /**
     * Setup container listeners
     */
    const setupContainerListeners = (): void => {
        const onDragOver = (e: DragEvent): void => {
            if (!currentDrag) return;

            // Check if we accept this group
            if (config.dropzone && !acceptsGroup(config, currentDrag.group)) {
                return;
            }

            // Check same group for sortable
            if (config.sortable && config.group && currentDrag.group !== config.group) {
                if (!acceptsGroup(config, currentDrag.group)) {
                    return;
                }
            }

            e.preventDefault();
            if (e.dataTransfer) {
                e.dataTransfer.dropEffect = 'move';
            }

            // Update drag over state
            if (!stateAdapter.get('isDragOver')) {
                stateAdapter.set('isDragOver', true);
            }

            // Calculate drop position
            if (config.sortable || config.tree) {
                // For tree mode, use all items including nested; otherwise just root items
                const allItems = config.tree ? getAllItems() : getItems();
                const items = allItems.filter(i => i !== currentDrag?.element);
                const dropInfo = calculateDropPosition(
                    element,
                    items,
                    e.clientX,
                    e.clientY,
                    config.axis,
                    config
                );

                stateAdapter.set('dropPosition', dropInfo.position);
                stateAdapter.set('isNesting', dropInfo.nestIntent);

                // Show visual feedback
                showDropIndicator(dropInfo.position, dropInfo.targetItem, element, config);
            }
        };

        const onDragEnter = (): void => {
            if (!currentDrag) return;

            if (config.dropzone && !acceptsGroup(config, currentDrag.group)) {
                return;
            }

            stateAdapter.set('isDragOver', true);
            element.classList.add('drag-over');

            dispatchDragEvent('enter', {
                id: config.id,
                oldIndex: currentDrag.sourceIndex,
                newIndex: -1,
                item: currentDrag.element,
                from: currentDrag.sourceInstance.element,
                to: element,
                group: currentDrag.group,
            });
        };

        const onDragLeave = (e: DragEvent): void => {
            // Check if we're actually leaving the container
            const relatedTarget = e.relatedTarget as Node | null;
            if (relatedTarget && element.contains(relatedTarget)) {
                return;
            }

            stateAdapter.set('isDragOver', false);
            stateAdapter.set('dropPosition', null);
            stateAdapter.set('isNesting', false);
            element.classList.remove('drag-over');

            // Hide drop indicator
            hideDropIndicator(element, config);

            // Remove ghost
            if (ghost && ghost.parentNode) {
                ghost.parentNode.removeChild(ghost);
                ghost = null;
            }

            if (currentDrag) {
                dispatchDragEvent('leave', {
                    id: config.id,
                    oldIndex: currentDrag.sourceIndex,
                    newIndex: -1,
                    item: currentDrag.element,
                    from: currentDrag.sourceInstance.element,
                    to: element,
                    group: currentDrag.group,
                });
            }
        };

        const onDrop = (e: DragEvent): void => {
            e.preventDefault();
            e.stopPropagation(); // Prevent bubbling to parent containers

            if (!currentDrag) return;

            // Check if we accept this
            if (config.dropzone && !acceptsGroup(config, currentDrag.group)) {
                return;
            }

            stateAdapter.set('isDragOver', false);
            element.classList.remove('drag-over');

            // For tree mode, use all items; otherwise just root items
            const allItems = config.tree ? getAllItems() : getItems();
            // Filter out dragged element AND its descendants (can't drop into self)
            const items = allItems.filter(i => {
                if (i === currentDrag?.element) return false;
                if (i.classList.contains('draggable-ghost')) return false;
                if (currentDrag?.element && currentDrag.element.contains(i)) return false;
                return true;
            });

            const dropInfo = calculateDropPosition(
                element,
                items,
                e.clientX,
                e.clientY,
                config.axis,
                config
            );

            // Hide drop indicator
            hideDropIndicator(element, config);

            // FLIP: Capture positions of all items BEFORE any DOM changes
            const beforeRects = new Map<HTMLElement, DOMRect>();
            getAllItems().forEach(el => {
                if (!el.classList.contains('draggable-ghost')) {
                    beforeRects.set(el, el.getBoundingClientRect());
                }
            });

            const draggedBeforeRect = currentDrag.element.getBoundingClientRect();

            // Remove ghost
            if (ghost && ghost.parentNode) {
                ghost.parentNode.removeChild(ghost);
                ghost = null;
            }

            const draggedItem = currentDrag.element;
            const currentContainer = getItemContainer(draggedItem);
            const currentSiblings = getDraggableItems(currentContainer, config);
            const currentIndex = currentSiblings.indexOf(draggedItem);

            // Handle nesting (tree mode) - drag INTO another item
            if (config.tree && dropInfo.position === 'inside' && dropInfo.targetItem) {
                // Don't allow nesting into self or own children
                if (dropInfo.targetItem !== draggedItem && !draggedItem.contains(dropInfo.targetItem)) {
                    nestItem(draggedItem, dropInfo.targetItem, 0);
                }
            } else if (dropInfo.targetItem) {
                // Get the container of the target item (could be root or nested)
                const targetContainer = getItemContainer(dropInfo.targetItem);

                // Remove the item from current position FIRST
                draggedItem.remove();

                // Now get target siblings (after removal, in case same container)
                const targetSiblings = getDraggableItems(targetContainer, config);
                let targetIndex = targetSiblings.indexOf(dropInfo.targetItem);

                // Calculate insert index
                let insertIndex = dropInfo.position === 'before' ? targetIndex : targetIndex + 1;

                // If moving within same container and was originally before target, adjust
                if (targetContainer === currentContainer && currentIndex < targetIndex) {
                    // Item was removed, so indices shifted - no need to adjust insertIndex
                    // But if we calculated based on "after", we need to account for shift
                    if (dropInfo.position === 'after') {
                        insertIndex = targetIndex + 1;
                    }
                }

                // Clamp to valid range
                const finalSiblings = getDraggableItems(targetContainer, config);
                insertIndex = Math.max(0, Math.min(insertIndex, finalSiblings.length));

                // Insert at new position
                if (insertIndex >= finalSiblings.length) {
                    targetContainer.appendChild(draggedItem);
                } else {
                    targetContainer.insertBefore(draggedItem, finalSiblings[insertIndex]);
                }

                // Re-setup listeners for the moved item (and its children if any)
                setupItemListeners(draggedItem);

                // Animate all affected items
                if (config.animation > 0) {
                    getAllItems().forEach(el => {
                        const beforeRect = beforeRects.get(el);
                        if (beforeRect) {
                            animateMove(el, beforeRect, config);
                        }
                    });
                }

                const eventType = targetContainer === currentContainer ? 'sort' : 'move';
                dispatchDragEvent(eventType, {
                    id: config.id,
                    oldIndex: currentIndex,
                    newIndex: insertIndex,
                    item: draggedItem,
                    from: currentContainer,
                    to: targetContainer,
                    group: config.group,
                    dropPosition: dropInfo.position,
                });
            }

            dispatchDragEvent('drop', {
                id: config.id,
                oldIndex: currentDrag.sourceIndex,
                newIndex: dropInfo.index,
                item: draggedItem,
                from: currentDrag.sourceInstance.element,
                to: element,
                group: currentDrag.group,
                dropPosition: dropInfo.position,
            });

            // Reset state
            stateAdapter.set('dropPosition', null);
            stateAdapter.set('isNesting', false);
        };

        element.addEventListener('dragover', onDragOver);
        element.addEventListener('dragenter', onDragEnter);
        element.addEventListener('dragleave', onDragLeave);
        element.addEventListener('drop', onDrop);

        cleanups.push(() => {
            element.removeEventListener('dragover', onDragOver);
            element.removeEventListener('dragenter', onDragEnter);
            element.removeEventListener('dragleave', onDragLeave);
            element.removeEventListener('drop', onDrop);
        });
    };

    /**
     * Setup all drag listeners
     */
    const setupDragListeners = (): void => {
        if (!isEnabled) return;

        // Setup container listeners
        setupContainerListeners();

        // Setup item listeners
        if (!config.dropzone) {
            const items = config.tree ? getAllItems() : getItems();
            items.forEach(setupItemListeners);
        }
    };

    /**
     * Cleanup drag listeners
     */
    const cleanupDragListeners = (): void => {
        cleanups.forEach(fn => fn());
        cleanups = [];
    };

    /**
     * Dispatch drag event
     */
    const dispatchDragEvent = (type: string, detail: DragEventDetail): void => {
        element.dispatchEvent(new CustomEvent(`drag${type}`, { detail, bubbles: true }));
        document.dispatchEvent(new CustomEvent(`accelade:drag:${type}`, { detail }));
    };

    /**
     * Dispose
     */
    const dispose = (): void => {
        cleanupDragListeners();

        // Cancel any running animations
        springAnimations.forEach((animation) => {
            cancelAnimationFrame(animation.id);
        });

        if (ghost && ghost.parentNode) {
            ghost.parentNode.removeChild(ghost);
        }

        hideDropIndicator(element, config);
        instances.delete(config.id);
    };

    // Initial setup
    setupDragListeners();

    const instance: DraggableInstance = {
        id: config.id,
        config,
        element,
        enable,
        disable,
        isEnabled: isEnabledFn,
        getItems,
        getItem,
        moveItem,
        addItem,
        removeItem,
        refresh,
        dispose,
        nestItem,
        unnestItem,
        getTree,
        getItemDepth: getItemDepthFn,
        toggleCollapse,
        isCollapsed,
    };

    instances.set(config.id, instance);

    return instance;
}

/**
 * Create draggable methods for template usage
 */
export function createDraggableMethods(instance: DraggableInstance): DraggableMethods {
    return {
        enableDrag: instance.enable,
        disableDrag: instance.disable,
        isDragEnabled: instance.isEnabled,
        getDragItems: instance.getItems,
        moveDragItem: instance.moveItem,
        refreshDrag: instance.refresh,
        nestDragItem: instance.nestItem,
        unnestDragItem: instance.unnestItem,
        getDragTree: instance.getTree,
        toggleDragCollapse: instance.toggleCollapse,
    };
}

/**
 * Get a draggable instance by ID
 */
export function getDraggableInstance(id: string): DraggableInstance | undefined {
    return instances.get(id);
}

/**
 * DraggableFactory namespace for module exports
 */
export const DraggableFactory = {
    parseConfig,
    create: createDraggable,
    createMethods: createDraggableMethods,
    getInstance: getDraggableInstance,
};
