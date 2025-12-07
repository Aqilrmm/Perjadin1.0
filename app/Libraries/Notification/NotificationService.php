<?php

namespace App\Libraries\Notification;

use App\Models\Notification\NotificationModel;

/**
 * Notification Service
 * 
 * Handles all notification operations for the application
 */
class NotificationService
{
    protected $notificationModel;

    public function __construct()
    {
        $this->notificationModel = new NotificationModel();
    }

    /**
     * Send notification to single user
     * 
     * @param int $userId
     * @param string $type (approval|rejection|submission|verification|info|warning|reminder)
     * @param string $title
     * @param string $message
     * @param string|null $link
     * @return bool
     */
    public function send(int $userId, string $type, string $title, string $message, ?string $link = null): bool
    {
        return $this->notificationModel->insert([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'link' => $link,
            'is_read' => 0,
            'created_at' => date('Y-m-d H:i:s')
        ]) !== false;
    }

    /**
     * Send notification to multiple users
     * 
     * @param array $userIds
     * @param string $type
     * @param string $title
     * @param string $message
     * @param string|null $link
     * @return bool
     */
    public function sendBulk(array $userIds, string $type, string $title, string $message, ?string $link = null): bool
    {
        $data = [];
        foreach ($userIds as $userId) {
            $data[] = [
                'user_id' => $userId,
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'link' => $link,
                'is_read' => 0,
                'created_at' => date('Y-m-d H:i:s')
            ];
        }
        
        return $this->notificationModel->insertBatch($data) !== false;
    }

    /**
     * Send notification to all users with specific role
     * 
     * @param string $role
     * @param string $type
     * @param string $title
     * @param string $message
     * @param string|null $link
     * @return bool
     */
    public function sendToRole(string $role, string $type, string $title, string $message, ?string $link = null): bool
    {
        $userModel = new \App\Models\User\UserModel();
        $users = $userModel->where('role', $role)
                          ->where('is_active', 1)
                          ->where('is_blocked', 0)
                          ->where('deleted_at', null)
                          ->findAll();
        
        $userIds = array_column($users, 'id');
        
        return $this->sendBulk($userIds, $type, $title, $message, $link);
    }

    /**
     * Mark notification as read
     * 
     * @param int $notificationId
     * @return bool
     */
    public function markAsRead(int $notificationId): bool
    {
        return $this->notificationModel->update($notificationId, [
            'is_read' => 1,
            'read_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Mark all user's notifications as read
     * 
     * @param int $userId
     * @return bool
     */
    public function markAllAsRead(int $userId): bool
    {
        return $this->notificationModel->where('user_id', $userId)
                                       ->where('is_read', 0)
                                       ->set([
                                           'is_read' => 1,
                                           'read_at' => date('Y-m-d H:i:s')
                                       ])
                                       ->update();
    }

    /**
     * Get unread notification count for user
     * 
     * @param int $userId
     * @return int
     */
    public function getUnreadCount(int $userId): int
    {
        return $this->notificationModel->where('user_id', $userId)
                                       ->where('is_read', 0)
                                       ->countAllResults();
    }

    /**
     * Delete notification
     * 
     * @param int $notificationId
     * @return bool
     */
    public function delete(int $notificationId): bool
    {
        return $this->notificationModel->delete($notificationId);
    }

    /**
     * Clean old notifications (older than specified days)
     * 
     * @param int $days
     * @return int Number of deleted notifications
     */
    public function clean(int $days = 30): int
    {
        $date = date('Y-m-d H:i:s', strtotime("-{$days} days"));
        
        $builder = $this->notificationModel->builder();
        $builder->where('is_read', 1)
                ->where('created_at <', $date);
        
        $count = $builder->countAllResults(false);
        $builder->delete();
        
        return $count;
    }

    /**
     * Get user notifications
     * 
     * @param int $userId
     * @param int $limit
     * @param bool $unreadOnly
     * @return array
     */
    public function getUserNotifications(int $userId, int $limit = 10, bool $unreadOnly = false): array
    {
        $builder = $this->notificationModel->where('user_id', $userId);
        
        if ($unreadOnly) {
            $builder->where('is_read', 0);
        }
        
        return $builder->orderBy('created_at', 'DESC')
                       ->limit($limit)
                       ->findAll();
    }

    /**
     * Get paginated notifications
     * 
     * @param int $userId
     * @param int $page
     * @param int $perPage
     * @return array
     */
    public function getPaginated(int $userId, int $page = 1, int $perPage = 20): array
    {
        $builder = $this->notificationModel->where('user_id', $userId)
                                           ->orderBy('created_at', 'DESC');
        
        return [
            'data' => $builder->paginate($perPage, 'default', $page),
            'pager' => $this->notificationModel->pager
        ];
    }

    /**
     * Delete all notifications for user
     * 
     * @param int $userId
     * @return bool
     */
    public function deleteAllForUser(int $userId): bool
    {
        return $this->notificationModel->where('user_id', $userId)->delete();
    }
}