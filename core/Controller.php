<?php

namespace Core;

/**
 * Base Controller class
 */
abstract class Controller
{
    protected View $view;
    protected Request $request;
    protected Session $session;
    
    public function __construct()
    {
        $this->view = new View();
        $this->session = new Session();
    }
    
    /**
     * Set request instance
     */
    public function setRequest(Request $request): void
    {
        $this->request = $request;
    }
    
    /**
     * Render view
     */
    protected function view(string $view, array $data = []): Response
    {
        $content = $this->view->render($view, $data);
        return Response::html($content);
    }
    
    /**
     * Return JSON response
     */
    protected function json(mixed $data, int $status = 200): Response
    {
        return Response::json($data, $status);
    }
    
    /**
     * Redirect to URL
     */
    protected function redirect(string $url, int $status = 302): Response
    {
        return Response::redirect($url, $status);
    }
    
    /**
     * Redirect back
     */
    protected function back(): Response
    {
        $referer = $this->request->header('Referer') ?? '/';
        return $this->redirect($referer);
    }
    
    /**
     * Redirect with flash message
     */
    protected function redirectWithMessage(string $url, string $type, string $message): Response
    {
        $this->session->flash($type, $message);
        return $this->redirect($url);
    }
    
    /**
     * Redirect back with flash message
     */
    protected function backWithMessage(string $type, string $message): Response
    {
        $this->session->flash($type, $message);
        return $this->back();
    }
    
    /**
     * Redirect with input data for form repopulation
     */
    protected function redirectWithInput(string $url, array $input = null): Response
    {
        $input = $input ?? $this->request->all();
        $this->session->flashInput($input);
        return $this->redirect($url);
    }
    
    /**
     * Redirect back with input
     */
    protected function backWithInput(): Response
    {
        $this->session->flashInput($this->request->all());
        return $this->back();
    }
    
    /**
     * Redirect with errors and input
     */
    protected function redirectWithErrors(string $url, array $errors, array $input = null): Response
    {
        $input = $input ?? $this->request->all();
        $this->session->flash('errors', $errors);
        $this->session->flashInput($input);
        return $this->redirect($url);
    }
    
    /**
     * Redirect back with errors and input
     */
    protected function backWithErrors(array $errors): Response
    {
        $this->session->flash('errors', $errors);
        $this->session->flashInput($this->request->all());
        return $this->back();
    }
    
    /**
     * Abort with HTTP status
     */
    protected function abort(int $status, string $message = ''): Response
    {
        if ($this->request->expectsJson()) {
            return $this->json(['error' => $message ?: "Error {$status}"], $status);
        }
        
        return Response::error($status, $message);
    }
    
    /**
     * Return 404 Not Found
     */
    protected function notFound(string $message = 'Not Found'): Response
    {
        return $this->abort(404, $message);
    }
    
    /**
     * Return 403 Forbidden
     */
    protected function forbidden(string $message = 'Forbidden'): Response
    {
        return $this->abort(403, $message);
    }
    
    /**
     * Validate request input
     */
    protected function validate(array $rules, array $messages = []): array
    {
        $validator = new Validator();
        $data = $this->request->all();
        
        $result = $validator->validate($data, $rules, $messages);
        
        if (!$result['valid']) {
            if ($this->request->expectsJson()) {
                throw new \Exception(json_encode([
                    'errors' => $result['errors'],
                    'message' => 'Validation failed'
                ]));
            }
            
            $this->backWithErrors($result['errors']);
        }
        
        return $result['data'];
    }
    
    /**
     * Check authorization
     */
    protected function authorize(string $permission): void
    {
        if (!auth()->check()) {
            if ($this->request->expectsJson()) {
                throw new \Exception(json_encode([
                    'error' => 'Unauthorized',
                    'message' => 'Authentication required'
                ]));
            }
            
            $this->session->flash('error', 'Please log in to continue');
            redirect('/login');
        }
        
        if (!auth()->can($permission)) {
            throw new \Exception('Insufficient permissions');
        }
    }
    
    /**
     * Require authentication
     */
    protected function requireAuth(): void
    {
        if (!auth()->check()) {
            if ($this->request->expectsJson()) {
                throw new \Exception(json_encode([
                    'error' => 'Unauthorized',
                    'message' => 'Authentication required'
                ]));
            }
            
            $this->session->flash('error', 'Please log in to continue');
            redirect('/login');
        }
    }
    
    /**
     * Get current user
     */
    protected function user(): ?\App\Models\User
    {
        return auth()->user();
    }
    
    /**
     * Handle pagination
     */
    protected function paginate(string $query, array $params = [], int $perPage = null): array
    {
        $perPage = $perPage ?? (int) config('app.pagination.per_page', 15);
        $page = max(1, (int) $this->request->query('page', 1));
        $offset = ($page - 1) * $perPage;
        
        // Get total count
        $countQuery = preg_replace('/SELECT .* FROM/', 'SELECT COUNT(*) as total FROM', $query);
        $total = DB::fetch($countQuery, $params)['total'] ?? 0;
        
        // Get paginated results
        $query .= " LIMIT {$offset}, {$perPage}";
        $items = DB::fetchAll($query, $params);
        
        return [
            'items' => $items,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'total_pages' => ceil($total / $perPage),
                'has_prev' => $page > 1,
                'has_next' => $page < ceil($total / $perPage)
            ]
        ];
    }
}