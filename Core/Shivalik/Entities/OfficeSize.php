<?php
namespace Core\Shivalik\Entities;

use PHPBackend\DBEntity;
use PHPBackend\PHPBackendException;

/**
 *
 * @author Esaie MHS
 *        
 */
class OfficeSize extends DBEntity
{
    /**
     * @var Size
     */
    private $size;
    
    /**
     * @var Office
     */
    private $office;
    
    /**
     * @var \DateTime
     */
    private $initDate;
    
    /**
     * @var \DateTime
     */
    private $closeDate;
    
    /**
     * @var OfficeSize
     */
    private $old;
    
    /**
     * @return Size
     */
    public function getSize() : ?Size
    {
        return $this->size;
    }

    /**
     * @return Office
     */
    public function getOffice() : ?Office
    {
        return $this->office;
    }

    /**
     * @return \DateTime
     */
    public function getInitDate() : ?\DateTime
    {
        return $this->initDate;
    }

    /**
     * @return \DateTime
     */
    public function getCloseDate() : ?\DateTime
    {
        return $this->closeDate;
    }

    /**
     * @return OfficeSize
     */
    public function getOld() : ?OfficeSize
    {
        return $this->old;
    }

    /**
     * @param Size $size
     */
    public function setSize($size) : void
    {
    	if ($size == null || $size instanceof Size) {
	        $this->size = $size;
    	}else if ($this->isInt($size)) {
    		$this->size = new Size(array('id' => $size));
    	}else {
    		throw new PHPBackendException("invalid value in method setSize() parameter");
    	}
    }

    /**
     * @param Office $office
     */
    public function setOffice($office) : void
    {
        $this->office = $office;
    }

    /**
     * @param \DateTime $initDate
     */
    public function setInitDate($initDate) : void
    {
        $this->initDate = $initDate;
    }

    /**
     * @param \DateTime $closeDate
     */
    public function setCloseDate($closeDate) : void
    {
        $this->closeDate = $closeDate;
    }

    /**
     * @param OfficeSize $old
     */
    public function setOld($old): void
    {
        if ($old == null || $old instanceof OfficeSize) {
            $this->old = $old;
        }else if($this->isInt($old)){            
            $this->old = new OfficeSize(array('id' => $old));
        } else {
            throw new PHPBackendException("invalid value param in setOld method");
        }
    }

}

