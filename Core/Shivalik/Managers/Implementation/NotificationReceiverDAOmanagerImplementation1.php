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

/**
 *
 * @author Esaie MUHASA
 *        
 */
class NotificationReceiverDAOmanagerImplementation1 extends NotificationReceiverDAOmanager
{

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
        $this->getDaoManager()->getManagerOf(Notification::class)->createInTransaction($entity->getNotification(), $pdo);
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
    public function update($entity, $id)
    {
        throw new DAOException("Updating operation is not support in this data access object manager");
    }

    
}

