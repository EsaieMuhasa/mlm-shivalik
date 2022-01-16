<?php
namespace Core\Shivalik\Managers\Implementation;

use Core\Shivalik\Entities\NotifiableComponent;
use Core\Shivalik\Entities\Notification;
use Core\Shivalik\Entities\NotificationReceiver;
use Core\Shivalik\Managers\NotificationReceiverDAOmanager;
use PHPBackend\Dao\DAOEvent;
use PHPBackend\Dao\DAOException;
use PHPBackend\Dao\UtilitaireSQL;

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
     * @param NotificationReceiver $entity
     */
    public function createItems(Notification $notification, array $components): void
    {
        try {
            $pdo = $this->getConnection();
            if ($pdo->beginTransaction()) {
                
                $this->getManagerFactory()->getManagerOf(Notification::class)->createInTransaction($notification, $pdo);
                /**
                 * @var NotifiableComponent $component
                 */
                foreach ($components as $component) {
                    $receiver = new NotificationReceiver();
                    $receiver->setNotification($notification);
                    $receiver->setReceiver($component);
                    $this->createInTransaction($receiver, $pdo);
                }
                $this->pdo->commit();
                
                foreach ($component as $component) {
                    $event = new DAOEvent($this, DAOEvent::TYPE_CREATION, $component);
                    $this->dispatchEvent($event);
                }
                
            } else {
                throw new DAOException("an error occurred while starting the transaction");
            }
        } catch (\PDOException $e) {
            try {
                $this->pdo->rollBack();
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

