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
    $direct_data = direct_path($connect, $_POST['src'], $_POST['dest']);
    $from_data = get_path($connect, $_POST['src']);//Surat
    $path = array();
    foreach ($from_data as $from) {
        //Ahm baroda 2nd level
        $path[] = get_path($connect, $from['to_city']);
    }
    $i=0;
    // ray();
    // $time = 0;
    $temp = 0;
    foreach($path as $p){
        foreach($p as $p1){
            $get_company_name = "SELECT `company_name`,`packet`,`time` from `tbl_route` WHERE `from_city` = '".$_POST['src']."' AND `to_city` = '".$p1['from_city']."'";
            //Third level
            $res_company = mysqli_query($connect, $get_company_name);
            $company = mysqli_fetch_assoc($res_company);
            $temp = $company['packet'];
            $time_tmp = $company['time'];
            $temp_path =  $_POST['src']." [".$company['company_name']."]  => ".$p1['from_city']." [".$p1['company_name']."]  => " ;
            $temp += $p1['packet'];
            $time_tmp += $p1['time'];

            if($p1['to_city'] === $_POST['dest']){
                $per_path[$i] = $temp_path.$p1['to_city'];
                $packet[$i] = $temp;
                $time[$i] = $time_tmp;
                $i++;
            }
            $to =$p1['to_city'];
            here:
                $path2 = get_path($connect, $to);
                foreach($path2 as $p2){
                    $temp_path .= $p2['from_city']." [".$p2['company_name']."]  => ";
                    $temp += $p2['packet'];
                    $time_tmp += $p2['time'];
                    $direct_query = "SELECT * FROM `tbl_route` WHERE `from_city` = '".$p2['from_city']."' AND `to_city` = '".$_POST['dest']."'";

                    $res_direct = mysqli_query($connect, $direct_query);
                    if($arr_route=mysqli_fetch_assoc($res_direct)){
                        $per_path[$i] = $temp_path.$arr_route['to_city'];
                        $packet[$i] = $temp;
                        $time[$i] = $time_tmp;
                        $i++;
                    }

                    if($p2['to_city'] === $_POST['dest']){
                        // $per_path[$i] = $temp_path;
                        $packet[$i] = $temp;
                        $time[$i] = $time_tmp;
                        $i++;
                    }else{
                        $to = $p2['to_city'];
                        goto here;
                    }
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
        if(isset($direct_data)){

            foreach ($direct_data as $direct_path) {
                echo "<tr><td>".$direct_path['from_city']." [".$direct_path['company_name']."] => ".$direct_path['to_city']."</td><td>".$direct_path['packet']."</td><td>".$direct_path['time']." Days </td></tr>";
            }
            $i=1;
        }
        if(isset($per_path)){

            foreach ($per_path as $path2) {
                echo "<tr><td>".$path2."</td><td>".$packet[$i]."</td><td>".$time[$i]." Days </td></tr>";
                $i++;
            }
        }
        if(isset($direct_data) && !isset($per_path)){
            echo "<tr><td colspan=3>No Route Found</td></tr>";
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
