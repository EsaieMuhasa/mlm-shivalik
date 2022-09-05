<?php
namespace Core\Shivalik\Entities;

use PHPBackend\DBEntity;
use PHPBackend\PHPBackendException;

/**
 *
 * @author Esaie MHS
 *        
 */
class Localisation extends DBEntity
{
    /**
     * @var Country
     */
    private $country;
    
    /**
     * @var string
     */
    private $city;
    
    /**
     * @var string
     */
    private $district;
    
    
    /**
     * @return Country
     */
    public function getCountry() : ?Country
    {
        return $this->country;
    }

    /**
     * @return string
     */
    public function getCity() : ?string
    {
        return $this->city;
    }

    /**
     * @return string
     */
    public function getDistrict() : ?string
    {
        return $this->district;
    }

    /**
     * @param Country $country
     */
    public function setCountry($country) : void
    {
        if ($country==null || $country instanceof Country) {
            $this->country = $country;
        }else if ($this->isInt($country)) {
            $this->country=new Country(array('id' => $country));
        }else {
            throw new PHPBackendException("invalid value in setCountry() method param");
        }
            
    }

    /**
     * @param string $city
     */
    public function setCity($city) : void
    {
        $this->city = $city;
    }

    /**
     * @param string $district
     */
    public function setDistrict($district) : void
    {
        $this->district = $district;
    }
    
    
    public function __toString() : string {
    	return (($this->district!=null? "{$this->district},":'')." {$this->city}, {$this->country->getAbbreviation()}");
    }

}

