<?php
namespace Library\Text;

/**
 *
 * @author Esaie MHS
 *        
 */
class HtmlFormater extends AbstractFormater
{
    /**
     * {@inheritDoc}
     * @see \Library\Text\AbstractFormater::__construct()
     */
    public function __construct(?string $data)
    {
        parent::__construct($data);
    }

    /**
     * {@inheritDoc}
     * @see \Library\Text\AbstractFormater::format()
     * @tutorial formatage de donnees au format HTML
     */
    public function format(): ?string
    {
        if ($this->data === null) {
            return null;
        }
        
        //En attenda le vrais script de formatage des donnees textuel
        $formated = str_replace('\n', '<br/>', $this->data);
        return $formated;
    }
    
    /**
     * utilitaire de formatage de information au format HTML.
     * cette methode est a utiliser pour des petit texte a formater, soit un texte qui sera formater une seul fois.
     * car on ne garde pas la reference vers l'obet qui gere le formatage de donnees au format HTML.
     * @param string $data
     * @return string|NULL
     */
    public static final function toHTML(?string $data): ?string{
        $html = new HtmlFormater($data);
        return $html->format();
    }

}

