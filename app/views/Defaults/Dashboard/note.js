// Dashboard JavaScript
document.addEventListener('DOMContentLoaded', function() {
    console.log('Dashboard loaded');
    
    // FontAwesome class management
    function ensureFontAwesomeClasses() {
        const htmlElement = document.documentElement;
        
        if (!htmlElement.classList.contains('fontawesome-i2svg-active')) {
            htmlElement.classList.add('fontawesome-i2svg-active');
            console.log('Added fontawesome-i2svg-active class');
        }
        
        if (!htmlElement.classList.contains('fontawesome-i2svg-complete')) {
            htmlElement.classList.add('fontawesome-i2svg-complete');
            console.log('Added fontawesome-i2svg-complete class');
        }
    }
    
    // Try multiple approaches to ensure FontAwesome works
    function initializeFontAwesome() {
        // Method 1: Use FontAwesome API if available
        if (window.FontAwesome && window.FontAwesome.dom && window.FontAwesome.dom.i2svg) {
            try {
                window.FontAwesome.dom.i2svg();
                console.log('FontAwesome i2svg triggered');
            } catch (e) {
                console.log('FontAwesome i2svg failed:', e);
            }
        }
        
        // Method 2: Check for FontAwesome elements and trigger processing
        const fontAwesomeElements = document.querySelectorAll('[class*="fa-"], [class*="fas"], [class*="far"], [class*="fal"], [class*="fab"]');
        if (fontAwesomeElements.length > 0) {
            console.log('Found FontAwesome elements:', fontAwesomeElements.length);
        }
        
        // Method 3: Force class addition as fallback
        ensureFontAwesomeClasses();
    }
    
    // Initialize immediately
    initializeFontAwesome();
    
    // Retry with delays to ensure classes are added
    setTimeout(ensureFontAwesomeClasses, 100);
    setTimeout(ensureFontAwesomeClasses, 500);
    setTimeout(ensureFontAwesomeClasses, 1000);
    setTimeout(ensureFontAwesomeClasses, 2000);
    
    // Also check when window loads completely
    window.addEventListener('load', function() {
        setTimeout(ensureFontAwesomeClasses, 100);
        setTimeout(ensureFontAwesomeClasses, 500);
    });
    
    // Monitor for class changes
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                ensureFontAwesomeClasses();
            }
        });
    });
    
    observer.observe(document.documentElement, {
        attributes: true,
        attributeFilter: ['class']
    });
});

// Additional FontAwesome support
if (typeof window.FontAwesome === 'undefined') {
    window.FontAwesome = {
        dom: {
            i2svg: function() {
                // Fallback implementation
                const htmlElement = document.documentElement;
                if (!htmlElement.classList.contains('fontawesome-i2svg-active')) {
                    htmlElement.classList.add('fontawesome-i2svg-active');
                }
                if (!htmlElement.classList.contains('fontawesome-i2svg-complete')) {
                    htmlElement.classList.add('fontawesome-i2svg-complete');
                }
            }
        }
    };
}
