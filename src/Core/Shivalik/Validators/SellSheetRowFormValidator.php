<?php
namespace Core\Shivalik\Validators;

use Core\Shivalik\Entities\Member;
use Core\Shivalik\Entities\Office;
use Core\Shivalik\Entities\SellSheetRow;
use Core\Shivalik\Managers\MonthlyOrderDAOManager;
use Core\Shivalik\Managers\ProductDAOManager;
use Core\Shivalik\Managers\SellSheetRowDAOManager;
use PHPBackend\Dao\DAOException;
use PHPBackend\Request;
use PHPBackend\Validator\DefaultFormValidator;
use PHPBackend\Validator\IllegalFormValueException;

/**
 * validation de operations qu viens de formulaire qui touche un element de 
 * la fiche individuel d'un membre
 * @author Esaie Muhasa <esaiemuhasa.dev@gmail.com>
 */
class SellSheetRowFormValidator extends DefaultFormValidator {

    const FIELD_QUANTITY = 'quantity';
    const FIELD_UNIT_PRICE = 'unitPrice';
    const FIELD_PRODUCT = 'product';

    const ATT_MEMBER = 'member';
    const ATT_OFFICE = 'office';

    /**
     * @var ProductDAOManager
     */
    private $productDAOManager;
    /**
     * @var MonthlyOrderDAOManager
     */
    private $monthlyOrderDAOManager;
    /**
     * @var SellSheetRowDAOManager
     */
    private $sellSheetRowDAOManager;

    /**
     * validation du produit auquel l'operation fait reference
     * @param int|string $product
     * @return void
     * @throws IllegalFormValueException
     */
    private function validationProduct ($product ) : void {
        if($product == null) {
            throw new IllegalFormValueException('Make sure you have selected a product');
        } else if (!preg_match(self::RGX_INT_POSITIF, $product)) {
            throw new IllegalFormValueException('Enter the reference to a product, in the valid format');
        } else {
            try {
                if (!$this->productDAOManager->checkById(intval($product, 10)));
            } catch (DAOException $e) {
                throw new IllegalFormValueException($e->getMessage());
            }
        }
    }

    /**
     * validation dela quantite commandee par le membre
     * @param int|string $quantity
     * @return void
     * @throws IllegalFormValueException
     */
    private function validationQuanity ($quantity) : void {
        if($quantity == null || $quantity === 0) {
            throw new IllegalFormValueException('this field is required');
        } else if (!preg_match(self::RGX_INT_POSITIF, $quantity)) {
            throw new IllegalFormValueException('quantity must be a positive numeric value');
        }
    }
    
    /**
     * validation du prix unitaire d'achat d'un produit
     * @param string|float $unitPrice
     * @return void
     * @throws IllegalFormValueException
     */
    private function validationUnitPrice ($unitPrice) : void {
        if($unitPrice == null || $unitPrice === 0) {
            throw new IllegalFormValueException('this field is required');
        } else if (!preg_match(self::RGX_INT_POSITIF, $unitPrice)) {
            throw new IllegalFormValueException('quantity must be a positive numeric value');
        }
    }

    /**
     * lancement processuce de validation/traitement du produit 
     * reference par ladite commande
     *
     * @param SellSheetRow $row
     * @param int|string $product
     * @return self
     */
    private function processProduct (SellSheetRow $row, $product) : self{
        try {
            $this->validationProduct($product);
            $row->setProduct(intval($product, 10));
        }catch (IllegalFormValueException $e) {
            $this->addError(self::FIELD_PRODUCT, $e->getMessage());
        }
        return $this;
    }

    /**
     * lancement processuce de validation/traitement de la quantite commande par le membre
     *
     * @param SellSheetRow $row
     * @param string|float $quantity
     * @return self
     */
    private function processQuantity (SellSheetRow $row, $quantity) : self {
        try {
            $this->validationQuanity($quantity);
        } catch (IllegalFormValueException $e) {
            $this->addError(self::FIELD_QUANTITY, $e->getMessage());
        }
        $row->setQuantity($quantity);
        return $this;
    }

    /**
     * processuce de validation/traitement du prix unitaire
     *
     * @param SellSheetRow $row
     * @param float|string $unitPrice
     * @return self
     */
    public function processUnitPrice (SellSheetRow $row, $unitPrice) : self {
        try {
            $this->validationUnitPrice($unitPrice);
        } catch (IllegalFormValueException $e) {
            $this->addError(self::FIELD_UNIT_PRICE, $e->getMessage());
        }
        $row->setUnitPrice($unitPrice);
        return $this;
    }


    /**
     * insersion d'une nouvelle ligne sur la fiche d'un membre.
     * une validation est faite avant de faire appel a la couche d'interfacage avec la base de donnee (DAO)
     * @param Request $request
     * @return SellSheetRow
     */
    public function createAfterValidation(Request $request)
    {
        $row = new SellSheetRow();
        /**
         * @var Member $member
         */
        $member = $request->getAttribute(self::ATT_MEMBER);
        /**
         * @var Office $office
         */
        $office = $request->getAttribute(self::ATT_OFFICE);

        $quantity = $request->getDataPOST(self::FIELD_QUANTITY);
        $product = $request->getDataPOST(self::FIELD_PRODUCT);
        $unitPrice = $request->getDataPOST(self::FIELD_UNIT_PRICE);

        $this
            ->processProduct($row, $product)
            ->processQuantity($row, $quantity)
            ->processUnitPrice($row, $unitPrice);

        //verification de virtual disponible dans l'office
        if(!$this->hasError()) {
            if ($office->getAvailableVirtualMoneyProduct() < $row->getTotalPrice()) {
                $this->setMessage("impossible to perform this operation because the product account of your office is insufficient: {$office->getAvailableVirtualMoneyProduct()}");
            }
        }
        //==

        if (!$this->hasError()) {
            try {
                $order = $this->monthlyOrderDAOManager->buildByMemberOfMonth($member->getId(), $office);
                $row->setMonthlyOrder($order);
                $row->setOffice($office);
                $this->sellSheetRowDAOManager->create($row);
            } catch (DAOException $e) {
                $this->setMessage($e->getMessage());
            }
        }

        $this->setResult('Insertion success of the operation', 'Operation failed');
        return $row;
    }

    /**
     * validation de donnees avant l'operation de modification d'une ligne de la fiche d'un membre
     * @param Request $request
     * @return SellSheetRow
     */
    public function updateAfterValidation(Request $request)
    {
        
    }

}