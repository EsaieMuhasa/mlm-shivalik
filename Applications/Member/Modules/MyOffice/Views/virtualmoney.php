<?php 
use Applications\Member\Modules\MyOffice\MyOfficeController;
use Core\Shivalik\Filters\SessionMemberFilter;
use PHPBackend\AppConfig;
use PHPBackend\Request;

/**
 * @var \Core\Shivalik\Entities\Office $office
 */
$office = $_SESSION[SessionMemberFilter::MEMBER_CONNECTED_SESSION]->getOfficeAccount();

/**
 * @var AppConfig $config
 */
$config = $_REQUEST[Request::ATT_APP_CONFIG];
$requests = $_REQUEST[MyOfficeController::ATT_VIRTUAL_MONEYS];
?>

<?php if (empty($requests)) : ?>
<div class="row">
	<div class="col-xs-12" style="padding-bottom: 15px;">
		<a href="request.html" class="btn btn-primary">
			<span class="fa fa-plus"></span> Send new request
		</a>
	</div>
</div>
<?php endif; ?>

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
    
    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
        <div class="info-box blue-bg">
            <i class="glyphicon glyphicon-retweet"></i>
            <div class="count"><?php echo ("{$office->getSoldRetroCommission()} {$config->get('devise')}"); ?></div>
            <div class="title">Afiliation</div>
        </div>
        <!--/.info-box-->
    </div>
    
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
        <div class="info-box blue-bg">
            <i class="glyphicon glyphicon-time"></i>
            <div class="count"><?php echo ("{$request->getAmount()} {$config->get('devise')}"); ?></div>
            <div class="title">request</div>
        </div>
        <!--/.info-box-->
    </div>
    <?php endforeach; ?>
    <?php endif; ?>
</div>