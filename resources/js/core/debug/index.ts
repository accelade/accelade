/**
 * Debug System Exports
 */

export {
    StateHistory,
    type StateSnapshot,
    type StateHistoryConfig,
} from './StateHistory';

export {
    NetworkInspector,
    type NetworkRecord,
    type NetworkInspectorConfig,
} from './NetworkInspector';

export {
    DebugManager,
    type DebugConfig,
    type AcceladeDevtools,
} from './DebugManager';

// Default export is DebugManager
export { DebugManager as default } from './DebugManager';
