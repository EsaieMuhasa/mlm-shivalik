<?php 
use Applications\Admin\Modules\Offices\OfficesController;
use Core\Shivalik\Entities\Office;
use PHPBackend\AppConfig;
use PHPBackend\Request;

/**
 * @var Office $office
 */
$office = $_REQUEST[OfficesController::ATT_OFFICE];

/**
 * @var AppConfig $config
 */
$config = $_REQUEST[Request::ATT_APP_CONFIG];
$requests = $_REQUEST[OfficesController::ATT_VIRTUAL_MONEYS];
?>

<?php if (empty($requests)) : ?>
<div class="row">
	<div class="col-xs-12" style="padding-bottom: 30px;">
		<a href="send.html" class="btn btn-primary">
			<span class="fa fa-plus"></span> Send virtual money
		</a>
	</div>
</div>
<?php endif;?>

<div class="row">
	<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
        <div class="info-box green-bg">
            <i class="glyphicon glyphicon-ok"></i>
            <div class="count"><?php echo ("{$office->getAvailableVirtualMoney()} {$config->get('devise')}"); ?></div>
            <div class="title">Available</div>
        </div>
        <!--/.info-box-->
    </div>
    
    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
        <div class="info-box green-bg">
            <i class="glyphicon glyphicon-trash"></i>
            <div class="count"><?php echo ("{$office->getUsedVirtualMoney()} {$config->get('devise')}"); ?></div>
            <div class="title">trash</div>
        </div>
        <!--/.info-box-->
    </div>
    
    <?php if ($office->getSoldRetroCommission()>0) : ?>
    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
        <div class="info-box blue-bg">
            <i class="glyphicon glyphicon-retweet"></i>
            <div class="count"><?php echo ("{$office->getSoldRetroCommission()} {$config->get('devise')}"); ?></div>
            <div class="title">membership</div>
        </div>
        <!--/.info-box-->
    </div>
    <?php endif; ?>
    
    <?php if ($office->hasDebts()) : ?>
    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
        <div class="info-box red-bg">
            <i class="glyphicon glyphicon-warning-sign"></i>
            <div class="count"><?php echo ("{$office->getDebts()} {$config->get('devise')}"); ?></div>
            <div class="title">Debts</div>
        </div>
        <!--/.info-box-->
    </div>
    <?php endif; ?>
    
    <?php if (!empty($requests)) : ?>
    <?php foreach ($requests as $request) : ?>
    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
        <a class="info-box red-bg" style="display: block;" href="<?php echo "/admin/offices/{$office->getId()}/virtualmoney/{$request->getId()}/send.html"; ?>">
            <i class="glyphicon glyphicon-time"></i>
            <span class="count" style="display: block;"><?php echo ("{$request->getAmount()} {$config->get('devise')}"); ?></span>
            <span class="title" style="display: block;">Request</span>
        </a>
        <!--/.info-box-->
    </div>
    <?php endforeach; ?>
    <?php endif; ?>
</div>