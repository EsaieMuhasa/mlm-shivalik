<div class="panel panel-default">
    <div class="panel-heading">
        <div>
            <strong class="panel-title">Main config</strong>
            <div class="btn-group pull-right" style="margin-top: 2px;">
                <a href="/admin/budget/new/select-element-config" class="btn btn-primary">
                    <span class="fa fa-plus"></span> new config
                </a>
            </div>
        </div>
    </div>

    <div class="panel-body">
        <div class="row">
            <div class="col-xs-12 col-sm-4">
                <?php if ($_REQUEST['config']) : ?>
                <div class="graphic" data-config="/admin/budget/config-<?php echo ($_REQUEST['config']->id);  ?>.json">
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
                            <th>Options</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($_REQUEST['elements'] as $item) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item->rubric->label); ?></td>
                                <td><?php echo htmlspecialchars($item->percent); ?> % </td>
                                <td><?php echo htmlspecialchars($item->getFormatedDateAjout()); ?></td>
                                <td>
                                    <a class="btn btn-primary" href="/admin/budget/sub-config/<?php echo $item->id; ?>/">
                                        <span class="fa fa-recycle"></span> Sub config
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>