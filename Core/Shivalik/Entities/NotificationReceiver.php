<?php
namespace Core\Shivalik\Entities;

use PHPBackend\DBEntity;
use PHPBackend\PHPBackendException;

/**
 *
 * @author Esaie MUHASA
 *        
 */
class NotificationReceiver extends DBEntity
{
    /**
     * @var Notification
     */
    private $notification;
    
    /**
     * @var NotifiableComponent
     */
    private $receiver;
    
    /**
     * est-ce que la notification est deja recu??
     * @var boolean
     */
    private $received;
    
    /**
     * @return Notification
     */
    public function getNotification() : ?Notification
    {
        return $this->notification;
    }

    /**
     * @return NotifiableComponent
     */
    public function getReceiver() : ?NotifiableComponent
    {
        return $this->receiver;
    }

    /**
     * @return boolean
     */
    public function isReceived() : ?bool
    {
        return $this->received;
    }

    /**
     * @param Notification $notification
     */
    public function setNotification($notification) : void
    {
        $this->notification = $notification;
    }

    /**
     * @param NotifiableComponent $receiver
     */
    public function setReceiver($receiver) : void
    {
        if ($receiver == null || $receiver instanceof NotifiableComponent) {
            $this->receiver = $receiver;
        }else if (self::isInt($receiver)) {
            $this->receiver = new NotifiableComponent(array('id' => $receiver));
        }else {
            throw new PHPBackendException("invalid argument exception");
        }
    }

    /**
     * @param boolean|int|string $received
     */
    public function setReceived($received) : void
    {
        $this->received = self::isTrue($received);
    }
    
    /**
     * generation d'une nouvelle notification
     * @param string $title
     * @param string $description
     * @param Notifiable $notificable
     * @return NotificationReceiver
     */
    public static function buildNotificationReceiver (string $title, string $description, Notifiable $notificable) : NotificationReceiver {
        $notification = new Notification();
        $notification->setTitle($title);
        $notification->setDescription($description);
        
        $receiver = new NotifiableComponent();
        $receiver->setNotifiable($notificable);
        
        $notificationReceiver = new NotificationReceiver();
        $notificationReceiver->setNotification($notification);
        $notificationReceiver->setReceiver($receiver);
        
        return $notificationReceiver;
    }

}

