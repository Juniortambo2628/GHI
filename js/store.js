/**
 * State Management using Zustand
 * Global Harmony Initiative Website
 */

import { create } from 'zustand';
import { persist, createJSONStorage } from 'zustand/middleware';

/**
 * Safe localStorage wrapper that handles tracking prevention
 */
function getSafeStorage() {
  try {
    // Test if localStorage is available
    const test = '__storage_test__';
    localStorage.setItem(test, test);
    localStorage.removeItem(test);
    return localStorage;
  } catch (e) {
    // Tracking prevention or private browsing mode
    console.warn('localStorage not available (tracking prevention or private mode). Using in-memory storage.');
    // Return a mock storage object that works in memory only
    const memoryStorage = {};
    return {
      getItem: (key) => memoryStorage[key] || null,
      setItem: (key, value) => { memoryStorage[key] = value; },
      removeItem: (key) => { delete memoryStorage[key]; },
      clear: () => { Object.keys(memoryStorage).forEach(key => delete memoryStorage[key]); },
    };
  }
}

/**
 * Main application store
 */
export const useStore = create((set, get) => ({
  // User state
  user: null,
  isAuthenticated: false,

  // UI state
  isLoading: false,
  notifications: [],

  // Data state
  data: {},

  // Actions
  setUser: (user) => set({ user, isAuthenticated: !!user }),
  clearUser: () => set({ user: null, isAuthenticated: false }),

  setLoading: (isLoading) => set({ isLoading }),

  addNotification: (notification) =>
    set((state) => ({
      notifications: [...state.notifications, notification],
    })),

  removeNotification: (id) =>
    set((state) => ({
      notifications: state.notifications.filter((n) => n.id !== id),
    })),

  clearNotifications: () => set({ notifications: [] }),

  setData: (key, value) =>
    set((state) => ({
      data: { ...state.data, [key]: value },
    })),

  getData: (key) => get().data[key],

  clearData: (key) =>
    set((state) => {
      const newData = { ...state.data };
      delete newData[key];
      return { data: newData };
    }),

  reset: () =>
    set({
      user: null,
      isAuthenticated: false,
      isLoading: false,
      notifications: [],
      data: {},
    }),
}));

/**
 * Persistent store (saved to localStorage)
 */
export const usePersistentStore = create(
  persist(
    (set, get) => ({
      // User preferences
      theme: 'light',
      language: 'en',
      preferences: {},

      // Actions
      setTheme: (theme) => set({ theme }),
      setLanguage: (language) => set({ language }),
      setPreference: (key, value) =>
        set((state) => ({
          preferences: { ...state.preferences, [key]: value },
        })),
      getPreference: (key) => get().preferences[key],
      clearPreferences: () => set({ preferences: {} }),
    }),
    {
      name: 'ghi-storage',
      storage: createJSONStorage(() => getSafeStorage()),
    }
  )
);

/**
 * Create custom store
 */
export function createStore(initialState, actions) {
  return create((set, get) => ({
    ...initialState,
    ...actions(set, get),
  }));
}

/**
 * Create persistent custom store
 */
export function createPersistentStore(name, initialState, actions) {
  return create(
    persist(
      (set, get) => ({
        ...initialState,
        ...actions(set, get),
      }),
      {
        name: name,
        storage: createJSONStorage(() => getSafeStorage()),
      }
    )
  );
}

export default {
  useStore,
  usePersistentStore,
  createStore,
  createPersistentStore,
};

