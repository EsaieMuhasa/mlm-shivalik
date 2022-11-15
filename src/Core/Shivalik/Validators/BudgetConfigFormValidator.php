<?php
namespace Core\Shivalik\Validators;

use Core\Shivalik\Entities\BudgetConfig;
use Core\Shivalik\Entities\BudgetRubric;
use Core\Shivalik\Entities\ConfigElement;
use Core\Shivalik\Managers\BudgetConfigDAOManager;
use DateTime;
use Exception;
use PHPBackend\PHPBackendException;
use PHPBackend\Request;
use PHPBackend\Validator\DefaultFormValidator;

class BudgetConfigFormValidator extends DefaultFormValidator {

    /**
     * @var BudgetConfigDAOManager
     */
    private $budgetConfigDAOManager;

    /**
     * {@inheritDoc}
     * 
     * insersion d'une nouvelle configuration.
     * la validation des elements de la repartition du budget c fait ici
     * 
     * @param Request $request
     * @param BudgetRubric[] $elements
     * @return BudgetConfig
     */
    public function createAfterValidation(Request $request, array $elements = []) : BudgetConfig
    {
        $config = new BudgetConfig();
        $now = new DateTime();
        foreach ($elements as $element) {
            $item = new ConfigElement();
            $percent = $request->getDataPOST("element{$element->getId()}");
            if(!preg_match(self::RGX_NUMERIC_POSITIF, $percent)) {
                $this->setMessage("All repartition value must the vali nmeric number.");
            } else {
                $item->setPercent($percent);
            }
            $item->setConfig($config);
            $item->setRubric($element);
            $item->setDateAjout($now);
            $config->addElement($item);
        }
        $config->setDateAjout($now);

        if(!$this->hasError() && $config->getSumOfElements() != 100) {
            $this->setMessage("the sum of values must total 100%");
        }

        if(!$this->hasError()) {
            try {
                $this->budgetConfigDAOManager->create($config);
            } catch (Exception $e) {
                $this->setMessage($e->getMessage());
            }
        }
        $this->setResult("Operation Execution Success", "Failed to execute operation");

        return $config;
    }

    public function updateAfterValidation(Request $request)
    {
        throw new PHPBackendException("Operation not supported");
    }
}