import { describe, it, expect, beforeEach, vi } from 'vitest';

const mockInstance = {
  get: vi.fn(),
  post: vi.fn(),
  put: vi.fn(),
  delete: vi.fn(),
  interceptors: {
    request: { use: vi.fn() },
    response: { use: vi.fn() },
  },
};

vi.mock('axios', () => ({
  default: {
    create: vi.fn(() => mockInstance),
  },
}));

const { apiService } = await import('../../js/api.js');

describe('API Service', () => {
  beforeEach(() => {
    vi.clearAllMocks();
  });

  describe('GET requests', () => {
    it('should make successful GET request', async () => {
      const mockResponse = {
        data: { id: 1, name: 'Test' },
        status: 200,
        headers: { 'content-type': 'application/json' },
      };
      mockInstance.get.mockResolvedValueOnce(mockResponse);

      const result = await apiService.get('/api/test');

      expect(result.success).toBe(true);
      expect(result.data).toEqual({ id: 1, name: 'Test' });
      expect(result.status).toBe(200);
    });

    it('should handle GET request errors', async () => {
      const mockError = {
        response: {
          data: { message: 'Not found' },
          status: 404,
        },
      };
      mockInstance.get.mockRejectedValueOnce(mockError);

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
        headers: { 'content-type': 'application/json' },
      };
      mockInstance.post.mockResolvedValueOnce(mockResponse);

      const result = await apiService.post('/api/create', postData);

      expect(result.success).toBe(true);
      expect(result.data).toHaveProperty('id');
      expect(result.status).toBe(201);
    });

    it('should handle POST request validation errors', async () => {
      const mockError = {
        response: {
          data: { errors: { email: 'Invalid email' } },
          status: 422,
        },
      };
      mockInstance.post.mockRejectedValueOnce(mockError);

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
        headers: {},
      };
      mockInstance.put.mockResolvedValueOnce(mockResponse);

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
        headers: {},
      };
      mockInstance.delete.mockResolvedValueOnce(mockResponse);

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
        status: 200,
      };
      mockInstance.post.mockResolvedValueOnce(mockResponse);

      const progressCallback = vi.fn();
      const result = await apiService.upload('/api/upload', formData, progressCallback);

      expect(result.success).toBe(true);
      expect(result.data.fileId).toBe(123);
    });
  });
});
