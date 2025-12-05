/**
 * Form Validation Schemas
 * Uses Yup for schema-based validation
 */

import * as yup from 'yup';

/**
 * Contact form validation schema
 */
export const contactSchema = yup.object({
  firstname: yup
    .string()
    .trim()
    .required('First name is required')
    .min(2, 'First name must be at least 2 characters'),
  lastname: yup
    .string()
    .trim()
    .required('Last name is required')
    .min(2, 'Last name must be at least 2 characters'),
  email: yup
    .string()
    .trim()
    .email('Please enter a valid email address')
    .required('Email is required'),
  subject: yup
    .string()
    .trim()
    .required('Subject is required')
    .min(5, 'Subject must be at least 5 characters'),
  message: yup
    .string()
    .trim()
    .required('Message is required')
    .min(10, 'Message must be at least 10 characters')
    .max(1000, 'Message must not exceed 1000 characters'),
});

/**
 * Newsletter subscription schema
 */
export const newsletterSchema = yup.object({
  email: yup
    .string()
    .trim()
    .email('Please enter a valid email address')
    .required('Email is required'),
});

/**
 * Validate form data against schema
 * @param {import('yup').ObjectSchema} schema - Yup schema to validate against
 * @param {Object} data - Form data to validate
 * @returns {Promise<{valid: boolean, errors: Object<string, string>}>} Validation result with errors if any
 * @throws {Error} If schema validation fails unexpectedly
 */
export const validateForm = async (schema, data) => {
  try {
    await schema.validate(data, { abortEarly: false });
    return { valid: true, errors: {} };
  } catch (err) {
    const errors = {};
    err.inner.forEach((error) => {
      if (error.path) {
        errors[error.path] = error.message;
      }
    });
    return { valid: false, errors };
  }
};

export default {
  contactSchema,
  newsletterSchema,
  validateForm,
};
