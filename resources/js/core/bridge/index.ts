/**
 * Bridge Module - Two-way binding between PHP and JavaScript
 */

export {
    createBridge,
    getBridge,
    getAllBridges,
    createMethodProxies,
    disposeBridge,
    type BridgeConfig,
    type BridgeCallResponse,
    type BridgeInstance,
} from './BridgeFactory';

export { default as BridgeFactory } from './BridgeFactory';
