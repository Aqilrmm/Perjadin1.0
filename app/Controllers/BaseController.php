<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 */
abstract class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var array
     */
    protected $helpers = ['auth', 'format', 'status', 'notification'];

    /**
     * Session instance
     *
     * @var \CodeIgniter\Session\Session
     */
    protected $session;

    /**
     * Last validation errors
     *
     * @var array
     */
    protected $validationErrors = [];

    /**
     * Constructor.
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.
        $this->session = \Config\Services::session();
    }

    /**
     * Return JSON response
     *
     * @param array $data
     * @param int $statusCode
     * @return ResponseInterface
     */
    protected function respond($data, $statusCode = 200)
    {
        return $this->response->setJSON($data)->setStatusCode($statusCode);
    }

    /**
     * Return success JSON response
     *
     * @param string $message
     * @param mixed $data
     * @return ResponseInterface
     */
    protected function respondSuccess($message, $data = null)
    {
        return $this->respond([
            'status' => true,
            'message' => $message,
            'data' => $data
        ]);
    }

    /**
     * Return error JSON response
     *
     * @param string $message
     * @param mixed $errors
     * @param int $statusCode
     * @return ResponseInterface
     */
    protected function respondError($message, $errors = null, $statusCode = 400)
    {
        return $this->respond([
            'status' => false,
            'message' => $message,
            'errors' => $errors
        ], $statusCode);
    }

    /**
     * Validate request data (compatible with CodeIgniter\Controller::validate)
     * Stores validation errors in `$this->validationErrors` when validation fails.
     *
     * @param mixed $rules
     * @param array $messages
     * @return bool
     */
    public function validate($rules, array $messages = []): bool
    {
        $validation = \Config\Services::validation();
        $validation->setRules($rules, $messages);

        if (!$validation->withRequest($this->request)->run()) {
            $this->validationErrors = $validation->getErrors();
            return false;
        }

        $this->validationErrors = [];
        return true;
    }

    /**
     * Get the last validation errors set by `validate()`
     *
     * @return array
     */
    protected function getValidationErrors(): array
    {
        return $this->validationErrors;
    }

    /**
     * Get current logged in user
     *
     * @return array|null
     */
    protected function getCurrentUser()
    {
        return $this->session->get('user_data');
    }

    /**
     * Check if user has specific role(s)
     *
     * @param string|array $roles
     * @return bool
     */
    protected function hasRole($roles)
    {
        $user = $this->getCurrentUser();
        if (!$user) return false;

        if (is_array($roles)) {
            return in_array($user['role'], $roles);
        }

        return $user['role'] === $roles;
    }

    /**
     * Log user activity
     *
     * @param string $action
     * @param string|null $description
     * @return void
     */
    protected function logActivity($action, $description = null)
    {
        $user = $this->getCurrentUser();

        $logModel = new \App\Models\Log\SecurityLogModel();
        $logModel->insert([
            'user_id' => $user['id'] ?? null,
            'action' => $action,
            'description' => $description,
            'ip_address' => $this->request->getIPAddress(),
            'user_agent' => $this->request->getUserAgent()->__toString()
        ]);
    }

    /**
     * Check if request is AJAX
     *
     * @return bool
     */
    protected function isAjax()
    {
        return $this->request->isAJAX();
    }

    /**
     * Get validation rules from model
     *
     * @param string $modelName
     * @param string|null $context Optional context name passed to model
     * @return array
     */
    protected function getModelRules($modelName, $context = null)
    {
        $model = new $modelName();
        if (method_exists($model, 'getValidationRuless')) {
            return $model->getValidationRuless($context);
        }

        // Fallback to property
        return $model->validationRules ?? [];
    }
}
