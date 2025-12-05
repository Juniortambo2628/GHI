/**
 * Intelligent Image Preloader
 * Preloads images intelligently using IntersectionObserver and priority queues
 */

/**
 * Image preloader with priority and lazy loading support
 */
class ImagePreloader {
  constructor(options = {}) {
    this.options = {
      // Priority levels: 'critical', 'high', 'normal', 'low'
      criticalThreshold: 0, // Load immediately
      highThreshold: 200, // Load when 200px away
      normalThreshold: 400, // Load when 400px away
      lowThreshold: 800, // Load when 800px away
      maxConcurrent: 2, // Max concurrent image loads (reduced to prevent browser crashes)
      ...options,
    };
    
    this.loadingQueue = {
      critical: [],
      high: [],
      normal: [],
      low: [],
    };
    
    this.activeLoads = 0;
    this.observer = null;
    this.preloadedImages = new Set();
  }

  /**
   * Determine if the preloader should manage this image
   */
  shouldManageImage(img) {
    return Boolean(this.getManagedSource(img));
  }

  /**
   * Get the source for deferred/preload images
   */
  getManagedSource(img) {
    if (!img) return null;

    const dataSrc = img.getAttribute('data-src');
    if (dataSrc) return dataSrc;

    const dataSrcset = img.getAttribute('data-srcset');
    if (dataSrcset) {
      const firstSrc = dataSrcset.split(',')[0]?.trim().split(' ')[0];
      if (firstSrc) {
        return firstSrc;
      }
    }

    if (img.hasAttribute('data-preload')) {
      const attr = img.getAttribute('data-preload');
      if (attr && attr !== 'true' && attr !== '1') {
        return attr;
      }
      return img.currentSrc || img.getAttribute('src') || dataSrc || null;
    }

    return null;
  }

  /**
   * Activate deferred loading by swapping data attributes
   */
  activateDeferredImage(img) {
    if (!img) return;

    const dataSrc = img.getAttribute('data-src');
    if (dataSrc) {
      img.setAttribute('src', dataSrc);
      img.removeAttribute('data-src');
    }

    const dataSrcset = img.getAttribute('data-srcset');
    if (dataSrcset) {
      img.setAttribute('srcset', dataSrcset);
      img.removeAttribute('data-srcset');
    }
  }

  /**
   * Initialize the preloader
   */
  init() {
    // Preload critical images immediately
    this.preloadCritical();
    
    // Setup IntersectionObserver for lazy loading
    this.setupObserver();
    
    // Start processing queue
    this.processQueue();
  }

  /**
   * Preload critical images (above the fold)
   */
  preloadCritical() {
    const criticalImages = document.querySelectorAll(
      'img[data-preload], img[data-src][loading="eager"], img[data-srcset][loading="eager"], img[fetchpriority="high"][data-src], img[fetchpriority="high"][data-srcset]'
    );
    
    criticalImages.forEach((img) => {
      if (!this.shouldManageImage(img)) {
        return;
      }
      
      const src = this.getManagedSource(img);
      if (src && !this.preloadedImages.has(src)) {
        if (img.hasAttribute('data-src') || img.hasAttribute('data-srcset')) {
          this.activateDeferredImage(img);
          this.preloadedImages.add(src);
        } else {
          this.preloadImage(src, 'critical');
        }
      }
    });
  }

  /**
   * Setup IntersectionObserver for lazy loading
   */
  setupObserver() {
    if (!('IntersectionObserver' in window)) {
      // Fallback: load all images
      this.loadAllImages();
      return;
    }

    const managedSelector = 'img[loading="lazy"][data-src], img[loading="lazy"][data-srcset], img[data-preload]';
    const lazyImages = document.querySelectorAll(managedSelector);

    if (lazyImages.length === 0) {
      return;
    }

    this.observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            const img = entry.target;
            if (!this.shouldManageImage(img)) {
              this.observer.unobserve(img);
              return;
            }

            const src = this.getManagedSource(img);
            const priority = this.getPriority(img);
            
            if (src && !this.preloadedImages.has(src)) {
              if (img.hasAttribute('data-src') || img.hasAttribute('data-srcset')) {
                this.activateDeferredImage(img);
                this.preloadedImages.add(src);
              } else {
                this.preloadImage(src, priority);
              }
            }
            
            // Unobserve once loaded
            this.observer.unobserve(img);
          }
        });
      },
      {
        rootMargin: '200px', // Start loading 200px before image enters viewport
        threshold: 0.01,
      }
    );

    // Observe managed lazy-loaded images
    lazyImages.forEach((img) => {
      if (this.shouldManageImage(img)) {
        this.observer.observe(img);
      }
    });
  }

  /**
   * Get priority level for an image
   */
  getPriority(img) {
    if (img.hasAttribute('fetchpriority') && img.getAttribute('fetchpriority') === 'high') {
      return 'critical';
    }
    if (img.closest('.carousel-item')) {
      return 'high';
    }
    if (img.closest('section:first-of-type, .hero, .about')) {
      return 'high';
    }
    if (img.closest('section:nth-of-type(2), section:nth-of-type(3)')) {
      return 'normal';
    }
    return 'low';
  }

  /**
   * Preload a single image
   */
  preloadImage(src, priority = 'normal') {
    if (this.preloadedImages.has(src)) {
      return Promise.resolve();
    }

    return new Promise((resolve, reject) => {
      // Add to queue
      this.loadingQueue[priority].push({ src, resolve, reject });
      
      // Process queue
      this.processQueue();
    });
  }

  /**
   * Process the loading queue
   */
  async processQueue() {
    // Process queues in priority order
    const priorities = ['critical', 'high', 'normal', 'low'];
    
    for (const priority of priorities) {
      while (
        this.loadingQueue[priority].length > 0 &&
        this.activeLoads < this.options.maxConcurrent
      ) {
        const { src, resolve, reject } = this.loadingQueue[priority].shift();
        this.activeLoads++;
        
        this.loadImage(src)
          .then(() => {
            this.preloadedImages.add(src);
            resolve();
          })
          .catch(reject)
          .finally(() => {
            this.activeLoads--;
            // Continue processing queue
            this.processQueue();
          });
      }
    }
  }

  /**
   * Load an image
   */
  loadImage(src) {
    return new Promise((resolve, reject) => {
      const img = new Image();
      
      img.onload = () => resolve(img);
      img.onerror = () => reject(new Error(`Failed to load image: ${src}`));
      
      img.src = src;
    });
  }

  /**
   * Fallback: load all images (for browsers without IntersectionObserver)
   */
  loadAllImages() {
    const images = document.querySelectorAll('img[loading="lazy"][data-src], img[loading="lazy"][data-srcset], img[data-preload]');
    images.forEach((img) => {
      if (!this.shouldManageImage(img)) {
        return;
      }

      if (img.hasAttribute('data-src') || img.hasAttribute('data-srcset')) {
        this.activateDeferredImage(img);
        const src = this.getManagedSource(img);
        if (src) {
          this.preloadedImages.add(src);
        }
      } else {
        const src = this.getManagedSource(img);
        if (src) {
          this.preloadImage(src, 'normal');
        }
      }
    });
  }

  /**
   * Preload images for next carousel slide
   */
  preloadNextCarouselSlide(carouselId) {
    const carousel = document.getElementById(carouselId);
    if (!carousel) return;

    const activeItem = carousel.querySelector('.carousel-item.active');
    if (!activeItem) return;

    const nextItem = activeItem.nextElementSibling || carousel.querySelector('.carousel-item');
    if (nextItem) {
      const nextImg = nextItem.querySelector('img');
      if (nextImg && this.shouldManageImage(nextImg)) {
        const src = this.getManagedSource(nextImg);
        if (src) {
          this.preloadImage(src, 'high');
        }
      }
    }
  }

  /**
   * Preload images for a specific section
   */
  preloadSection(selector) {
    const section = document.querySelector(selector);
    if (!section) return;

    const images = section.querySelectorAll('img[loading="lazy"][data-src], img[loading="lazy"][data-srcset], img[data-preload]');
    images.forEach((img) => {
      if (!this.shouldManageImage(img)) {
        return;
      }

      if (img.hasAttribute('data-src') || img.hasAttribute('data-srcset')) {
        this.activateDeferredImage(img);
        const src = this.getManagedSource(img);
        if (src) {
          this.preloadedImages.add(src);
        }
      } else {
        const src = this.getManagedSource(img);
        if (src) {
          this.preloadImage(src, 'normal');
        }
      }
    });
  }
}

// Create global instance
let imagePreloader = null;

/**
 * Initialize image preloader
 */
export function initImagePreloader(options = {}) {
  if (imagePreloader) {
    return imagePreloader;
  }

  imagePreloader = new ImagePreloader(options);
  
  // Initialize when DOM is ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
      imagePreloader.init();
    });
  } else {
    imagePreloader.init();
  }

  return imagePreloader;
}

/**
 * Preload specific images
 */
export function preloadImages(urls, priority = 'normal') {
  if (!imagePreloader) {
    initImagePreloader();
  }

  urls.forEach((url) => {
    imagePreloader.preloadImage(url, priority);
  });
}

/**
 * Preload next carousel slide
 */
export function preloadNextCarouselSlide(carouselId) {
  if (!imagePreloader) {
    initImagePreloader();
  }

  imagePreloader.preloadNextCarouselSlide(carouselId);
}

/**
 * Preload section images
 */
export function preloadSection(selector) {
  if (!imagePreloader) {
    initImagePreloader();
  }

  imagePreloader.preloadSection(selector);
}

export default {
  init: initImagePreloader,
  preloadImages,
  preloadNextCarouselSlide,
  preloadSection,
};



