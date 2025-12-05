/**
 * GSAP Animation Service
 * Global Harmony Initiative Website
 * 
 * Fresh implementation with modern GSAP best practices
 * Provides smooth, performant animations for the website
 */

import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

// Register GSAP plugins
gsap.registerPlugin(ScrollTrigger);

/**
 * Animation Configuration
 */
const ANIMATION_CONFIG = {
  defaults: {
    duration: 0.8,
    ease: 'power3.out',
    startTrigger: 'top 85%',
  },
  reducedMotion: window.matchMedia('(prefers-reduced-motion: reduce)').matches,
};

/**
 * Check if animations should be disabled
 */
function shouldDisableAnimations() {
  return ANIMATION_CONFIG.reducedMotion || document.documentElement.classList.contains('no-animations');
}

/**
 * Initialize all animations
 */
export function initializeAnimations() {
  if (shouldDisableAnimations()) {
    console.log('Animations disabled - reduced motion preference detected');
    return;
  }

  // Wait for DOM to be ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
      setupAnimations();
    });
  } else {
    setupAnimations();
  }
}

/**
 * Setup all animation types
 */
function setupAnimations() {
  try {
    initializeScrollAnimations();
    initializeStaggerAnimations();
    initializeCounterAnimations();
    initializeParallaxEffects();
    initializeHoverEffects();
    
    // Refresh ScrollTrigger after setup
    ScrollTrigger.refresh();
  } catch (error) {
    console.error('Animation setup error:', error);
  }
}

/**
 * Initialize scroll-triggered animations
 * Supports data-animate-on-scroll attribute
 */
function initializeScrollAnimations() {
  const elements = document.querySelectorAll('[data-animate-on-scroll]');
  
  if (elements.length === 0) return;

  elements.forEach((element) => {
    if (!element || !element.offsetParent) return;

    const animationType = element.getAttribute('data-animate-on-scroll') || 'fadeIn';
    const duration = parseFloat(element.getAttribute('data-duration')) || ANIMATION_CONFIG.defaults.duration;
    const delay = parseFloat(element.getAttribute('data-delay')) || 0;
    const once = element.getAttribute('data-once') !== 'false';

    // Set initial state based on animation type
    const fromVars = getAnimationFromVars(animationType);
    
    gsap.set(element, { ...fromVars, opacity: 0 });

    // Create animation
    gsap.to(element, {
      ...getAnimationToVars(animationType),
      opacity: 1,
      duration,
      delay,
      ease: ANIMATION_CONFIG.defaults.ease,
      scrollTrigger: {
        trigger: element,
        start: ANIMATION_CONFIG.defaults.startTrigger,
        toggleActions: once ? 'play none none none' : 'play none none reverse',
        once,
        invalidateOnRefresh: true,
        onEnter: () => {
          element.style.opacity = '';
        },
      },
    });
  });
}

/**
 * Get initial animation state based on type
 */
function getAnimationFromVars(type) {
  const animations = {
    fadeIn: {},
    slideInLeft: { x: -100, y: 0 },
    slideInRight: { x: 100, y: 0 },
    slideInUp: { x: 0, y: 100 },
    slideInDown: { x: 0, y: -100 },
    zoomIn: { scale: 0.8 },
    zoomOut: { scale: 1.2 },
    rotateIn: { rotation: -180, scale: 0.8 },
  };

  return animations[type] || animations.fadeIn;
}

/**
 * Get target animation state based on type
 */
function getAnimationToVars(type) {
  const animations = {
    fadeIn: {},
    slideInLeft: { x: 0, y: 0 },
    slideInRight: { x: 0, y: 0 },
    slideInUp: { x: 0, y: 0 },
    slideInDown: { x: 0, y: 0 },
    zoomIn: { scale: 1 },
    zoomOut: { scale: 1 },
    rotateIn: { rotation: 0, scale: 1 },
  };

  return animations[type] || animations.fadeIn;
}

/**
 * Initialize stagger animations for grids and lists
 */
function initializeStaggerAnimations() {
  // Card grids
  const grids = document.querySelectorAll('.row.g-4, .grid-cards, [data-stagger]');
  
  grids.forEach((grid) => {
    requestAnimationFrame(() => {
      const items = grid.querySelectorAll('.card, .col, .grid-card, .listing-item');
      const visibleItems = Array.from(items).filter(item => item.offsetParent !== null);
      
      if (visibleItems.length > 0) {
        gsap.from(visibleItems, {
          y: 50,
          opacity: 0,
          duration: 0.6,
          stagger: 0.1,
          ease: 'power2.out',
          scrollTrigger: {
            trigger: grid,
            start: ANIMATION_CONFIG.defaults.startTrigger,
            toggleActions: 'play none none reverse',
          },
        });
      }
    });
  });
}

/**
 * Initialize counter animations
 */
function initializeCounterAnimations() {
  const counters = document.querySelectorAll('[data-counter]');
  
  counters.forEach((counter) => {
    const target = parseFloat(counter.getAttribute('data-counter')) || 0;
    const from = parseFloat(counter.getAttribute('data-counter-from')) || 0;
    const duration = parseFloat(counter.getAttribute('data-counter-duration')) || 2;
    
    ScrollTrigger.create({
      trigger: counter,
      start: ANIMATION_CONFIG.defaults.startTrigger,
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
 * Initialize parallax effects
 */
function initializeParallaxEffects() {
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
 * Initialize hover effects for buttons and cards
 */
function initializeHoverEffects() {
  // Button hover effects
  const buttons = document.querySelectorAll('.btn:not(.no-hover)');
  
  buttons.forEach((button) => {
    button.addEventListener('mouseenter', () => {
      gsap.to(button, {
        scale: 1.05,
        duration: 0.2,
        ease: 'power2.out',
      });
    });
    
    button.addEventListener('mouseleave', () => {
      gsap.to(button, {
        scale: 1,
        duration: 0.2,
        ease: 'power2.out',
      });
    });
  });

  // Card hover effects
  const cards = document.querySelectorAll('.card:not(.no-hover)');
  
  cards.forEach((card) => {
    card.addEventListener('mouseenter', () => {
      gsap.to(card, {
        y: -5,
        duration: 0.3,
        ease: 'power2.out',
      });
    });
    
    card.addEventListener('mouseleave', () => {
      gsap.to(card, {
        y: 0,
        duration: 0.3,
        ease: 'power2.out',
      });
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
 * Fade in element
 */
export function fadeIn(element, duration = 0.8, delay = 0) {
  gsap.set(element, { opacity: 0 });
  
  return gsap.to(element, {
    opacity: 1,
    duration,
    delay,
    ease: ANIMATION_CONFIG.defaults.ease,
  });
}

/**
 * Fade out element
 */
export function fadeOut(element, duration = 0.8, delay = 0) {
  return gsap.to(element, {
    opacity: 0,
    duration,
    delay,
    ease: ANIMATION_CONFIG.defaults.ease,
  });
}

/**
 * Slide in element
 */
export function slideIn(element, direction = 'left', duration = 0.8, delay = 0) {
  const directions = {
    left: { x: -100 },
    right: { x: 100 },
    top: { y: -100 },
    bottom: { y: 100 },
  };

  const from = directions[direction] || directions.left;
  gsap.set(element, { ...from, opacity: 0 });

  return gsap.to(element, {
    x: 0,
    y: 0,
    opacity: 1,
    duration,
    delay,
    ease: ANIMATION_CONFIG.defaults.ease,
  });
}

/**
 * Refresh ScrollTrigger (call after DOM changes)
 */
export function refreshAnimations() {
  ScrollTrigger.refresh();
}

/**
 * Kill all animations
 */
export function killAllAnimations() {
  gsap.killTweensOf('*');
  ScrollTrigger.getAll().forEach(trigger => trigger.kill());
}

// Initialize smooth scroll for anchor links
function initializeSmoothScroll() {
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
}

// Auto-initialize
initializeAnimations();
initializeSmoothScroll();

// Refresh on window load
window.addEventListener('load', () => {
  setTimeout(() => {
    ScrollTrigger.refresh();
  }, 100);
});

// Export default object
export default {
  init: initializeAnimations,
  refresh: refreshAnimations,
  killAll: killAllAnimations,
  smoothScroll: smoothScrollTo,
  fadeIn,
  fadeOut,
  slideIn,
};
