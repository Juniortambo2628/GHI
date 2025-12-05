/**
 * Validation Service using Zod
 * Global Harmony Initiative Website
 */

import { z } from 'zod';

// Common validation schemas
export const schemas = {
  email: z.string().email('Invalid email address'),
  password: z.string().min(8, 'Password must be at least 8 characters'),
  required: z.string().min(1, 'This field is required'),
  url: z.string().url('Invalid URL format'),
  phone: z.string().regex(/^[\d\s\-\+\(\)]+$/, 'Invalid phone number format'),
  positiveNumber: z.number().positive('Must be a positive number'),
  nonEmptyString: z.string().min(1, 'Cannot be empty'),
};

// Form validation schemas
export const formSchemas = {
  contact: z.object({
    firstname: z.string().min(2, 'First name must be at least 2 characters').optional().or(z.literal('')),
    lastname: z.string().optional().or(z.literal('')),
    name: z.string().min(2, 'Name must be at least 2 characters').optional().or(z.literal('')),
    email: schemas.email,
    subject: z.string().min(3, 'Subject must be at least 3 characters').optional().or(z.literal('')),
    message: z.string().min(10, 'Message must be at least 10 characters'),
  }).refine((data) => data.firstname || data.name, {
    message: 'Name is required',
    path: ['name'],
  }),

  login: z.object({
    email: schemas.email,
    password: schemas.password,
    remember: z.boolean().optional(),
  }),

  volunteer: z.object({
    name: z.string().min(2, 'Name must be at least 2 characters').optional().or(z.literal('')),
    firstname: z.string().min(2, 'First name must be at least 2 characters').optional().or(z.literal('')),
    lastname: z.string().optional().or(z.literal('')),
    email: schemas.email,
    phone: z.string().optional().or(z.literal('')),
    message: z.string().optional().or(z.literal('')),
    event_name: z.string().optional().or(z.literal('')),
  }).refine((data) => data.firstname || data.name, {
    message: 'Name is required',
    path: ['name'],
  }),

  newsletter: z.object({
    email: schemas.email,
  }),
};

/**
 * Validate data against schema
 */
export function validate(schema, data) {
  try {
    const result = schema.parse(data);
    return {
      success: true,
      data: result,
      errors: null,
    };
  } catch (error) {
    if (error instanceof z.ZodError) {
      const errors = {};
      error.errors.forEach((err) => {
        const path = err.path.join('.');
        if (!errors[path]) {
          errors[path] = [];
        }
        errors[path].push(err.message);
      });

      return {
        success: false,
        data: null,
        errors: errors,
      };
    }

    return {
      success: false,
      data: null,
      errors: { _general: ['Validation failed'] },
    };
  }
}

/**
 * Validate form field
 */
export function validateField(schema, fieldName, value) {
  try {
    const fieldSchema = schema.shape[fieldName];
    if (!fieldSchema) {
      return { success: true, error: null };
    }

    fieldSchema.parse(value);
    return { success: true, error: null };
  } catch (error) {
    if (error instanceof z.ZodError) {
      return {
        success: false,
        error: error.errors[0]?.message || 'Invalid value',
      };
    }
    return { success: false, error: 'Validation failed' };
  }
}

/**
 * Validate async (for server-side validation)
 */
export async function validateAsync(schema, data) {
  return validate(schema, data);
}

export default {
  validate,
  validateField,
  validateAsync,
  schemas,
  formSchemas,
};

