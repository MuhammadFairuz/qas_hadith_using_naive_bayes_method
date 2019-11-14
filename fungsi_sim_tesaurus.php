<h1>Indexing Data Tesaurus</h1>
<?php
// ini_set('max_execution_time', '300'); //300 seconds = 5 minutes
// ini_set('max_execution_time', '0'); // for infinite time of execution

include('fungsi.php');

function buatSimTes(){
    include('koneksi.php');
    mysqli_query($connect, "TRUNCATE TABLE tb_sim_tesaurus");
    $sqlResult = mysqli_query($connect, "SELECT * FROM tb_tes ORDER BY id_tes");
    while($row = mysqli_fetch_array($sqlResult, MYSQLI_ASSOC)){
        $id_tes = $row['id_tes'];
        $term = $row['term'];
        $tes = $row['tesaurus'];
        $termTes = explode(" ", $tes);
        mysqli_query($connect, "INSERT INTO tb_sim_tesaurus(term, sin) VALUES ('$term','$term')");
        foreach($termTes as $key => $valueTermTes){
            if($termTes[$key] != ""){
                mysqli_query($connect, "INSERT INTO tb_sim_tesaurus(term, sin) VALUES ('$term', '$termTes[$key]')");
            }
        }
    }
}

function buatIndexTes($term){
    include('koneksi.php');
    mysqli_query($connect, "TRUNCATE TABLE tb_index_tes");
    $sqlDataTerm = mysqli_query($connect, "SELECT * FROM tb_sim_tesaurus WHERE term='$term'");
    while($rowData = mysqli_fetch_array($sqlDataTerm)){
        $sin = $rowData['sin'];
        $result = mysqli_query($connect, "SELECT * FROM tb_tes WHERE term='$sin' ORDER BY  id_tes");
        $num_rows = mysqli_num_rows($result);

        while($row = mysqli_fetch_array($result)){
            $id_tes = $row['id_tes'];
            $tes = $row['term']." ".$row['tesaurus'];
            $termTes = explode(" ", trim($tes));

            foreach($termTes as $key => $valueTermTes){
                if($termTes[$key] != ""){
                    $resCount = mysqli_query($connect, "SELECT count FROM tb_index_tes WHERE term = '$termTes[$key]' AND id_tes=$id_tes");
                    $num_rows = mysqli_num_rows($resCount);
                    if($num_rows > 0){
                        $rowCount = mysqli_fetch_array($resCount);
                        $count = $rowCount['count'];
                        $count++;
                        mysqli_query($connect, "UPDATE tb_index_tes SET count = $count WHERE term = '$termTes[$key]' AND id_tes=$id_tes");
                    }else{
                        mysqli_query($connect, "INSERT INTO tb_index_tes(term, id_tes, count) VALUES ('$termTes[$key]', $id_tes, 1)");
                    }
                }
            }
        }
    }
}   

function hitungBobotTes(){
    include('koneksi.php');
    $sqlnum = mysqli_query($connect, "SELECT DISTINCT id_tes FROM tb_index_tes");
    $num = mysqli_num_rows($sqlnum);
    $sqlBobot = mysqli_query($connect, "SELECT * FROM tb_index_tes ORDER BY id");
    $num_rows = mysqli_num_rows($sqlBobot);
    while($rowBobot = mysqli_fetch_array($sqlBobot)){
        $term = $rowBobot['term'];
        $tf = $rowBobot['count'];
        $id = $rowBobot['id'];

        $resultNTerm = mysqli_query($connect, "SELECT count(*) as N FROM tb_index_tes WHERE term = '$term'");
        $rowNTerm = mysqli_fetch_array($resultNTerm);
        $NTerm = $rowNTerm['N'];
        echo $NTerm . 'tes <br>';
        if($NTerm > 0 ){
            $w = $tf * log($num/$NTerm);
            echo $w.'<br>';
        }
        $resUpdateBobot = mysqli_query($connect, "UPDATE tb_index_tes SET bobot = $w WHERE id = $id");
    }
}

function vektorTes(){
    include('koneksi.php');
    mysqli_query($connect, "TRUNCATE TABLE tb_vektor_tes");
    $resId_tes = mysqli_query($connect, "SELECT DISTINCT id_tes FROM tb_index_tes");
    $num_rows = mysqli_num_rows($resId_tes);
    while($rowId_tes = mysqli_fetch_array($resId_tes)){
        $id_tes = $rowId_tes['id_tes'];
        $resVektor = mysqli_query($connect, "SELECT bobot FROM tb_index_tes WHERE id_tes = $id_tes");
        $panjangVektor = 0;
        while ($rowVektor = mysqli_fetch_array($resVektor)) {
            $panjangVektor = $panjangVektor + $rowVektor['bobot'] * $rowVektor['bobot'];
        }
        $panjangVektor = sqrt($panjangVektor);
        $resInsertVektor = mysqli_query($connect, "INSERT INTO tb_vektor_tes (id_tes, panjang_vektor) VALUES ($id_tes, $panjangVektor)");
    }

}

function similarityTesaurus($term){
    include('koneksi.php');
    $resn = mysqli_query($connect, "SELECT count(*) as a FROM tb_vektor_tes");
    $rown = mysqli_fetch_array($resn);
    $n = $rown['a'];
    $sql = mysqli_query($connect, "SELECT * FROM tb_tes WHERE term='$term'");
    $result = mysqli_fetch_array($sql);
    $id_tesawal = $result['id_tes'];
    $tesaurus = $result['term']." ".$result['tesaurus'];

    echo "<br>".$tesaurus;
    $atesaurus = explode(" ", $tesaurus);
    $panjangTes = 0;
    $aBobotTes = array();

    for($i=0; $i < count($atesaurus); $i++){
        $resNTerm = mysqli_query($connect, "SELECT count(*) as N FROM tb_index_tes WHERE term = '$atesaurus[$i]'");
        $rowNTrem = mysqli_fetch_array($resNTerm);
        $NTerm = $rowNTrem['N'];
        if($NTerm > 0){
            $idf = @log10($n/$NTerm);
        }else {
            $idf = 0;
        }
        $aBobotTes[] = $idf;
        echo "bobot ".$aBobotTes[$i]." <br>";
        $panjangTes = $panjangTes + ($idf * $idf);
    }
    $panjangTes = sqrt($panjangTes);
    echo "tanda ".$panjangTes." <br>";

    $resimtes = mysqli_query($connect, "SELECT * FROM tb_sim_tesaurus WHERE term='$term'");
    while($result1 = mysqli_fetch_array($resimtes)){
        $sin = $result1['sin'];
        $sqlterm = mysqli_query($connect, "SELECT * FROM tb_tes WHERE term='$sin'");
        $resulttermid = mysqli_fetch_array($sqlterm);
        $termid = $resulttermid['id_tes'];

        $resId_tes = mysqli_query($connect, "SELECT * FROM tb_vektor_tes WHERE id_tes = $termid");
        $rowId_tes = mysqli_fetch_array($resId_tes);
        $dotproduct = 0;
        $id_tes = $rowId_tes['id_tes'];
        $panjangId_tes = $rowId_tes['panjang_vektor'];
        $resTerm = mysqli_query($connect, "SELECT * FROM tb_index_tes WHERE id_tes = $id_tes");
        while ($rowTerm = mysqli_fetch_array($resTerm)) {
            for($i=0; $i < count($atesaurus); $i++){
                if($rowTerm['term'] == $atesaurus[$i]){
                    $dotproduct = $dotproduct + $rowTerm['bobot'] * $aBobotTes[$i];
                    echo "<br>".$rowTerm['term'];
                    echo "<br>".$atesaurus[$i];
                    echo "<br>".$rowTerm['bobot'];
                    echo "<br>".$aBobotTes[$i];
                    echo "<br>".$dotproduct;
                }
            }
        }
        if($dotproduct > 0){
            echo "<br>. ini hasil dot".$dotproduct;
            $sim = $dotproduct / ($panjangTes * $panjangId_tes);
            echo "<br> ini sim".$sim;
            mysqli_query($connect, "UPDATE tb_sim_tesaurus SET sim = $sim WHERE term = '$term' AND sin ='$sin'");
        }else{
            echo "string";
        }
    }
    return $sim;
}
function indexSimTesaurus(){
    include('koneksi.php');
    buatSimTes();
    $sql = mysqli_query($connect, "SELECT * FROM tb_tes ORDER BY id_tes");
    while ($result = mysqli_fetch_array($sql)) {
        $term = $result['term'];
        $tesaurus = $result['tesaurus'];
        buatIndexTes($term);
        hitungBobotTes();
        vektorTes();

        $sim = similarityTesaurus($term);
    }
}

indexSimTesaurus();
?>