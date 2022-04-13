<?php 
use Applications\Admin\Modules\Products\ProductsController;
use PHPBackend\Text\HtmlFormater;
use Core\Shivalik\Entities\Category;


/**
 * @var Category[] $categotries
 */
$categotries = $_REQUEST[ProductsController::ATT_CATEGORIES];
?>

<ul class="list-group">
	<?php foreach ($categotries  as $c) : ?>
	<li class="list-group-item">
		<h4><?php echo htmlspecialchars($c->getTitle());?></h4>
		<p><?php echo HtmlFormater::toHTML($c->getDescription()); ?></p>
	</li>
	<?php endforeach; ?>
</ul>