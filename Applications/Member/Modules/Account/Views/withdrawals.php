<?php
use Applications\Root\Modules\Settings\SettingsController;
use Library\Config;
use Applications\Member\Modules\Account\AccountController;

$config = Config::getInstance();

/**
 * @var Entities\Account $account
 */
$account = $_REQUEST[AccountController::ATT_ACCOUNT];
?>

<div class="row">
    <div class="col-lg-12">
    	<h3 class="page-header"><i class="fa fa-money"></i> <?php echo ($_REQUEST[SettingsController::ATT_VIEW_TITLE]); ?></h3>
    </div>
    <?php if (!$account->hasWithdrawRequest()) : ?>
	<div class="col-xs-12">
		<a class="btn btn-primary" href="/member/withdrawals/new.html">
			<span class="fa fa-plus"></span> Withdrawal
		</a>
	</div>
	<?php endif; ?>
</div>
<hr/>

<div class="row">
	<?php foreach ($_REQUEST[AccountController::ATT_WITHDRAWELS] as $w) : ?>
    <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
    	<?php if ($w->admin==null) { ?>
        <a class="info-box blue-bg" href="/member/withdrawals/<?php echo ($w->id); ?>/update.html" style="display: block;">
        	<strong>Request ID: <span class="badge"><?php echo ($w->id); ?></span> 
        		<span class="label label-danger"><?php echo ($w->telephone); ?></span>
        	</strong>
            <i class="glyphicon glyphicon-remove-sign"></i>
            <span class="count" style="display: block;"><?php echo ("{$w->amount} {$config->get('devise')}"); ?></span>
            <br/>
            <span class="title" style="display: block;text-transform: inherit;">
            	<?php echo htmlspecialchars("requested at {$w->dateAjout->format('d/m/Y \a\t H:i')}, to withdraw at the office {$w->office->name}"); ?>
            </span>
        </a>
        <?php } else { ?>
        <div class="info-box green-bg">
        	<strong>Request ID: <span class="badge"><?php echo ($w->id); ?></span>  <span class="label label-danger"><?php echo ($w->telephone); ?></span></strong>
            <i class="glyphicon glyphicon-ok-sign"></i>
            <span class="count" style="display: block;"><?php echo ("{$w->amount} {$config->get('devise')}"); ?></span>
            <br/>
            <span class="title" style="display: block;text-transform: inherit;">
            	<?php echo htmlspecialchars("requested at {$w->dateAjout->format('d/m/Y \a\t H:i')}, withdraw at the office {$w->office->name}"); ?>
            </span>
        </div>
        <?php }?>
    </div>
    <?php endforeach;?>
</div>