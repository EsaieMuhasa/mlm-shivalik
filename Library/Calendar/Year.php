<?php
namespace Library\Calendar;

use Library\LibException;

/**
 *
 * @author Esaie MHS
 *        
 */
class Year
{
    
    private $months = array();
    
    /**
     * @var int
     */
    private $year;
    
    /**
     * Configuration du pemier jour de la semaine
     * @var int
     */
    private $firstDayOfWeek;
    
    
    /**
     * Collection des mois deja selectionner
     * @var Month[]
     */
    private $selectedMonths = array();

    /**
     * @param int $year
     * @param int $fistDayOfWeek
     */
    public function __construct(?int $year=null, int $firstDayOfWeek = Month::FIRST_DAY_MONDAY)
    {
        if($year === null){
            $year = intval((new \DateTime())->format('Y'), 10);
        }
        
        $this->year = $year;
        $this->firstDayOfWeek = $firstDayOfWeek;
        
        for ($i = 1; $i <= 12; $i++) {
            $this->months[] = new Month($i, $this->year);
        }
		
    }
    
    
    /**
     * Revoie l'annee actuel, selon la configuration du serveur
     * @return Year
     */
    public static function getCurrentYear() : Year{
        return new Year();
    }
    
    /**
     * Recuperation du mois x de l'annee
     * @param int $index l'index doit etre une valeur compris entre 1 et 12
     * @throws LibException
     * @return Month
     */
    public function get(int $index) : Month {
        if ($index < 1 || $index > 12) {
            throw new LibException('L\'index du mois doit etre une valeur compise entre 1 et 12');
        }
        
        return $this->months[$index-1];
    }
    
    /**
     * Renvoie l'annee suivante
     * @return Year
     */
    public function nextYear() : Year {
        return new Year($this->year+1, $this->firstDayOfWeek);
    }
    
    /**
     * Renveoi l'annee precedante
     * @return Year
     */
    public function previousYear () : Year {
        return new Year($this->year-1, $this->firstDayOfWeek);
    }
    
    /**
     * @return string
     */
    public function __toString() : string {
        return "{$this->year}";
    }
    
    /**
     * Aliace de la fonction __toString(): string
     * @return string
     */
    public function toString () : string {
        return $this->__toString();
    }
    
    /**
     * renvoie la collection des mois deja selectionner
     * @return multitype:\Utilitaires\Calendar\Month 
     */
    public function getSelectedMonths() : array
    {
        return $this->selectedMonths;
    }
    
    /**
     * raze les contenuer de la collection des mois deja selectionner
     */
    public function clearSelectedMonths () : void {
        $this->selectedMonths = array();
    }
    
    
    /**
     * Ajout d'un mois parmis les mois deja selectionner
     * @param int $index un nombre compris entre 1 et 12
     */
    public function addSelectedMonth (int $index) : void {
        if ($index < 1 || $index > 12) {
            throw new LibException("Index invalide en paranetre de la methode addSelectedMonth() : {$index}");
        }
        
        if (!$this->isSelectedMonth($index)) {
            $this->selectedMonths[] = $this->months[$index-1];;
        }
    }
    
    
    /**
     * Retire le mois de la collection des mois deja selectionner
     * @param int $index
     */
    public function removeSelectedMonth(int $index) : void {
        if ($index < 1 || $index > 12) {
            throw new LibException("Index invalide en paranetre de la methode removeSelectedMonth() : {$index}");
        }
        
        if ($this->isSelectedMonth($index)) {
            foreach ($this->selectedMonths as $key => $month) {
                if ($month->getMonth() == $index) {
                    unset($this->selectedMonths[$key]);
                    break;
                }
            }
        }
    }
    
    /**
     * On verfie si le mois est deja selectionner
     * @param int $index
     * @return bool
     */
    public function isSelectedMonth (int $index) : bool{
        foreach ($this->selectedMonths as $month) {
            if ($month->getMonth() == $index) {
                return true;
            }
        }
        
        return false;        
    }
    
    /**
     * Revoie le premier mois selectionner
     * @return Month|NULL
     */
    public function getFirstSelectedMonth () : ?Month {
        if (empty($this->selectedMonths)) {
            return null;
        }
        return $this->selectedMonths[array_key_first($this->selectedMonths)];
    }

    /**
     * 
     * @return string
     */
    public function toXML () : string {
        $xml = '<year name="'.$this.'">';
        foreach ($this->months as $month) {
            $xml .= $month->toXML();
        }
        $xml .= '</year>';
        
        return $xml;
    }
    
    /**
     *
     * @return string
     */
    public function toJSON () : string {
        $json = '{';
        foreach ($this->months as $key => $month) {
            $json .= '"'.$key.'" : '.$month->toJSON().($key >= (count($this->months)-1)? '':', ');
        }
        $json .= '}';
        
        return $json;
    }
}

