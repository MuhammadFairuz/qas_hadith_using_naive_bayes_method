<?php
ini_set('max_execution_time', '300'); //300 seconds = 5 minutes
ini_set('max_execution_time', '0'); // for infinite time of execution

if(isset($_POST['preproses'])){
    start_preproses();
}

function start_preproses(){
    // set_time_limit(0);
    buat_index();
}

function stopword($hadis){
    include("koneksi.php");
    $atanda = array("'", "-", ")", "(", "\"", "/", "=", ".", ",", ":", ";", "!", "?");

    foreach ($atanda as $i => $value) {
        $hadis = str_replace($atanda, "", $hadis);
    }

    $hadis = strtolower(trim($hadis));

    $query = mysqli_query($connect, "SELECT * from tb_stopword");

    while ($row_stopword = mysqli_fetch_array($query)) {
        $stopword[] = trim($row_stopword['stopword']);
    }

    $pieces = explode(" ", $hadis);

    $jml = count($pieces) - 1;
    for ($i = 0; $i <= $jml; $i++) {
        if (in_array($pieces[$i], $stopword)) {
            unset($pieces[$i]);
        }
    }

    $hadis = implode(" ", $pieces);
    $hadis = strtolower(trim($hadis));
    return $hadis;
}

function stemming($hadis){
    include('koneksi.php');
    include_once('stemming.php');

    //memanggil hasil topword
    $hasil_stopword = stopword($hadis);

    //hasil stopword dipisah perkata
    $pecah_kata = explode(" ", $hasil_stopword);

    //hitung jumlah kata
    $jml_kata = count($pecah_kata);

    //dirubah menjadi kata dasar
    for ($i=0; $i<$jml_kata; $i++){
        $pecah_kata[$i] = stemming_n($pecah_kata[$i]);
    }

    //menggabungkan kata
    $hadis = implode(" ", $pecah_kata);
    $hadis = trim($hadis);
    return $hadis;
}

function buat_index(){
    include('koneksi.php');
    
    //hapus index sebelumnya
    mysqli_query($connect, "TRUNCATE TABLE tb_index");

    //mengambil data terjemah hadis
    $panggil_hadis = mysqli_query($connect, "SELECT * FROM tb_hadis ORDER BY nomor_hadis");

    $jml_data = mysqli_num_rows($panggil_hadis);

    print("Mengindeks sebanyak " .$jml_data. " hadis. <br />");

    while($dataHadis = mysqli_fetch_array($panggil_hadis)){
        $nomor_hadis = $dataHadis['nomor_hadis'];
        $hadis = $dataHadis['bab']." ".$dataHadis['isi'];

        $hadis = stopword($hadis);
        $hadis = stemming($hadis);

        $ahadis = explode(" ", trim($hadis));
        foreach ($ahadis as $j => $value) {

            if ($ahadis[$j] != "") {

                $rescount = mysqli_query($connect, "SELECT Count FROM tb_index WHERE Term = '$ahadis[$j]' AND nomor_hadis = $nomor_hadis");
                $jml_data = mysqli_num_rows($rescount);

                if ($jml_data > 0) {
                    $rowcount = mysqli_fetch_array($rescount);
                    $count = $rowcount['Count'];
                    $count++;
                    mysqli_query($connect, "UPDATE tb_index SET Count = $count WHERE Term = '$ahadis[$j]' AND nomor_hadis = $nomor_hadis");
                } else {
                    mysqli_query($connect, "INSERT INTO tb_index (Term, nomor_hadis, Count) VALUES ('$ahadis[$j]', $nomor_hadis, 1)");
                }
            }
        }
    }
}

//fungsi Ekspansi Tesaurus
function ekspansiTesaurus($query){
    include('koneksi.php');
    $arrQuery = explode(" ", $query);
    $lengthq = count($arrQuery);

    for($i=0; $i<$lengthq; $i++){
        $sql = mysqli_query($connect, "SELECT * FROM tb_sim_tesaurus WHERE term = '$arrQuery[$i]' ORDER BY sim DESC limit 1 offset 1");
        while($row = mysqli_fetch_array($sql)){
            $sin = $row['sin'];
            array_push($arrQuery, $sin);
        }
    }

    $arrQuery = implode(" ", $arrQuery);
    $hasil = strtolower(trim($arrQuery));
    $hasil = stopword($hasil);
    $hasil = stemming($hasil);

    return $hasil;
}

//fungsi Tesaurus
function tesaurus($query){
    include('koneksi.php');
    $arrQuery = explode(" ", $query);
    $lengthq = count($arrQuery);

    for($i=0; $i<$lengthq; $i++){
        $sql = mysqli_query($connect, "SELECT * FROM tb_tesaurus WHERE kata = '$arrQuery[$i]'");
        while($row = mysqli_query_fetch_array($sql)){
            $sinonim = $row['sinonim'];
            $asinonim = explode(",", $sinonim);
            $length = count($asinonim);

            for($j=0; $j<$length; $j++){
                array_push($arrQuery, $asinonim[$j]);
            }
        }
    }

    $arrQuery = implode(" ", $arrQuery);
    $hasil = strtolower(trim($arrQuery));
    $hasil = stopword($hasil);
    $hasil = stemming($hasil);
    return $hasil;
}

// fungsi naive_bayes
function naive_bayes($query){
    include('koneksi.php');

    mysqli_query($connect,"SET CHARACTER SET utf8");
												
    //request dataset
    $sqlResult = mysqli_query($connect, "SELECT * FROM tb_index");
    $dataset = array();
    while ($row = mysqli_fetch_array($sqlResult, MYSQLI_ASSOC)) {
        $dataset[] = $row;
    }

    //request data Nomor Hadits
    $sqlNoHadist = mysqli_query($connect, "SELECT nomor_hadis from tb_index GROUP BY nomor_hadis");
    $noHadist = array();
    while ($rowHadis = mysqli_fetch_array($sqlNoHadist, MYSQLI_ASSOC)) {
        $noHadist[] = $rowHadis;
    }

    //prepare data
    $kata = explode(" ", $query);

    $datasetWithKeyword = array();
    $datasetNonKeyword = array();
    $datasetResTotal = array();
    
    foreach($kata as $key => $value){
        foreach ($noHadist as $keyNoHadist => $valueNoHadist) {
            $cek = true;
            foreach ($dataset as $keyDataset => $valueDataset) {
                if($valueDataset["nomor_hadis"] == $valueNoHadist["nomor_hadis"]) {
                    if($valueDataset["term"] == $value) {
                        $index = array_search($valueDataset["term"], array_column($datasetWithKeyword, "term"));
                        if(is_int($index)){
                            $datasetWithKeyword[$index]["doc ".$valueDataset["nomor_hadis"]] = $valueDataset["count"];
                        }else{
                            $data = array(
                                "term"=>$valueDataset["term"],
                                "doc ".$valueDataset["nomor_hadis"]=>$valueDataset["count"]);
                            $datasetWithKeyword[] = $data;
                        }
                        $cek = false;
                    }
                }
            }

            if($cek) {
                $index = array_search($value, array_column($datasetWithKeyword, "term"));
                if(is_int($index)) {
                    $datasetWithKeyword[$index]["doc ".$valueNoHadist["nomor_hadis"]] = 0.1;
                } else {
                    $data = array(
                        "term" => $value,
                        "doc ".$valueNoHadist["nomor_hadis"] => 0.1);
                    $datasetWithKeyword[] = $data;
                }
            }
        }

        //request dataset bukan keyword
        mysqli_query($connect, "SET CHARACTER SET utf8");
        $sqlResDataBukanKeyword = mysqli_query($connect, "SELECT nomor_hadis, SUM(count) as count FROM tb_index WHERE term NOT LIKE '$value' GROUP BY nomor_hadis");
        $datasetBukanKeyword = array();
        while($row = mysqli_fetch_array($sqlResDataBukanKeyword, MYSQLI_ASSOC)){
            $datasetBukanKeyword[] = $row;
        }

        //Load Array Dataset bukan Keyword
        foreach($datasetBukanKeyword as $KeyNonDataset => $valueNonDataset){
            $dataNonKeyword = array(
                "term" =>"bukan ".$value,
                "doc ".$valueNonDataset["nomor_hadis"]=>$valueNonDataset["count"]);
            $indexNonDataset = array_search($dataNonKeyword["term"], array_column($datasetNonKeyword, "term"));
                if(is_int($indexNonDataset)){
                    $datasetNonKeyword[$indexNonDataset]["doc ".$valueNonDataset["nomor_hadis"]]= $valueNonDataset["count"];
                }else{
                    $dataNonKeyword;
                    array_push($datasetNonKeyword, $dataNonKeyword);  
                }
        }
    }

    //request dataset total
    $sqlResDataTotal = mysqli_query($connect, "SELECT nomor_hadis, SUM(count) as count FROM tb_index GROUP BY nomor_hadis");
    $datasetTotal = array();
    while($row = mysqli_fetch_array($sqlResDataTotal, MYSQLI_ASSOC)){
        $datasetTotal[] = $row;
    }
    
    //Load Array Dataset Total
    $datasetResTotal["term"] = "total";
    foreach($datasetTotal as $keydataTotal => $valueDatasetTotal){
        $datasetResTotal["doc ".$datasetTotal[$keydataTotal]["nomor_hadis"]] = $datasetTotal[$keydataTotal]["count"];
    }

    // Array Merge datasetWithKeyword | Dataset Non Keyword
    $merge = array_merge($datasetWithKeyword, $datasetNonKeyword);
    
    // Menghitung total pada kolom
    foreach($merge as $key => $valueKey){
        $jumlah = array_sum($merge[$key]);
        $merge[$key]["total"] = $jumlah;
    }

    //menghitung Base Rates
    $baserates = array();
    foreach($datasetResTotal as $key => $valueKey){
        if($key != "term"){
            $jumlah = array_sum($datasetResTotal);
            $baserates[$key] =  $valueKey / $jumlah;
        }
    }

    //menghitung Evidence
    $evidence = array();
    foreach($merge as $key => $valueKey){
        $jumlah = array_sum($datasetResTotal); 
        $evidence[] = $merge[$key]["total"] / $jumlah;
    }

    //menghitung Likelihood
    $likelihood = array(); 
    $hitung = array();
    foreach($merge as $keyMerge => $valueMerge){
        foreach($valueMerge as $key => $value){
            if($key != "term" && $key != "total"){
                $hitung = $value / $datasetResTotal[$key];
                if(key_exists($key, $likelihood)){
                    $likelihood[$key][$valueMerge["term"]] = $hitung;
                }else{
                    $likelihood[$key] = array($valueMerge["term"] => $hitung);
                }
            }
        }
    }

    //menghitung Probability Document
    $probabilityCalculation = array();
    foreach($baserates as $keyBaserate => $valueBaserate){
        $hitung = $valueBaserate;
        foreach($likelihood[$keyBaserate] as $keyLikelihood => $valueLikelihood){
            $key = explode(" ", $keyLikelihood);
            if(sizeof($key) == 1){
                $hitung = $hitung * $valueLikelihood;
                $probabilityCalculation[$keyBaserate] = $hitung * 100;
            }
        }
    }

    //mengurutkan hasil pencarian dari data yang terbesar ke data yang terkecil
    $max = max($probabilityCalculation);
    $index = array_search($max, $probabilityCalculation);

    //Update data probability ke database
    $arrSizeProbCal = sizeof($probabilityCalculation);
    foreach($probabilityCalculation as $keyProbability => $valueProbability){
        $dataUrutHadis = explode(" ", $keyProbability);
        foreach($dataUrutHadis as $keyUrutHadis => $valueDataUrutHadis){
            if($keyUrutHadis == 1){
                for($i=0; $i<$arrSizeProbCal; $i++){
                    $hasilProbability = round($valueProbability, 10, PHP_ROUND_HALF_DOWN);
                    $cek = mysqli_num_rows(mysqli_query($connect,"SELECT * FROM tb_cache WHERE query='$query' AND nomor_hadis=$valueDataUrutHadis"));
                    if ($cek > 0){
                        // mysqli_query($connect, "SELECT * FROM tb_cache");
                    }else {
                        mysqli_query($connect,"INSERT INTO tb_cache (query,nomor_hadis,nilai_sim) VALUES ('$query', $valueDataUrutHadis, $hasilProbability)");
                    }
                }
            }
        }
    }
}

//fungsi retrieve data
function retrieve($keyword){
    //tampilkan data pada table
    include("koneksi.php");
    mysqli_query($connect,"SET CHARACTER SET utf8");
    $resCache = mysqli_query($connect, "SELECT * FROM tb_cache WHERE query = '$keyword' and nilai_sim >=0 ORDER BY nilai_sim DESC LIMIT 10");
    $num_rows = mysqli_num_rows($resCache);
    $no = 1;
    if ($num_rows > 0) {
        while ($rowCache = mysqli_fetch_array($resCache)) {
            $nomor_hadis = $rowCache['nomor_hadis'];
            $sim = $rowCache['nilai_sim'];
            if ($nomor_hadis != 0) {
                $resHadis = mysqli_query($connect, "SELECT * FROM tb_hadis WHERE nomor_hadis = $nomor_hadis");
                $rowHadis = mysqli_fetch_array($resHadis);

                $no = $no;
                $kitab = $rowHadis["kitab"];
                $bab = $rowHadis["bab"];
            	$hadis = $rowHadis["hadits"];
                $isi = $rowHadis["isi"];
                $nomor_hadis = $rowHadis["nomor_hadis"];
                $bobot = $rowCache["nilai_sim"];
                    
                echo '<tr> 
            	    <td>'.$no.'</td> 
            		<td>'.$kitab.'</td> 
            		<td>'.$bab.'</td>
                    <td>'.$hadis.'</td> 
                    <td>'.$isi.'</td> 
                	<td>'.$nomor_hadis.'</td>
            		<td>'.$bobot.'</td> 
            		</tr>';
            
                $no++;
            } else {
                print("<p class='bg-danger'><b>Data Not Found</b></p><hr />");
            }
        }
    }
    else {
        naive_bayes($keyword);
        $resCache = mysqli_query($connect, "SELECT * FROM tb_cache WHERE Query = '$keyword' and nilai_sim >=0 ORDER BY nilai_sim DESC LIMIT 10");
        $num_rows = mysqli_num_rows($resCache);
        while ($rowCache = mysqli_fetch_array($resCache)) {
            $nomor_hadis = $rowCache['nomor_hadis'];
            $sim = $rowCache['nilai_sim'];
            if ($nomor_hadis != 0) {
                $resBerita = mysqli_query($connect, "SELECT * FROM tb_hadis WHERE nomor_hadis = $nomor_hadis");
                $rowHadis = mysqli_fetch_array($resBerita);
               
                $no = $no;
                $kitab = $rowHadis["kitab"];
                $bab = $rowHadis["bab"];
            	$hadis = $rowHadis["hadits"];
                $isi = $rowHadis["isi"];
                $nomor_hadis = $rowHadis["nomor_hadis"];
                $bobot = $rowCache["nilai_sim"];
                    
                echo '<tr> 
            	    <td>'.$no.'</td> 
            		<td>'.$kitab.'</td> 
            		<td>'.$bab.'</td>
                    <td>'.$hadis.'</td> 
                    <td>'.$isi.'</td> 
                	<td>'.$nomor_hadis.'</td>
            		<td>'.$bobot.'</td> 
            		</tr>';
            
                $no++;
            } else {
                print("<p class='bg-warning'><b>Data Not Found</b></p><hr />");
            }
        } 
    }
    
    // echo "=============================================================";
    // echo "<br>Merge Data With Keyword & Data Non Keyword";
    // echo "<br>=============================================================";
    // echo "<pre>";
    // print_r($merge);
    // echo "</pre>";
    // echo "=============================================================";
    // echo "<br>Dataset Total";
    // echo "<br>=============================================================";
    // echo "<pre>";
    // print_r($datasetResTotal);
    // echo "</pre>";
    // echo "=============================================================";
    // echo "<br>Baserates";
    // echo "<br>=============================================================";
    // echo "<pre>";
    // print_r($baserates);
    // echo "</pre>";
    // echo "=============================================================";
    // echo "<br>Evidence";
    // echo "<br>=============================================================";
    // echo "<pre>";
    // print_r($evidence);
    // echo "</pre>";
    // echo "=============================================================";
    // echo "<br>Likelihood";
    // echo "<br>=============================================================";
    // echo "<pre>";
    // print_r($likelihood);
    // echo "</pre>";
    // echo "=============================================================";
    // echo "<br>Probability";
    // echo "<br>=============================================================";
    // echo "<pre>";
    // print_r($probabilityCalculation);
    // echo "</pre>";
    // echo "=============================================================";
    // echo "<br>HASIL";
    // echo "<br>=============================================================";
    // echo "<br>Berdasarkan perhitungan, dokumen yang paling cocok dengan query adalah $index, dengan nilai probability = $max"."<br>";
    // echo "=============================================================";
}
?>