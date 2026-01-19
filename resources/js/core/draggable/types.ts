/**
 * Draggable Component Types
 *
 * Types for the Draggable component which provides drag and drop functionality
 * for sortable lists, kanban boards, tree structures, and file uploads.
 */

/**
 * Axis constraint for dragging
 */
export type DraggableAxis = 'x' | 'y' | null;

/**
 * Drop position indicator for nesting
 */
export type DropPosition = 'before' | 'after' | 'inside';

/**
 * Draggable configuration parsed from element attributes
 */
export interface DraggableConfig {
    /** Unique identifier for the draggable instance */
    id: string;
    /** Group name for drag between lists */
    group: string | null;
    /** CSS selector for drag handle (if null, entire item is draggable) */
    handle: string | null;
    /** Animation duration in milliseconds */
    animation: number;
    /** CSS class applied to ghost/placeholder element */
    ghostClass: string;
    /** CSS class applied to element being dragged */
    dragClass: string;
    /** Whether dragging is disabled */
    disabled: boolean;
    /** Whether items are sortable within container */
    sortable: boolean;
    /** Whether this is a dropzone only (no draggable items) */
    dropzone: boolean;
    /** Group names this dropzone accepts (comma-separated or null for all) */
    accepts: string | null;
    /** Constrain drag to axis */
    axis: DraggableAxis;
    /** Enable tree/nested drag support */
    tree: boolean;
    /** CSS selector for nested container within items */
    nestedContainer: string;
    /** Maximum nesting depth (0 = unlimited) */
    maxDepth: number;
    /** Indent size in pixels for tree visualization */
    indentSize: number;
    /** CSS class for drop indicator line */
    dropIndicatorClass: string;
    /** CSS class for nesting indicator */
    nestIndicatorClass: string;
    /** Threshold in pixels to detect nesting intent (horizontal drag distance) */
    nestThreshold: number;
    /** Enable spring physics animation */
    springAnimation: boolean;
    /** Spring stiffness (higher = snappier) */
    springStiffness: number;
    /** Spring damping (higher = less bounce) */
    springDamping: number;
}

/**
 * Drag event detail
 */
export interface DragEventDetail {
    /** Draggable instance ID */
    id: string;
    /** Index of the dragged item in source container */
    oldIndex: number;
    /** Index of the dragged item in target container */
    newIndex: number;
    /** The dragged element */
    item: HTMLElement;
    /** Source container element */
    from: HTMLElement;
    /** Target container element */
    to: HTMLElement;
    /** Group name */
    group: string | null;
    /** Parent item (for tree operations) */
    parent?: HTMLElement | null;
    /** Previous parent (for tree operations) */
    oldParent?: HTMLElement | null;
    /** Depth level in tree */
    depth?: number;
    /** Drop position relative to target */
    dropPosition?: DropPosition;
}

/**
 * Tree node data structure
 */
export interface TreeNode {
    /** Element reference */
    element: HTMLElement;
    /** Parent node (null for root) */
    parent: TreeNode | null;
    /** Child nodes */
    children: TreeNode[];
    /** Depth level (0 = root) */
    depth: number;
    /** Index within parent */
    index: number;
    /** Whether the node is collapsed */
    collapsed: boolean;
}

/**
 * Draggable state
 */
export interface DraggableState {
    /** Whether currently dragging */
    isDragging: boolean;
    /** Whether drag is over this container */
    isDragOver: boolean;
    /** Reference to dragged item element */
    draggedItem: HTMLElement | null;
    /** Index of dragged item */
    draggedIndex: number | null;
    /** Array of item data */
    items: unknown[];
    /** Current drop position */
    dropPosition: DropPosition | null;
    /** Current drop target */
    dropTarget: HTMLElement | null;
    /** Whether nesting is indicated */
    isNesting: boolean;
}

/**
 * Draggable instance returned by the factory
 */
export interface DraggableInstance {
    /** Unique identifier */
    id: string;
    /** Configuration */
    config: DraggableConfig;
    /** The source element */
    element: HTMLElement;
    /**
     * Enable dragging
     */
    enable: () => void;
    /**
     * Disable dragging
     */
    disable: () => void;
    /**
     * Check if dragging is enabled
     */
    isEnabled: () => boolean;
    /**
     * Get current items order
     */
    getItems: () => HTMLElement[];
    /**
     * Get item at index
     */
    getItem: (index: number) => HTMLElement | null;
    /**
     * Move item from one index to another
     */
    moveItem: (fromIndex: number, toIndex: number) => void;
    /**
     * Add item at index
     */
    addItem: (element: HTMLElement, index?: number) => void;
    /**
     * Remove item at index
     */
    removeItem: (index: number) => HTMLElement | null;
    /**
     * Update the sortable items (re-scan DOM)
     */
    refresh: () => void;
    /** Dispose the draggable instance */
    dispose: () => void;
    /**
     * Nest item inside another item (tree mode)
     */
    nestItem: (item: HTMLElement, parent: HTMLElement, index?: number) => void;
    /**
     * Unnest item to parent level (tree mode)
     */
    unnestItem: (item: HTMLElement) => void;
    /**
     * Get tree structure
     */
    getTree: () => TreeNode[];
    /**
     * Get item depth in tree
     */
    getItemDepth: (item: HTMLElement) => number;
    /**
     * Collapse/expand tree node
     */
    toggleCollapse: (item: HTMLElement) => void;
    /**
     * Check if item is collapsed
     */
    isCollapsed: (item: HTMLElement) => boolean;
}

/**
 * Draggable methods exposed to templates
 */
export interface DraggableMethods {
    /** Enable dragging */
    enableDrag: () => void;
    /** Disable dragging */
    disableDrag: () => void;
    /** Check if enabled */
    isDragEnabled: () => boolean;
    /** Get items */
    getDragItems: () => HTMLElement[];
    /** Move item */
    moveDragItem: (fromIndex: number, toIndex: number) => void;
    /** Refresh items */
    refreshDrag: () => void;
    /** Nest item inside another (tree mode) */
    nestDragItem: (item: HTMLElement, parent: HTMLElement, index?: number) => void;
    /** Unnest item (tree mode) */
    unnestDragItem: (item: HTMLElement) => void;
    /** Get tree structure */
    getDragTree: () => TreeNode[];
    /** Toggle collapse state */
    toggleDragCollapse: (item: HTMLElement) => void;
}

/**
 * Drop zone configuration
 */
export interface DropZoneConfig {
    /** Accepts items from these groups */
    accepts: string[];
    /** Callback when item enters zone */
    onDragEnter?: (event: DragEventDetail) => void;
    /** Callback when item leaves zone */
    onDragLeave?: (event: DragEventDetail) => void;
    /** Callback when item is dropped */
    onDrop?: (event: DragEventDetail) => void;
}

/**
 * Spring animation state
 */
export interface SpringState {
    /** Current position */
    position: { x: number; y: number };
    /** Current velocity */
    velocity: { x: number; y: number };
    /** Target position */
    target: { x: number; y: number };
}
