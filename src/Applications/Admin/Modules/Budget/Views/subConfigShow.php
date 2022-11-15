<?php
$elements = $_REQUEST['elements'];
$element = $_REQUEST['element'];
$items = $_REQUEST['items'];
?>

<div class="row">
    <div class="col-xs-12 col-sm-7 col-md-8 col-lg-9">

        <div class="panel panel-default">
            <div class="panel-heading">
                <div>
                    <strong class="panel-title"><?php echo $element->rubric->label; ?></strong>
                    <div class="btn-group pull-right" style="margin-top: 2px;">
                        <a href="/admin/budget/sub-config/<?php echo $element->id; ?>/new/select-element-config" class="btn btn-primary">
                            <span class="fa fa-plus"></span> new config
                        </a>
                    </div>
                </div>
            </div>
        
            <div class="panel-body">
                <div class="row">
                    <div class="col-xs-12 col-sm-4">
                        <?php if (!empty($items)) : ?>
                        <div class="graphic" data-config="/admin/budget/sub-config/<?php echo ($element->id);  ?>/catalogue.json">
                            <canvas></canvas>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-xs-12 col-sm-8">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Label</th>
                                    <th>Percent</th>
                                    <th>recording date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($items as $item) : ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($item->rubric->label); ?></td>
                                        <td><?php echo htmlspecialchars($item->percent); ?> % </td>
                                        <td><?php echo htmlspecialchars($item->getFormatedDateAjout()); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <div class="col-xs-12 col-sm-5 col-md-4 col-lg-3">
        <h3>Other elements</h3>
        <div class="list list-group">
            <?php foreach ($elements as $item) : ?>
                <a href="/admin/budget/sub-config/<?php echo $item->id; ?>/" class="list-group-item <?php echo $item->id == $element->id? 'active' : '' ?>">
                    <?php echo htmlspecialchars($item->rubric->label); ?>
                    <span class="badge"><?php echo htmlspecialchars($item->percent); ?>%</span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>
