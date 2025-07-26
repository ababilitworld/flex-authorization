(function () {
    "use strict";

    // Wait for DOM to be ready
    document.addEventListener('DOMContentLoaded', function() {
        // Find all tab containers
        document.querySelectorAll('.fpba').forEach(container => {
            const tabItems = container.querySelectorAll('.tab-item');
            const tabContents = container.querySelectorAll('.tab-content');
            
            // Initialize tabs
            function initTabs() {
                // Hide all tab contents and deactivate all tabs
                tabContents.forEach(content => {
                    content.classList.remove('active');
                    content.setAttribute('aria-hidden', 'true');
                });
                
                tabItems.forEach(item => {
                    item.classList.remove('active');
                    item.setAttribute('aria-selected', 'false');
                    item.setAttribute('tabindex', '-1');
                });
                
                // Activate first tab by default if none specified
                const currentHash = window.location.hash;
                let activeTab = currentHash 
                    ? container.querySelector(`.tab-item[data-tabs-target="${currentHash}"]`)
                    : tabItems[0];
                
                if (activeTab) {
                    activateTab(activeTab);
                }
            }
            
            // Activate a tab
            function activateTab(tabItem) {
                const target = tabItem.getAttribute('data-tabs-target');
                if (!target) return;
                
                // Deactivate all
                tabItems.forEach(item => {
                    item.classList.remove('active');
                    item.setAttribute('aria-selected', 'false');
                    item.setAttribute('tabindex', '-1');
                });
                
                tabContents.forEach(content => {
                    content.classList.remove('active');
                    content.setAttribute('aria-hidden', 'true');
                });
                
                // Activate current
                tabItem.classList.add('active');
                tabItem.setAttribute('aria-selected', 'true');
                tabItem.removeAttribute('tabindex');
                
                const targetContent = container.querySelector(target);
                if (targetContent) {
                    targetContent.classList.add('active');
                    targetContent.setAttribute('aria-hidden', 'false');
                }
                
                // Update URL
                window.location.hash = target;
            }
            
            // Click handler
            tabItems.forEach(tabItem => {
                tabItem.addEventListener('click', function(e) {
                    e.preventDefault();
                    activateTab(this);
                });
                
                // Keyboard navigation
                tabItem.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        activateTab(this);
                    }
                    
                    // Arrow key navigation
                    if (e.key === 'ArrowRight' || e.key === 'ArrowLeft') {
                        const currentIndex = Array.from(tabItems).indexOf(this);
                        let nextIndex;
                        
                        if (e.key === 'ArrowRight') {
                            nextIndex = (currentIndex + 1) % tabItems.length;
                        } else {
                            nextIndex = (currentIndex - 1 + tabItems.length) % tabItems.length;
                        }
                        
                        tabItems[nextIndex].focus();
                    }
                });
            });
            
            // Initialize tabs
            initTabs();
            
            // Handle hash changes
            window.addEventListener('hashchange', function() {
                const currentHash = window.location.hash;
                if (currentHash) {
                    const tabToActivate = container.querySelector(`.tab-item[data-tabs-target="${currentHash}"]`);
                    if (tabToActivate) {
                        activateTab(tabToActivate);
                    }
                }
            });
        });
    });
})();