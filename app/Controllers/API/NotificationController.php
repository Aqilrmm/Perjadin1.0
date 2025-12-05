<?php

namespace App\Controllers\API;

use App\Controllers\BaseController;

// ========================================
// NOTIFICATION CONTROLLER
// ========================================

class NotificationController extends BaseController
{
    protected $notificationModel;

    public function __construct()
    {
        $this->notificationModel = new \App\Models\Notification\NotificationModel();
    }

    /**
     * Get user notifications
     */
    public function index()
    {
        $limit = $this->request->getGet('limit') ?: 10;
        $unreadOnly = $this->request->getGet('unread_only') == 'true';

        $notifications = $this->notificationModel->getByUser(user_id(), $limit, $unreadOnly);
        $unreadCount = $this->notificationModel->getUnreadCount(user_id());

        return $this->respondSuccess('Notifications retrieved', [
            'total' => count($notifications),
            'unread' => $unreadCount,
            'notifications' => $notifications,
        ]);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($id)
    {
        if ($this->notificationModel->markAsRead($id)) {
            return $this->respondSuccess('Notification marked as read');
        }

        return $this->respondError('Failed to mark as read', null, 500);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        if ($this->notificationModel->markAllAsRead(user_id())) {
            return $this->respondSuccess('All notifications marked as read');
        }

        return $this->respondError('Failed to mark all as read', null, 500);
    }

    /**
     * Delete notification
     */
    public function delete($id)
    {
        if ($this->notificationModel->deleteNotification($id, user_id())) {
            return $this->respondSuccess('Notification deleted');
        }

        return $this->respondError('Failed to delete notification', null, 500);
    }
}