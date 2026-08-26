import { describe, it, expect } from 'vitest';
import { formSchemas, validate } from '../../js/validation.js';

const contactSchema = formSchemas.contact;
const newsletterSchema = formSchemas.newsletter;

async function validateForm(schema, data) {
  const result = validate(schema, data);
  return {
    valid: result.success,
    errors: result.errors ? Object.fromEntries(Object.entries(result.errors).map(([k, v]) => [k, v.join(' ')])) : {},
    data: result.data,
  };
}

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
      expect(result.errors.firstname).toBeDefined();
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
      expect(result.errors.subject).toBeDefined();
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
      expect(result.errors.message).toBeDefined();
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
      expect(result.errors.email).toBeDefined();
    });

    it('should accept firstname or name field', async () => {
      const dataWithName = {
        name: 'John Doe',
        email: 'john@example.com',
        subject: 'Test Subject',
        message: 'This is a valid message.'
      };

      const result = await validateForm(contactSchema, dataWithName);
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
    });

    it('should reject invalid email format', async () => {
      const invalidData = {
        email: 'not-an-email'
      };

      const result = await validateForm(newsletterSchema, invalidData);

      expect(result.valid).toBe(false);
      expect(result.errors.email).toBeDefined();
    });

    it('should reject empty email', async () => {
      const invalidData = {
        email: ''
      };

      const result = await validateForm(newsletterSchema, invalidData);

      expect(result.valid).toBe(false);
      expect(result.errors.email).toBeDefined();
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
