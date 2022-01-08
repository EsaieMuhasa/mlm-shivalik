<?php
namespace PHPBackend\Calendar;



use PHPBackend\PHPBackendException;

/**
 * Calendrier d'un mois d'un annee
 * @author Esaie MHS
 * @tutorial cette classe vous facilite la manipulation des calendrier.
 * Il est totalement configurable
 */
class Month
{
    /**
     * Pour considerer le premier jour de la semain come le dimenche
     * @var integer
     */
    const FIRST_DAY_SUNDAY = 1;
    
    /**
     * Pour considerer le premier jour de la semaine comme le lundi
     * @var integer
     */
    const FIRST_DAY_MONDAY = 2;
    
    const LOCAL_FR = "fr";
    const LOCAL_EN = "en";
    
    /**
     * Collection des jours de la semaine.
     * Le premier jour de la semaine est le dimenche
     * @var array
     */
    private $days_1 =  [ 'Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
    private $days_1_2 =  [ 'di', 'lu', 'ma', 'me', 'je', 've', 'sa'];
    
    private $days_1_EN =  [ 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thusday', 'Friday', 'Saturday'];
    private $days_1_2_EN =  [ 'Sun', 'Mon', 'Tue', 'Wed', 'Thurs', 'Fri', 'Sat'];
    
    /**
     * Collection des jours de la semaine
     * Le premier jours de la semaine est le lundi
     * @var array
     */
    private $days_2 =  ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimenche'];
    private $days_2_2 =  ['lu', 'ma', 'me', 'je', 've', 'sa', 'di'];
    
    private $days_2_EN =  ['Monday', 'Tuesday', 'Wednesday', 'Thusday', 'Friday', 'Saturday', 'Sunday'];
    private $days_2_2_EN =  ['Mon', 'Tue', 'Wed', 'Thurs', 'Fri', 'Sat', 'Sun'];
    
    /**
     * Collection des mois des l'annees
     * @var array
     */
    const MONTHS_NAMES = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
    const MONTHS_SHORT_NAMES = ['janv', 'févr', 'mars', 'avr', 'mai', 'juin', 'juil', 'août', 'sept', 'oct', 'nov', 'déc'];

    const MONTHS_NAMES_EN = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    const MONTHS_SHORT_NAMES_EN = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sept', 'Oct', 'Nov', 'Dec'];
    /**
     * @var int
     */
    private $year;
    
    /**
     * @var int
     */
    private $month;
    
    /**
     * Lague du calendrier
     * @var string
     */
    private $local;
    
    /*
     * Collection des date selectionner
     * @var \DateTime[]
     */
    private $selectedDates = array();
    
    /**
     * collection des dates pour lequels les evenements existe
     * @var \DateTime[]
     */
    private $eventDates = array();
    
    /**
     * Pour conserver le nombre des mois
     * @var int
     */
    private $weeksNumber=0;
    
    /**
     * @var string
     */
    private $firstDayOfWeek;
    
    //variable pour l'iteration des jours du mois.
    //(manipulation du curseur)
    //========================================================
    
    /**
     * la semaine encours d'iteration
     * doit varier en 0 et (nombre de semaine -1)
     * par defaut le curseur est a -1
     * @var integer
     */
    private $currentWeek = -1;
    
    /**
     * Le numero du jours de la semaine encours d'iteration
     * @var int
     */
    private $currentDay = 0;
    
    /**
     * constructeur d'initialisation d'un moi du calendier
     * @param int $month le mois de l'un. une valeur comprise entre 1 et 12
     * @param int $year l'annee, une valeur superieur à 1970
     * @param int $firstDayOfWeek pour indiquer le premier jours de la semaine
     * @param string $local langue
     * @throws PHPBackendException
     */
    public function __construct(?int $month=null, ?int $year=null, ?int $firstDayOfWeek = null, ?string $local = null){
        
        if ($month===null) {
            $month = intval(date('m'), 10);
        }
        
        if ($year === null) {
            $year = intval(date('Y'), 10);
        }
        
        if ($local == null) {
            $local = self::LOCAL_FR;
        }
        
        if ($firstDayOfWeek==null || ($firstDayOfWeek != self::FIRST_DAY_MONDAY && $firstDayOfWeek != self::FIRST_DAY_SUNDAY)) {
            $this->firstDayOfWeek = self::FIRST_DAY_SUNDAY;
        }else{
            $this->firstDayOfWeek = $firstDayOfWeek;
        }
        
        if ($month<1 || $month > 12 ) {
            throw new PHPBackendException('=> '.$month.' <= Le mois doit etre une valeur compise entre 1 et 12');
        }
        
        if ($year < 1970){
            throw new PHPBackendException('=> '.$month.' <= L\'année doit être une valeur supérieur à 19970');
        }
        
        $this->year = $year;
        $this->month = $month;
        $this->maxDays = $this->getWeeks() * 7;
        $this->setLocal($local);
    }
    
    /**
     * @return string
     */
    public function getLocal() : string
    {
        return $this->local;
    }

    /**
     * @param string $local
     */
    public function setLocal(?string $local) : void
    {
        $this->local = ($local == null ? self::LOCAL_FR : $local);
    }

    /**
     * comptage du nombre des date entre les deux instances des dates en parametre
     * @param \DateTime $dateMin
     * @param \DateTime $dateMax
     * @return int
     */
    public static function countDates (\DateTime $dateMin, \DateTime $dateMax) : int{
        /**
         * @var \DateTime $date
         */
        $date = clone $dateMin;
        $nombre = 1;
        do{
            $date->modify('+1 days');
            $nombre++;
        } while ($date->format('d-m-Y') != $dateMax->format('d-m-Y'));
        
        return $nombre;
    }
    
    /**
     * Retourne le mois en toute letre. Ex: Mai 2021
     * @return string
     */
    public function toString () : string{
        return ($this->local==self::LOCAL_FR? self::MONTHS_NAMES[$this->month-1] : self::MONTHS_NAMES_EN[$this->month-1]).' '.$this->year;
    }
    
    /**
     * Revoie le nom du mois
     * @return string
     */
    public function getName() : string {
        return ($this->local==self::LOCAL_FR? self::MONTHS_NAMES[$this->month-1] : self::MONTHS_NAMES_EN[$this->month-1]);
    }
    
    /**
     * Revoie le nom court du mois
     * @return string
     */
    public function getShortName() : string {
        return ($this->local==self::LOCAL_FR? self::MONTHS_SHORT_NAMES[$this->month-1] : self::MONTHS_SHORT_NAMES_EN[$this->month-1]);
    }
    
    /**
     * @return string
     */
    public function __toString() :string {
        return $this->toString();
    }
    
    
    /**
     * Recuperation du premier jour de la premiere semaine du mois.
     * cela depend de la configuration du premier jours de la semain 
     * @return \DateTime
     */
    public function getFirstDayOfFirstWeek() : \DateTime{
        $first = null;
        if ($this->firstDayOfWeek == self::FIRST_DAY_SUNDAY) {
            $first = $this->getFirstSunday();
        }else{
            $first = $this->getFirstMonday();
        }
        
        //correction de certains incoherences
        $f = $this->getFirstDay();
        $fw = $f->format('w');
        $firstw = $first->format('w');
        
        if ($fw === $firstw) {
            return $f;
        }
        return $first;
    }
    
    
    /**
     * Recuperation du dernier jour de la dernier semaine du mois
     * @return \DateTime
     */
    public function getLastDayOfLastWeek() : \DateTime {
        $last = null;
        if ($this->firstDayOfWeek == self::FIRST_DAY_SUNDAY) {
            $last = $this->getLastSaturday();
        }else{
            $last = $this->getLastSunday();
        }
        
        //correction de certains incoherences dans le cas ou le denier jours de la dernier semaine est le denier jours du mois
        $l = $this->getLastDay();
        $lw = $l->format('w');
        $lastw = $last->format('w');
        
        if ($lw === $lastw) {
            return $l;
        }
        return $last;
    }
    
    /**
     * Recuperation du premier lundi de la premiere semaine du mois
     * @return \DateTime
     */
    public function getFirstMonday() : \DateTime{
        return $this->getFirstDay()->modify('last monday');
    }
    
    /**
     * Reduperation du premier dimenche de lapremier semaine du mois
     * @return \DateTime
     */
    public function getFirstSunday() : \DateTime{
        return $this->getFirstDay()->modify('last sunday');
    }
    
    
    /**
     * Revoie le premier jour du mois
     * @return \DateTime
     */
    public function getFirstDay () : \DateTime {   
        $date = new \DateTime("{$this->year}-{$this->month}-01");
        return $date;
    }
    
    
    /**
     * Recuperation du dernier sabbat de la dernier semaine du mois
     * @return \DateTime
     */
    public function getLastSaturday() : \DateTime{
        return $this->getLastDay()->modify('last saturday +7 day');
    }
    
    
    /**
     * Recuperation u denier dimanche de la derniere semaine du mois
     * @return \DateTime
     */
    public function getLastSunday() : \DateTime{
        return $this->getLastDay()->modify('last sunday +7 day');
    }
    
    /**
     * Renvoie le dernier jour du mois
     * @return \DateTime
     */
    public function getLastDay () : \DateTime {
        return $this->getFirstDay()->modify('+1 month -1 day');
    }
    
    /**
     * Recuperation de la liste de nom des jours de la semaine
     * L'aragment de la liste depend de la configuration du premier jours d ela semaine
     * @return array
     */
    public function getDaysName() : array{
        if($this->firstDayOfWeek == self::FIRST_DAY_SUNDAY){
            return $this->local == self::LOCAL_FR? $this->days_1 : $this->days_1_EN;
        }
        return $this->local == self::LOCAL_FR? $this->days_2 : $this->days_2_EN;
    }
    
    /**
     * Revoie un tableau des abreviations des noms de jours de la semaine 
     * @return array
     */
    public function getShortDaysName () : array{
        if($this->firstDayOfWeek == self::FIRST_DAY_SUNDAY){
            return ($this->local == self::LOCAL_FR? $this->days_1_2 : $this->days_1_2_EN);
        }
        return ($this->local == self::LOCAL_FR? $this->days_2_2 : $this->days_2_2_EN);
    }
    
    
    /**
     * recuperation du nombre des semaine d'un moi
     * @return int
     */
    public function getWeeks () : int { 
        
        if ($this->weeksNumber==0) {
            $start = $this->getFirstDay();
            $end = $this->getLastDay();
            $weeks = intval($end->format('W')) - intval($start->format('W'));
            if ($weeks < 0) {
                $weeks =intval($end->format('W'));
            }            
            $this->weeksNumber = $weeks + 2;
        }
        
        return $this->weeksNumber;
    }
    
    /**
     * Renvoie le mois suivant
     * @return Month
     */
    public function nextMonth () : Month{
        $month = $this->month + 1;
        $year = $this->year;
        
        if ($month>12) {
            $month = 1;
            $year++;
        }
        
        return new Month($month, $year, $this->firstDayOfWeek);
    }
    
    /**
     * Renvoie le mois precedent
     * @return Month
     */
    public function previousMonth() : Month{
        $month = $this->month - 1;
        $year = $this->year;

        if ($month<1) {
            $month = 12;
            $year--;
        }
        
        return new Month($month, $year, $this->firstDayOfWeek);
    }
    
    
    //Pour l'iteration des jours du mois
    
    /**
     * Pour savoir s'il possible d'acceder au jours suivant de la semain du mois encours
     * @return bool
     */
    public function hasNextDay() : bool{
        return ($this->currentDay + 1) < 8;
    }
    
    /**
     * y a-il une semaine suivante??
     * @return bool
     */
    public function hasNextWeek () : bool{
        return (($this->currentWeek+1) < $this->getWeeks());
    }
    
    /**
     * Remise en zero du curseur d'iteration des jours du mois
     */
    public function resetCursor () : void{
        $this->currentDay = 0;
        $this->currentWeek = -1;
        $this->currentDate = null;
    }
    
    /**
     * Deplacement du curseur a la semaine suivante
     * @throws PHPBackendException
     * @return int
     */
    public function nextWeek() : int{
        if ($this->hasNextWeek()) {
            $this->currentWeek++;
            //avance d'un cran, puis on remet les jours a zero
            $this->currentDay=0;
            return $this->currentWeek;
        }
        throw new PHPBackendException('Impossible de de faire avancer le curseur sur la semaine suivante');
    }
    
    /**
     * Recuperation du jours suivant du mois.
     * Si le cursor a deja attein la fin, alors une exception sera lever
     * @throws PHPBackendException
     * @return \DateTime
     */
    public function nextDay () : \DateTime{
        $date = $this->getFirstDayOfFirstWeek()->modify(($this->currentDay + ($this->currentWeek*7)).' days');
        $this->currentDay++;
        return $date;
    }
    
    /**
     * Cette date est-elle le dernier jours de la semaine
     * @param \DateTime $date
     * @return bool
     */
    public function isLastDayOfWeek(\DateTime $date) : bool{
        if ($this->firstDayOfWeek == self::FIRST_DAY_SUNDAY) {
            if ($date->format('w') == '6') {
                return true;
            }
        }else {
            if ($date->format('w') == '0') {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Revoie le non du jours de la semaine en toute letres
     * @param \DateTime $date
     * @return string
     */
    public function getDayName (\DateTime $date) : string {
        $index = 0;
        if ($this->firstDayOfWeek == self::FIRST_DAY_SUNDAY) {
            $index = intval($date->format('w'));
        }else {
            $index = intval($date->format('N'))-1;
        }
        return $this->getDaysName()[$index];
    }
    
    /**
     * Revoie le nom courte du jours de la semaine
     * @param \DateTime $date
     * @return string
     */
    public function getShortDayName (\DateTime $date) : string {
        $index = 0;
        if ($this->firstDayOfWeek == self::FIRST_DAY_SUNDAY) {
            $index = intval($date->format('w'));
        }else {
            $index = intval($date->format('N'))-1;
        }
        return $this->getShortDaysName()[$index];
    }
    
    /**
     * Verification si la date fait partie des jours du mois.
     * Ext-ce que le jour fais partien des jours du mois encours?
     * @tutorial cela est utilise pour eviter des incoherence dans pour les jours des semaines du mois
     * mains qui ne font pas partie des jours proprement dite du mois
     * Ex: il se peut qui le premier jours du mois commence un certain jeudi.
     * alors, lors de l'iteration on comencera toujours par le lund, soit le dimenche de la semaine selon la configuration
     * mains du lundi/dimenche au mercredi, ces jours ne font partien des jours du mois
     * @param \DateTime $date
     * @return bool
     */
    public function inMonth(\DateTime $date)  : bool{
        return $this->getFirstDay()->format('Y-m') === $date->format('Y-m');
    }
    
    /**
     * Pour savoir si la date correspond a la date actuel du serveur
     * @param \DateTime $date
     * @return bool
     */
    public function isToday(\DateTime $date) : bool{
        return $date->format('Y-m-d') === (new \DateTime())->format('Y-m-d');
    }
    
    /**
     * Recuperation de la date actuel
     * @return \DateTime
     */
    public function getToday() : \DateTime{
        return new \DateTime();
    }
    
    /**
     * @return number
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * @return number
     */
    public function getMonth()
    {
        return $this->month;
    }
    
    /**
     * @return \DateTime[]
     */
    public function getSelectedDates() : array {
        return $this->selectedDates;
    }
    
    /**
     * Revoie la premiere date des dates selectionner
     * @return \DateTime|NULL
     */
    public function getFirstSelectedDate () : ?\DateTime {
        foreach ($this->selectedDates as $date) {
            return $date;
        }
        return null;
    }
    
    /**
     * La date est-elle selectionner???
     * @param \DateTime $date
     * @return bool
     */
    public function isSelectedDate(\DateTime $date) : bool{
        foreach ($this->selectedDates as $dt) {
            if ($dt->format('Y-m-d') === $date->format('Y-m-d')) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * y-a-il des aumoin une date selectionner??
     * @return bool
     */
    public function hasSelectedDate () : bool{
        return !empty($this->selectedDates);
    }
    
    /**
     * Ajout d'une date a la collection des date selectionner
     * @param \DateTime $date
     */
    public function addSelectedDate(\DateTime $date) : void{
        if (!$this->isSelectedDate($date) && $this->getFirstDay()->format('Y-m')===$date->format('Y-m')) {
            $this->selectedDates[] = clone $date;
        }
    }
    
    /**
     * Supression d'un date de la collection des dates selectionner
     * @param \DateTime $date
     */
    public function removeSelectedDate(\DateTime $date) : void{
        $this->popSelectedDate($date);
    }
    
    /***
     * On retire un date de la liste de date selectionner
     * @param \DateTime $date
     * @return \DateTime|NULL
     */
    public function popSelectedDate(\DateTime $date) : ?\DateTime {
        for($i=0; $i < count($this->selectedDates); $i++) {
            $dt = $this->selectedDates[$i];
            
            if ($dt->format('Y-m-d') === $date->format('Y-m-d')) {
                unset($this->selectedDates[$i]);
                return $dt;
            }
        }
        return null;
    }
    
    /**
     * @return multitype:DateTime 
     */
    public function getEventDates() : array
    {
        return $this->eventDates;
    }
    
    /**
     * Cette date est-elle parmis les date qui ont des evenements??
     * @param \DateTime $date
     * @return bool
     */
    public function isEventDate(\DateTime $date) : bool {
        foreach ($this->eventDates as $d) {
            if ($date == $d || $date->format('Y-m-d') === $d->format('Y-m-d')) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Ajout d'un date a la quel il y a aumoin un evement
     * @param \DateTime $date
     */
    public function addEventDate (\DateTime $date) : void {
        foreach ($this->eventDates as $d) {
            if ($date == $d) {//si meme reference
                return;
            }
        }
        
        $this->eventDates[] = $date;
    }
    
    /**
     * Supression d'un date deja ajouter aux dates qui ont des evements
     * @param \DateTime $date
     */
    public function removeEventDate(\DateTime $date) : void {
        if ($this->isEventDate($date)) {
            foreach ($this->eventDates as $key => $d) {
                if ($d == $date || $date->format('Y-d-m') == $d->format('Y-d-m')) {
                    unset($this->eventDates[$key]);
                    return;
                }
            }
        }
    }
    
    /**
     * Supression du derner element des dates qui ont des evements
     * @return \DateTime|NULL
     */
    public function popEventDate () : ?\DateTime {
        return array_pop($this->eventDates);
    }

    /**
     * @param multitype:DateTime  $eventDates
     */
    public function setEventDates(array $eventDates) : void
    {
        $this->eventDates = $eventDates;
    }

    /**
     * Ajoute une date a la liste de dates selectionner
     * et retourne un reference vers l'objet month qui ait recu la date
     * @param \DateTime $date
     * @return Month
     */
    public function pushSelectedDate(\DateTime $date) : Month {
        $this->addSelectedDate($date);
        return $this;
    }
    
    /**
     * Conversion de tout le mois en XML
     * @return string
     */
    public function toXML() : string{
        $xml = "<month name=\"".self::MONTHS[$this->month-1]."\" number = \"{$this->month}\" year = \"{$this->year}\">";
        $xml .= '<metadatas>';
        
        $xml .= ' <metadata name="dayNames" value="';
        foreach ($this->getDaysName() as $key => $name) {
            $xml .= $name.($key==(count($this->getDaysName())-1)? '"/>':';');
        }
        
        $xml .= ' <metadata name="dayShortNames" value="';
        foreach ($this->getShortDaysName() as $key => $name) {
            $xml .= $name.($key==(count($this->getDaysName())-1)? '"/>':';');
        }
        
        $xml .= ' <metadata name="firstDay">';
        $date = $this->getFirstDay();
        $xml .= "<date timestemp=\"{$date->getTimestamp()}\" shortDate=\"{$date->format('d-m-Y')}\" selected=\"".($this->isSelectedDate($date)? 'true':'false')."\" inMonth=\"".($this->inMonth($date)? 'true':'false')."\" day=\"{$date->format('d')}\"/>";
        $xml .= ' </metadata>';
        
        $xml .= ' <metadata name="lastDay">';
        $date = $this->getLastDay();
        $xml .= "<date timestemp=\"{$date->getTimestamp()}\" shortDate=\"{$date->format('d-m-Y')}\" selected=\"".($this->isSelectedDate($date)? 'true':'false')."\" inMonth=\"".($this->inMonth($date)? 'true':'false')."\" day=\"{$date->format('d')}\"/>";
        $xml .= ' </metadata>';
        
        $xml .= '</metadatas>';
        $number = 0;
        while ($this->hasNextWeek()) {
            $this->nextWeek();
            $xml .= "<week number=\"{$number}\">";
            while ($this->hasNextDay()){
                /**
                 * @var \DateTime $date
                 */
                $date =  $this->nextDay();
                $xml .= "<date timestemp=\"{$date->getTimestamp()}\" shortDate=\"{$date->format('d-m-Y')}\" selected=\"".($this->isSelectedDate($date)? 'true':'false')."\" inMonth=\"".($this->inMonth($date)? 'true':'false')."\" day=\"{$date->format('d')}\"/>";
            }
            $xml .= "</week>";
            $number++;
        }
        $xml .= '</month>';
        return $xml;
    }
    
    /**
     * Conversion de tout le mois en JSON
     * @return string
     */
    public function toJSON() : string{
        
        //Start convertion of calendar month to JSON
        $json = '{"name":"'.self::MONTHS[$this->month-1].'", "number":'.$this->month.', "year" :'.$this->year.',';
        $json .= '"metadatas" : {';//Start metadata
        
        $json .= '"dayNames": [';
        foreach ($this->getDaysName() as $key => $name) {
            $json .= '"'.$name.'"'.($key==(count($this->getDaysName())-1)? '],':',');
        }
        
        $json .= '"dayShortNames":[';
        foreach ($this->getShortDaysName() as $key => $name) {
            $json .= '"'.$name.'"'.($key==(count($this->getDaysName())-1)? '],':',');
        }
        
        $json .= '"firstDay":{';
        $date = $this->getFirstDay();
        $json .= '"timestemp":'.$date->getTimestamp().',';
        $json .= '"shortDate":"'.$date->format('d-m-Y').'",';
        $json .= '"selected":'.($this->isSelectedDate($date)? 'true':'false').',';
        $json .= '"inMonth":'.($this->inMonth($date)? 'true':'false').',';
        $json .= '"day":"'.$date->format('d').'"}, ';
        
        $date = $this->getLastDay();
        $json .= '"lastDay":{';
        $date = $this->getFirstDay();
        $json .= '"timestemp":"'.$date->getTimestamp().'",';
        $json .= '"shortDate":"'.$date->format('d-m-Y').'",';
        $json .= '"selected":"'.($this->isSelectedDate($date)? 'true':'false').'",';
        $json .= '"inMonth":"'.($this->inMonth($date)? 'true':'false').'",';
        $json .= '"day":"'.$date->format('d').'"}';
        
        $json .= '},';//End metadata
        $number = 0;
        
        $json .= '"days": [';//tab of Month weeks
        while ($this->hasNextWeek()) {
            $this->nextWeek();
            $json .= "[";//tab of days of  week
            $count = 1;
            while ($this->hasNextDay()){
                /**
                 * @var \DateTime $date
                 */
                $date =  $this->nextDay();
                $json .= '{';
                $date = $this->getFirstDay();
                $json .= '"timestemp":'.$date->getTimestamp().',';
                $json .= '"shortDate":"'.$date->format('d-m-Y').'",';
                $json .= '"selected":'.($this->isSelectedDate($date)? 'true':'false').',';
                $json .= '"inMonth":'.($this->inMonth($date)? 'true':'false').',';
                $json .= '"day":"'.$date->format('d').'"}'.($count==7? '':',');
                $count++;
                
            }
            $number++;
            $json .= "]".($number<($this->getWeeks())? ',':'');//End tab of days of week
        }
        $json .= ']}';//End of calendar converted
        return $json;
    }
    

}

