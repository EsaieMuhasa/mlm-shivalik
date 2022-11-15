<?php
namespace Core\Shivalik\Validators;

use Core\Shivalik\Entities\BudgetConfig;
use Core\Shivalik\Entities\ConfigElement;
use Core\Shivalik\Entities\SubConfigElement;
use Core\Shivalik\Managers\SubConfigElementDAOManager;
use DateTime;
use Exception;
use PHPBackend\PHPBackendException;
use PHPBackend\Request;
use PHPBackend\Validator\DefaultFormValidator;

class SubConfigElementFormValidator extends DefaultFormValidator {

    /**
     * @var SubConfigElementDAOManager
     */
    private $subConfigElementDAOManager;

    public function handleRequest (Request $request, ConfigElement $config, array $elements) : array {
        $data = [];
        $now = new DateTime();
        foreach ($elements as $element) {
            $item = new SubConfigElement();
            $percent = $request->getDataPOST("element{$element->getId()}");
            if(!preg_match(self::RGX_NUMERIC_POSITIF, $percent)) {
                $this->setMessage("All repartition value must the vali nmeric number.");
            } else {
                $item->setPercent($percent);
            }
            $item->setConfig($config);
            $item->setRubric($element);
            $item->setDateAjout($now);
            $data[] = $item;
            $config->addElement($item);
        }

        if(!$this->hasError() && $config->getSumOfElements() != 100) {
            $this->setMessage("the sum of values must total 100%");
        }

        if(!$this->hasError()) {
            try {
                $this->subConfigElementDAOManager->createAll($data);
            } catch (Exception $e) {
                $this->setMessage($e->getMessage());
            }
        }
        $this->setResult("Operation Execution Success", "Failed to execute operation");

        return $data;
    }

    /**
     * {@inheritDoc}
     */
    public function createAfterValidation(Request $request, array $elements = []) : BudgetConfig
    {
        throw new PHPBackendException("Opeation not supported");
    }

    public function updateAfterValidation(Request $request)
    {
        throw new PHPBackendException("Operation not supported");
    }
}