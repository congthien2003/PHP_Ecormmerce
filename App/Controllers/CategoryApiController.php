<?php

require_once 'App/Models/CategoryModel.php';
require_once 'App/Repository/CategoryRepository.php';
require_once('App/config/database.php');

class CategoryApiController {
    private $categoryRepository;
    private $db;

    public function __construct() {
        $this->db = (new Database())->getConnection();
        $this->categoryRepository = new CategoryRepository($this->db);
    }

    // GET: /categories
    public function getAll() {
        try {
            $result = $this->categoryRepository->getAllCategories();
            Response::json([
                'status' => 'success',
                'data' => $result
            ]);
        } catch (\Exception $e) {
            Response::error('Failed to fetch categories', 500);
        }
    }

    // GET: /categories/{id}
    public function getById($id) {
        try {
            $category = $this->categoryRepository->getCategoryById($id);
            
            if (!$category) {
                Response::error('Category not found', 404);
                return;
            }

            Response::json([
                'status' => 'success',
                'data' => $category
            ]);
        } catch (\Exception $e) {
            Response::error('Failed to fetch category', 500);
        }
    }

    // POST: /categories
    public function create() {
        try {
            $data = json_decode(file_get_contents('php://input'), true);

            // Validate required fields
            if (!isset($data['name']) || empty($data['name'])) {
                Response::error('Category name is required', 400);
                return;
            }

            $description = isset($data['description']) ? $data['description'] : '';
            
            $category = $this->categoryRepository->createCategory($data['name'], $description);
            
            if ($category) {
                Response::json([
                    'status' => 'success',
                    'message' => 'Category created successfully',
                    'data' => $category
                ], 201);
            } else {
                Response::error('Failed to create category', 500);
            }
        } catch (\Exception $e) {
            Response::error('Failed to create category: ' . $e->getMessage(), 500);
        }
    }

    // PUT: /categories/{id}
    public function update($id) {
        try {
            $data = json_decode(file_get_contents('php://input'), true);
            
            // Check if category exists
            $category = $this->categoryRepository->getCategoryById($id);
            if (!$category) {
                Response::error('Category not found', 404);
                return;
            }

            if (!isset($data['name']) || empty($data['name'])) {
                Response::error('Category name is required', 400);
                return;
            }

            $description = isset($data['description']) ? $data['description'] : '';
            
            $success = $this->categoryRepository->updateCategory($id, $data['name'], $description);
            
            if ($success) {
                Response::json([
                    'status' => 'success',
                    'message' => 'Category updated successfully'
                ]);
            } else {
                Response::error('Failed to update category', 400);
            }
        } catch (\Exception $e) {
            Response::error('Failed to update category: ' . $e->getMessage(), 500);
        }
    }

    // DELETE: /categories/{id}
    public function delete($id) {
        try {
            // Check if category exists
            $category = $this->categoryRepository->getCategoryById($id);
            if (!$category) {
                Response::error('Category not found', 404);
                return;
            }

            $success = $this->categoryRepository->deleteCategory($id);
            
            if ($success) {
                Response::json([
                    'status' => 'success',
                    'message' => 'Category deleted successfully'
                ]);
            } else {
                Response::error('Failed to delete category', 400);
            }
        } catch (\Exception $e) {
            Response::error('Failed to delete category: ' . $e->getMessage(), 500);
        }
    }
}
