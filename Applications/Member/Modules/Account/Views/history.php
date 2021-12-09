<?php
use Applications\Member\Modules\Account\AccountController;
use Library\Calendar\Month;
use Library\Calendar\Year;
use Entities\Withdrawal;
use Library\Config;
use Entities\PointValue;
use Entities\BonusGeneration;

/**
 * @var Month $month
 */
$month = $_REQUEST[AccountController::ATT_MONTH];
$prev = $month->previousMonth();
$next = $month->nextMonth();

$year = new Year($month->getYear());

$config = Config::getInstance();

/**
 * @var Withdrawal[] $withdrawals
 */
$withdrawals = $_REQUEST[AccountController::ATT_WITHDRAWELS];

/**
 * @var PointValue[] $points
 */
$points = $_REQUEST[AccountController::ATT_POINTS_VALUES];

/**
 * @var BonusGeneration[] $bonus
 */
$bonus = $_REQUEST[AccountController::ATT_BONUS_GENERATIONS];

if ($month->hasSelectedDate()) {
    $date = $month->getFirstSelectedDate();
}else{
    $date = null;
}
?>
<div class="row">
    <div class="col-lg-12">
    	<h1 class="page-header"><i class="fa fa-calendar"></i> <?php echo ($_REQUEST[AccountController::ATT_VIEW_TITLE]); ?></h1><hr/>
    </div>
</div>
<div class="row">
	<div class="col-lg-8 col-md-7 col-sm-6 col-xs-12">
		<h2><?php echo htmlspecialchars(($date==null? "":"{$date->format('d ')}").$month); ?></h2>
		<hr/>
		<?php if (!empty($withdrawals)) : ?>
		<section class="table-responsive">
        	<table class="table panel panel-default">
        		<caption>Withdrawals</caption>
        		<thead class="panel-heading">
        			<tr>
        				<th>N°</th>
        				<th>Amount</th>
        				<th>Served</th>
        				<th>date and time</th>
        			</tr>
        		</thead>
        		<tbody class="panel-body">
        			<?php $num = 0; ?>
					<?php foreach ($withdrawals as $withdrawal): ?>
    					<tr>
    						<td><?php  $num++; echo ($num);?> </td>
    						<td><?php echo ("{$withdrawal->getAmount()} {$config->get('devise')}");?></td>
    						<td class="<?php echo ($withdrawal->getAdmin()!=null? "text-success":"text-danger"); ?>">
    							<span class="glyphicon glyphicon-<?php echo ($withdrawal->getAdmin()!=null? "ok":"remove"); ?>"></span>
    						</td>
    						<td><?php echo ($withdrawal->dateAjout->format('d/m/Y \a\t H\h:i'));?></td>
    					</tr>
					<?php endforeach; ?>
        		</tbody>
        	</table>
        </section>
		<?php endif; ?>
		
		<?php if (!empty($points)) : ?>
		<section class="table-responsive">
        	<table class="table panel panel-default">
        		<caption>Generational bonus</caption>
        		<thead class="panel-heading">
        			<tr>
        				<th>N°</th>
        				<th>Amount</th>
        				<th>date and time</th>
        			</tr>
        		</thead>
        		<tbody class="panel-body">
        			<?php $num = 0; ?>
					<?php foreach ($bonus as $bn): ?>
    					<tr>
    						<td><?php  $num++; echo ($num);?> </td>
    						<td><?php echo ("{$bn->getAmount()} {$config->get('devise')}");?></td>
    						<td><?php echo ($bn->dateAjout->format('d/m/Y \a\t H\h:i'));?></td>
    					</tr>
					<?php endforeach; ?>
        		</tbody>
        	</table>
        </section>
		<?php endif; ?>
		
		<?php if (!empty($points)) : ?>
		<section class="table-responsive">
        	<table class="table panel panel-default">
        		<caption>PV</caption>
        		<thead class="panel-heading">
        			<tr>
        				<th>N°</th>
        				<th>Value</th>
        				<th>Foot</th>
        				<th>date and time</th>
        			</tr>
        		</thead>
        		<tbody class="panel-body">
        			<?php $num = 0; ?>
					<?php foreach ($points as $pv): ?>
    					<tr>
    						<td><?php  $num++; echo ($num);?> </td>
    						<td><?php echo ("{$pv->getValue()} PV");?></td>
    						<td><?php echo ("{$pv->getFootName()}");?></td>
    						<td><?php echo ($pv->dateAjout->format('d/m/Y \a\t H\h:i'));?></td>
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
    	            	<a title="<?php echo ($prev);?>" class="btn btn-primary" href="<?php echo ("/member/history/{$prev->getFirstDay()->format('m-Y')}");?>.html">
    	        			<span class="glyphicon glyphicon-step-backward"></span>
        	        	</a>
        	        	<a title="<?php echo ($next);?>" class="btn btn-info" href="<?php echo ("/member/history/{$next->getFirstDay()->format('m-Y')}");?>.html">
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
	                    	<a href="<?php echo ("/member/history/{$date->format('d-m-Y')}");?>.html" class="btn btn-xs <?php echo ($month->isSelectedDate($date)? 'btn-primary ':'').($month->isToday($date)? ' text-primary':'').($month->isEventDate($date)? 'text-danger':'');?>">
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
        	        	<a title="<?php echo ($month->getYear()-1);?>" class="btn btn-primary btn-block" href="<?php echo "/member/history/01-".($month->getYear()-1);?>.html">
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
                        			<a href="<?php echo "/member/history/{$m->getMonth()}-{$year}";?>.html"><?php echo htmlspecialchars($m->getName())?></a>
                        		</li>
                        		<?php endfor;?>
                        	</ul>
                        </div>
	        		</div>
	        		<div class="col-xs-3">
        	        	<a title="<?php echo ($month->getYear()+1);?>" class="btn btn-primary btn-block" href="<?php echo "/member/history/01-".($month->getYear()+1);?>.html">
        	        		<?php echo htmlspecialchars($month->getYear()+1)?> <span class="glyphicon glyphicon-fast-forward"></span> 
        	        	</a>
	        		</div>
	        	</div> 
	        </div>
	   	</div>
	</div>
</div>