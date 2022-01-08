<?php 

use Applications\Member\Modules\MyOffice\MyOfficeController;

/**
 * @var \Core\Shivalik\Entities\GradeMember[] $packets
 */
$packets = $_REQUEST[MyOfficeController::ATT_GRADES_MEMBERS];
?>

<div class="row">
	<div class="col-sm-12">
    	<?php if (!empty($packets)) : ?>
        <div class="panel panel-default">
            <header class="panel-heading">
            	<h2 class="panel-title">Upgrades</h2>
            </header>
            <section class="table-responsive">
            	<table class="table">
            		<thead>
            			<tr>
            				<th>N°</th>
            				<th>Old</th>
            				<th>New</th>
            				<th>Photo</th>
            				<th>Names</th>
            				<th>ID</th>
            				<th>date and time</th>
            			</tr>
            		</thead>
            		<tbody>
            			<?php $num = 0; ?>
    					<?php foreach ($packets as $packet): ?>
        					<tr>
        						<td><?php  $num++; echo ($num);?> </td>
        						<td style="width: 30px;" title="<?php echo htmlspecialchars($packet->getOld()->getGrade()->getName());?>">
        							<img alt="<?php echo htmlspecialchars($packet->getOld()->getGrade()->getName());?>" style="width: 30px;" src="/<?php echo ($packet->getOld()->getGrade()->getIcon());?>" >
        						</td>
        						<td style="width: 30px;" title="<?php echo htmlspecialchars($packet->getGrade()->getName());?>">
        							<img style="width: 30px;" alt="<?php echo htmlspecialchars($packet->getGrade()->getName());?>" src="/<?php echo ($packet->getGrade()->getIcon());?>">
        						</td>
        						<td style="width: 30px;">
        							<img style="width: 30px;" alt="<?php echo htmlspecialchars($packet->getMember()->getNames());?>" src="/<?php echo ($packet->getMember()->getPhoto());?>">
        						</td>
        						<td><?php echo htmlspecialchars($packet->getMember()->getNames());?></td>
        						<td><?php echo ($packet->getMember()->getMatricule());?></td>
        						<td><?php echo ($packet->dateAjout->format('d/m/Y \a\t H\h:i'));?></td>
        					</tr>
    					<?php endforeach; ?>
            		</tbody>
            	</table>
            </section>
        </div>
        <?php endif; ?>
	</div>
</div>