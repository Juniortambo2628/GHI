import { describe, it, expect, beforeEach, vi } from 'vitest';
import { apiService } from '../../js/api.js';
import axios from 'axios';

// Mock axios
vi.mock('axios');

describe('API Service', () => {
  beforeEach(() => {
    // Clear all mocks before each test
    vi.clearAllMocks();
  });

  describe('GET requests', () => {
    it('should make successful GET request', async () => {
      const mockData = { id: 1, name: 'Test' };
      const mockResponse = {
        data: mockData,
        status: 200,
        headers: { 'content-type': 'application/json' }
      };

      // Mock axios.create to return an object with get method
      const mockGet = vi.fn().mockResolvedValue(mockResponse);
      axios.create = vi.fn(() => ({
        get: mockGet,
        interceptors: {
          request: { use: vi.fn() },
          response: { use: vi.fn() }
        }
      }));

      const result = await apiService.get('/api/test');

      expect(result.success).toBe(true);
      expect(result.data).toEqual(mockData);
      expect(result.status).toBe(200);
    });

    it('should handle GET request errors', async () => {
      const mockError = {
        response: {
          data: { message: 'Not found' },
          status: 404
        }
      };

      const mockGet = vi.fn().mockRejectedValue(mockError);
      axios.create = vi.fn(() => ({
        get: mockGet,
        interceptors: {
          request: { use: vi.fn() },
          response: { use: vi.fn() }
        }
      }));

      const result = await apiService.get('/api/missing');

      expect(result.success).toBe(false);
      expect(result.status).toBe(404);
      expect(result.error).toEqual({ message: 'Not found' });
    });
  });

  describe('POST requests', () => {
    it('should make successful POST request with JSON data', async () => {
      const postData = { name: 'Test', email: 'test@example.com' };
      const mockResponse = {
        data: { id: 1, ...postData },
        status: 201,
        headers: { 'content-type': 'application/json' }
      };

      const mockPost = vi.fn().mockResolvedValue(mockResponse);
      axios.create = vi.fn(() => ({
        post: mockPost,
        interceptors: {
          request: { use: vi.fn() },
          response: { use: vi.fn() }
        }
      }));

      const result = await apiService.post('/api/create', postData);

      expect(result.success).toBe(true);
      expect(result.data).toHaveProperty('id');
      expect(result.status).toBe(201);
    });

    it('should handle POST request validation errors', async () => {
      const mockError = {
        response: {
          data: { errors: { email: 'Invalid email' } },
          status: 422
        }
      };

      const mockPost = vi.fn().mockRejectedValue(mockError);
      axios.create = vi.fn(() => ({
        post: mockPost,
        interceptors: {
          request: { use: vi.fn() },
          response: { use: vi.fn() }
        }
      }));

      const result = await apiService.post('/api/create', { email: 'bad' });

      expect(result.success).toBe(false);
      expect(result.status).toBe(422);
      expect(result.error.errors).toHaveProperty('email');
    });
  });

  describe('PUT requests', () => {
    it('should make successful PUT request', async () => {
      const updateData = { name: 'Updated' };
      const mockResponse = {
        data: { id: 1, ...updateData },
        status: 200,
        headers: {}
      };

      const mockPut = vi.fn().mockResolvedValue(mockResponse);
      axios.create = vi.fn(() => ({
        put: mockPut,
        interceptors: {
          request: { use: vi.fn() },
          response: { use: vi.fn() }
        }
      }));

      const result = await apiService.put('/api/update/1', updateData);

      expect(result.success).toBe(true);
      expect(result.data.name).toBe('Updated');
    });
  });

  describe('DELETE requests', () => {
    it('should make successful DELETE request', async () => {
      const mockResponse = {
        data: { message: 'Deleted successfully' },
        status: 200,
        headers: {}
      };

      const mockDelete = vi.fn().mockResolvedValue(mockResponse);
      axios.create = vi.fn(() => ({
        delete: mockDelete,
        interceptors: {
          request: { use: vi.fn() },
          response: { use: vi.fn() }
        }
      }));

      const result = await apiService.delete('/api/delete/1');

      expect(result.success).toBe(true);
      expect(result.data.message).toBe('Deleted successfully');
    });
  });

  describe('File Upload', () => {
    it('should upload file with progress tracking', async () => {
      const formData = new FormData();
      formData.append('file', new Blob(['test']), 'test.txt');
      
      const mockResponse = {
        data: { fileId: 123, filename: 'test.txt' },
        status: 200
      };

      const mockPost = vi.fn().mockResolvedValue(mockResponse);
      axios.create = vi.fn(() => ({
        post: mockPost,
        interceptors: {
          request: { use: vi.fn() },
          response: { use: vi.fn() }
        }
      }));

      const progressCallback = vi.fn();
      const result = await apiService.upload('/api/upload', formData, progressCallback);

      expect(result.success).toBe(true);
      expect(result.data.fileId).toBe(123);
    });
  });
});
