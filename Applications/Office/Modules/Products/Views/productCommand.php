<?php 
use Applications\Office\Modules\Products\ProductsController;
use Core\Shivalik\Entities\AuxiliaryStock;
use PHPBackend\Request;
use PHPBackend\AppConfig;
use Core\Shivalik\Entities\Command;

/**
 * @var Command $command
 * @var AppConfig $config
 * @var AuxiliaryStock[] $stocks
 */
$stocks = $_REQUEST[ProductsController::ATT_STOCKS];
$command = $_SESSION[ProductsController::ATT_COMMAND];
$config = $_REQUEST[Request::ATT_APP_CONFIG];
?>
<section class="panel">
    <header class="panel-heading" style="padding-top: 15px;">
    	<p style="border-bottom: 1px solid #fff;">
    		<a class="btn btn-primary "  href="/office/products/command/member-<?php echo $command->member->matricule; ?>.html">
        		<img alt="" style="width: 30px;border-radius: 20px;" src="/<?php echo $command->getMember()->getPhoto(); ?>"> 
        		<strong><?php echo htmlspecialchars($command->getMember()->getNames()); ?></strong>
    		</a>
    	</p>
    	<strong><span class="text-danger">Step 2</span> >> Choose the products</strong>
    	<p class="text-info">Layers only products ordered by the member</p>
    </header>
    <div class="panel-body">
    	<form role="form" action="" method="POST">
    		
    		<?php if (isset($_REQUEST['result'])){?>
    		<div class="alert alert-<?php echo (isset($_REQUEST['errors']) && empty($_REQUEST['errors'])? 'success':'danger')?>">
    			<?php echo ($_REQUEST['result']);?>
    			<hr/>
    			<?php if (isset($_REQUEST['errors'])): ?>
    			<?php foreach ($_REQUEST['errors'] as $msg) : ?>
    			<p><?php echo ($msg);?></p>
    			<?php endforeach; ?>
    			<?php endif;?>
    		</div>
    		<?php }?>
    		
    		<div class="row">
    		<?php foreach ($stocks as $stock) : ?>
    			<div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
    				<div class="thumbnail">
    					<p class="h4 alert alert-info">
    						<label for="stocks<?php echo $stock->id; ?>">
        						<input type="checkbox" id="stocks<?php echo $stock->id; ?>" value="<?php echo $stock->id; ?>" name="stocks[]"/> <span><?php echo htmlspecialchars($stock->getProduct()->name); ?></span>
    						</label>
    						
    						<span class="pull-right">
    							<?php echo "{$config->get('devise')} {$stock->getUnitPrice()}"; ?>
    						</span>
    					</p>
    					
    					<label class="thumbnail" for="stocks<?php echo $stock->id; ?>">
        					<img alt="" src="/<?php echo $stock->product->picture; ?>"/>
    					</label>
    					
    					<div class="form-group">
    						<span class="input-group">
                    			<input type="number" name="quantity<?php echo $stock->id; ?>" min="1" value="1" max="<?php echo $stock->getSold(); ?>" id="product-quantity" class="form-control" autocomplete="off"/>
                    			<span class="input-group-addon"> / <?php echo $stock->getSold(); ?></span>
                			</span>
                			<?php if (isset($_REQUEST['errors']["quantity{$stock->id}"])) {?>
                			<p class="help-block"><?php echo htmlspecialchars($_REQUEST['errors']["quantity{$stock->id}"]);?></p>
                			<?php }?>
    					</div>
    				</div>
    			</div>
    		<?php endforeach; ?>
    		</div>
    		
    		<div class="form-group text-center">
    			<button class="btn btn-primary" type="submit"><span class="fa fa-forward"></span> Next</button>
    			<a class="btn btn-danger" href="/office/products/command/cancel.html">
            		<span class="glyphicon glyphicon-remove"></span> Cancel
            	</a>
    		</div>
		</form>
	</div>
</section>