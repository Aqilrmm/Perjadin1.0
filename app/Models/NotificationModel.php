<?php

// ========================================
// NOTIFICATION MODEL
// ========================================

namespace App\Models\Notification;

use App\Models\BaseModel;

class NotificationModel extends BaseModel
{
    protected $table = 'notifications';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'user_id', 'type', 'title', 'message', 'link', 'is_read', 'read_at'
    ];

    protected $useTimestamps = false;
    protected $useSoftDeletes = false;
    protected $createdField = 'created_at';

    /**
     * Get notifications by user
     */
    public function getByUser($userId, $limit = 10, $unreadOnly = false)
    {
        $builder = $this->where('user_id', $userId);
        
        if ($unreadOnly) {
            $builder->where('is_read', 0);
        }
        
        return $builder->orderBy('created_at', 'DESC')
                       ->limit($limit)
                       ->findAll();
    }

    /**
     * Get unread count
     */
    public function getUnreadCount($userId)
    {
        return $this->where('user_id', $userId)
                    ->where('is_read', 0)
                    ->countAllResults();
    }

    /**
     * Mark as read
     */
    public function markAsRead($notificationId)
    {
        return $this->update($notificationId, [
            'is_read' => 1,
            'read_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Mark all as read for user
     */
    public function markAllAsRead($userId)
    {
        return $this->where('user_id', $userId)
                    ->where('is_read', 0)
                    ->set(['is_read' => 1, 'read_at' => date('Y-m-d H:i:s')])
                    ->update();
    }

    /**
     * Delete notification
     */
    public function deleteNotification($notificationId, $userId)
    {
        return $this->where('id', $notificationId)
                    ->where('user_id', $userId)
                    ->delete();
    }

    /**
     * Delete all notifications for user
     */
    public function deleteAllForUser($userId)
    {
        return $this->where('user_id', $userId)->delete();
    }

    /**
     * Send notification
     */
    public function sendNotification($userId, $type, $title, $message, $link = null)
    {
        return $this->insert([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'link' => $link,
            'is_read' => 0
        ]);
    }

    /**
     * Send bulk notifications
     */
    public function sendBulkNotifications($userIds, $type, $title, $message, $link = null)
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
        
        return $this->insertBatch($data);
    }

    /**
     * Get recent notifications with pagination
     */
    public function getRecentPaginated($userId, $page = 1, $perPage = 20)
    {
        $builder = $this->where('user_id', $userId)
                        ->orderBy('created_at', 'DESC');
        
        return [
            'data' => $builder->paginate($perPage, 'default', $page),
            'pager' => $this->pager
        ];
    }

    /**
     * Clean old read notifications (older than X days)
     */
    public function cleanOldNotifications($days = 30)
    {
        $date = date('Y-m-d H:i:s', strtotime("-{$days} days"));
        return $this->where('is_read', 1)
                    ->where('created_at <', $date)
                    ->delete();
    }
}