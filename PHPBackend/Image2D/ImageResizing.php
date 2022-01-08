<?php
namespace PHPBackend\Image2D;


use PHPBackend\PHPBackendException;

/**
 *
 * @author Esaie Muhasa
 *        
 */
class ImageResizing extends ImageBuilder
{
    
    /**
     * Collection des caracterisique des images a generer
     * @var Image[]
     */
    protected $images;
    
    /**
     * {@inheritDoc}
     * @see ImageBuilder::__construct()
     * @param Image $images[] collection des carracteristique des image
     */
    public function __construct(Image $image, array $images)
    {
        parent::__construct($image);
        if (!$this->image->fileExists()) {
            throw new PHPBackendException('Le redimensionnement d\'une image ce base sur une image existant physiquement sur le disque dur. " '.$image->getFileName().'" ');
        }
        
        if ($images === null || empty($images)) {
            throw new PHPBackendException('La collections de la description des informations de redimensionnement ne doit pas etre null ou vide.');
        }
        
        $this->images = $images;
    }

    /**
     * {@inheritDoc}
     * @see ImageBuilder::build()
     */
    public function build() : void
    {
        foreach ($this->images as $img) {
            
            $size = $img->getSize();
            //si X=0 ou Y = 0, alors on calcule la nouvelle position de X soit de y
            $x = $size->getX();
            $y = $size->getY();
            
            /**
             * @var resource $source
             */
            $source = null;
            
            //Creation de la ressource de type image
            if ($this->image->getType() == Image::IMAGE_TYPE_PNG) {
                $source = imagecreatefrompng($this->image->getFileName());
            }else {
                $source = imagecreatefromjpeg($this->image->getFileName());
            }
            
            
            if ( $x == 0 ){//pour x
                $imagex = imagesx($source);
                $pref_x = ($imagex - $size->getWidth()) / 2;
                if ($imagex > ($pref_x + $size->getWidth())) {//Si l'image est plus grand que la taile demander                     
                    $x = $pref_x;
                }
            }
            
            if ( $y == 0){//pour y
                $imagey = imagesy($source);
                $pref_y = ($imagey - $size->getHeight()) / 2;
                if ($imagey > ($pref_y+$size->getHeight())) {//Si l'image a une hauteur plus elevee que la taille demander
                    $y =  $pref_y;
                }
                
            }
            
            $destination = imagecreatetruecolor($size->getWidth(), $size->getHeight());
            $statut = imagecopyresampled($destination, $source, 0, 0, $x, $y, $size->getWidth(), $size->getHeight(), $size->getWidth(), $size->getHeight());
            
            if (!$statut) {
                throw new PHPBackendException('Eche de redimensionnement de l\'image.');
            }
            
            if ($this->image->getType() == Image::IMAGE_TYPE_PNG){
                $statut = imagepng($destination, $img->getFileName());
            } else {
                $statut= imagejpeg($destination, $img->getFileName());                
            }
            
            if (!$statut) {
                throw new PHPBackendException('Eche d\'écriture de l\'image rédimensionner sur le disque dur');
            }
        }
    }
    
    /***
     * Generation des images aux dimensions requise
     * @param Image $image
     * @param bool $remove suprimer le fichier original??/
     * @throws PHPBackendException
     */
    public static function builder(Image $image, $remove = true) : void{
        //Si l'image d'existe pas physiquement sur le serveur
        if (!$image->fileExists()) {
            throw new PHPBackendException("Impossible de demanrer les algorithmes des redimensionnement des images. l\'image doit exister physiquement sur le serveur. > {$image->getFileName()} <");
        }
        
        $matches = array();
        if (preg_match('#^(.+)-reel\.(png|PNG|jpg|jpeg|JPG|JPEG)$#', $image->getFileName(), $matches)) {
            $baseName = $matches[1];
            $extension = $matches[2];
        }else {
            throw new PHPBackendException("impossible de poursuivre les traitements car l'outils de redimensionnement n'arrive pas a separer le nom de l'image a son extension. Image non prise en charge {$image->getFileName()}");
        }
            
        /*
         * \\pour la taille normale
         * =====================================================
         * Pour la photo de dimension requise, un copression simple est naicessaire
         */
        $sizeNormale = ImageRect::getDefaultSize();
        $imageName = $baseName.'.'.$extension;//le nom de l'image de base
        $destination = new Image($imageName, $sizeNormale);
        
        self::equalize($image, $destination);
        
        
        /*
         * \\Pour les images minuatures
         * ================================================================
         */
        $sizeMin = ImageRect::getDefaultMinuatureSize();
        $imageMinName = $baseName.'-min.'.$extension;
        $min_destination = new Image($imageMinName, $sizeMin);
        self::equalize($image, $min_destination);
        
        
        
        /*
         * \\Pour les thumbs
         * ======================================================
         */
        
        /*
         * Creation du thumb normal
         * ----------------------------
         * Le principe de base est de calculer le pourcentage de la hoteur de l'image, qui veau 200px
         */
        $to_pourcent_200px = (100 * 200) / $sizeNormale->getHeight();
        
        $thumb_w = ($sizeNormale->getWidth()*$to_pourcent_200px)/100; 
        $thumb_h = 200;
        $sizeThumb = new ImageRect($thumb_w, $thumb_h);
        $thumb_name = $baseName.'-thumb.'.$extension;
        
        $thumb_destination = new Image($thumb_name, $sizeThumb);
        self::equalize($image, $thumb_destination);
        
        
        //Creation du thumb carree
        $sizeThumb_carre = new ImageRect(200, 200, (($thumb_w-200)/2), 1);
        $thumb_carre_name = $baseName.'-thumb-carre.'.$extension;
        
        $images_thumb = array();
        $img_thumb = new Image($thumb_name);
        $images_thumb[] = new Image($thumb_carre_name, $sizeThumb_carre);
        $resize = new ImageResizing($img_thumb, $images_thumb);
        $resize->build();
        
        //creation du minuature du thumb carree
        $thumb_carre_min_name = $baseName.'-thumb-carre-xs.'.$extension;
        $thumb_carre_source = new Image($thumb_carre_name);
        $thumb_carre_destination = new Image($thumb_carre_min_name, ImageRect::getDefaultXsCarreSize());
        self::equalize($thumb_carre_source, $thumb_carre_destination);
            
        //Supression du fichier original
        if ($remove) {
	        @unlink($image->getFileName());        	
        }
    }
    
    /**
     * Dimesionnement d'une photo de profile
     * @param Image $image
     * @param ImageRect $imageRect
     * @param bool $remove supprimer l'original????
     */
    public static function profiling(Image $image, ?ImageRect $imageRect = null, $remove=true) : void{
        $matches = array();
        
        if (!$image->fileExists()) {
            throw new PHPBackendException("Impossible de demanrer les algorithmes de generation des images. l\'image doit exister physiquement sur le serveur. > {$image->getFileName()} <");
        }
        
        $baseName = '';
        $extension = '';
        
        if (preg_match('#^(.+)-reel\.(png|PNG|jpg|jpeg|JPG|JPEG)$#', $image->getFileName(), $matches)) {
            $baseName = $matches[1];
            $extension = $matches[2];
        }else {
            throw new PHPBackendException('Les images pise en charge sont uniquement de PNG et JPG');
        }
            
        /*
         * \\Recherche de l'orientation de l'image
         * =====================================================
         * pour les images en paysage on redimensionne pous onle coupe
         * pour les images en portrait on redimensionne puis on le coupe
         * pour les carre on redimensionne uniquement
         */
        if ($image->getType() == Image::IMAGE_TYPE_PNG) {
            $source = imagecreatefrompng($image->getFileName());
        }else {
            $source = imagecreatefromjpeg($image->getFileName());
        }
        
        
        $width = imagesx($source);
        $height = imagesy($source);
        
        
        $w_pourcent20 = ($width * 20) / 100;//40% de la largeur
        $h_pourcent20 = ($height * 20) / 100;//40% de la hoteur
        
        
        $imageName = $baseName.'-resized.'.$extension;//le nom de l'image de base
        $size = null;//la taille de l'image
        
        if ($width >= ($height + $w_pourcent20) ) {//Pour les image en paysage
            $size = ImageRect::getA6Landscape();
        }elseif ($height >= ($width + $h_pourcent20)){//pour les imges en portrait
            $size = ImageRect::getA6Portrait();
            
        }else {//Pour les image carree ou presque carree
            $s_port = ImageRect::getA6Portrait();
            $size = new ImageRect($s_port->getWidth(), $s_port->getWidth());
        }

        
        if ($width >= ($height + $w_pourcent20) ) {//Pour les image en paysage
            $carreSize = new ImageRect($size->getHeight(), $size->getHeight());
        }else {//Pour les image carree ou presque carree carree ou en portrait
            $carreSize = new ImageRect($size->getWidth(), $size->getWidth());
        }
        
        $imageResized = new Image($imageName, $size);
        self::equalize($image, $imageResized);//Redimensionnement de l'image
        

        //Generation des minuature de l'image
        $requieredBigName = $baseName.'-lg.'.$extension;//Profil grand carree
        
        $rs_image = new Image($imageResized->getFileName(), $carreSize);
        
        $images = array();
        $images[] = new Image($requieredBigName, $carreSize);
        $resizing = new ImageResizing($rs_image, $images);
        $resizing->build();
        
        $requieredName = $baseName.'.'.$extension;//profil normale
        $requieredSmName = $baseName.'-sm.'.$extension;//le small du profile
        $requieredXsName = $baseName.'-xs.'.$extension;//le xsmall du profile
        $requieredSize = ImageRect::getProfilSize();
        $requieredSmSize = ImageRect::getProfilSmSize();
        $requieredXsSize = ImageRect::getProfilXsSize();
        
        $requieredImage = new Image($requieredName, $requieredSize);
        $requieredSmImage = new Image($requieredSmName, $requieredSmSize);
        $requieredXsImage = new Image($requieredXsName, $requieredXsSize);
        $rs_source = new Image($requieredBigName);
        self::equalize($rs_source, $requieredImage);
        self::equalize($rs_source, $requieredSmImage);
        self::equalize($rs_source, $requieredXsImage);
       
        if ($remove) {
	        @unlink($image->getFileName());
        }
    }
    
    
    /***
     * mise en echel d'une image
     * @param Image $img_source
     * @param Image $img_destination
     */
    protected static function equalize(Image $img_source, Image $img_destination) : void{
        if ($img_source->getType() == Image::IMAGE_TYPE_PNG) {
            $source = imagecreatefrompng($img_source->getFileName());
        }else {
            $source = imagecreatefromjpeg($img_source->getFileName());
        }
        $size = $img_destination->getSize();
        $destination = imagecreatetruecolor($size->getWidth(), $size->getHeight());
        $statut = imagecopyresampled($destination, $source, 0, 0, 0, 0, $size->getWidth(), $size->getHeight(), $img_source->getSize()->getWidth(), $img_source->getSize()->getHeight());
        if (!$statut) {
            throw new PHPBackendException('Echec d\'execution de l\'operation. une erreur est survenue lors de construction de l\'image.');
        }
        
        
        //Ecriture de l'image sur le dique dur
        if ($img_source->getType() == Image::IMAGE_TYPE_PNG){
            $statut = imagepng($destination, $img_destination->getFileName());
        } else {
            $statut= imagejpeg($destination, $img_destination->getFileName());
        }
        
        if (!$statut) {
            throw new PHPBackendException('Echec d\'execution de l\'operation. une erreur est survenue lors de l\'ecriture de l\'image sur le disque dur du serveur.');
        }
    }

}

