<?php
namespace Core\Shivalik\Managers\Implementation;

use Core\Shivalik\Entities\NotifiableComponent;
use Core\Shivalik\Entities\Notification;
use Core\Shivalik\Entities\NotificationReceiver;
use Core\Shivalik\Managers\NotificationReceiverDAOmanager;
use PHPBackend\Dao\DAOEvent;
use PHPBackend\Dao\DAOException;
use PHPBackend\Dao\UtilitaireSQL;
use Core\Shivalik\Entities\Notifiable;
use PHPBackend\Dao\DefaultDAOInterface;

/**
 *
 * @author Esaie MUHASA
 *        
 */
class NotificationReceiverDAOManagerImplementation1 extends DefaultDAOInterface implements NotificationReceiverDAOmanager
{
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\NotificationReceiverDAOmanager::checkByNotifiable()
     */
    public function checkByNotifiable (int $componentId, bool $received = false, ?int $limit = null, int $offset = 0) : bool{
        return UtilitaireSQL::checkAll($this->getConnection(), $this->getTableName(), [
            'id' => $componentId,
            'received' => $received? '1' : '0'
        ], $limit, $offset);
    }
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\NotificationReceiverDAOmanager::findByNotifiable()
     */
    public function findByNotifiable (int $componentId, bool $received = false, ?int $limit = null, int $offset = 0) : array{
        return UtilitaireSQL::findAll($this->getConnection(), $this->getTableName(), $this->getMetadata()->getName(), self::FIELD_DATE_AJOUT, true, [
            'id' => $componentId,
            'received' => $received? '1' : '0'
        ], $limit, $offset);
    }
    
    
    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\NotificationReceiverDAOmanager::countNotifications()
     */
    public function countNotifications (int $componentId, bool $received = false) : int {
        return UtilitaireSQL::count($this->getConnection(), $this->getTableName(), [
            'id' => $componentId,
            'received' => $received? '1' : '0'
        ]);
    }

    /**
     * {@inheritDoc}
     * @see \Core\Shivalik\Managers\NotificationReceiverDAOmanager::createItems()
     */
    public function createItems(Notification $notification, array $notifiables): void
    {
        try {
            $pdo = $this->getConnection();
            if ($pdo->beginTransaction()) {
                
                $this->getManagerFactory()->getManagerOf(Notification::class)->createInTransaction($notification, $pdo);
                $receivers = [];
                
                /**
                 * @var Notifiable $notificable
                 */
                foreach ($notifiables as $notifiable) {
                    $receiver = new NotificationReceiver();
                    $component = new NotifiableComponent();
                    $component->setNotifiable($notifiable);
                    $receiver->setNotification($notification);
                    $receiver->setReceiver($component);
                    $this->createInTransaction($receiver, $pdo);
                    $receivers [] = $receiver;
                }
                $pdo->commit();
                
                foreach ($receivers as $rs) {
                    $event = new DAOEvent($this, DAOEvent::TYPE_CREATION, $rs);
                    $this->dispatchEvent($event);
                }                
            } else {
                throw new DAOException("an error occurred while starting the transaction");
            }
        } catch (\PDOException $e) {
            try {
                $pdo->rollBack();
            } catch (\PDOException $e) {}
            throw new DAOException("An error occurred during the transaction");
        }
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Dao\DAOInterface::createInTransaction()
     * @param NotificationReceiver $entity
     */
    public function createInTransaction($entity, \PDO $pdo): void
    {
        if ($entity->getNotification()->getId() == null || $entity->getNotification()->getId() <= 0) {
            $this->getDaoManager()->getManagerOf(Notification::class)->createInTransaction($entity->getNotification(), $pdo);
        }
        if ($entity->getReceiver()->getId() == null || $entity->getReceiver()->getId() <= 0) {
            $this->getDaoManager()->getManagerOf(NotifiableComponent::class)->createInTransaction($entity->getReceiver(), $pdo);
        }
        $id = UtilitaireSQL::insert($pdo, $this->getTableName(), [            
            "notification" => $entity->getNotification()->getId(),
            "receiver" => $entity->getReceiver()->getId(),
            self::FIELD_DATE_AJOUT => $entity->getFormatedDateAjout()
        ]);
        $entity->setId($id);
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Dao\DAOInterface::update()
     */
    public function update($entity, $id) : void
    {
        throw new DAOException("Updating operation is not support in this data access object manager");
    }

    
}

