<?php
namespace Core\Shivalik\Validators;

use PHPBackend\Validator\DefaultFormValidator;
use Core\Shivalik\Managers\ProductDAOManager;
use PHPBackend\Validator\IllegalFormValueException;
use PHPBackend\Dao\DAOException;
use PHPBackend\File\UploadedFile;
use Core\Shivalik\Entities\Product;
use PHPBackend\File\FileManager;
use PHPBackend\Image2D\ImageResizing;
use PHPBackend\Image2D\Image;

/**
 *
 * @author Esaie MUHASA
 *        
 */
class ProductValidator extends DefaultFormValidator
{
    
    const MAX_LENGTH_NAME = 150;
    const MIN_LENGTH_NAME = 3;
    
    const MAX_LENGTH_DESCRIPTION = 1000;
    const MIN_LENGTH_DESCRIPTION = 300;
    
    const FIELD_NAME = 'name';
    const FIELD_PICTURE = 'picture';
    const FIELD_DESCRIPTION = 'description';
    const FIELD_DEFAULT_UNIT_PRICE = 'defaultUnitPrice';
    
    /**
     * @var ProductDAOManager
     */
    private $productDAOManager;
    
    /**
     * validation du nom d'un produit
     * @param string $name
     * @param int $id l'identifiant du produit. utile lors de la modification du nom d'un produit
     * @throws IllegalFormValueException
     */
    private function validationName ($name, $id = null) : void {
        if ($name == null) {
            throw new IllegalFormValueException("product name is required");
        } else if (strlen($name) <= self::MIN_LENGTH_NAME || strlen($name) > self::MAX_LENGTH_NAME) {
            throw new IllegalFormValueException("product name can not be succed ");
        }
        
        try {
            if ($this->productDAOManager->checkByName($name, $id)) {
                throw new IllegalFormValueException("product name are used");
            }
        } catch (DAOException $e) {
            throw new IllegalFormValueException($e->getMessage(), $e->getCode(), $e);
        }
    }
    
    /**
     * validation de la description d'un produit
     * @param string $description
     * @throws IllegalFormValueException
     */
    private function validationDescription ($description) : void {
        if ($description ==  null) {
            throw new IllegalFormValueException("producti description is required");
        } else if (strlen($description) <= self::MIN_LENGTH_DESCRIPTION || strlen($description) > self::MAX_LENGTH_DESCRIPTION) {
            throw new IllegalFormValueException("product name can not succed ");
        }
    }
    
    /**
     * validation du prix par defaut d'un produit
     * @param number $defaultUnitPrice
     * @throws IllegalFormValueException
     */
    private function validationDefaultUnitPrice ($defaultUnitPrice) : void {
        if ($defaultUnitPrice == null) {
            throw new IllegalFormValueException("default unit price is required");
        } else if (!preg_match(self::RGX_NUMERIC_POSITIF, $defaultUnitPrice)) {
            throw new IllegalFormValueException("value of this field mast be only numeric");
        }
    }
    
    /**
     * validation de l'image du produit
     * @param UploadedFile $file
     * @param bool $onCreate
     * @throws IllegalFormValueException
     */
    private function validationPicture (UploadedFile $file, bool $onCreate = true) : void {
        parent::validationImage($file);
        if (!$file->isFile() && $onCreate) {
            throw new IllegalFormValueException("photo is required");
        }
    }
    
    /**
     * sequance traitement/validation du nom d'un produit
     * @param Product $product
     * @param string $name
     */
    private function processName (Product $product, $name) : void {
        try {
            $this->validationName($name, $product->getId());
        } catch (IllegalFormValueException $e) {
            $this->addError(self::FIELD_NAME, $e->getMessage());
        }
        $product->setName($name);
    }
    
    /**
     * traitement/validation de l'image d'un produit
     * @param Product $product
     * @param UploadedFile $file
     * @param bool $write
     */
    private function processPicture (Product $product, UploadedFile $file, bool $write = false) : void {
        try {
            if ($write && $file->isImage()) {
                $time = time();
                $base = "img/products/{$product->getId()}/{$time}";
                $fileName = "{$base}-reel.{$file->getExtension()}";
                $picture = "{$base}.{$file->getExtension()}";
                $reelName = FileManager::writeUploadedFile($file, $fileName);
                ImageResizing::builder(new Image($reelName));
                $product->setPicture($picture);
            } else {
                $this->validationPicture($file, $product->getId() === null);
            }
        } catch (IllegalFormValueException $e) {
            $this->addError(self::FIELD_PICTURE, $e->getMessage());
        }
    }
    
    /**
     * validation/traitement du prix unitaire par defaut d'un produit
     * @param Product $product
     * @param number|string $defaultUnitPrice une valeur numerique/soit une chainer de caracter qui contiens un valeur numerique
     */
    private function processDefaultUnitPrice (Product $product, $defaultUnitPrice) : void {
        try {
            $this->validationDefaultUnitPrice($defaultUnitPrice);
        } catch (IllegalFormValueException $e) {
            $this->addError(self::FIELD_DEFAULT_UNIT_PRICE, $e->getMessage());
        }
        $product->setDefaultUnitPrice($defaultUnitPrice);
    }
    
    /**
     * traitement/validation de la description courte d'un produit
     * @param Product $product
     * @param string $description
     */
    private function processDescription (Product $product, $description) : void {
        try {
            $this->validationDescription($description);
        } catch (IllegalFormValueException $e) {
            $this->addError(self::FIELD_DESCRIPTION, $e->getMessage());
        }
        $product->setDescription($description);
    }
   
    /**
     * {@inheritDoc}
     * @see \PHPBackend\Validator\FormValidator::createAfterValidation()
     * @return Product
     */
    public function createAfterValidation(\PHPBackend\Request $request)
    {
        $product = new Product();
        $name = $request->getDataPOST(self::FIELD_NAME);
        $description = $request->getDataPOST(self::FIELD_DESCRIPTION);
        $defaultUnitPrice = $request->getDataPOST(self::FIELD_DEFAULT_UNIT_PRICE);
        $file = $request->getUploadedFile(self::FIELD_PICTURE);
        
        $this->processName($product, $name);
        $this->processDefaultUnitPrice($product, $defaultUnitPrice);
        $this->processDescription($product, $description);
        $this->processPicture($product, $file);
        
        if (!$this->hasError()) {
            try {
                $this->productDAOManager->create($product);
                $this->processPicture($product, $file, true);
                $this->productDAOManager->updatePicture($product->getPicture(), $product->getId());
            } catch (DAOException $e) {
                $this->setMessage($e->getMessage());
            }
        }
        
        $this->result = $this->hasError()? "Failure registration" : "Success full registration";
        
        return $product;
    }

    /**
     * {@inheritDoc}
     * @see \PHPBackend\Validator\FormValidator::updateAfterValidation()
     * @return Product
     */
    public function updateAfterValidation(\PHPBackend\Request $request)
    {
        $product = new Product();
        $name = $request->getDataPOST(self::FIELD_NAME);
        $description = $request->getDataPOST(self::FIELD_DESCRIPTION);
        $defaultUnitPrice = $request->getDataPOST(self::FIELD_DEFAULT_UNIT_PRICE);
        $file = $request->getUploadedFile(self::FIELD_PICTURE);
        
        $product->setId($request->getDataGET(self::FIELD_ID));
        
        $this->processName($product, $name);
        $this->processDefaultUnitPrice($product, $defaultUnitPrice);
        $this->processDescription($product, $description);
        $this->processPicture($product, $file);
        
        if (!$this->hasError()) {
            try {
                $this->productDAOManager->update($product, $product->getId());
                if ($file->isImage()) {
                    $this->processPicture($product, $file, true);
                    $this->productDAOManager->updatePicture($product->getPicture(), $product->getId());
                }
            } catch (DAOException $e) {
                $this->setMessage($e->getMessage());
            }
        }
        
        $this->result = $this->hasError()? "Failure registration" : "Success full registration";
        
        return $product;
    }

}

