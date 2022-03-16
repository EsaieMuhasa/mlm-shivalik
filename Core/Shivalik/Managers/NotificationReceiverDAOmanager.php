<?php
namespace Core\Shivalik\Managers;

use Core\Shivalik\Entities\Notification;
use PHPBackend\Dao\DAOInterface;
use Core\Shivalik\Entities\NotificationReceiver;

/**
 *
 * @author Esaie MUHASA
 *        
 */
interface  NotificationReceiverDAOmanager extends DAOInterface
{
    /**
     * envoie d'une notification a une collection de composent notifiable
     * @param Notification $notification
     * @param Notifiable [] $notificables
     */
    public function createItems (Notification $notification, array $notificables) : void;
    
    /**
     * verification des message
     * @param int $componentId
     * @param bool $received
     * @return bool
     */
    public function checkByNotifiable (int $componentId, bool $received = false, ?int $limit = null, int $offset = 0) : bool;
    
    /**
     * revoie une collection des notification d'un notificationcomponent
     * @param int $componentId
     * @param bool $received
     * @param int $limit
     * @param int $offset
     * @return NotificationReceiver[]
     */
    public function findByNotifiable (int $componentId, bool $received = false, ?int $limit = null, int $offset = 0) : array;
    
    
    /**
     * comptage des notifications d'un composent notifiable
     * @param int $componentId
     * @param bool $received
     * @return int
     */
    public function countNotifications (int $componentId, bool $received = false) : int ;
}

