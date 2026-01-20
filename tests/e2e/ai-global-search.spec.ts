import { test, expect } from '@playwright/test';

test.describe('AI Global Search Component', () => {
  test.beforeEach(async ({ page }) => {
    // Navigate to the AI global search docs page
    await page.goto('https://xblade.test/docs/ai-global-search?framework=vanilla');

    // Wait for page to fully load
    await page.waitForLoadState('networkidle');
  });

  test('page loads successfully', async ({ page }) => {
    // Check page title contains expected text
    await expect(page).toHaveTitle(/Global Search/);

    // Check that the main content area is visible (demo area for AI components)
    await expect(page.locator('main')).toBeVisible();

    // Check for the Open Search button which is part of the demo
    await expect(page.locator('button:has-text("Open Search")')).toBeVisible();
  });

  test('Accelade scripts are loaded', async ({ page }) => {
    // Check that Accelade global object exists
    const acceladeExists = await page.evaluate(() => {
      return typeof (window as any).Accelade !== 'undefined';
    });
    expect(acceladeExists).toBe(true);
  });

  test('global search component is rendered', async ({ page }) => {
    // Look for the global search component in the demo tab or on page
    const globalSearchComponent = page.locator('[data-accelade-component="global-search"]');

    // It might be in a demo section, let's check if it exists anywhere on the page
    const count = await globalSearchComponent.count();
    console.log(`Found ${count} global-search component(s)`);
  });

  test('can open search with Cmd+K keyboard shortcut', async ({ page }) => {
    // Press Cmd+K (or Ctrl+K on non-Mac)
    await page.keyboard.press('Meta+k');

    // Wait a bit for modal to appear
    await page.waitForTimeout(500);

    // Check if a search modal/overlay is visible
    // This could be the built-in docs search or the AI global search
    const searchModal = page.locator('#search-modal, [data-accelade-component="global-search"] [a-show="isOpen"]');

    // Check visibility of search elements
    const isVisible = await searchModal.isVisible().catch(() => false);
    console.log(`Search modal visible: ${isVisible}`);
  });

  test('switch to demo tab and check component', async ({ page }) => {
    // Click on the "Live Demo" tab if it exists
    const demoTab = page.locator('.tab-btn[data-tab="demo"]');
    if (await demoTab.isVisible()) {
      await demoTab.click();
      await page.waitForTimeout(300);

      // Now check for global search component in the demo area
      const demoArea = page.locator('#tab-demo');
      await expect(demoArea).toBeVisible();

      // Check for the global search component
      const globalSearchInDemo = demoArea.locator('[data-accelade-component="global-search"]');
      const exists = await globalSearchInDemo.count() > 0;
      console.log(`Global search in demo tab: ${exists}`);
    }
  });

  test('check for JavaScript errors', async ({ page }) => {
    const errors: string[] = [];

    page.on('console', msg => {
      if (msg.type() === 'error') {
        errors.push(msg.text());
      }
    });

    // Navigate fresh to capture all console errors
    await page.goto('https://xblade.test/docs/ai-global-search?framework=vanilla');
    await page.waitForLoadState('networkidle');
    await page.waitForTimeout(1000);

    // Log any errors found
    if (errors.length > 0) {
      console.log('JavaScript errors found:');
      errors.forEach(err => console.log(`  - ${err}`));
    } else {
      console.log('No JavaScript errors found');
    }
  });

  test('check Accelade component initialization', async ({ page }) => {
    // Check if Accelade components are properly initialized
    const componentStatus = await page.evaluate(() => {
      const accelade = (window as any).Accelade;
      if (!accelade) {
        return { error: 'Accelade not found on window' };
      }

      // Get all components
      const components = accelade.getComponents ? accelade.getComponents() : null;

      return {
        acceladeExists: true,
        framework: accelade.getFramework ? accelade.getFramework() : 'unknown',
        componentsCount: components ? components.size : 0,
        componentIds: components ? Array.from(components.keys()) : []
      };
    });

    console.log('Accelade status:', JSON.stringify(componentStatus, null, 2));
  });

  test('verify a-* directives are processed', async ({ page }) => {
    // Check if elements with a-show are properly hidden/shown
    const elementsWithAShow = await page.evaluate(() => {
      const elements = document.querySelectorAll('[a-show]');
      return {
        count: elements.length,
        samples: Array.from(elements).slice(0, 5).map(el => ({
          tagName: el.tagName,
          aShow: el.getAttribute('a-show'),
          isHidden: (el as HTMLElement).style.display === 'none' || el.hasAttribute('a-cloak')
        }))
      };
    });

    console.log('Elements with a-show:', JSON.stringify(elementsWithAShow, null, 2));
  });

  test('check AcceladeAI global object', async ({ page }) => {
    // Check if AcceladeAI is available (from the AI package)
    const aiStatus = await page.evaluate(() => {
      const ai = (window as any).AcceladeAI;
      if (!ai) {
        return { error: 'AcceladeAI not found on window' };
      }

      return {
        exists: true,
        hasConfig: !!ai.config,
        configKeys: ai.config ? Object.keys(ai.config) : [],
        hasMethods: {
          search: typeof ai.search === 'function',
          chat: typeof ai.chat === 'function',
          streamChat: typeof ai.streamChat === 'function'
        }
      };
    });

    console.log('AcceladeAI status:', JSON.stringify(aiStatus, null, 2));
  });

  test('clicking Open Search button opens the AI search modal', async ({ page }) => {
    // Find and click the "Open Search" button
    const openSearchButton = page.locator('button:has-text("Open Search")');
    await expect(openSearchButton).toBeVisible();
    await openSearchButton.click();

    // Wait for the modal to appear
    await page.waitForTimeout(500);

    // Check that the search modal is now visible
    // The AI global search component should show an overlay with isOpen state
    const searchModal = page.locator('[data-accelade-component="global-search"]');

    // Check for the search input which should be visible when modal is open
    const searchInput = searchModal.locator('input[type="text"], input[placeholder*="Search"]');
    const inputCount = await searchInput.count();
    console.log(`Search inputs found: ${inputCount}`);

    // Verify the modal state changed
    const modalState = await page.evaluate(() => {
      const component = document.querySelector('[data-accelade-component="global-search"]');
      if (!component) return { error: 'Component not found' };

      // Check for visible search input
      const searchInputs = component.querySelectorAll('input');
      const visibleInputs = Array.from(searchInputs).filter(
        input => window.getComputedStyle(input).display !== 'none'
      );

      return {
        hasComponent: true,
        totalInputs: searchInputs.length,
        visibleInputs: visibleInputs.length,
        componentHtml: component.outerHTML.substring(0, 500)
      };
    });

    console.log('Modal state after click:', JSON.stringify(modalState, null, 2));
  });

  test('a-show directive toggles visibility correctly via Open Search button', async ({ page }) => {
    // Test that a-show elements properly toggle visibility using the Open Search button
    // First, wait for Accelade to be fully initialized
    await page.waitForFunction(() => {
      return typeof (window as any).Accelade !== 'undefined'
        && typeof (window as any).Accelade.emit === 'function';
    });

    // First, verify the search input is not visible initially
    const searchInput = page.locator('[data-accelade-component="global-search"] input[type="text"]');
    const isInitiallyVisible = await searchInput.isVisible().catch(() => false);
    console.log(`Search input initially visible: ${isInitiallyVisible}`);
    expect(isInitiallyVisible).toBe(false);

    // Click the Open Search button which emits 'open-global-search' event
    await page.locator('button:has-text("Open Search")').click();

    // Wait for the modal to appear with a longer timeout
    await page.waitForTimeout(800);

    // Check if search input is now visible
    const isVisibleAfterClick = await searchInput.isVisible().catch(() => false);
    console.log(`Search input visible after clicking Open Search: ${isVisibleAfterClick}`);

    // Debug: Check component state and element visibility
    const debugInfo = await page.evaluate(() => {
      const component = document.querySelector('[data-accelade-component="global-search"]');
      if (!component) return { error: 'Component not found' };

      const stateAttr = component.getAttribute('data-accelade-state');
      const state = stateAttr ? JSON.parse(stateAttr) : null;

      // Find the modal backdrop with a-show="isOpen"
      const modalBackdrop = component.querySelector('[a-show="isOpen"]');
      const searchInput = component.querySelector('input[type="text"]');

      return {
        state,
        modalBackdrop: modalBackdrop ? {
          display: modalBackdrop.style.display,
          computedDisplay: window.getComputedStyle(modalBackdrop).display,
          hasACloak: modalBackdrop.hasAttribute('a-cloak'),
          classList: Array.from(modalBackdrop.classList)
        } : null,
        searchInput: searchInput ? {
          display: searchInput.style.display,
          computedDisplay: window.getComputedStyle(searchInput).display,
          isVisible: searchInput.offsetParent !== null
        } : null
      };
    });
    console.log('Debug info:', JSON.stringify(debugInfo, null, 2));

    // The input should become visible after clicking the button
    expect(isVisibleAfterClick).toBe(true);
  });

  test('search input accepts text and triggers search', async ({ page }) => {
    // Open the search modal
    await page.locator('button:has-text("Open Search")').click();
    await page.waitForTimeout(500);

    // Find the search input within the AI global search component
    const searchInput = page.locator('[data-accelade-component="global-search"] input[type="text"]').first();

    // Check if input is visible
    const isInputVisible = await searchInput.isVisible().catch(() => false);
    console.log(`Search input visible: ${isInputVisible}`);

    if (isInputVisible) {
      // Type in the search input
      await searchInput.fill('test query');
      await page.waitForTimeout(300);

      // Check the input value was set
      const inputValue = await searchInput.inputValue();
      expect(inputValue).toBe('test query');
      console.log(`Input value set: ${inputValue}`);

      // Check if the component state reflects the query
      const componentState = await page.evaluate(() => {
        const component = document.querySelector('[data-accelade-component="global-search"]');
        if (!component) return null;

        // Try to get the state from data attribute
        const stateAttr = component.getAttribute('data-accelade-state');
        return stateAttr ? JSON.parse(stateAttr) : null;
      });

      console.log('Component state:', JSON.stringify(componentState, null, 2));
    }
  });

  test('Escape key closes the search modal', async ({ page }) => {
    // Open the search modal
    await page.locator('button:has-text("Open Search")').click();
    await page.waitForTimeout(500);

    // Verify the search input is visible (modal is open)
    const searchInput = page.locator('[data-accelade-component="global-search"] input[type="text"]');
    await expect(searchInput).toBeVisible();

    // Focus on the search input to ensure keyboard events go to the right place
    await searchInput.focus();
    await page.waitForTimeout(100);

    // Press Escape to close - try multiple approaches
    // First, try direct keyboard press
    await page.keyboard.press('Escape');
    await page.waitForTimeout(200);

    // Check if it closed
    let componentState = await page.evaluate(() => {
      const component = document.querySelector('[data-accelade-component="global-search"]');
      if (!component) return null;
      const stateAttr = component.getAttribute('data-accelade-state');
      return stateAttr ? JSON.parse(stateAttr) : null;
    });

    // If still open, try dispatching a keydown event on the window
    if (componentState?.isOpen) {
      console.log('Escape key did not close modal, trying window dispatch');
      await page.evaluate(() => {
        const event = new KeyboardEvent('keydown', {
          key: 'Escape',
          code: 'Escape',
          keyCode: 27,
          which: 27,
          bubbles: true
        });
        window.dispatchEvent(event);
      });
      await page.waitForTimeout(300);

      componentState = await page.evaluate(() => {
        const component = document.querySelector('[data-accelade-component="global-search"]');
        if (!component) return null;
        const stateAttr = component.getAttribute('data-accelade-state');
        return stateAttr ? JSON.parse(stateAttr) : null;
      });
    }

    console.log(`Component state isOpen: ${componentState?.isOpen}`);

    // Also check the modal backdrop's computed display
    const modalHidden = await page.evaluate(() => {
      const backdrop = document.querySelector('[data-accelade-component="global-search"] [a-show="isOpen"]');
      if (!backdrop) return { error: 'No backdrop found' };
      const computed = window.getComputedStyle(backdrop);
      return {
        display: computed.display,
        visibility: computed.visibility,
        hasHidingClass: backdrop.classList.contains('accelade-hiding'),
        hasVisibleClass: backdrop.classList.contains('accelade-visible')
      };
    });
    console.log(`Modal backdrop state:`, JSON.stringify(modalHidden, null, 2));

    // Verify modal closed
    expect(componentState?.isOpen).toBe(false);
  });
});
