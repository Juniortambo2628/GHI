/**
 * Modal Handlers for Main Website
 * Handles data-attribute based modal opening and social interactions
 */

import { openModal } from './modals.js';

// Base URL for image paths
const BASE_URL = window.BASE_URL || '';

/**
 * Initialize modal handlers from data attributes
 */
export function initializeModalHandlers() {
  // Event modal handlers
  document.querySelectorAll('[data-open-event-modal]').forEach((button) => {
    button.addEventListener('click', function (e) {
      e.preventDefault();
      const eventData = JSON.parse(this.getAttribute('data-open-event-modal'));
      openEventModal(eventData);
    });
  });

  // Story modal handlers
  document.querySelectorAll('[data-open-story-modal]').forEach((button) => {
    button.addEventListener('click', function (e) {
      e.preventDefault();
      const storyData = JSON.parse(this.getAttribute('data-open-story-modal'));
      openStoryModal(storyData);
    });
  });

  // Initiative modal handlers
  document.querySelectorAll('[data-open-initiative-modal]').forEach((button) => {
    button.addEventListener('click', function (e) {
      e.preventDefault();
      const initiativeData = JSON.parse(this.getAttribute('data-open-initiative-modal'));
      openInitiativeModal(initiativeData);
    });
  });

  // Impact modal handlers
  document.querySelectorAll('[data-open-impact-modal]').forEach((button) => {
    button.addEventListener('click', function (e) {
      e.preventDefault();
      const impactData = JSON.parse(this.getAttribute('data-open-impact-modal'));
      openImpactModal(impactData);
    });
  });

  // Cause modal handlers
  document.querySelectorAll('[data-open-cause-modal]').forEach((button) => {
    button.addEventListener('click', function (e) {
      e.preventDefault();
      const causeData = JSON.parse(this.getAttribute('data-open-cause-modal'));
      openCauseModal(causeData);
    });
  });

  // Story social interactions
  document.querySelectorAll('[data-like-story]').forEach((button) => {
    button.addEventListener('click', function (e) {
      e.preventDefault();
      const storyId = parseInt(this.getAttribute('data-like-story'));
      likeStory(storyId);
    });
  });

  document.querySelectorAll('[data-comment-story]').forEach((button) => {
    button.addEventListener('click', function (e) {
      e.preventDefault();
      const storyId = parseInt(this.getAttribute('data-comment-story'));
      commentStory(storyId);
    });
  });

  document.querySelectorAll('[data-share-story]').forEach((button) => {
    button.addEventListener('click', function (e) {
      e.preventDefault();
      const storyId = parseInt(this.getAttribute('data-share-story'));
      shareStory(storyId);
    });
  });
}

/**
 * Open event modal
 */
function openEventModal(event) {
  const imageUrl = event.image ? `${BASE_URL}/Banners-and-portraits/${event.image}` : null;

  const modalData = {
    title: event.title,
    description: event.description,
    image: imageUrl,
    thumbnail: imageUrl,
    meta: {
      Initiative: event.initiative,
      Date: new Date(event.date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
      }),
      Location: event.location,
      Status: event.status.charAt(0).toUpperCase() + event.status.slice(1),
    },
    tags: [event.initiative, event.status],
    action_url: `${BASE_URL}/get-involved.php?event=${event.id}`,
    action_text: 'Get Involved',
  };
  openModal('eventModal', modalData);
}

/**
 * Open story modal
 */
function openStoryModal(story) {
  const imageUrl = story.image
    ? `${BASE_URL}/Banners-and-portraits/${story.image}`
    : null;

  const modalData = {
    title: story.title,
    description: story.content || story.description,
    image: imageUrl,
    thumbnail: imageUrl,
    meta: {
      Region: story.region ? story.region.charAt(0).toUpperCase() + story.region.slice(1) : '',
      Category: story.category
        ? story.category.charAt(0).toUpperCase() + story.category.slice(1)
        : '',
      Date: new Date(story.date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
      }),
      Author: story.author || '',
    },
    tags: [story.region, story.category].filter(Boolean),
    action_url: '#',
    action_text: 'Share Story',
  };
  openModal('storyModal', modalData);
}

/**
 * Open initiative modal
 */
function openInitiativeModal(initiative) {
  const imageUrl = initiative.image
    ? `${BASE_URL}/Banners-and-portraits/${initiative.image}`
    : null;

  const modalData = {
    title: initiative.title,
    description: initiative.description,
    image: imageUrl,
    thumbnail: imageUrl,
    meta: {
      'Core Objective': initiative.objective
        ? initiative.objective.charAt(0).toUpperCase() + initiative.objective.slice(1)
        : '',
      Status: initiative.status
        ? initiative.status.charAt(0).toUpperCase() + initiative.status.slice(1)
        : '',
      'Events Planned': initiative.events_planned || 0,
      'Events Completed': initiative.events_completed || 0,
    },
    tags: [initiative.objective, initiative.status].filter(Boolean),
    action_url: `${BASE_URL}/events.php?initiative=${initiative.id}`,
    action_text: 'View Events',
  };
  openModal('initiativeModal', modalData);
}

/**
 * Open impact modal
 */
function openImpactModal(impact) {
  const imageUrl = impact.image
    ? `${BASE_URL}/Banners-and-portraits/${impact.image}`
    : null;

  const modalData = {
    title: impact.title,
    description: impact.description,
    image: imageUrl,
    thumbnail: imageUrl,
    meta: {
      Location: impact.location || '',
      Date: impact.date
        ? new Date(impact.date).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
          })
        : '',
      'Lives Impacted': impact.lives_impacted || 0,
    },
    tags: [impact.category].filter(Boolean),
    action_url: `${BASE_URL}/impact.php?activity=${impact.id}`,
    action_text: 'Learn More',
  };
  openModal('impactModal', modalData);
}

/**
 * Open cause modal
 */
function openCauseModal(cause) {
  const imageUrl = cause.image ? `${BASE_URL}/Banners-and-portraits/${cause.image}` : null;

  const modalData = {
    title: cause.title,
    description: cause.description,
    image: imageUrl,
    thumbnail: imageUrl,
    meta: {
      Category: cause.category
        ? cause.category.charAt(0).toUpperCase() + cause.category.slice(1)
        : '',
      Status: cause.status
        ? cause.status.charAt(0).toUpperCase() + cause.status.slice(1)
        : '',
    },
    tags: [cause.category].filter(Boolean),
    action_url: `${BASE_URL}/causes.php?cause=${cause.id}`,
    action_text: 'Learn More',
  };
  openModal('causeModal', modalData);
}

/**
 * Like story
 */
function likeStory(storyId) {
  // TODO: Implement AJAX call to like story
  const likesElement = document.getElementById('likes-' + storyId);
  if (likesElement) {
    const currentLikes = parseInt(likesElement.textContent) || 0;
    likesElement.textContent = currentLikes + 1;
  }
}

/**
 * Comment story
 */
function commentStory(storyId) {
  // TODO: Implement comment functionality
  alert('Comment functionality coming soon!');
}

/**
 * Share story
 */
function shareStory(storyId) {
  // TODO: Implement share functionality
  if (navigator.share) {
    navigator.share({
      title: 'GHI Story',
      text: 'Check out this inspiring story from Global Harmony Initiative',
      url: window.location.href,
    });
  } else {
    // Fallback: copy to clipboard
    navigator.clipboard.writeText(window.location.href);
    alert('Link copied to clipboard!');
  }
}

// Make functions available globally for backward compatibility
if (typeof window !== 'undefined') {
  window.openEventModal = openEventModal;
  window.openStoryModal = openStoryModal;
  window.openInitiativeModal = openInitiativeModal;
  window.openImpactModal = openImpactModal;
  window.openCauseModal = openCauseModal;
  window.likeStory = likeStory;
  window.commentStory = commentStory;
  window.shareStory = shareStory;
}

export default {
  initialize: initializeModalHandlers,
  openEventModal,
  openStoryModal,
  openInitiativeModal,
  openImpactModal,
  openCauseModal,
  likeStory,
  commentStory,
  shareStory,
};

