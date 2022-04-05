<?php 
use Applications\Admin\Modules\Products\ProductsController;
use Core\Shivalik\Entities\Categorie;
use PHPBackend\Text\HtmlFormater;


/**
 * @var Categorie[] $categotries
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