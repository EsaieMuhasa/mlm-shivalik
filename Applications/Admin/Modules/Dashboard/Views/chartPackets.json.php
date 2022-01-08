<?php
use Applications\Admin\Modules\Dashboard\DashboardController;

echo "\"chart\": {$_REQUEST[DashboardController::ATT_CHART_CONFIG]->toJSON()}";