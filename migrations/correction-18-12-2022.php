<?php

require_once dirname(__DIR__).DIRECTORY_SEPARATOR."vendor".DIRECTORY_SEPARATOR."autoload.php";

use PHPBackend\Dao\UtilitaireSQL;

$db1 = new PDO(
    'mysql:host=localhost;dbname=shivalik_db1', 'muhasa', 'esaiemuhasa',
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

$db2 = new PDO(
    'mysql:host=localhost;dbname=shivalik_db2', 'muhasa', 'esaiemuhasa',
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);


/**
 * (0) armonisation des virtualMoneys
 * (1) selection des membres depuis db2 qui ne sont pas dans db1
 * (2) insersion de ceux-ci dans db1 (plus leurs packets)
 * (3) importation des operations des PVs
 * (4) importation des matchings du db2 dans db1 (uniquement ceux qui ne figure pas dans db1)
 */

if (!$db1->beginTransaction()){
    echo "Echec de creation de la transaction\n\n";
    return false;
}


//virtual money
$st_vm = $db2->prepare('SELECT * FROM VirtualMoney');
$st_vm->execute();
while ($vm = $st_vm->fetch()) {
    $st_vm1 = $db1->prepare('SELECT * FROM VirtualMoney WHERE id = ?');
    $st_vm1->execute([$vm['id']]);
    if ($vm1 = $st_vm1->fetch()) {
        continue;
    }

    echo sprintf("virtual: [%d] product = %d USD et afiliate = %d USD\n", $vm['id'], $vm['product'], $vm['afiliate']);
    UtilitaireSQL::insert($db1, "VirtualMoney", [
        'id' => $vm['id'],
        'product' => $vm['product'],
        'config' => $vm['config'],
        'afiliate' => $vm['afiliate'],
        'office' => $vm['office'],
        'request' => $vm['request'],
        'amount' => $vm['amount'],
        'dateAjout' => $vm['dateAjout']
    ], false);

    //bonus des offices
    $st_ob = $db2->prepare('SELECT * FROM OfficeBonus WHERE virtualMoney = ?');
    $st_ob->execute([$vm['id']]);
    while ($bonus = $st_ob->fetch()) {
        UtilitaireSQL::insert($db1, "OfficeBonus", [
            'id' => $bonus['id'],
            'member' => $bonus['member'],
            'generator' => $bonus['generator'],
            'amount' => $bonus['amount'],
            'virtualMoney' => $bonus['virtualMoney'],
            'dateAjout' => $bonus['dateAjout']
        ], false);
    }
}


//========================
// Membres
//========================
$st = $db2->prepare('SELECT * FROM Member ORDER BY id');//IDs des membres du db2
if($st->execute()) {
    while ($row = $st->fetch()) {//

        $stdb1 = $db1->prepare('SELECT * FROM Member WHERE id = ?');
        $stdb1->execute([$row['id']]);
        if ($member = $stdb1->fetch()) {
            continue;
        }

        echo sprintf("%d %s %s %s\n", $row['id'], $row['name'], $row['postName'], $row['lastName']);

        $st_loc = $db2->prepare('SELECT * FROM Localisation WHERE id = ?');
        $st_loc->execute([$row['localisation']]);

        if ($loc = $st_loc->fetch()) {
            UtilitaireSQL::insert($db1, 'Localisation', [
                'id' => $loc['id'],
                'country' => $loc['country'],
                'city' => $loc['city'],
                'district' => $loc['district'],
                'dateAjout' => $loc['dateAjout']
            ],false);
        }

        UtilitaireSQL::insert($db1, "Member", [
            'id' => $row['id'],
            'name' => $row['name'],
            'postName' => $row['postName'],
            'lastName'=> $row['lastName'],
            'pseudo' => $row['pseudo'],
            'password' => $row['password'],
            'email' => $row['email'],
            'kind' => $row['kind'],
            'matricule' => $row['matricule'],
            'telephone' => $row['telephone'],
            'foot' => $row['foot'],
            'parent' => $row['parent'],
            'sponsor' => $row['sponsor'],
            'admin' => $row['admin'],
            'office' => $row['office'],
            'localisation'=> $row['localisation'],
            'dateAjout' => $row['dateAjout']
        ], false);
    }
}
$st->closeCursor();
//============================================


//vente des produits
//========================
$st_month = $db2->prepare('SELECT * FROM MonthlyOrder');
$st_month->execute();
while ($month = $st_month->fetch()) {
    $st_month1 = $db1->prepare('SELECT * FROM MonthlyOrder WHERE id = ?');
    $st_month1->execute([$month['id']]);

    if ($month1 = $st_month1->fetch()) {
        continue;
    }

    UtilitaireSQL::insert($db1, "MonthlyOrder", [
        'id' => $month['id'],
        'member' => $month['member'],
        'disabilityDate' => $month['disabilityDate'],
        'manualAmount' => $month['manualAmount'],
        'office' => $month['office'],
        'dateAjout' => $month['dateAjout']
    ], false);

    //bonus memsuel (bonus de reachat)
    $st_reacha = $db2->prepare('SELECT * FROM PurchaseBonus WHERE monthlyOrder = ?');
    $st_reacha->execute([$month['id']]);
    while ($bonus = $st_reacha->fetch()) {
        UtilitaireSQL::insert($db1, "PurchaseBonus", [
            'id' => $bonus['id'],
            'generator' => $bonus['generator'],
            'member' => $bonus['member'],
            'generation' => $bonus['generation'],
            'monthlyOrder' => $bonus['monthlyOrder'],
            'amount' => $bonus['amount'],
            'dateAjout' => $bonus['dateAjout']
        ], false);
    }

    $st_pv = $db2->prepare('SELECT * FROM PointValue WHERE monthlyOrder = ?');
    $st_pv->execute([$month['id']]);

    while ($pv = $st_pv->fetch()) {
        UtilitaireSQL::insert($db1, "PointValue", [
            'id' => $pv['id'],
            'member' => $pv['member'],
            'generator' => $pv['generator'],
            'value' => $pv['value'],
            'foot' => $pv['foot'],
            'monthlyOrder' => $pv['monthlyOrder'],
            'dateAjout' => $pv['dateAjout']
        ], false);
    }
}

//elements de la fiche
$st_row = $db2->prepare('SELECT * FROM SellSheetRow');
$st_row->execute();
while ($sheet = $st_row->fetch()) {
    $st_row1 = $db1->prepare('SELECT * FROM SellSheetRow WHERE id = ?');
    $st_row1->execute([$sheet['id']]);

    if ($sheet1 = $st_row1->fetch()) {
        continue;
    }

    echo sprintf("Sell: [%d]\n", $sheet['id']);

    UtilitaireSQL::insert($db1, "SellSheetRow", [
        'id' => $sheet['id'],
        'product' => $sheet['product'],
        'monthlyOrder' => $sheet['monthlyOrder'],
        'dateAjout' => $sheet['dateAjout'],
        'quantity' => $sheet['quantity'],
        'unitPrice' => $sheet['unitPrice'],
        'office' => $sheet['office']
    ], false);
    
    //sustraction aux virtuals de l'office
    $st_item = $db2->prepare('SELECT * FROM SellSheetRowVirtualMoney WHERE sheet = ?');
    $st_item->execute([$sheet['id']]);
    while ($item = $st_item->fetch()) {
        UtilitaireSQL::insert($db1, "SellSheetRowVirtualMoney", [
            'id' => $item['id'],
            'sheet' => $item['sheet'],
            'money' => $item['money'],
            'dateAjout' => $item['dateAjout'],
            'amount' => $item['amount']
        ], false);
    }
}

//========================
// Packets des membres
//========================
echo "\n";
$st = $db2->prepare('SELECT * FROM GradeMember ORDER BY id');//IDs des membres du db2
if($st->execute()) {
    while ($row = $st->fetch()) {//

        $stdb1 = $db1->prepare('SELECT * FROM GradeMember WHERE id = ?');
        $stdb1->execute([$row['id']]);
        if ($gm = $stdb1->fetch()) {
            UtilitaireSQL::update($db1, "GradeMember", [
                'initDate' => $row['initDate'],
                'closeDate' => $row['closeDate'],
                'enable' => $row['enable'],
                'dateModif' => $row['dateModif']
            ], $row['id']);
            continue;
        }

        echo sprintf("\t[Packet] %d %d\n", $row['id'], $row['member']);

        UtilitaireSQL::insert($db1, "GradeMember", [
            'id' => $row['id'],
            'member' => $row['member'],
            'old' => $row['old'],
            'initDate' => $row['initDate'],
            'closeDate' => $row['closeDate'],
            'enable' => $row['enable'],
            'virtualMoney' => $row['virtualMoney'],
            'officePart' => $row['officePart'],
            'monthlyOrder' => $row['monthlyOrder'],
            'grade' => $row['grade'],
            'product' => $row['product'],
            'membership' => $row['membership'],
        	'office' => $row['office'],
            'dateAjout' => $row['dateAjout']
        ], false);

        //reference de MoneyGradeMember
        $st_mgm = $db2->prepare('SELECT * FROM MoneyGradeMember WHERE gradeMember = ?');
        $st_mgm->execute([$row['id']]);
        while ($mgm = $st_mgm->fetch()) {
            UtilitaireSQL::insert($db1, "MoneyGradeMember", [
                'id' => $mgm['id'],
                'product' => $mgm['product'],
                'afiliate' => $mgm['afiliate'],
                'gradeMember' => $mgm['gradeMember'],
                'virtualMoney' => $mgm['virtualMoney'],
                'dateAjout' => $mgm['dateAjout']
            ], false);
        }

        //on recupere directement les PVS correspondant aux generateurs
        $st_pv = $db2->prepare('SELECT * FROM PointValue WHERE generator = ?');
        $st_pv->execute([$row['id']]);

        while ($pv = $st_pv->fetch()) {
            UtilitaireSQL::insert($db1, "PointValue", [
                'id' => $pv['id'],
                'member' => $pv['member'],
                'generator' => $pv['generator'],
                'value' => $pv['value'],
                'foot' => $pv['foot'],
                'monthlyOrder' => $pv['monthlyOrder'],
                'dateAjout' => $pv['dateAjout']
            ], false);
        }

        //bonus generationnel
        //=====================
        $st_bg = $db2->prepare('SELECT * FROM BonusGeneration WHERE generator = ?');
        $st_bg->execute([$row['id']]);
        while ($bg = $st_bg->fetch()) {

            UtilitaireSQL::insert($db1, "BonusGeneration", [
                'id' => $bg['id'],
                'generator' => $bg['generator'],
                'member' => $bg['member'], 
                'generation' => $bg['generation'], 
                'amount' => $bg['amount'],
                'dateAjout' => $bg['dateAjout']
            ], false);
        }
    }
}
//============================================



//operation de matching
$st_out = $db2->prepare("SELECT * FROM Withdrawal");
$st_out->execute();

while ($out = $st_out->fetch()) {
    $st_out1 = $db1->prepare('SELECT * FROM Withdrawal WHERE id = ?');
    $st_out1->execute([$out['id']]);
    if($out1 = $st_out1->fetch()) {
        continue;
    }

    UtilitaireSQL::insert($db1, "Withdrawal", [
        'id' => $out['id'],
        'amount' => $out['amount'],
        'telephone' => $out['telephone'],
        'member' => $out['member'],
        'admin' => $out['admin'],
        'office' => $out['office'],
        'raport' => $out['raport'],
        'dateAjout' => $out['dateAjout'],
        'dateModif' => $out['dateModif']
    ], false);
}

if (!$db1->commit()) {
    echo "\n-> Echec de validation de la transaction\n\n";
}