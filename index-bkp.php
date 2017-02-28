<title>Path Finder</title>
<link rel="stylesheet" href="bootstrap.css">
<?php
$connect = mysqli_connect("localhost","root","root","db_city_practice") or die(mysqli_error($connect));
$select_query = "SELECT * FROM tbl_route";
$resource = mysqli_query($connect, $select_query);
$all_data = array();
while ($row = mysqli_fetch_assoc($resource))
    $all_data[] = $row;

/* Select from City from Databse */
$query_from = "SELECT DISTINCT `from_city` from tbl_route";
$res_from = mysqli_query($connect, $query_from);
$from_data = array();
while($row = mysqli_fetch_assoc($res_from))
    $from_data[] = $row;

/* Select To City from Database */
$query_to = "SELECT DISTINCT `to_city` from tbl_route";
$res_to = mysqli_query($connect, $query_to);
$to_data = array();
while($row = mysqli_fetch_assoc($res_to))
    $to_data[] = $row;
?>
<div class="col-md-12">
<table class="table">
  <th>Id</th>
  <th>From City</th>
  <th>To City</th>
  <th>Packet</th>
  <th>Time</th>
  <th>Company Name</th>
  <?php
  foreach ($all_data as $data) {
  ?>
  <tr>
      <td><?=$data['id']?></td>
      <td><?=$data['from_city']?></td>
      <td><?=$data['to_city']?></td>
      <td><?=$data['packet']?></td>
      <td><?=$data['time']?></td>
      <td><?=$data['company_name']?></td>
  </tr>
  <?php } ?>
</table>
</div>
<div class="col-md-12">
    <form method="post">
        From :
        <select class="select" name="src">
            <?php
            foreach($from_data as $data){
            ?>
            <option <?php if (isset($_POST['src']) && $_POST['src'] == $data['from_city']) {
                echo 'selected';
            } ?>  value="<?=$data['from_city']?>"><?=$data['from_city']?></option>
            <?php } ?>
        </select>
        To :
        <select class="select" name="dest">
            <?php
            foreach($to_data as $data){
            ?>
            <option  <?php if (isset($_POST['dest']) && $_POST['dest'] == $data['to_city']) {
                echo 'selected';
            } ?> value="<?=$data['to_city']?>"><?=$data['to_city']?></option>
            <?php } ?>
        </select>
        <button type="Submit" name="submit" value="submit" class="btn btn-default">
            Search
        </button>
    </form>
</div>

<?php
if(isset($_POST['submit'])){
    echo "<pre>";
    $direct_data = direct_path($connect, $_POST['src'], $_POST['dest']);
    $from_data = get_path($connect, $_POST['src']);
    $path = array();
    foreach ($from_data as $from) {
        $path[] = get_path($connect, $from['to_city']);
    }
    // echo  "Path ==>";
    // print_r($path);
    echo "</pre>";
    ?>

    <?php
    echo "<pre>";
    $i=0;
    foreach($path as $p){
        echo "<br> Inner<br>";
        foreach($p as $p1){
            //print_r($p1);
            //static $count = 0;
            //if($count == 0){
                $get_company_name = "SELECT `company_name` from `tbl_route` WHERE `from_city` = '".$_POST['src']."' AND `to_city` = '".$p1['from_city']."'";
                $res_company = mysqli_query($connect, $get_company_name);
                $company = mysqli_fetch_assoc($res_company);
                //print_r($company);
                // $count++;
            //}

            $per_path[$i] =  $_POST['src']."[".$company['company_name']."]===> ".$p1['from_city']."[".$p1['company_name']."]=/=>".$p1['to_city']." ";
            echo $per_path[$i];
            // echo "<br> To =>> ".$p1['to_city']."<br>";
            //echo strcmp($_POST['dest'], $p1['to_city'])."<br>";
            //while(true){
            if($p1['to_city'] === $_POST['dest']){
                //echo "<br>".strcmp($_POST['dest'], $p1['to_city'])."<br>";
                // echo " <==here Stop<br>";
                $i++;
            }
            // $temp[$i] = $per_path[$i];
            $to =$p1['to_city'];
            here:
                // echo "<br>==>".$to."<br>";
                $path2 = get_path($connect, $to);
                foreach($path2 as $p2){
                    $per_path[$i]  .= "//=".$p2['from_city']."[".$p2['company_name']."]==>//<==";
                    echo "<br><br>Path => ".$per_path[$i]."<br><br>";
                    // echo "<br> Temp => ".var_dump($temp)."<br>";
                    $direct_query = "SELECT * FROM `tbl_route` WHERE `from_city` = '".$p2['from_city']."' AND `to_city` = '".$_POST['dest']."'";
                    $res_direct = mysqli_query($connect, $direct_query);
                    if($arr_route=mysqli_fetch_assoc($res_direct)){
                        $per_path[$i] .= $arr_route['to_city'];
                        echo $per_path[$i]."<=ends<br>";
                        $i++;
                    }
                    //  echo "==>Query <br>";
                    //  echo $direct_query."<br> OutPut =>".$arr_route['from_city']."=> ".$arr_route['to_city']."<br>";
                    if($p2['to_city'] === $_POST['dest']){
                        //echo "<br>".strcmp($_POST['dest'], $p1['to_city'])."<br>";
                        echo " <== Stop ==>  <br>  ";
                        $i++;
                    }else{
                        // echo "<br>from =/////=> ".$p2['from_city']."=>> ".$p2['to_city']."<br>";
                        $to = $p2['to_city'];
                        goto here;
                    }
                    echo "<br>";
                }
        }
    }
    ?>
    <div class="col-md-12">
        <table border="1" class="table">
            <th>Path</th>
            <th>Packet</th>
            <th>time</th>

        <?php
            foreach ($direct_data as $direct_path) {
                echo "<tr><td>".$direct_path['from_city']." [".$direct_path['company_name']."] => ".$direct_path['to_city']."</td><td>".$direct_path['packet']."</td><td>".$direct_path['time']."</td></tr>";
            }
            foreach ($per_path as $path2) {
                echo "<tr><td>".$path2."</td></tr>";
            }
         ?>

        </table>
    </div>
    <?php
}
function direct_path($connect, $src, $dest){
    $query = "SELECT * FROM tbl_route WHERE `from_city` = '".$src."' AND `to_city` = '".$dest."' ORDER BY `packet`";
    $from_res = mysqli_query($connect, $query);
    $from_data = array();
    while($row = mysqli_fetch_assoc($from_res))
        $from_data[] = $row;
    return $from_data;
}
function get_path($connect, $src){
    $query = "SELECT * FROM tbl_route WHERE `from_city` = '".$src."'";
    $from_res = mysqli_query($connect, $query);
    $from_data = array();
    while($row = mysqli_fetch_assoc($from_res))
        $from_data[] = $row;
    return $from_data;
}
 ?>
