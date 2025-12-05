/**
 * Optimized GSAP Animations
 * Global Harmony Initiative Website
 * 
 * Performance-optimized animations with hardware acceleration
 * Handles scroll-triggered animations, counters, parallax, and more
 */

import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

// Register GSAP plugins
gsap.registerPlugin(ScrollTrigger);

/**
 * Animation configuration
 */
const config = {
  reducedMotion: window.matchMedia('(prefers-reduced-motion: reduce)').matches,
  defaultDuration: 0.8,
  defaultEase: 'power3.out',
  startTrigger: 'top 85%',
};

/**
 * Check if animations should be disabled
 */
function shouldSkipAnimations() {
  return config.reducedMotion || document.documentElement.classList.contains('no-animations');
}

/**
 * Determine if an element or its parents opt out of animations
 */
function hasAnimationDisabled(element) {
  if (!element) return false;
  return Boolean(element.closest('.no-animation, [data-disable-animation]'));
}

/**
 * Initialize all animations (optimized for performance)
 */
export function initializeAnimations(waitForContent = true) {
  if (shouldSkipAnimations()) {
    console.log('Animations disabled - reduced motion preference');
    return;
  }

  // Start animations immediately, don't wait for all content
  setupAllAnimations();
  
  // Refresh ScrollTrigger after a short delay to ensure layout is stable
  if (waitForContent) {
    waitForCriticalContent().then(() => {
      // Only refresh ScrollTrigger, animations already started
      requestAnimationFrame(() => {
        ScrollTrigger.refresh();
      });
    });
  } else {
    requestAnimationFrame(() => {
      ScrollTrigger.refresh();
    });
  }
}

/**
 * Wait for critical content to load (optimized - faster)
 */
function waitForCriticalContent() {
  return new Promise((resolve) => {
    // If document is already complete, resolve immediately
    if (document.readyState === 'complete') {
      resolve();
      return;
    }

    // Only wait for hero images (above fold)
    const criticalImages = document.querySelectorAll('img[loading="eager"], img[fetchpriority="high"]');
    
    if (criticalImages.length === 0) {
      // No critical images, resolve immediately
      resolve();
      return;
    }

    const imagePromises = Array.from(criticalImages).map((img) => {
      if (img.complete) return Promise.resolve();
      
      return new Promise((resolveImg) => {
        img.addEventListener('load', resolveImg, { once: true });
        img.addEventListener('error', resolveImg, { once: true });
        // Shorter timeout for faster initialization
        setTimeout(resolveImg, 500);
      });
    });

    // Much shorter timeout - don't wait too long
    Promise.race([
      Promise.all(imagePromises),
      new Promise((resolveTimeout) => setTimeout(resolveTimeout, 800)),
    ]).then(resolve);
  });
}

/**
 * Setup all animation types
 */
function setupAllAnimations() {
  try {
    // First, ensure all opted-out sections are visible and cleared of GSAP styles
    clearOptedOutAnimations();
    
    setupScrollReveal();
    setupStaggerAnimations();
    setupCounterAnimations();
    setupParallaxEffects();
    setupHoverEffects();
    setupCarouselAnimation();
  } catch (error) {
    console.warn('Animation setup error:', error);
  }
}

/**
 * Clear any GSAP styles from elements that have opted out of animations
 */
function clearOptedOutAnimations() {
  const optedOutElements = document.querySelectorAll('.no-animation, [data-disable-animation]');
  optedOutElements.forEach((element) => {
    // Clear any GSAP inline styles
    gsap.set(element, { clearProps: 'all' });
    
    // Ensure all children are also cleared
    const children = element.querySelectorAll('*');
    children.forEach((child) => {
      gsap.set(child, { clearProps: 'all' });
    });
    
    // Ensure opacity is 1
    element.style.opacity = '1';
  });
}

/**
 * Setup scroll reveal animations
 * Handles data-animate-on-scroll and data-aos attributes
 */
function setupScrollReveal() {
  // Handle data-animate-on-scroll
  const animateElements = document.querySelectorAll('[data-animate-on-scroll]');
  
  animateElements.forEach((element) => {
    if (hasAnimationDisabled(element)) {
      element.style.opacity = '';
      return;
    }

    if (!element?.offsetParent) return;

    const animationType = element.getAttribute('data-animate-on-scroll') || 'fadeIn';
    const duration = parseFloat(element.getAttribute('data-duration')) || config.defaultDuration;
    const delay = parseFloat(element.getAttribute('data-delay')) || 0;
    const once = element.getAttribute('data-once') !== 'false';

    const fromVars = getFromVars(animationType);
    gsap.set(element, { ...fromVars, opacity: 0 });

    gsap.to(element, {
      ...getToVars(animationType),
      opacity: 1,
      duration,
      delay,
      ease: config.defaultEase,
      scrollTrigger: {
        trigger: element,
        start: config.startTrigger,
        toggleActions: once ? 'play none none none' : 'play none none reverse',
        once,
        invalidateOnRefresh: true,
        onEnter: () => {
          element.style.opacity = '';
        },
      },
    });
  });

  // Handle legacy data-aos attributes
  const aosElements = document.querySelectorAll('[data-aos]');
  
  aosElements.forEach((element) => {
    if (hasAnimationDisabled(element)) {
      element.style.opacity = '';
      return;
    }

    if (!element?.offsetParent) return;

    const animation = element.getAttribute('data-aos');
    const delay = parseFloat(element.getAttribute('data-aos-delay') || 0) / 1000;
    
    const fromVars = getAosFromVars(animation);
    
    gsap.from(element, {
      ...fromVars,
      opacity: 0,
      duration: config.defaultDuration,
      delay,
      ease: config.defaultEase,
      scrollTrigger: {
        trigger: element,
        start: config.startTrigger,
        toggleActions: 'play none none reverse',
      },
    });
  });
}

/**
 * Get initial animation state for data-animate-on-scroll
 */
function getFromVars(type) {
  const map = {
    fadeIn: {},
    slideInLeft: { x: -100, y: 0 },
    slideInRight: { x: 100, y: 0 },
    slideInUp: { x: 0, y: 100 },
    slideInDown: { x: 0, y: -100 },
    zoomIn: { scale: 0.8 },
  };
  return map[type] || map.fadeIn;
}

/**
 * Get target animation state for data-animate-on-scroll
 */
function getToVars(type) {
  const map = {
    fadeIn: {},
    slideInLeft: { x: 0, y: 0 },
    slideInRight: { x: 0, y: 0 },
    slideInUp: { x: 0, y: 0 },
    slideInDown: { x: 0, y: 0 },
    zoomIn: { scale: 1 },
  };
  return map[type] || map.fadeIn;
}

/**
 * Get initial animation state for data-aos
 */
function getAosFromVars(type) {
  const map = {
    'fade-up': { y: 50 },
    'fade-down': { y: -50 },
    'fade-left': { x: -50 },
    'fade-right': { x: 50 },
    'fadeIn': {},
  };
  return map[type] || map['fade-up'];
}

/**
 * Setup stagger animations for grids (optimized - only visible grids)
 */
function setupStaggerAnimations() {
  // Use IntersectionObserver for better performance
  const allGrids = document.querySelectorAll('.row.g-4, .grid-cards, [data-stagger]');
  const grids = Array.from(allGrids).filter((grid) => {
    if (!grid) return false;
    if (grid.classList.contains('no-animation')) return false;
    if (grid.hasAttribute('data-disable-animation')) return false;
    if (hasAnimationDisabled(grid)) return false;
    return true;
  });
  
  if (grids.length === 0) return;

  const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        const grid = entry.target;
        observer.unobserve(grid); // Only animate once
        
        requestAnimationFrame(() => {
          const items = grid.querySelectorAll('.card, .col, .grid-card, .listing-item, .service-item, .blog-item, .causes-item, .volunteer-img');
          const visibleItems = Array.from(items).filter((item) => {
            if (hasAnimationDisabled(item)) return false;
            return item.offsetParent !== null;
          });
          
          if (visibleItems.length > 0 && visibleItems.length <= 20) { // Limit to 20 items max for performance
            gsap.from(visibleItems, {
              y: 30,
              opacity: 0,
              duration: 0.5,
              stagger: 0.08,
              ease: 'power2.out',
              onComplete: () => {
                gsap.set(visibleItems, { clearProps: 'transform,opacity' });
              },
            });
          }
        });
      }
    });
  }, {
    rootMargin: '100px', // Start observing earlier
    threshold: 0.01,
  });

  grids.forEach((grid) => {
    observer.observe(grid);
  });
}

/**
 * Setup counter animations
 */
function setupCounterAnimations() {
  const counters = document.querySelectorAll('[data-counter]');
  
  counters.forEach((counter) => {
    if (hasAnimationDisabled(counter)) {
      counter.style.opacity = '';
      return;
    }

    const target = parseFloat(counter.getAttribute('data-counter')) || 0;
    const from = parseFloat(counter.getAttribute('data-counter-from')) || 0;
    const duration = parseFloat(counter.getAttribute('data-counter-duration')) || 2;
    
    ScrollTrigger.create({
      trigger: counter,
      start: config.startTrigger,
      onEnter: () => {
        gsap.fromTo(counter,
          { innerHTML: from },
          {
            innerHTML: target,
            duration,
            ease: 'power1.out',
            snap: { innerHTML: 1 },
            onUpdate: function() {
              const value = Math.ceil(this.targets()[0].innerHTML);
              counter.innerHTML = value;
            },
          }
        );
      },
      once: true,
    });
  });
}

/**
 * Setup parallax effects
 */
function setupParallaxEffects() {
  const parallaxElements = document.querySelectorAll('[data-parallax]');
  
  parallaxElements.forEach((element) => {
    const speed = parseFloat(element.getAttribute('data-parallax')) || 0.5;
    
    gsap.to(element, {
      y: () => -(element.offsetHeight * speed),
      ease: 'none',
      scrollTrigger: {
        trigger: element,
        start: 'top bottom',
        end: 'bottom top',
        scrub: true,
      },
    });
  });
}

/**
 * Setup carousel hero image animation
 */
function setupCarouselAnimation() {
  const carouselImages = document.querySelectorAll('.carousel-header .carousel-item.active img');
  
  carouselImages.forEach((img) => {
    // Create subtle 3D animation effect
    gsap.to(img, {
      scale: 1.15,
      rotationY: 0,
      duration: 20,
      ease: 'sine.inOut',
      repeat: -1,
      yoyo: true,
      onUpdate: function() {
        const progress = this.progress();
        const rotation = Math.sin(progress * Math.PI * 2) * 1;
        const translateX = Math.sin(progress * Math.PI * 2) * -1;
        const translateY = Math.cos(progress * Math.PI * 2) * -0.5;
        
        gsap.set(img, {
          rotationY: rotation,
          x: translateX + '%',
          y: translateY + '%',
        });
      },
    });
  });
  
  // Re-animate when carousel slides change
  const carousel = document.querySelector('#carouselId');
  if (carousel) {
    carousel.addEventListener('slid.bs.carousel', function() {
      const activeImg = carousel.querySelector('.carousel-item.active img');
      if (activeImg) {
        setupCarouselAnimation();
      }
    });
  }
}

/**
 * Setup hover effects
 */
function setupHoverEffects() {
  // Button hover
  document.querySelectorAll('.btn:not(.no-hover)').forEach((button) => {
    button.addEventListener('mouseenter', () => {
      gsap.to(button, { scale: 1.05, duration: 0.2, ease: 'power2.out' });
    });
    button.addEventListener('mouseleave', () => {
      gsap.to(button, { scale: 1, duration: 0.2, ease: 'power2.out' });
    });
  });

  // Card hover
  document.querySelectorAll('.card:not(.no-hover)').forEach((card) => {
    card.addEventListener('mouseenter', () => {
      gsap.to(card, { y: -5, duration: 0.3, ease: 'power2.out' });
    });
    card.addEventListener('mouseleave', () => {
      gsap.to(card, { y: 0, duration: 0.3, ease: 'power2.out' });
    });
  });
}

/**
 * Smooth scroll to element
 */
export function smoothScrollTo(target, offset = 0) {
  const element = typeof target === 'string' ? document.querySelector(target) : target;
  if (!element) return;

  gsap.to(window, {
    duration: 1,
    scrollTo: {
      y: element,
      offsetY: offset,
    },
    ease: 'power2.inOut',
  });
}

/**
 * Refresh ScrollTrigger
 */
export function refresh() {
  ScrollTrigger.refresh();
}

/**
 * Kill all animations
 */
export function killAll() {
  gsap.killTweensOf('*');
  ScrollTrigger.getAll().forEach(trigger => trigger.kill());
}

// Initialize on DOM ready
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', () => {
    initializeAnimations(true);
  });
} else {
  initializeAnimations(true);
}

// Refresh on window load
window.addEventListener('load', () => {
  setTimeout(() => {
    ScrollTrigger.refresh();
  }, 100);
});

// Initialize smooth scroll
document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
  anchor.addEventListener('click', function(e) {
    const href = this.getAttribute('href');
    if (href === '#' || href === '#!') return;
    
    const target = document.querySelector(href);
    if (target) {
      e.preventDefault();
      smoothScrollTo(target, 100);
    }
  });
});

// Export default
export default {
  init: initializeAnimations,
  refresh,
  killAll,
  smoothScroll: smoothScrollTo,
};
