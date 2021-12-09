<?php
namespace Entities;

use Library\LibException;

/**
 *
 * @author Esaie MHS
 *        
 */
class Transfer extends Operation
{
    /**
     * @var Member
     */
    private $receiver;
    
    /**
     * @return \Entities\Member
     */
    public function getReceiver() : ?Member
    {
        return $this->receiver;
    }

    /**
     * @param \Entities\Member $receiver
     */
    public function setReceiver($receiver)
    {
        if ($this->isInt($receiver)) {
            $this->receiver = new Member(array('id'=>$receiver));
        }elseif ($receiver instanceof Member || $receiver == null){
            $this->receiver = $receiver;
        }else {
            throw new LibException("invalid param value");
        }
    }
    
    /**
     * @param Member $source
     */
    public function setSource ($source) : void {
        $this->setMember($source);
    }
    
    public function getSource () : ?Member{
        return $this->getMember();
    }

}

