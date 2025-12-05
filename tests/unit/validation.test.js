import { describe, it, expect, beforeEach, vi } from 'vitest';
import { contactSchema, newsletterSchema, validateForm } from '../../js/schemas.js';

describe('Schema Validation', () => {
  describe('Contact Form Schema', () => {
    it('should validate valid contact form data', async () => {
      const validData = {
        firstname: 'John',
        lastname: 'Doe',
        email: 'john.doe@example.com',
        subject: 'Test Subject Line',
        message: 'This is a valid test message with sufficient length.'
      };

      const result = await validateForm(contactSchema, validData);
      
      expect(result.valid).toBe(true);
      expect(result.errors).toEqual({});
    });

    it('should reject contact form with invalid email', async () => {
      const invalidData = {
        firstname: 'John',
        lastname: 'Doe',
        email: 'not-an-email',
        subject: 'Test Subject',
        message: 'Valid message'
      };

      const result = await validateForm(contactSchema, invalidData);
      
      expect(result.valid).toBe(false);
      expect(result.errors.email).toBeDefined();
      expect(result.errors.email).toContain('valid email');
    });

    it('should reject contact form with short firstname', async () => {
      const invalidData = {
        firstname: 'J',
        lastname: 'Doe',
        email: 'john@example.com',
        subject: 'Test Subject',
        message: 'Valid message here'
      };

      const result = await validateForm(contactSchema, invalidData);
      
      expect(result.valid).toBe(false);
      expect(result.errors.firstname).toContain('at least 2 characters');
    });

    it('should reject contact form with short subject', async () => {
      const invalidData = {
        firstname: 'John',
        lastname: 'Doe',
        email: 'john@example.com',
        subject: 'Hi',
        message: 'Valid message content'
      };

      const result = await validateForm(contactSchema, invalidData);
      
      expect(result.valid).toBe(false);
      expect(result.errors.subject).toContain('at least 5 characters');
    });

    it('should reject contact form with short message', async () => {
      const invalidData = {
        firstname: 'John',
        lastname: 'Doe',
        email: 'john@example.com',
        subject: 'Valid Subject',
        message: 'Short'
      };

      const result = await validateForm(contactSchema, invalidData);
      
      expect(result.valid).toBe(false);
      expect(result.errors.message).toContain('at least 10 characters');
    });

    it('should reject contact form with too long message', async () => {
      const invalidData = {
        firstname: 'John',
        lastname: 'Doe',
        email: 'john@example.com',
        subject: 'Valid Subject',
        message: 'a'.repeat(1001)
      };

      const result = await validateForm(contactSchema, invalidData);
      
      expect(result.valid).toBe(false);
      expect(result.errors.message).toContain('not exceed 1000 characters');
    });

    it('should reject contact form with missing required fields', async () => {
      const invalidData = {
        firstname: '',
        lastname: '',
        email: '',
        subject: '',
        message: ''
      };

      const result = await validateForm(contactSchema, invalidData);
      
      expect(result.valid).toBe(false);
      expect(Object.keys(result.errors).length).toBeGreaterThan(0);
      expect(result.errors.firstname).toBeDefined();
      expect(result.errors.email).toBeDefined();
    });

    it('should trim whitespace from inputs', async () => {
      const dataWithWhitespace = {
        firstname: '  John  ',
        lastname: '  Doe  ',
        email: '  john@example.com  ',
        subject: '  Test Subject  ',
        message: '  This is a test message  '
      };

      const result = await validateForm(contactSchema, dataWithWhitespace);
      
      expect(result.valid).toBe(true);
    });
  });

  describe('Newsletter Schema', () => {
    it('should validate valid newsletter email', async () => {
      const validData = {
        email: 'subscriber@example.com'
      };

      const result = await validateForm(newsletterSchema, validData);
      
      expect(result.valid).toBe(true);
      expect(result.errors).toEqual({});
    });

    it('should reject invalid email format', async () => {
      const invalidData = {
        email: 'not-an-email'
      };

      const result = await validateForm(newsletterSchema, invalidData);
      
      expect(result.valid).toBe(false);
      expect(result.errors.email).toContain('valid email');
    });

    it('should reject empty email', async () => {
      const invalidData = {
        email: ''
      };

      const result = await validateForm(newsletterSchema, invalidData);
      
      expect(result.valid).toBe(false);
      expect(result.errors.email).toBeDefined();
    });

    it('should trim whitespace from email', async () => {
      const dataWithWhitespace = {
        email: '  test@example.com  '
      };

      const result = await validateForm(newsletterSchema, dataWithWhitespace);
      
      expect(result.valid).toBe(true);
    });

    it('should accept various valid email formats', async () => {
      const validEmails = [
        'simple@example.com',
        'user.name@example.com',
        'user+tag@example.co.uk',
        'user_name@example-domain.com'
      ];

      for (const email of validEmails) {
        const result = await validateForm(newsletterSchema, { email });
        expect(result.valid).toBe(true);
      }
    });
  });
});
