<?php
use Applications\Office\Modules\Dashboard\DashboardController;
use Core\Shivalik\Entities\MoneyGradeMember;
use PHPBackend\AppConfig;
use PHPBackend\Calendar\Month;
use PHPBackend\Calendar\Year;
use PHPBackend\Request;

/**
 * @var Month $month
 */
$month = $_REQUEST[DashboardController::ATT_MONTH];
$prev = $month->previousMonth();
$next = $month->nextMonth();

$year = new Year($month->getYear());

/**
 * @var AppConfig $config
 */
$config = $_REQUEST[Request::ATT_APP_CONFIG];


/**
 * @var MoneyGradeMember[] $data
 */
$data = $_REQUEST['moneys'];

?>

<div class="row">
    <div class="col-lg-8 col-md-7 col-sm-12 col-xs-12">
		<?php if (!empty($data)) : ?>
			<section class="table-responsive">
				<table class="table panel panel-default">
					<caption>
						Operations of <?php echo $month; ?> <?php echo $month->getFirstSelectedDate() ? ', date of '.$month->getFirstSelectedDate()->format('d/m/Y'):''; ?>
					</caption>
					<thead class="panel-heading">
						<tr>
							<th>N°</th>
							<th>Membership</th>
							<th>Product</th>
							<th>date and time</th>
						</tr>
					</thead>
					<tbody class="panel-body">
						<?php $num = 0; ?>
						<?php foreach ($data as $item): ?>
							<tr>
								<td><?php  $num++; echo ($num);?> </td>
								<td><?php echo ("{$item->getAfiliate()} {$config->get('devise')}");?></td>
								<td><?php echo ("{$item->getProduct()} {$config->get('devise')}");?></td>
								<td><?php echo ($item->dateAjout->format('d/m/Y \a\t H\h:i'));?></td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</section>
		<?php endif; ?>
    </div>

    <!-- calendar -->
	<div class="col-lg-4 col-md-5 col-md-offset-0 col-sm-6 col-sm-offset-6 col-xs-12 col-xs-offset-0">
		<div class="panel panel-default">
	        <header class="panel-heading">
	            <h2 class="panel-title"><?php echo $month->toString();?></h2>
	            <span class="pull-right">
	            	<span class="btn-group">
    	            	<a title="<?php echo ($prev);?>" class="btn btn-primary" href="<?php echo ("/office/virtualmoney/{$prev->getFirstDay()->format('m-Y')}");?>.html">
    	        			<span class="glyphicon glyphicon-step-backward"></span>
        	        	</a>
        	        	<a title="<?php echo ($next);?>" class="btn btn-info" href="<?php echo ("/office/virtualmoney/{$next->getFirstDay()->format('m-Y')}");?>.html">
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
	                </tr>
	            </thead>
	            
	            <tbody>
	            	<?php while ($month->hasNextWeek()) :
	            	   $month->nextWeek();
	            	?>
	                <tr>
	                    <?php while ($month->hasNextDay()) : ?>
	                    <?php $date =  $month->nextDay(); ?>
	                    <td class="<?php echo ($month->isSelectedDate($date)? 'selected-date ':'').($month->isToday($date)? ' today':'').($month->isEventDate($date)? 'has-event':'');?>">
	                        <?php if ($month->inMonth($date)) :?>
	                    	<a href="<?php echo ("/office/virtualmoney/{$date->format('d-m-Y')}");?>.html" class="btn btn-xs <?php echo ($month->isSelectedDate($date)? 'btn-primary ':'').($month->isToday($date)? ' text-primary':'').($month->isEventDate($date)? 'text-danger':'');?>">
	                    		<span class=""><?php echo $date->format('d');?></span>
	                    	</a>
	                        <?php endif; ?>
	                    </td>
	                    <?php endwhile; ?>
	                </tr>
	                <?php endwhile; ?>
	            </tbody>
	        </table>
	        <div class="panel-footer">
	        	<div class="row">
	        		<div class="col-xs-3">
        	        	<a title="<?php echo ($month->getYear()-1);?>" class="btn btn-primary btn-block" href="<?php echo "/office/virtualmoney/01-".($month->getYear()-1);?>.html">
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
                        			<a href="<?php echo "/office/virtualmoney/{$m->getMonth()}-{$year}";?>.html"><?php echo htmlspecialchars($m->getName())?></a>
                        		</li>
                        		<?php endfor;?>
                        	</ul>
                        </div>
	        		</div>
	        		<div class="col-xs-3">
        	        	<a title="<?php echo ($month->getYear()+1);?>" class="btn btn-primary btn-block" href="<?php echo "/office/virtualmoney/01-".($month->getYear()+1);?>.html">
        	        		<?php echo htmlspecialchars($month->getYear()+1)?> <span class="glyphicon glyphicon-fast-forward"></span> 
        	        	</a>
	        		</div>
	        	</div> 
	        </div>
	   	</div>
	</div>
</div>