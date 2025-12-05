import { describe, it, expect } from 'vitest';
import {
  dateUtils,
  clone,
  isEmptyValue,
  isEqualValue,
} from '../../js/utils.js';

describe('Utility Functions', () => {
  describe('Date Utils', () => {
    describe('format', () => {
      it('should format date with default format', () => {
        const date = '2025-01-15';
        const formatted = dateUtils.format(date);
        expect(formatted).toBe('2025-01-15');
      });

      it('should format date with custom format', () => {
        const date = '2025-01-15';
        const formatted = dateUtils.format(date, 'DD/MM/YYYY');
        expect(formatted).toBe('15/01/2025');
      });

      it('should format date with month name', () => {
        const date = '2025-01-15';
        const formatted = dateUtils.format(date, 'MMMM DD, YYYY');
        expect(formatted).toBe('January 15, 2025');
      });
    });

    describe('isValid', () => {
      it('should recognize valid date', () => {
        expect(dateUtils.isValid('2025-01-15')).toBe(true);
        expect(dateUtils.isValid('2025/01/15')).toBe(true);
        expect(dateUtils.isValid(new Date())).toBe(true);
      });

      it('should recognize invalid date strings', () => {
        expect(dateUtils.isValid('invalid')).toBe(false);
        expect(dateUtils.isValid('')).toBe(false);
        expect(dateUtils.isValid(null)).toBe(false);
      });
    });

    describe('add', () => {
      it('should add days to date', () => {
        const date = '2025-01-15';
        const result = dateUtils.add(date, 5, 'day');
        expect(dateUtils.format(result)).toBe('2025-01-20');
      });

      it('should add months to date', () => {
        const date = '2025-01-15';
        const result = dateUtils.add(date, 2, 'month');
        expect(dateUtils.format(result, 'YYYY-MM')).toBe('2025-03');
      });

      it('should add years to date', () => {
        const date = '2025-01-15';
        const result = dateUtils.add(date, 1, 'year');
        expect(dateUtils.format(result, 'YYYY')).toBe('2026');
      });
    });

    describe('subtract', () => {
      it('should subtract days from date', () => {
        const date = '2025-01-15';
        const result = dateUtils.subtract(date, 5, 'day');
        expect(dateUtils.format(result)).toBe('2025-01-10');
      });

      it('should subtract months from date', () => {
        const date = '2025-03-15';
        const result = dateUtils.subtract(date, 2, 'month');
        expect(dateUtils.format(result, 'YYYY-MM')).toBe('2025-01');
      });
    });

    describe('diff', () => {
      it('should calculate difference in days', () => {
        const date1 = '2025-01-20';
        const date2 = '2025-01-15';
        const diff = dateUtils.diff(date1, date2, 'day');
        expect(diff).toBe(5);
      });

      it('should calculate difference in months', () => {
        const date1 = '2025-03-15';
        const date2 = '2025-01-15';
        const diff = dateUtils.diff(date1, date2, 'month');
        expect(diff).toBe(2);
      });

      it('should handle negative differences', () => {
        const date1 = '2025-01-10';
        const date2 = '2025-01-15';
        const diff = dateUtils.diff(date1, date2, 'day');
        expect(diff).toBe(-5);
      });
    });
  });

  describe('Object Utils', () => {
    describe('clone', () => {
      it('should deep clone simple object', () => {
        const original = { a: 1, b: 2 };
        const cloned = clone(original);
        
        expect(cloned).toEqual(original);
        expect(cloned).not.toBe(original);
      });

      it('should deep clone nested object', () => {
        const original = {
          a: 1,
          b: {
            c: 2,
            d: { e: 3 }
          }
        };
        const cloned = clone(original);
        
        expect(cloned).toEqual(original);
        expect(cloned.b).not.toBe(original.b);
        expect(cloned.b.d).not.toBe(original.b.d);
      });

      it('should clone array', () => {
        const original = [1, 2, { a: 3 }];
        const cloned = clone(original);
        
        expect(cloned).toEqual(original);
        expect(cloned).not.toBe(original);
        expect(cloned[2]).not.toBe(original[2]);
      });
    });

    describe('isEmptyValue', () => {
      it('should recognize empty values', () => {
        expect(isEmptyValue(null)).toBe(true);
        expect(isEmptyValue(undefined)).toBe(true);
        expect(isEmptyValue('')).toBe(true);
        expect(isEmptyValue([])).toBe(true);
        expect(isEmptyValue({})).toBe(true);
      });

      it('should recognize non-empty values', () => {
        expect(isEmptyValue('test')).toBe(false);
        expect(isEmptyValue([1, 2, 3])).toBe(false);
        expect(isEmptyValue({ a: 1 })).toBe(false);
        // Note: lodash isEmpty treats numbers and booleans as EMPTY (they're not array-like or object-like)
      });
    });

    describe('isEqualValue', () => {
      it('should compare primitive values', () => {
        expect(isEqualValue(1, 1)).toBe(true);
        expect(isEqualValue('test', 'test')).toBe(true);
        expect(isEqualValue(true, true)).toBe(true);
        
        expect(isEqualValue(1, 2)).toBe(false);
        expect(isEqualValue('test', 'TEST')).toBe(false);
      });

      it('should deep compare objects', () => {
        const obj1 = { a: 1, b: { c: 2 } };
        const obj2 = { a: 1, b: { c: 2 } };
        const obj3 = { a: 1, b: { c: 3 } };
        
        expect(isEqualValue(obj1, obj2)).toBe(true);
        expect(isEqualValue(obj1, obj3)).toBe(false);
      });

      it('should deep compare arrays', () => {
        const arr1 = [1, 2, { a: 3 }];
        const arr2 = [1, 2, { a: 3 }];
        const arr3 = [1, 2, { a: 4 }];
        
        expect(isEqualValue(arr1, arr2)).toBe(true);
        expect(isEqualValue(arr1, arr3)).toBe(false);
      });
    });
  });
});
