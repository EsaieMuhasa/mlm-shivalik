<?php
namespace Core\Shivalik\Validators;

use Core\Shivalik\Entities\BudgetRubric;
use Core\Shivalik\Entities\Output;
use Core\Shivalik\Managers\OutputDAOManager;
use PHPBackend\Dao\DAOException;
use PHPBackend\Request;
use PHPBackend\Validator\DefaultFormValidator;
use PHPBackend\Validator\IllegalFormValueException;

class OutputFormValidator extends DefaultFormValidator {
    const FIELD_AMOUNT = 'amount';

    /**
     * @var OutputDAOManager
     */
    private $outputDAOManager;
    
    public function createAfterValidation(Request $request, BudgetRubric $rubric =  null)
    {
        $out = new Output();
        $amount = $request->getDataPOST(self::FIELD_AMOUNT);
        $out->setRubric($rubric);

        $this->processingAmount($out, $amount);

        if (!$this->hasError()) {
            try {
                $this->outputDAOManager->create($out);
            } catch (DAOException $e) {
                $this->setMessage($e->getMessage());
            }
        }

        $this->setResult("operation execution success", "operation execution failure");

        return $out;
    }

    public function updateAfterValidation(Request $request)
    {
        $out = new Output();
        return $out;
    }

    /**
     * utilitaire de validation du montant
     *
     * @param Output $out
     * @param string|float $amount
     * @return void
     */
    protected function processingAmount (Output $out, $amount) : void {
        try {
            if(!preg_match(self::RGX_NUMERIC_POSITIF, $amount)) {
                throw new IllegalFormValueException("must a positive number");
            }
            $out->setAmount($amount);

            if($out->getRubric()->getAvailable() < $out->getAmount()) {
                throw new IllegalFormValueException("we cannot perform this request");
            }
        } catch (\Exception $e) {
            $this->addError(self::FIELD_AMOUNT, $e->getMessage());
        }
    }
}