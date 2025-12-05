/**
 * Lazy Load Images Module - SIMPLIFIED VERSION
 * Direct implementation without vanilla-lazyload dependency
 */

let lazyLoadObserver = null;
const loadedImages = new Set();

/**
 * Load an image
 */
const loadImage = (img) => {
  if (loadedImages.has(img)) return;
  
  const src = img.dataset.src || img.getAttribute('data-src');
  if (!src) return;
  
  // Set the src to trigger loading
  img.src = src;
  img.classList.add('loaded');
  loadedImages.add(img);
  
  // Remove data-src attribute
  img.removeAttribute('data-src');
  
  console.log('Lazy loaded image:', src);
};

/**
 * Initialize lazy loading using Intersection Observer
 */
export const initLazyLoad = () => {
  if (lazyLoadObserver) {
    return lazyLoadObserver;
  }

  // Get all lazy images
  const lazyImages = document.querySelectorAll('img.lazy[data-src]');
  
  console.log(`Found ${lazyImages.length} lazy images to load`);

  // Use Intersection Observer for efficient lazy loading
  if ('IntersectionObserver' in window) {
    lazyLoadObserver = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          const img = entry.target;
          loadImage(img);
          lazyLoadObserver.unobserve(img);
        }
      });
    }, {
      rootMargin: '300px' // Load 300px before element enters viewport
    });

    // Observe all lazy images
    lazyImages.forEach(img => lazyLoadObserver.observe(img));
  } else {
    // Fallback: Load all images immediately if Intersection Observer not supported
    console.warn('IntersectionObserver not supported, loading all images immediately');
    lazyImages.forEach(img => loadImage(img));
  }

  return lazyLoadObserver;
};

/**
 * Update lazy load instance (call after DOM changes)
 */
export const updateLazyLoad = () => {
  // Re-initialize to catch any new images
  if (lazyLoadObserver) {
    const newImages = document.querySelectorAll('img.lazy[data-src]');
    newImages.forEach(img => {
      if (!loadedImages.has(img)) {
        lazyLoadObserver.observe(img);
      }
    });
  }
};

/**
 * Destroy lazy load instance
 */
export const destroyLazyLoad = () => {
  if (lazyLoadObserver) {
    lazyLoadObserver.disconnect();
    lazyLoadObserver = null;
  }
};

// Auto-initialize on DOM ready
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', () => {
    initLazyLoad();
  });
} else {
  initLazyLoad();
}

export default {
  init: initLazyLoad,
  update: updateLazyLoad,
  destroy: destroyLazyLoad,
};
