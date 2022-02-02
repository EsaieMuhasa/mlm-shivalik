<?php
namespace Core\Shivalik\Managers;

use Core\Shivalik\Entities\Notification;
use PHPBackend\Dao\DefaultDAOInterface;
use PHPBackend\Dao\UtilitaireSQL;
use Core\Shivalik\Entities\Notifiable;
use Core\Shivalik\Entities\NotificationReceiver;

/**
 *
 * @author Esaie MUHASA
 *        
 */
abstract class NotificationReceiverDAOmanager extends DefaultDAOInterface
{
    /**
     * envoie d'une notification a une collection de composent notifiable
     * @param Notification $notification
     * @param Notifiable[] $notificables
     */
    public abstract function createItems (Notification $notification, array $notificables) : void;
    
    /**
     * verification des message
     * @param int $componentId
     * @param bool $received
     * @return bool
     */
    public function checkByNotifiable (int $componentId, bool $received = false, ?int $limit = null, int $offset = 0) : bool{
        return UtilitaireSQL::checkAll($this->getConnection(), $this->getTableName(), [
            'id' => $componentId,
            'received' => $received? '1' : '0'
        ], $limit, $offset);
    }
    
    /**
     * revoie une collection des notification d'un notificationcomponent
     * @param int $componentId
     * @param bool $received
     * @param int $limit
     * @param int $offset
     * @return NotificationReceiver[]
     */
    public function findByNotifiable (int $componentId, bool $received = false, ?int $limit = null, int $offset = 0) : array{
        return UtilitaireSQL::findAll($this->getConnection(), $this->getTableName(), $this->getMetadata()->getName(), self::FIELD_DATE_AJOUT, true, [
            'id' => $componentId,
            'received' => $received? '1' : '0'
        ], $limit, $offset);
    }
    
    
    /**
     * comptage des notifications d'un composent notifiable
     * @param int $componentId
     * @param bool $received
     * @return int
     */
    public function countNotifications (int $componentId, bool $received = false) : int {
        return UtilitaireSQL::count($this->getConnection(), $this->getTableName(), [
            'id' => $componentId,
            'received' => $received? '1' : '0'
        ]);
    }
}

