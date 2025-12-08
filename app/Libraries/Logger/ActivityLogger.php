<?php

namespace App\Libraries\Logger;

use App\Models\Log\SecurityLogModel;

/**
 * Activity Logger Service
 * 
 * Comprehensive logging system for user activities, CRUD operations, and system events
 */
class ActivityLogger
{
    protected $logModel;
    protected $session;
    protected $request;

    public function __construct()
    {
        $this->logModel = new SecurityLogModel();
        $this->session = \Config\Services::session();
        $this->request = \Config\Services::request();
    }

    /**
     * Log any activity
     * 
     * @param string $action
     * @param string $description
     * @param int|null $userId
     * @param array $metadata
     * @return bool
     */
    public function log(string $action, string $description, ?int $userId = null, array $metadata = []): bool
    {
        if ($userId === null) {
            $userId = $this->getCurrentUserId();
        }

        $data = [
            'user_id' => $userId,
            'action' => $action,
            'description' => $description,
            'ip_address' => $this->getIpAddress(),
            'user_agent' => $this->getUserAgent(),
            'metadata' => !empty($metadata) ? json_encode($metadata) : null,
            'created_at' => date('Y-m-d H:i:s')
        ];

        return $this->logModel->insert($data) !== false;
    }

    /**
     * Log CRUD operations
     * 
     * @param string $action CREATE|UPDATE|DELETE|READ
     * @param string $table
     * @param int|string $recordId
     * @param int|null $userId
     * @param array $changes
     * @return bool
     */
    public function logCRUD(string $action, string $table, $recordId, ?int $userId = null, array $changes = []): bool
    {
        $description = match($action) {
            'CREATE' => "Created new record in {$table} with ID: {$recordId}",
            'UPDATE' => "Updated record in {$table} with ID: {$recordId}",
            'DELETE' => "Deleted record from {$table} with ID: {$recordId}",
            'READ' => "Viewed record from {$table} with ID: {$recordId}",
            default => "{$action} operation on {$table} with ID: {$recordId}"
        };

        $metadata = [
            'table' => $table,
            'record_id' => $recordId,
            'operation' => $action,
            'changes' => $changes
        ];

        return $this->log('CRUD_' . $action, $description, $userId, $metadata);
    }

    /**
     * Log login attempt
     * 
     * @param int|null $userId
     * @param bool $success
     * @param string|null $nipNik
     * @param string|null $reason
     * @return bool
     */
    public function logLogin(?int $userId, bool $success, ?string $nipNik = null, ?string $reason = null): bool
    {
        $action = $success ? 'LOGIN_SUCCESS' : 'LOGIN_FAILED';
        
        $description = $success 
            ? "User successfully logged in" 
            : "Failed login attempt" . ($reason ? ": {$reason}" : "");

        if ($nipNik) {
            $description .= " (NIP/NIK: {$nipNik})";
        }

        $metadata = [
            'nip_nik' => $nipNik,
            'success' => $success,
            'reason' => $reason
        ];

        return $this->log($action, $description, $userId, $metadata);
    }

    /**
     * Log logout
     * 
     * @param int|null $userId
     * @return bool
     */
    public function logLogout(?int $userId = null): bool
    {
        if ($userId === null) {
            $userId = $this->getCurrentUserId();
        }

        return $this->log('LOGOUT', 'User logged out', $userId);
    }

    /**
     * Log resource access
     * 
     * @param int|null $userId
     * @param string $resource
     * @param string $action
     * @return bool
     */
    public function logAccess(?int $userId, string $resource, string $action = 'VIEW'): bool
    {
        $description = "Accessed {$resource}";
        
        if ($action !== 'VIEW') {
            $description .= " ({$action})";
        }

        $metadata = [
            'resource' => $resource,
            'action' => $action,
            'url' => $this->request->getUri()->getPath()
        ];

        return $this->log('RESOURCE_ACCESS', $description, $userId, $metadata);
    }

    /**
     * Log error
     * 
     * @param \Throwable|string $error
     * @param int|null $userId
     * @param array $context
     * @return bool
     */
    public function logError($error, ?int $userId = null, array $context = []): bool
    {
        if ($error instanceof \Throwable) {
            $description = $error->getMessage();
            $metadata = [
                'exception' => get_class($error),
                'file' => $error->getFile(),
                'line' => $error->getLine(),
                'trace' => $error->getTraceAsString(),
                'context' => $context
            ];
        } else {
            $description = (string) $error;
            $metadata = ['context' => $context];
        }

        // Also log to CodeIgniter's log
        log_message('error', $description);

        return $this->log('ERROR', $description, $userId, $metadata);
    }

    /**
     * Log security event
     * 
     * @param string $event
     * @param string $description
     * @param int|null $userId
     * @param string $severity 'low'|'medium'|'high'|'critical'
     * @return bool
     */
    public function logSecurity(string $event, string $description, ?int $userId = null, string $severity = 'medium'): bool
    {
        $metadata = [
            'security_event' => $event,
            'severity' => $severity,
            'ip' => $this->getIpAddress()
        ];

        return $this->log('SECURITY_' . strtoupper($event), $description, $userId, $metadata);
    }

    /**
     * Log API request
     * 
     * @param string $endpoint
     * @param string $method
     * @param int $statusCode
     * @param float $duration
     * @return bool
     */
    public function logAPI(string $endpoint, string $method, int $statusCode, float $duration): bool
    {
        $description = "{$method} {$endpoint} - {$statusCode} ({$duration}ms)";
        
        $metadata = [
            'endpoint' => $endpoint,
            'method' => $method,
            'status_code' => $statusCode,
            'duration_ms' => $duration,
            'request_body' => $this->request->getJSON(),
            'response_time' => date('Y-m-d H:i:s')
        ];

        return $this->log('API_REQUEST', $description, $this->getCurrentUserId(), $metadata);
    }

    /**
     * Log file upload
     * 
     * @param string $filename
     * @param string $type
     * @param int $size
     * @param int|null $userId
     * @return bool
     */
    public function logFileUpload(string $filename, string $type, int $size, ?int $userId = null): bool
    {
        $description = "Uploaded file: {$filename} ({$type}, " . format_file_size($size) . ")";
        
        $metadata = [
            'filename' => $filename,
            'type' => $type,
            'size' => $size
        ];

        return $this->log('FILE_UPLOAD', $description, $userId, $metadata);
    }

    /**
     * Log file deletion
     * 
     * @param string $filename
     * @param int|null $userId
     * @return bool
     */
    public function logFileDelete(string $filename, ?int $userId = null): bool
    {
        $description = "Deleted file: {$filename}";
        
        $metadata = ['filename' => $filename];

        return $this->log('FILE_DELETE', $description, $userId, $metadata);
    }

    /**
     * Log approval/rejection
     * 
     * @param string $entity 'program'|'kegiatan'|'sppd'
     * @param int $entityId
     * @param bool $approved
     * @param string|null $notes
     * @param int|null $userId
     * @return bool
     */
    public function logApproval(string $entity, int $entityId, bool $approved, ?string $notes = null, ?int $userId = null): bool
    {
        $action = $approved ? 'APPROVE' : 'REJECT';
        $status = $approved ? 'approved' : 'rejected';
        
        $description = ucfirst($status) . " {$entity} ID: {$entityId}";
        
        if ($notes) {
            $description .= " - Notes: {$notes}";
        }

        $metadata = [
            'entity' => $entity,
            'entity_id' => $entityId,
            'approved' => $approved,
            'notes' => $notes
        ];

        return $this->log($action . '_' . strtoupper($entity), $description, $userId, $metadata);
    }

    /**
     * Log data export
     * 
     * @param string $type
     * @param string $format
     * @param int $recordCount
     * @param int|null $userId
     * @return bool
     */
    public function logExport(string $type, string $format, int $recordCount, ?int $userId = null): bool
    {
        $description = "Exported {$recordCount} {$type} records to {$format}";
        
        $metadata = [
            'export_type' => $type,
            'format' => $format,
            'record_count' => $recordCount
        ];

        return $this->log('DATA_EXPORT', $description, $userId, $metadata);
    }

    /**
     * Log bulk operation
     * 
     * @param string $operation
     * @param string $entity
     * @param int $count
     * @param int|null $userId
     * @return bool
     */
    public function logBulk(string $operation, string $entity, int $count, ?int $userId = null): bool
    {
        $description = "Bulk {$operation} on {$count} {$entity} records";
        
        $metadata = [
            'operation' => $operation,
            'entity' => $entity,
            'count' => $count
        ];

        return $this->log('BULK_' . strtoupper($operation), $description, $userId, $metadata);
    }

    /**
     * Log notification sent
     * 
     * @param int $recipientId
     * @param string $type
     * @param string $title
     * @return bool
     */
    public function logNotification(int $recipientId, string $type, string $title): bool
    {
        $description = "Sent {$type} notification to user ID: {$recipientId} - {$title}";
        
        $metadata = [
            'recipient_id' => $recipientId,
            'notification_type' => $type,
            'title' => $title
        ];

        return $this->log('NOTIFICATION_SENT', $description, $this->getCurrentUserId(), $metadata);
    }

    /**
     * Get current user ID from session
     * 
     * @return int|null
     */
    protected function getCurrentUserId(): ?int
    {
        $userData = $this->session->get('user_data');
        return $userData['id'] ?? null;
    }

    /**
     * Get client IP address
     * 
     * @return string
     */
    public function getIpAddress(): string
    {
        $ipAddress = $this->request->getIPAddress();
        
        // Check for proxy
        if ($this->request->hasHeader('X-Forwarded-For')) {
            $ipAddress = $this->request->getHeaderLine('X-Forwarded-For');
        } elseif ($this->request->hasHeader('X-Real-IP')) {
            $ipAddress = $this->request->getHeaderLine('X-Real-IP');
        }

        return $ipAddress ?: '0.0.0.0';
    }

    /**
     * Get user agent string
     * 
     * @return string
     */
    public function getUserAgent(): string
    {
        return $this->request->getUserAgent()->__toString();
    }

    /**
     * Get recent logs for user
     * 
     * @param int $userId
     * @param int $limit
     * @return array
     */
    public function getRecentLogs(int $userId, int $limit = 20): array
    {
        return $this->logModel->where('user_id', $userId)
                              ->orderBy('created_at', 'DESC')
                              ->limit($limit)
                              ->findAll();
    }

    /**
     * Get logs by action type
     * 
     * @param string $action
     * @param int $limit
     * @return array
     */
    public function getLogsByAction(string $action, int $limit = 100): array
    {
        return $this->logModel->where('action', $action)
                              ->orderBy('created_at', 'DESC')
                              ->limit($limit)
                              ->findAll();
    }

    /**
     * Clean old logs (older than specified days)
     * 
     * @param int $days
     * @return int Number of deleted logs
     */
    public function cleanOldLogs(int $days = 365): int
    {
        $date = date('Y-m-d H:i:s', strtotime("-{$days} days"));
        
        $builder = $this->logModel->builder();
        $builder->where('created_at <', $date);
        
        $count = $builder->countAllResults(false);
        $builder->delete();
        
        $this->log('CLEAN_LOGS', "Cleaned {$count} logs older than {$days} days");
        
        return $count;
    }

    /**
     * Get statistics
     * 
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    public function getStatistics(string $startDate, string $endDate): array
    {
        $builder = $this->logModel->builder();
        
        $totalLogs = $builder->where('created_at >=', $startDate)
                            ->where('created_at <=', $endDate . ' 23:59:59')
                            ->countAllResults();

        $loginAttempts = $this->logModel->where('action', 'LOGIN_SUCCESS')
                                        ->orWhere('action', 'LOGIN_FAILED')
                                        ->where('created_at >=', $startDate)
                                        ->where('created_at <=', $endDate . ' 23:59:59')
                                        ->countAllResults();

        $errors = $this->logModel->where('action', 'ERROR')
                                 ->where('created_at >=', $startDate)
                                 ->where('created_at <=', $endDate . ' 23:59:59')
                                 ->countAllResults();

        $securityEvents = $this->logModel->like('action', 'SECURITY_', 'after')
                                         ->where('created_at >=', $startDate)
                                         ->where('created_at <=', $endDate . ' 23:59:59')
                                         ->countAllResults();

        return [
            'total_logs' => $totalLogs,
            'login_attempts' => $loginAttempts,
            'errors' => $errors,
            'security_events' => $securityEvents,
            'period' => [
                'start' => $startDate,
                'end' => $endDate
            ]
        ];
    }

    /**
     * Create instance with fluent interface
     * 
     * @return self
     */
    public static function create(): self
    {
        return new self();
    }
}