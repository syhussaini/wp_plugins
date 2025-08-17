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
        if (checkbox && checkbox.checked) {
            // User checked "don't show again"
            if (options.dismiss_mode === 'session') {
                sessionStorage.setItem(STORAGE_KEYS.DISMISSED, 'true');
            } else if (options.dismiss_mode === 'cooldown') {
                const cooldownMs = (options.cooldown_minutes || 15) * 60 * 1000;
                const nextTime = Date.now() + cooldownMs;
                sessionStorage.setItem(STORAGE_KEYS.NEXT_SHOW_TIME, nextTime.toString());
            }
        } else {
            // User didn't check the box, set cooldown anyway
            if (options.dismiss_mode === 'cooldown') {
                const cooldownMs = (options.cooldown_minutes || 15) * 60 * 1000;
                const nextTime = Date.now() + cooldownMs;
                sessionStorage.setItem(STORAGE_KEYS.NEXT_SHOW_TIME, nextTime.toString());
            }
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
        if (options.close_on_esc !== false) {
            document.addEventListener('keydown', handleKeydown);
        }
        
        // Click outside modal
        if (modalOverlay) {
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
