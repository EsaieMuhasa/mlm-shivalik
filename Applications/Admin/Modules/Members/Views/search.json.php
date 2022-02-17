<?php
use Applications\Admin\Modules\Members\MembersController;
use Core\Shivalik\Entities\Member;

/**
 * @var Member[] $membres
 */
$membres = $_REQUEST[MembersController::ATT_MEMBERS];
$feedback = $_REQUEST[MembersController::ATT_FORM_VALIDATOR];

echo "\"members\" : [";

$max = count($membres);
foreach ($membres as $key => $m) {
    echo "{$m->toJSON()}" .($max == ($key+1)? '':',');
}
echo "]";
echo ", \"feedback\" : ".$feedback->toJSON();