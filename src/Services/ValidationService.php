<?php

/**
 * Validation Service using Symfony Validator
 * Global Harmony Initiative Website
 */

namespace GHI\Services;

use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

class ValidationService
{
    private static ?ValidatorInterface $instance = null;

    /**
     * Get validator instance (Singleton)
     */
    public static function getInstance(): ValidatorInterface
    {
        if (!self::$instance instanceof \Symfony\Component\Validator\Validator\ValidatorInterface) {
            self::$instance = Validation::createValidator();
        }

        return self::$instance;
    }

    /**
     * Validate email
     */
    public static function validateEmail(string $email): array
    {
        $violations = self::getInstance()->validate($email, [
            new Assert\NotBlank(['message' => 'Email is required']),
            new Assert\Email(['message' => 'Invalid email format']),
        ]);

        return self::violationsToArray($violations);
    }

    /**
     * Validate password
     */
    public static function validatePassword(string $password, int $minLength = 8): array
    {
        $violations = self::getInstance()->validate($password, [
            new Assert\NotBlank(['message' => 'Password is required']),
            new Assert\Length([
                'min' => $minLength,
                'minMessage' => sprintf('Password must be at least %d characters', $minLength),
            ]),
        ]);

        return self::violationsToArray($violations);
    }

    /**
     * Validate required field
     */
    public static function validateRequired($value, string $fieldName = 'Field'): array
    {
        $violations = self::getInstance()->validate($value, [
            new Assert\NotBlank(['message' => $fieldName . ' is required']),
        ]);

        return self::violationsToArray($violations);
    }

    /**
     * Validate string length
     */
    public static function validateLength(string $value, int $min = 0, int $max = null, string $fieldName = 'Field'): array
    {
        $constraints = [];

        if ($min > 0) {
            $constraints[] = new Assert\Length([
                'min' => $min,
                'minMessage' => sprintf('%s must be at least %d characters', $fieldName, $min),
            ]);
        }

        if ($max !== null) {
            $constraints[] = new Assert\Length([
                'max' => $max,
                'maxMessage' => sprintf('%s must not exceed %d characters', $fieldName, $max),
            ]);
        }

        if ($constraints === []) {
            return [];
        }

        $violations = self::getInstance()->validate($value, $constraints);

        return self::violationsToArray($violations);
    }

    /**
     * Validate URL
     */
    public static function validateUrl(string $url): array
    {
        $violations = self::getInstance()->validate($url, [
            new Assert\Url(['message' => 'Invalid URL format']),
        ]);

        return self::violationsToArray($violations);
    }

    /**
     * Validate date
     */
    public static function validateDate(string $date, string $format = 'Y-m-d'): array
    {
        $violations = self::getInstance()->validate($date, [
            new Assert\NotBlank(['message' => 'Date is required']),
            new Assert\Date(['message' => 'Invalid date format']),
        ]);

        return self::violationsToArray($violations);
    }

    /**
     * Validate file upload
     */
    public static function validateFile(array $file, array $allowedTypes = [], int $maxSize = 10485760): array
    {
        $errors = [];

        // Check if file was uploaded
        if (! isset($file['tmp_name']) || ! is_uploaded_file($file['tmp_name'])) {
            $errors[] = 'No file was uploaded';
            return $errors;
        }

        // Check file size
        if (isset($file['size']) && $file['size'] > $maxSize) {
            $maxSizeMB = round($maxSize / 1048576, 2);
            $errors[] = sprintf('File size exceeds maximum allowed size of %sMB', $maxSizeMB);
        }

        // Check file type
        if ($allowedTypes !== [] && isset($file['name'])) {
            $extension = strtolower(pathinfo((string) $file['name'], PATHINFO_EXTENSION));
            if (! in_array($extension, array_map('strtolower', $allowedTypes), true)) {
                $errors[] = 'File type not allowed. Allowed types: ' . implode(', ', $allowedTypes);
            }
        }

        // Check for upload errors
        if (isset($file['error']) && $file['error'] !== UPLOAD_ERR_OK) {
            $uploadErrors = [
                UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize directive',
                UPLOAD_ERR_FORM_SIZE => 'File exceeds MAX_FILE_SIZE directive',
                UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
                UPLOAD_ERR_NO_FILE => 'No file was uploaded',
                UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
                UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
                UPLOAD_ERR_EXTENSION => 'File upload stopped by extension',
            ];
            $errors[] = $uploadErrors[$file['error']] ?? 'Unknown upload error';
        }

        return $errors;
    }

    /**
     * Validate array of data against rules
     */
    public static function validate(array $data, array $rules): array
    {
        $errors = [];

        foreach ($rules as $field => $fieldRules) {
            $value = $data[$field] ?? null;

            foreach ($fieldRules as $rule => $params) {
                switch ($rule) {
                    case 'required':
                        if ($params && (empty($value) && $value !== '0')) {
                            $errors[$field][] = ucfirst($field) . ' is required';
                        }

                        break;

                    case 'email':
                        if (! empty($value)) {
                            $emailErrors = self::validateEmail($value);
                            if ($emailErrors !== []) {
                                $errors[$field] = array_merge($errors[$field] ?? [], $emailErrors);
                            }
                        }

                        break;

                    case 'min':
                        if (! empty($value) && strlen((string) $value) < $params) {
                            $errors[$field][] = ucfirst($field) . sprintf(' must be at least %s characters', $params);
                        }

                        break;

                    case 'max':
                        if (! empty($value) && strlen((string) $value) > $params) {
                            $errors[$field][] = ucfirst($field) . sprintf(' must not exceed %s characters', $params);
                        }

                        break;

                    case 'url':
                        if (! empty($value)) {
                            $urlErrors = self::validateUrl($value);
                            if ($urlErrors !== []) {
                                $errors[$field] = array_merge($errors[$field] ?? [], $urlErrors);
                            }
                        }

                        break;
                }
            }
        }

        return $errors;
    }

    /**
     * Convert violations to array
     */
    private static function violationsToArray(\Symfony\Component\Validator\ConstraintViolationListInterface $violations): array
    {
        $errors = [];
        foreach ($violations as $violation) {
            $errors[] = $violation->getMessage();
        }

        return $errors;
    }
}
