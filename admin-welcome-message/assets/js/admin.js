/**
 * Admin Welcome Modal JavaScript
 */

(function($) {
    'use strict';
    
    // Modal data from PHP
    const modalData = window.awmModalData || {};
    const options = modalData.options || {};
    
    // DOM elements
    let modalOverlay, modalContainer, checkbox, closeBtn, ctaBtn;
    
    // Session storage keys
    const STORAGE_KEYS = {
        DISMISSED: 'awm_modal_dismissed',
        NEXT_SHOW_TIME: 'awm_modal_next_show_time'
    };
    
    /**
     * Initialize modal functionality
     */
    function init() {
        // Get DOM elements
        modalOverlay = document.getElementById('awm-admin-modal-overlay');
        modalContainer = document.getElementById('awm-admin-modal');
        checkbox = document.getElementById('awm-hide-session-checkbox');
        closeBtn = document.getElementById('awm-close-modal-btn');
        ctaBtn = document.getElementById('awm-access-help-btn');
        
        if (!modalOverlay || !modalContainer) {
            return;
        }
        
        // Check if modal should be shown
        if (shouldShowModal()) {
            showModal();
        }
        
        // Bind events
        bindEvents();
        
        // Initialize color pickers if on settings page
        if (window.awmAdminData) {
            initColorPickers();
            initSettingsTabs();
        }
    }
    
    /**
     * Check if modal should be shown
     */
    function shouldShowModal() {
        const dismissed = sessionStorage.getItem(STORAGE_KEYS.DISMISSED);
        const nextShowTime = sessionStorage.getItem(STORAGE_KEYS.NEXT_SHOW_TIME);
        const now = Date.now();
        
        // If dismissed for session, don't show
        if (dismissed === 'true') {
            return false;
        }
        
        // If cooldown mode and time hasn't passed, don't show
        if (options.dismiss_mode === 'cooldown' && nextShowTime && now < parseInt(nextShowTime)) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Show the modal
     */
    function showModal() {
        if (modalOverlay) {
            modalOverlay.style.display = 'flex';
            
            // Focus management
            setTimeout(() => {
                if (modalContainer) {
                    modalContainer.focus();
                }
            }, 100);
            
            // Trap focus
            trapFocus();
        }
    }
    
    /**
     * Hide the modal
     */
    function hideModal() {
        if (modalOverlay) {
            modalOverlay.style.display = 'none';
            
            // Restore focus
            restoreFocus();
        }
    }
    
    /**
     * Close modal and handle session logic
     */
    function closeModal() {
        // Determine session hide intent
        const wantSessionHide = (options.enable_session_hide === true) && checkbox && checkbox.checked;
        if (wantSessionHide) {
            sessionStorage.setItem(STORAGE_KEYS.DISMISSED, 'true');
            sessionStorage.removeItem(STORAGE_KEYS.NEXT_SHOW_TIME);
        } else {
            sessionStorage.removeItem(STORAGE_KEYS.DISMISSED);
        }

        // Determine cooldown/always behavior
        if (options.dismiss_mode === 'cooldown') {
            const cooldownMs = (options.cooldown_minutes || 15) * 60 * 1000;
            const nextTime = Date.now() + cooldownMs;
            sessionStorage.setItem(STORAGE_KEYS.NEXT_SHOW_TIME, nextTime.toString());
        } else if (options.dismiss_mode === 'always') {
            sessionStorage.removeItem(STORAGE_KEYS.NEXT_SHOW_TIME);
        }

        hideModal();
    }
    
    /**
     * Handle CTA button click
     */
    function handleCtaClick() {
        if (options.close_on_cta) {
            closeModal();
        }
        
        // Let the link work normally
        return true;
    }
    
    /**
     * Bind event listeners
     */
    function bindEvents() {
        // Close button
        if (closeBtn) {
            closeBtn.addEventListener('click', closeModal);
        }
        
        // CTA button
        if (ctaBtn) {
            ctaBtn.addEventListener('click', handleCtaClick);
        }
        
        // ESC key
        if (options.close_on_esc === true) {
            document.addEventListener('keydown', handleKeydown);
        }
        
        // Click outside modal - controlled by setting
        if (modalOverlay && options.close_on_overlay === true) {
            modalOverlay.addEventListener('click', handleOverlayClick);
        }
        
        // Prevent modal clicks from closing
        if (modalContainer) {
            modalContainer.addEventListener('click', handleModalClick);
        }
    }
    
    /**
     * Handle keyboard events
     */
    function handleKeydown(e) {
        if (e.key === 'Escape' && modalOverlay && modalOverlay.style.display === 'flex') {
            closeModal();
        }
    }
    
    /**
     * Handle overlay click (close modal)
     */
    function handleOverlayClick(e) {
        if (e.target === modalOverlay) {
            closeModal();
        }
    }
    
    /**
     * Handle modal click (prevent closing)
     */
    function handleModalClick(e) {
        e.stopPropagation();
    }
    
    /**
     * Trap focus within modal
     */
    function trapFocus() {
        const focusableElements = modalContainer.querySelectorAll(
            'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
        );
        
        if (focusableElements.length === 0) {
            return;
        }
        
        const firstElement = focusableElements[0];
        const lastElement = focusableElements[focusableElements.length - 1];
        
        // Store current focus for restoration
        window.awmPreviousFocus = document.activeElement;
        
        // Focus first element
        firstElement.focus();
        
        // Handle tab key
        modalContainer.addEventListener('keydown', function(e) {
            if (e.key === 'Tab') {
                if (e.shiftKey) {
                    if (document.activeElement === firstElement) {
                        e.preventDefault();
                        lastElement.focus();
                    }
                } else {
                    if (document.activeElement === lastElement) {
                        e.preventDefault();
                        firstElement.focus();
                    }
                }
            }
        });
    }
    
    /**
     * Restore focus
     */
    function restoreFocus() {
        if (window.awmPreviousFocus && window.awmPreviousFocus.focus) {
            window.awmPreviousFocus.focus();
        }
    }
    
    /**
     * Initialize color pickers on settings page
     */
    function initColorPickers() {
        if (typeof $.fn.wpColorPicker !== 'undefined') {
            $('.awm-color-picker').wpColorPicker();
        }
    }
    
    /**
     * Initialize settings page tabs
     */
    function initSettingsTabs() {
        const tabLinks = document.querySelectorAll('.nav-tab');
        const tabContents = document.querySelectorAll('.awm-tab-content');
        
        tabLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Remove active class from all tabs and contents
                tabLinks.forEach(tab => tab.classList.remove('nav-tab-active'));
                tabContents.forEach(content => content.classList.remove('active'));
                
                // Add active class to clicked tab
                this.classList.add('nav-tab-active');
                
                // Show corresponding content
                const targetTab = this.getAttribute('data-tab');
                const targetContent = document.getElementById(targetTab + '-tab');
                if (targetContent) {
                    targetContent.classList.add('active');
                }
            });
        });
        
        // Initialize live preview button
        const previewBtn = document.getElementById('awm-live-preview-btn');
        if (previewBtn) {
            previewBtn.addEventListener('click', function() {
                // Show preview modal with current form values
                showPreviewModal();
            });
        }
    }
    
    /**
     * Show preview modal with current settings
     */
    function showPreviewModal() {
        // Get current form values
        const form = document.querySelector('form[method="post"]');
        if (!form) return;
        
        const formData = new FormData(form);
        const options = {};
        
        // Parse form data to build options object
        for (let [key, value] of formData.entries()) {
            if (key.startsWith('awm_options[')) {
                const cleanKey = key.replace('awm_options[', '').replace(']', '');
                if (cleanKey.includes('[')) {
                    // Handle nested arrays like colors[header_bg]
                    const [parent, child] = cleanKey.split('[');
                    const childKey = child.replace(']', '');
                    if (!options[parent]) options[parent] = {};
                    options[parent][childKey] = value;
                } else {
                    options[cleanKey] = value;
                }
            }
        }
        
        // Create preview modal
        createPreviewModal(options);
    }
    
    /**
     * Create and show preview modal
     */
    function createPreviewModal(options) {
        // Remove existing preview modal
        const existingPreview = document.getElementById('awm-preview-modal');
        if (existingPreview) {
            existingPreview.remove();
        }
        
        // Create preview modal HTML
        const previewHTML = `
            <div id="awm-preview-modal" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 999999; display: flex; align-items: center; justify-content: center;">
                <div style="background: white; padding: 20px; border-radius: 8px; max-width: 600px; width: 90%; max-height: 90vh; overflow-y: auto;">
                    <h2 style="margin-top: 0;">Live Preview</h2>
                    <div id="awm-preview-content"></div>
                    <div style="text-align: center; margin-top: 20px;">
                        <button type="button" onclick="document.getElementById('awm-preview-modal').remove()" class="button button-primary">
                            Close Preview
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        document.body.insertAdjacentHTML('beforeend', previewHTML);
        
        // Render preview content
        renderPreviewContent(options);
    }
    
    /**
     * Render preview content
     */
    function renderPreviewContent(options) {
        const previewContent = document.getElementById('awm-preview-content');
        if (!previewContent) return;
        
        // Build CSS variables for colors
        const colors = options.colors || {};
        const cssVars = [];
        
        const colorMappings = {
            'header_bg': '--awm-header-bg',
            'header_text': '--awm-header-color',
            'body_bg': '--awm-body-bg',
            'body_text': '--awm-body-color',
            'footer_bg': '--awm-footer-bg',
            'footer_text': '--awm-footer-color',
            'btn_bg': '--awm-btn-bg',
            'btn_text': '--awm-btn-color',
            'btn_bg_hover': '--awm-btn-bg-hover',
            'btn_text_hover': '--awm-btn-color-hover'
        };
        
        for (const [optionKey, cssVar] of Object.entries(colorMappings)) {
            if (colors[optionKey]) {
                cssVars.push(`${cssVar}: ${colors[optionKey]};`);
            }
        }
        
        const cssVarsString = cssVars.join(' ');
        
        // Create preview modal HTML
        const previewModalHTML = `
            <div id="awm-admin-modal" style="${cssVarsString}">
                <div id="awm-admin-modal-header">
                    ${options.title || 'Welcome'}
                </div>
                <div id="awm-admin-modal-content">
                    ${options.message || '<p>Your message will appear here.</p>'}
                </div>
                <div id="awm-admin-modal-buttons">
                    <a href="${options.cta_url || '#'}" class="awm-modal-btn" style="text-decoration: none;">
                        ${options.cta_text || 'Access Help'}
                    </a>
                    <button class="awm-modal-btn">Close</button>
                </div>
                <div id="awm-admin-modal-footer">
                    <input type="checkbox" id="awm-hide-session-checkbox">
                    <label for="awm-hide-session-checkbox">
                        ${options.footer_note || "Don't show this again during my current session"}
                    </label>
                </div>
            </div>
        `;
        
        previewContent.innerHTML = previewModalHTML;
    }
    
    /**
     * Reset modal session data (for testing)
     */
    function resetModal() {
        sessionStorage.removeItem(STORAGE_KEYS.DISMISSED);
        sessionStorage.removeItem(STORAGE_KEYS.NEXT_SHOW_TIME);
    }
    
    // Expose reset function for debugging
    window.awmResetModal = resetModal;
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
    
})(jQuery);
