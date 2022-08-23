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

<div class="panel panel-default">
    <div class="panel-heading"><strong class="panel-title">Availables</strong></div>
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
        </div>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading"><strong class="panel-title">Trash</strong></div>
    <div class="panel-body">
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <div class="info-box red-bg">
                    <i class="glyphicon glyphicon-trash"></i>
                    <div class="count"><?php echo ("{$office->getTrashVirtualMoneyProduct()} {$config->get('devise')}"); ?></div>
                    <div class="title">Product</div>
                </div>
                <!--/.info-box-->
            </div>
            
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <div class="info-box red-bg">
                    <i class="glyphicon glyphicon-trash"></i>
                    <div class="count"><?php echo ("{$office->getTrashVirtualMoneyAfiliate()} {$config->get('devise')}"); ?></div>
                    <div class="title">Membership</div>
                </div>
                <!--/.info-box-->
            </div>
        </div>
    </div>
</div>

<?php if (!empty($requests)) : ?>
    <div class="panel panel-default">
        <div class="panel-heading"><strong class="panel-title">Requested</strong></div>
            <div class="panel-body">
                <div class="row">
                <?php foreach ($requests as $request) : ?>
                    <?php if ($request->getProduct() != 0) : ?>
                    <div class="col-lg-6 col-md-4 col-sm-6 col-xs-12">
                        <div class="info-box blue-bg">
                            <i class="glyphicon glyphicon-time"></i>
                            <div class="count"><?php echo ("{$request->getProduct()} {$config->get('devise')}"); ?></div>
                            <div class="title">product</div>
                        </div>
                        <!--/.info-box-->
                    </div>
                    <?php endif; ?>

                    <?php if ($request->getAffiliation() != 0) : ?>
                    <div class="col-lg-6 col-md-4 col-sm-6 col-xs-12">
                        <div class="info-box blue-bg">
                            <i class="glyphicon glyphicon-users"></i>
                            <div class="count"><?php echo ("{$request->getAffiliation()} {$config->get('devise')}"); ?></div>
                            <div class="title">Affiliation</div>
                        </div>
                        <!--/.info-box-->
                    </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
<?php endif; ?>