<?php 

use Applications\Office\Modules\Products\ProductsController;
use PHPBackend\Calendar\Month;
use PHPBackend\Request;
use PHPBackend\AppConfig;
use Core\Shivalik\Entities\Command;

/**
 * @var Month $month
 */
$month = $_REQUEST[ProductsController::ATT_MONTH];
$next = $month->nextMonth();
$prev = $month->previousMonth();

$year = $_REQUEST[ProductsController::ATT_YEAR];

/**
 * @var AppConfig $config
 */
$config = $_REQUEST[Request::ATT_APP_CONFIG];

/**
 * @var Command[] $commands
 */
$commands = $_REQUEST[ProductsController::ATT_COMMANDS];

$somme = 0 ;
foreach ($commands as $c) {
    $somme += $c->getAmount();
}
?>

<h2><?php echo ($_REQUEST['title']); ?></h2>

<div class="row">
	<!-- calendar -->
	<div class="col-lg-6 col-md-7 col-sm-12 col-xs-12">
		<div class="panel panel-default">
	        <header class="panel-heading">
	            <h2 class="panel-title"><?php echo $month->toString();?></h2>
	            <span class="pull-right">
	            	<span class="btn-group">
    	            	<a title="<?php echo ($prev);?>" class="btn btn-primary" href="<?php echo ("/office/products/commands-of-{$prev->getFirstDay()->format('m-Y')}");?>/">
    	        			<span class="glyphicon glyphicon-step-backward"></span>
        	        	</a>
        	        	<a title="<?php echo ($next);?>" class="btn btn-info" href="<?php echo ("/office/products/commands-of-{$next->getFirstDay()->format('m-Y')}");?>/">
        	        		<span class="glyphicon glyphicon-step-forward"></span>
        	        	</a>
	            	</span>
	            </span>
	        </header>
	        
	        <table class="table table-bordered text-center table-links">
	            <thead class="text-center">
	                <tr>
	                	<?php foreach($month->getShortDaysName() as $dayName) : ?>
	                    <th class="text-center"><?php echo $dayName;?></th>
	                    <?php endforeach; ?>
	                    <th class="text-center info">Week</th>
	                </tr>
	            </thead>
	            
	            <tbody>
	            	<?php while ($month->hasNextWeek()) :
	            	   $month->nextWeek();
	            	?>
	                <tr>
	                    <?php while ($month->hasNextDay()) : ?>
	                    <?php $date =  $month->nextDay(); ?>
	                    <td class="<?php echo (($month->isSelectedWeek($month->getCurrentWeek()))? 'danger':'');?>">
	                        <?php if ($month->inMonth($date)) { ?>
	                    	<a href="<?php echo ("/office/products/commands-of-{$date->format('d-m-Y')}");?>/" class="btn btn-xs <?php echo ($month->isSelectedDate($date)? 'btn-primary ':'').($month->isToday($date)? ' text-primary':'').($month->isEventDate($date)? 'text-danger':'');?>">
	                    		<span class=""><?php echo $date->format('d');?></span>
	                    	</a>
	                        <?php } else { ?>
	                        <small class="text"><?php echo $date->format('d');?></small>
	                        <?php } ?>
	                    </td>
	                    <?php endwhile; ?>
	                    <td class="<?php echo ($month->isSelectedWeek($month->getCurrentWeek())? 'danger':'info');?>">
	                    	<a href="<?php echo ("/office/products/commands-of-{$month->getFirstDayOfCurrentWeek()->format('d-m-Y')}-to-{$month->getLastDayOfCurrentWeek()->format('d-m-Y')}-week-{$month->getCurrentWeek()}");?>/" class="btn btn-xs">
	                    		<strong class="text-danger"><?php echo ($month->getCurrentWeek()+1); ?></strong>
	                    	</a>
	                    </td>
	                </tr>
	                <?php endwhile; ?>
	            </tbody>
	        </table>
	        <div class="panel-footer">
	        	<div class="row">
	        		<div class="col-xs-3">
        	        	<a title="<?php echo ($month->getMonth()<10? '0'.$month->getMonth():'').' '.($month->getYear()-1);?>" class="btn btn-primary btn-block" href="<?php echo "/office/products/commands-of-".($month->getMonth()<10? '0':'').$month->getMonth().'-'.($month->getYear()-1);?>/">
        	        		<span class="glyphicon glyphicon-fast-backward"></span> <?php echo htmlspecialchars($month->getYear()-1)?>
        	        	</a>
	        		</div>
	        		<div class="col-xs-6">
        	        	<div class="dropup">
                        	<button class="btn btn-info dropdown-toggle btn-block" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        		<?php echo htmlspecialchars($month)?> <span class="caret"></span>
                        	</button>
                        	<ul class="dropdown-menu" aria-labelledby="dropdownMenu2">
                        		<?php for ($i = 1; $i <= 12 ; $i++) : 
                        		  $m = $year->get($i); ?>                        		
                        		<li class="<?php echo ($i == $month->getMonth()? "active" : ""); ?>">
                        			<a href="<?php echo "/office/products/commands-of-{$m->getMonth()}-{$year}";?>/"><?php echo htmlspecialchars($m->getName())?></a>
                        		</li>
                        		<?php endfor;?>
                        	</ul>
                        </div>
	        		</div>
	        		<div class="col-xs-3">
        	        	<a title="<?php echo ($month->getMonth()<10? '0'.$month->getMonth():'').($month->getYear()+1);?>" class="btn btn-primary btn-block" href="<?php echo "/office/products/commands-of-".($month->getMonth()<10? '0':'').$month->getMonth().'-'.($month->getYear()+1);?>/">
        	        		<?php echo htmlspecialchars($month->getYear()+1)?> <span class="glyphicon glyphicon-fast-forward"></span> 
        	        	</a>
	        		</div>
	        	</div> 
	        </div>
	   	</div>
	</div>
    <!-- //calendar -->
    
    <div class="col-lg-6 col-md-5 col-sm-12 col-xs-12">
    	<div class="row">
    		<div class="col-lg-12 col-md-12 col-sm-6 col-xs-12">
    			<div class="info-box blue-bg">
		            <i class="fa fa-money"></i>
		            <div class="count"><?php echo "{$somme} {$config->get('devise')}"; ?></div>
		            <div class="title">vente</div>
		        </div>
    		</div>
    		<div class="col-lg-12 col-md-12 col-sm-6 col-xs-12">
    			<div class="info-box blue-bg">
		            <i class="fa fa-comment"></i>
		            <div class="count"><?php echo count($commands); ?></div>
		            <div class="title">command count</div>
		        </div>
    		</div>
    	</div>
    </div>
</div>

<div class="">
	<?php foreach ($commands as $command) : ?>
    <div class="thumbnail">
    	<div class="row">
    		<div class="col-lg-1 col-md-1 col-sm-2 col-xs-3">
    			<img class="thumbnail" alt="<?php echo htmlspecialchars($command->getMember()->getNames()); ?>" src="/<?php echo $command->getMember()->getPhoto(); ?>">
    		</div>
    		<div class="col-lg-6 col-md-6 col-sm-8 col-xs-7">
    			<h4 class="text-primary"><?php echo htmlspecialchars($command->getMember()->getMatricule()); ?></h4>
    			<p><?php echo htmlspecialchars($command->getMember()->getNames()); ?></p>
    		</div>
    	</div><div style="border-bottom: 1px solid #010101;"></div>
    	
    	<table class="table table-bordered">
    		<thead>
    			<tr>
    				<th>N°</th>
    				<th>Product</th>
    				<th>Quantity</th>
    				<th>Unit price</th>
    				<th>Total</th>
    			</tr>
    		</thead>
    		<tbody>
    			<?php foreach ($command->getProducts() as $key => $product) : ?>
    			<tr>
    				<td><?php echo $key+1; ?></td>
    				<td><?php echo htmlspecialchars($product->product->name); ?></td>
    				<td><?php echo htmlspecialchars($product->quantity); ?></td>
    				<td><?php echo htmlspecialchars("{$product->stock->unitPrice} {$config->get('devise')}"); ?></td>
    				<td><?php echo htmlspecialchars("{$product->amount} {$config->get('devise')}"); ?></td>
    			</tr>
    			<?php endforeach; ?>
    		</tbody>
    		<tfoot>
    			<tr>
        			<th colspan="2">Total</th>
        			<th><?php echo ($command->getTotalQuantity()); ?></th>
        			<th><?php echo ("{$command->getTotalUnitPrice()} {$config->get('devise')}"); ?></th>
        			<th class="info text-primary"><?php echo ("{$command->getAmount()} {$config->get('devise')}"); ?></th>
    			</tr>
    		</tfoot>
    	</table>
    	
    	<div class="row">
    		<div class="col-xs-12 text-right">
    			<div class=" btn-group">
            		<?php if ($command->getDeliveryDate() == null) : ?>
        			<a class="btn btn-primary" href="<?php echo "/office/products/commands/{$command->id}/delivered.html"; ?>">
        				<span class="glyphicon glyphicon-ok"></span> Delivered
    				</a>
            		<?php endif;?>
    				<a class="btn btn-info" target="_black" href="<?php echo "/office/products/commands/{$command->id}/pdf.pdf"; ?>">
    					<span class="fa fa-print"></span> PDF
    				</a>
    			</div>
    		</div>
    	</div>
    </div>
	<?php endforeach; ?>
</div>