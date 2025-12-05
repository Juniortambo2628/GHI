/**
 * PDF Generation Utility using jsPDF
 * Global Harmony Initiative Website
 */

import jsPDF from 'jspdf';
import autoTable from 'jspdf-autotable';

/**
 * Generate PDF from data
 * @param {Object} options - PDF options
 */
export function generatePDF(options = {}) {
  const {
    title = 'Report',
    data = [],
    filename = 'report.pdf',
    orientation = 'portrait', // 'portrait' or 'landscape'
    headerColor = [0, 6, 86], // GHI primary color
    showDate = true,
  } = options;

  try {
    const doc = new jsPDF({
      orientation,
      unit: 'mm',
      format: 'a4',
    });

    // Add title
    doc.setFontSize(18);
    doc.setTextColor(...headerColor);
    doc.text(title, 14, 20);

    // Add date if requested
    if (showDate) {
      doc.setFontSize(10);
      doc.setTextColor(100, 100, 100);
      const date = new Date().toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      });
      doc.text(`Generated: ${date}`, 14, 27);
    }

    // Add table if data provided
    if (data.length > 0) {
      const headers = Object.keys(data[0]);
      const rows = data.map(row => Object.values(row));

      autoTable(doc, {
        head: [headers],
        body: rows,
        startY: showDate ? 35 : 30,
        styles: { 
          fontSize: 10,
          cellPadding: 3,
        },
        headStyles: { 
          fillColor: headerColor,
          textColor: [255, 255, 255],
          fontStyle: 'bold',
        },
        alternateRowStyles: {
          fillColor: [245, 247, 250],
        },
        margin: { top: 30 },
      });
    }

    // Save PDF
    doc.save(filename);
  } catch (error) {
    console.error('PDF generation failed:', error);
    throw error;
  }
}

/**
 * Generate PDF from HTML element
 * @param {string} elementId - ID of element to convert
 * @param {string} filename - Output filename
 * @param {Object} options - Additional options
 */
export function generatePDFFromHTML(elementId, filename = 'export.pdf', options = {}) {
  const element = document.getElementById(elementId);
  if (!element) {
    console.error('Element not found:', elementId);
    return;
  }

  try {
    const doc = new jsPDF({
      orientation: options.orientation || 'portrait',
      unit: 'mm',
      format: options.format || 'a4',
    });

    // Convert HTML to PDF
    doc.html(element, {
      callback: (doc) => {
        doc.save(filename);
      },
      x: 10,
      y: 10,
      width: 190,
      windowWidth: options.windowWidth || 800,
    });
  } catch (error) {
    console.error('HTML to PDF conversion failed:', error);
    throw error;
  }
}

/**
 * Generate PDF from Tabulator table
 * @param {Tabulator} table - Tabulator instance
 * @param {string} filename - Output filename
 */
export function generatePDFFromTabulator(table, filename = 'table-export.pdf') {
  if (!table) {
    console.error('Tabulator table instance required');
    return;
  }

  try {
    const data = table.getData();
    const tableTitle = table.element?.getAttribute('data-table-title') || 'Table Export';
    
    generatePDF({
      title: tableTitle,
      data,
      filename,
    });
  } catch (error) {
    console.error('Tabulator PDF export failed:', error);
    throw error;
  }
}

export default {
  generatePDF,
  generatePDFFromHTML,
  generatePDFFromTabulator,
};

