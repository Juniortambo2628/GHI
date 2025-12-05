/**
 * Charts Service using Chart.js
 * Global Harmony Initiative Website
 */

import { Chart, registerables } from 'chart.js';

Chart.register(...registerables);

const DEFAULT_FALLBACK_COLORS = ['#000656', '#f1b829', '#7695bb', '#01317a', '#41699f', '#41699f', '#aec6dc'];
let cachedThemeColors = null;

function getThemeColors() {
  if (cachedThemeColors) {
    return cachedThemeColors;
  }

  if (typeof window === 'undefined' || typeof document === 'undefined') {
    return DEFAULT_FALLBACK_COLORS;
  }

  const style = getComputedStyle(document.documentElement);
  const cssVars = ['--ghi-primary', '--ghi-secondary', '--ghi-accent-4', '--ghi-accent-5', '--ghi-accent-6', '--ghi-accent-7'];
  const colors = cssVars
    .map((varName) => style.getPropertyValue(varName)?.trim())
    .filter((value) => Boolean(value));

  cachedThemeColors = colors.length ? colors : DEFAULT_FALLBACK_COLORS;
  return cachedThemeColors;
}

function hexToRgba(hex, alpha = 0.2) {
  if (!hex) return `rgba(0,0,0,${alpha})`;
  let normalized = hex.trim();
  if (!normalized.startsWith('#')) {
    return normalized;
  }
  normalized = normalized.replace('#', '');
  if (normalized.length === 3) {
    normalized = normalized.split('').map((char) => char + char).join('');
  }
  const int = parseInt(normalized, 16);
  const r = (int >> 16) & 255;
  const g = (int >> 8) & 255;
  const b = int & 255;
  return `rgba(${r}, ${g}, ${b}, ${alpha})`;
}

function withAlpha(color, alpha = 0.2) {
  if (!color) return `rgba(0,0,0,${alpha})`;
  const trimmed = color.trim();
  if (trimmed.startsWith('#')) {
    return hexToRgba(trimmed, alpha);
  }
  if (trimmed.startsWith('rgba')) {
    return trimmed.replace(/rgba?\(([^)]+)\)/, (_match, contents) => {
      const [r = 0, g = 0, b = 0] = contents.split(',').map((value) => parseFloat(value.trim()));
      return `rgba(${r}, ${g}, ${b}, ${alpha})`;
    });
  }
  if (trimmed.startsWith('rgb')) {
    const parts = trimmed
      .replace('rgb(', '')
      .replace(')', '')
      .split(',')
      .map((value) => parseFloat(value.trim()));
    const [r = 0, g = 0, b = 0] = parts;
    return `rgba(${r}, ${g}, ${b}, ${alpha})`;
  }
  return trimmed;
}

function applyThemePalette(config, chartType) {
  if (!config?.data?.datasets) {
    return;
  }

  const palette = getThemeColors();
  const labels = config.data.labels || [];

  config.data = {
    ...config.data,
    datasets: config.data.datasets.map((dataset, index) => {
      const color = palette[index % palette.length];
      const base = { ...dataset };

      if (chartType === 'pie' || chartType === 'doughnut') {
        if (!base.backgroundColor) {
          const segmentColors = labels.length
            ? labels.map((_, labelIndex) => palette[labelIndex % palette.length])
            : palette;
          base.backgroundColor = segmentColors;
        }
        base.borderColor = base.borderColor || '#ffffff';
        base.borderWidth = base.borderWidth ?? 1;
      } else if (chartType === 'bar') {
        base.backgroundColor = base.backgroundColor || withAlpha(color, 0.55);
        base.borderColor = base.borderColor || color;
        base.borderWidth = base.borderWidth ?? 1;
      } else if (chartType === 'line' || chartType === 'area') {
        const fillAlpha = chartType === 'area' ? 0.3 : 0.15;
        base.borderColor = base.borderColor || color;
        base.pointBackgroundColor = base.pointBackgroundColor || color;
        base.backgroundColor = base.backgroundColor || withAlpha(color, fillAlpha);
        if (chartType === 'area' && base.fill === undefined) {
          base.fill = true;
        }
      }

      return base;
    }),
  };
}

/**
 * Create line chart
 */
export function createLineChart(canvas, data, options = {}) {
  const defaultOptions = {
    type: 'line',
    data: data,
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: 'top',
        },
        title: {
          display: options.title ? true : false,
          text: options.title || '',
        },
      },
      scales: {
        y: {
          beginAtZero: true,
        },
      },
      ...options.chartOptions,
    },
  };

  applyThemePalette(defaultOptions, 'line');
  return new Chart(canvas, defaultOptions);
}

/**
 * Create bar chart
 */
export function createBarChart(canvas, data, options = {}) {
  const defaultOptions = {
    type: 'bar',
    data: data,
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: 'top',
        },
        title: {
          display: options.title ? true : false,
          text: options.title || '',
        },
      },
      scales: {
        y: {
          beginAtZero: true,
        },
      },
      ...options.chartOptions,
    },
  };

  applyThemePalette(defaultOptions, 'bar');
  return new Chart(canvas, defaultOptions);
}

/**
 * Create pie chart
 */
export function createPieChart(canvas, data, options = {}) {
  const defaultOptions = {
    type: 'pie',
    data: data,
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: 'right',
        },
        title: {
          display: options.title ? true : false,
          text: options.title || '',
        },
      },
      ...options.chartOptions,
    },
  };

  applyThemePalette(defaultOptions, 'pie');
  return new Chart(canvas, defaultOptions);
}

/**
 * Create doughnut chart
 */
export function createDoughnutChart(canvas, data, options = {}) {
  const defaultOptions = {
    type: 'doughnut',
    data: data,
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: 'right',
        },
        title: {
          display: options.title ? true : false,
          text: options.title || '',
        },
      },
      ...options.chartOptions,
    },
  };

  applyThemePalette(defaultOptions, 'doughnut');
  return new Chart(canvas, defaultOptions);
}

/**
 * Create area chart (line chart with fill)
 */
export function createAreaChart(canvas, data, options = {}) {
  const defaultOptions = {
    type: 'line',
    data: {
      ...data,
      datasets: data.datasets.map(dataset => ({
        ...dataset,
        fill: true,
      })),
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: 'top',
        },
        title: {
          display: options.title ? true : false,
          text: options.title || '',
        },
      },
      scales: {
        y: {
          beginAtZero: true,
        },
      },
      ...options.chartOptions,
    },
  };

  applyThemePalette(defaultOptions, 'area');
  return new Chart(canvas, defaultOptions);
}

/**
 * Destroy chart
 */
export function destroyChart(chart) {
  if (chart) {
    chart.destroy();
  }
}

/**
 * Update chart data
 */
export function updateChart(chart, data) {
  if (!chart) return;
  chart.data = data;
  chart.update();
}

export default {
  line: createLineChart,
  bar: createBarChart,
  pie: createPieChart,
  doughnut: createDoughnutChart,
  area: createAreaChart,
  destroy: destroyChart,
  update: updateChart,
};

