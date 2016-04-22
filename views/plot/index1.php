<?php
/* @var $this yii\web\View */


use yii\helpers\Json;

$arr = [['Country', 'Popularity']];
foreach($list as $row) {
    $code = $row['option_id'];
    if ($code[0]!='E') $arr[] = [$row['option_id'], intval($row['c'])];
}
$json = Json::encode($arr);

?>

<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">
    google.load("visualization", "1", {packages:["geochart"]});
    google.setOnLoadCallback(drawRegionsMap);

    function drawRegionsMap() {

        var data = google.visualization.arrayToDataTable(<?=$json?>);

        var options = {region: '019',magnifyingGlass:{enable: true, zoomFactor: 70.5}};

        var chart = new google.visualization.GeoChart(document.getElementById('regions_div'));

        chart.draw(data, options);
    }
</script>

<h1>plot/index</h1>
    <div id="regions_div" style="width: 900px; height: 900px;"></div>

    <table class="table">
        <?php foreach($list as $row) { ?>
            <tr>
                <td><?=$row['option_id']?></td>
                <td><?=$row['name']?></td>
                <td><?=$row['c']?></td>
            </tr>
        <?php } ?>
    </table>
