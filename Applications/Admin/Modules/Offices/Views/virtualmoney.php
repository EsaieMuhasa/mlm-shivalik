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

<div class="panel panel-default">
	<div class="panel-heading">
		<strong class="panel-title">Virtual moneys</strong>
	</div>
	<div class="panel-body">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <div class="info-box dark-bg">
                    <i class="glyphicon glyphicon-leaf"></i>
                    <div class="count"><?php echo ("{$office->getAvailableVirtualMoneyProduct()} {$config->get('devise')}"); ?></div>
                    <div class="title">Available product account</div>
                </div>
                <!--/.info-box-->
            </div>
        
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <div class="info-box dark-bg">
                    <i class="glyphicon glyphicon-user"></i>
                    <div class="count"><?php echo ("{$office->getAvailableVirualMoneyAfiliate()} {$config->get('devise')}"); ?></div>
                    <div class="title">Available membership account</div>
                </div>
                <!--/.info-box-->
            </div>
            
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <div class="info-box red-bg">
                    <i class="glyphicon glyphicon-trash"></i>
                    <div class="count"><?php echo ("{$office->getTrashVirtualMoneyProduct()} {$config->get('devise')}"); ?></div>
                    <div class="title">Trash Product</div>
                </div>
                <!--/.info-box-->
            </div>
        
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <div class="info-box red-bg">
                    <i class="glyphicon glyphicon-trash"></i>
                    <div class="count"><?php echo ("{$office->getTrashVirtualMoneyAfiliate()} {$config->get('devise')}"); ?></div>
                    <div class="title">Trash Membership</div>
                </div>
                <!--/.info-box-->
            </div>
            
            <?php if (!empty($requests)) : ?>
            <?php foreach ($requests as $request) : ?>
            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                <div class="info-box red-bg">
                    <i class="glyphicon glyphicon-time"></i>
                    <div class="count"><?php echo ("{$request->getAmount()} {$config->get('devise')}"); ?></div>
                    <div class="title">Request</div>
                </div>
                <!--/.info-box-->
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
	</div>
</div>
