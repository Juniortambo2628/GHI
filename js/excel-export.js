/**
 * Excel Export Utility using xlsx
 * Global Harmony Initiative Website
 */

import * as XLSX from 'xlsx';

/**
 * Export data to Excel file
 * @param {Array} data - Array of objects to export
 * @param {string} filename - Output filename
 * @param {string} sheetName - Sheet name (default: 'Sheet1')
 */
export function exportToExcel(data, filename = 'export.xlsx', sheetName = 'Sheet1') {
  if (!data || data.length === 0) {
    console.error('No data to export');
    return;
  }

  try {
    // Create workbook
    const wb = XLSX.utils.book_new();
    
    // Convert array of objects to worksheet
    const ws = XLSX.utils.json_to_sheet(data);
    
    // Auto-size columns (approximate)
    const colWidths = Object.keys(data[0]).map(key => ({
      wch: Math.max(key.length, ...data.map(row => String(row[key] || '').length))
    }));
    ws['!cols'] = colWidths;
    
    // Add worksheet to workbook
    XLSX.utils.book_append_sheet(wb, ws, sheetName);
    
    // Write file
    XLSX.writeFile(wb, filename);
  } catch (error) {
    console.error('Excel export failed:', error);
    throw error;
  }
}

/**
 * Export Tabulator table to Excel
 * @param {Tabulator} table - Tabulator instance
 * @param {string} filename - Output filename
 */
export function exportTabulatorToExcel(table, filename = 'table-export.xlsx') {
  if (!table) {
    console.error('Tabulator table instance required');
    return;
  }

  try {
    const data = table.getData();
    const tableTitle = table.element?.getAttribute('data-table-title') || 'Table';
    exportToExcel(data, filename, tableTitle);
  } catch (error) {
    console.error('Tabulator export failed:', error);
    throw error;
  }
}

/**
 * Export HTML table to Excel
 * @param {string|HTMLElement} tableSelector - Table selector or element
 * @param {string} filename - Output filename
 */
export function exportTableToExcel(tableSelector, filename = 'table-export.xlsx') {
  const table = typeof tableSelector === 'string' 
    ? document.querySelector(tableSelector)
    : tableSelector;

  if (!table || table.tagName !== 'TABLE') {
    console.error('Valid table element required');
    return;
  }

  try {
    // Convert HTML table to workbook
    const wb = XLSX.utils.table_to_book(table);
    
    // Write file
    XLSX.writeFile(wb, filename);
  } catch (error) {
    console.error('Table export failed:', error);
    throw error;
  }
}

export default {
  exportToExcel,
  exportTabulatorToExcel,
  exportTableToExcel,
};

