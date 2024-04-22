<?php
session_start();
// include 'koneksi.php';
require_once '../connection.php';
require_once '../api/controllers/Helper.php';

	
$myusername = $_SESSION['dinew_smyid'];
$vsupp 		= $_POST['supp'];
$vtgl  		= ($_POST['didate'] <= 9 ? '0'.$_POST['didate'] : $_POST['didate']) ;
$vbln  		= ($_POST['dimonth'] <= 9 ? '0'.$_POST['dimonth'] : $_POST['dimonth']) ;
$vthn  		= $_POST['diyear'];
$vsq   		= $_POST['disq'];
$vthn 		= substr($vthn,2,2); 
$vtglbln	= $vtgl . "/" . $vbln . "/" . $vthn;
$vtglj2 	= $vtgl . "/" . $vbln;
	
echo $vtglj2;
	
$sqlsupp	= "select SuppCode,SuppName,Declaration from supplier ";
$sqlsupp	= $sqlsupp . "where SuppCode = '$vsupp'";

$rssupp		= $pdo->query($sqlsupp)->fetchAll();
$suppdec 	= $rssupp[0][2];
$suppname 	= $rssupp[0][1];
$suppcode 	= $rssupp[0][0];
// $rssupp->Close();
	
echo "<html><body>";
echo "MAKE DELIVERY DATA<br>";
echo "Delivery Date : " ;
echo $vtglbln;
echo "<br>";
echo "Supplier : " . $suppname . "(" . $suppcode . ")" . " - " . $suppdec;
echo "<br>";

if ($suppdec == 'J1')
{
  $sqltgl = "select ". Helper::translateQueryGetdateToMysql("MySQL");
  $tgldb = $pdo->query($sqltgl)->fetchAll();
  $tglserver = $tgldb[0][0];
  // $tgldb->Close();
  $ythn = substr($tglserver,2,2);
  $ybln = substr($tglserver,5,2);
  $ytgl = substr($tglserver,8,2);
  $tglcek = $ybln . "/" . $ytgl . "/" . $ythn;
  $vblntglthn = $vbln . "/" . $vtgl . "/" . $vthn;
  $obsupp 	= trim($vsupp);
  //cek diget
  $sdhget = 0;
  $proses = 1;
  $sqprev = $vsq - 1;  // nilai sequence sebelumnya
  echo "<br>sqprev : " . $sqprev . "<br>";
  //	jika hasil nol tdk di proses cek sequence
  if ($sqprev > 0 )
  {
    $cekget 	= $obsupp . $vthn . $vbln . $vtgl . $sqprev . "%";
	  $sqlcekget 	= "select count(*) as tget from di where (supptglpo like '{$cekget}') ";
	  $rscekget	= $pdo->query($sqlcekget)->fetchAll();
	  $sdhget 	= $rscekget[0][0];
	  // $rscekget->Close();
	  echo "record di get : " . $sdhget;
	  if ($sdhget == 0)
	  {
	    $proses = 0;
	    echo "<br>data sequence sebelumnya tdk ada....<br>"; 
	    // header("Location:jdimaketgl.php?p=Data Delivery Instruction untuk sequence sebelumnya belum ada !!!...");
      echo "<script>window.location = 'jdimaketgl.php?p=Data Delivery Instruction untuk sequence sebelumnya belum ada !!!...'</script>";
    }
	  else
	  {
	    $proses = 1;
	  }	
  } // end of $sqprev > 0		
  //	jika proses 1
  if($proses == 1 )
  {
    echo "<br>prosesnyanya = 1<br>";
	  //---------------- hapus tabel diget sebelum insert -------------------
    $sqldeldiget 	= "delete from diget where (supp = :supp) and (tgl = :tgl) and (sq=:sq)";
    $data_deldiget = [
      'supp' => $vsupp,
      'tgl' => $vblntglthn,
      'sq' => $vsq
      ];
	  $rsdeldiget		= $pdo->prepare($sqldeldiget)->execute($data_deldiget);
	  // $rsdeldiget->Close();
	  //----------------- end of hapus tabel diget ---------------------------
				
	  //------------- hapus di status 0 ----------------------------
	  $vdel 		= trim($vsupp) . $vthn . $vbln . $vtgl . $vsq . '%';
	  $sqldelnob 	= "delete from di where (supptglpo like :supptglpo) and (status = :status)";
    $data_delnob = [
      'supptglpo' => $vdel,
      'status' => '0'
    ];
    echo '<br>';
    // echo $sqldelnob;
    $rsdelnob	= $pdo->preapre($sqldelnob)->execute($data_delnob);
    // $rsdelnob->Close();
    //---------- end of hapus di status 0 ------------------------
				
    //--------------------- hapus digetsum -------------------------
    $sqldelgetsum 	= "delete from digetsum where supp = :supp";
    $data_delgetsum = [
      'supp' => $obsupp
    ];
    echo '<br>';
    // echo $sqldelgetsum;
    $rsdelgetsum	= $pdo->prepare($sqldelgetsum)->execute($data_delgetsum);
    // $rsdelgetsum->Close();
    // --------------- end of hapus digetsum ----------------------
				
    //------------------- ins data ke table getsum -----------------
    try {
      //code...
      $sqlinsgetsum   = "insert into digetsum(supp,partno,qty) select max(supp) as 
      supp,partno,sum(qty) as qty from diget where ( supp = :supp ) and 
      (tgl >= :tgl )  group by partno order by partno";
      $data_insgetsum = [
        'supp' => $obsupp,
        'tgl' => $tglcek
      ];
      $rsinsgetsum = $pdo->prepare($sqlinsgetsum)->execute($data_insgetsum);  
    } catch (PDOException $e) {
      echo "jdimake 126 error : " . $e->getMessage();
    }
      
    echo '<br>';
    // echo $sqlinsgetsum;
    // obsupp
    // tglcek
    // --------------- end of ins data ke table getsum ----------------------
				
    //----------------------- hapus diupload ---------------------
    try {
      //code...
      $sqldiupdel  = "delete from diupload where supp = ?";
      // echo '<br>';
      // echo $sqldiupdel;
      $rsdiupdel  = $pdo->prepare($sqldiupdel)->execute($obsupp);
    } catch (exception $e) {
      echo '<br>' . $e->getMessage();
    }
    
    // $rsdiupdel->Close();
    // --------------- end of ins data ke table getsum ----------------------
				
	  // ambil summary per partno dari data yg sudah di upload
	  // dan kemudian input ke tabel diupload
	  //----------------------------------------------------------
    $sqldiup	= "insert into diupload(supp,partno,qty) select max(supp) as 
      supp,partno,sum(qty) as qty from di where ( supp = :supp ) and 
      (tgld >= :tgld) and ( status <> :status ) group by partno order by partno";
    echo '<br>';
    // echo $sqldiup;
    $data_diup = [
      'supp' => $obsupp,
      'tgld' => $tglcek,
      'status' => '0',
    ];
    $rsdiup		= $pdo->prepare($sqldiup)->execute($data_diup);
    $rsdiup->Close();

    

    //--------------- end of ins data ke table upload ----------
				
	  // hapus dibal berdasarkan supplier
	  //----------------------------------------------------------
    $sqldibaldel 	= "delete dibal where supp = :supp";
    echo '<br>';
    // echo $sqldibaldel;
    $rsdibaldel		= $pdo->prepare($sqldibaldel)->execute(['supp' => $obsupp]);
    // $rsdibaldel->Close();
    //---------------- end of hapus dibal ----------------------
				
    // cari balance qty antara di yg sudah diupload dengan di original
    // kemudian insert data ke table dibal
    //------------------------------------------------------------------
    $sqldibal	= "select digetsum.supp,digetsum.partno,digetsum.qty - diupload.qty 
      as qty from digetsum INNER JOIN diupload ON digetsum.partno = diupload.partno 
      where (digetsum.supp = '{$obsupp}') order by digetsum.partno";
    echo '<br>';
    echo $sqldibal;
    
    try {
      $rsdibal  = $pdo->query($sqldibal)->fetchAll();
    } catch (PDOException $e) {
      echo $e->getMessage();
    }

    foreach ($rsdibal as $row) {
      $data_dibal = [
        'supp' => $row[0],
        'partno' => $row[1],
        'balqty' => $row[2]
      ];
      $sqlinsdibal  = "insert into dibal(supp,partno,balqty) values(:supp,:partno,:balqty)";
      $rsinsdibal  = $pdo->prepare($sqlinsdibal)->execute($data_dibal);

    }

    // while(!$rsdibal->EOF)
    // {
    //   $rsdibalsupp 		= $rsdibal->fields[0];
    //   $rsdibalpartno 	= $rsdibal->fields[1];
    //   $rsdibalqty 		= $rsdibal->fields[2];
    //   $sqlinsdibal	= "insert into dibal(supp,partno,balqty) values('{$rsdibalsupp}',
    //     '{$rsdibalpartno}','{$rsdibalqty}')";
	  //   $rsinsdibal	= $pdo->query($sqlinsdibal);
    //   // $rsinsdibal->Close();	
	  //   $rsdibal->MoveNext();
	  // }
	  echo '<br>';
	  // $rsdibal->Close();
	  //-------------- end of ins data ke table dibal --------------------
	
    //---------------  delete virtual order balance --------------------
    $sqldelvob 		= "delete from ordbalvir where suppcode = :suppcode";
    echo '<br>';
    echo $sqldelvob;
    $rsdelvob		= $pdo->prepare($sqldelvob)->execute(['suppcode' => $vsupp]);
    // $rsdelvob->Close();
    //-----------------  end of delete virtual order balance --------------

    //---------------- copy record from ordbalact to ordbalvir --------------------------------
    $sqlinsvob 		= "insert into ordbalvir select transdate, suppcode, partnumber, partname, " . 
      "orderqty, reqdate, ponumber, posq, orderbalance, supprest, model, issuedate, potype, " .
    "statuspart, remark, statusread from ordbalact where suppcode = :suppcode";
    echo '<br>';
    echo $sqlinsvob;
    $rsdelvob		= $pdo->prepare($sqlinsvob)->execute(['suppcode' => $vsupp]);
    // $rsdelvob->Close();
    // ---------------- end of copy record form ordbalact to ordbalvir ------------------------
	
    //------------------ hapus ordbalactupd ---------------------------------------------------
    $sqldelobup 	= "delete from ordbalactupd where supp = :supp";
    echo '<br>';
    echo $sqldelobup;
    $rsdelobup		= $pdo->prepare($sqldelobup)->execute(['supp' => $vsupp]);
    // $rsdelobup->Close();
    //-----------------------------------------------------------------------------------------
	
    // mencari orderbalance dikurangi di yg sudah upload
    $sqlobvir 	= "select di.supp,di.po,ordbalvir.orderbalance - di.qty as balqty from 
      ordbalvir inner join di on ordbalvir.ponumber = di.po where (di.status <> '0') and 
      (di.supp = '{$vsupp}') and (di.tgld >= '{$tglcek}') order by partno,disq";
    echo '<br>';
    echo $sqlobvir;
    $rsobvir	= $pdo->query($sqlobvir)->fetchAll();
	  foreach ($rsobvir as $row) {
      $data_obvir = [
        'supp' => $row[0],
        'po' => $row[1],
        'balqty' => $row[2]
      ];
      $sqlinsobup   = "insert into ordbalactupd(supp,po,balqty) values(:supp,:po,:balqty)";
      $rsinsobup  = $pdo->prepare($sqlinsobup)->execute($data_obvir);
    }
    // while (!$rsobvir->EOF)
    // {
    //   $recobvir0 	= $rsobvir->fields[0];
    //   $recobvir1 	= $rsobvir->fields[1];
    //   $recobvir2 	= $rsobvir->fields[2];
    //   $sqlinsobup 	= "insert into ordbalactupd(supp,po,balqty) values('{$recobvir0}',
    //     '{$recobvir1}','{$recobvir2}')";
    //   $rsinsobup	= $pdo->query(sqlinsobup);
    //   $rsinsobup->Close();
    //   $rsobvir->MoveNext();
    // } //end of while (!$rsobvir->EOF)
	  echo '<br>';
	  // echo $sqlinsobup;
	  // $rsobvir->Close();

    // mencari po yg sudah dipakai
    $sqlobupd = "select supp,po,balqty from ordbalactupd where supp = '{$vsupp}'";
    echo '<br>';
    echo $sqlobupd;
    $rsobupd  = $pdo->query($sqlobupd)->fetchAll();
	  
    foreach ($rsobupd as $row) {
      $csupp = $row[0];
      $cpo   = $row[1];
      $cqty  = $row[2];
      if ($cqty == 0) {
        $sqlobvirdel = "delete ordbalvir where ponumber = :ponumber";
        $rsobvirdel  = $pdo->prepare($sqlobvirdel)->execute(['ponumber' => $cpo]);
        // $rsobvirdel->Close();
      }
      if ($cqty > 0) {
        $sqlobvirdel = "update ordbalvir set orderbalance = :orderbalance where ponumber = :ponumber";
        $rsobvirdel  = $pdo->prepare($sqlobvirdel)->execute(['orderbalance'=> $cqty, 'ponumber'=> $cpo]);
        // $rsobvirdel->Close();
      }
    }
    // while(!$rsobupd->EOF)
    // {
    //   $csupp = $rsobupd->fields[0];
    //   $cpo   = $rsobupd->fields[1];
    //   $cqty  = $rsobupd->fields[2];
    //   if ($cqty == 0)
    //   {
    //     $sqlobvirdel = "delete ordbalvir where ponumber = '{$cpo}'";
    //     $rsobvirdel  = $pdo->query($sqlobvirdel);
		//     $rsobvirdel->Close();
    //   }
    //   if ($cqty > 0)
    //   {
    //     $sqlobvirdel = "update ordbalvir set orderbalance = $cqty where ponumber = '$cpo'";
    //     $rsobvirdel  = $pdo->query($sqlobvirdel);
		//     $rsobvirdel->Close();
    //   }
	  //   $rsobupd->MoveNext();
    // }  // end of while 
	  echo '<br>';
	  // echo $sqlobvirdel;
    // $rsobupd->Close();
    echo "<br>";
    echo "Jenis Delivery : " . $suppdec;		

    // ---------------------------------------------------------------------
    // Proses make DIGET hanya jika sequence = '1' ( normal 1X delivery)
    // ----------------------------------------------------------------------  
    if ($vsq == '1')
    {
      // cari tanggal dari header
      $sql = "select transdate,hd,tm,suppcode,partno,partname,balqty,qty1,qty2,qty3,qty4,
      qty5,qty6,qty7,qty8,qty9,qty10,qty11,qty12,qty13,qty14,qty15,qty16,
      qty17,qty18,qty19,qty20,qty21,qty22,qty23,qty24,qty25,qty26,qty27,
      qty28,qty29,qty30,qty31,qty32 from TDSACT where SuppCode = '{$vsupp}' and HD = 'H' order by PartNo";
	    echo '<br>';
	    echo $sql;
      $rs  = $pdo->query($sql)->fetchAll();
	    $test = 'kosong';
      $cek = $vtglj2;
      $tgl = $vtglj2;
      //echo "mencari index kolom yang aktif ...<br>";
	    $kolomtgl = 0;
      foreach ($rs as $row) {
        for ($i = 0; $i <= 30; $i++) {
          $cek = substr($row[$i], 0, 5);
          if ($cek == $tgl) {
            $kolomtgl = $i;
            if ($vsq == '1') {
              $jamdel = substr($row[$i], 6, 2);
            }
            if ($vsq == '2') {
              $jamdel = substr($row[$i + 1], 6, 2);
            }
            break;
          }
        }
      }
      // while(!$rs->EOF)
      // {
      //   for ($i = 0; $i <=30; $i++)
      //   {
      //     $cek = substr($rs->fields[$i],0,5);
      //     if ($cek == $tgl)
      //     {
		  //       $kolomtgl = $i;
      //       if ($vsq == '1')
      //       {
      //         $jamdel = substr($rs->fields[$i],6,2);
      //       }
      //       if ($vsq == '2')
      //       {
      //         $jamdel = substr($rs->fields[$i+1],6,2);
      //       }
      //       break;
      //     }
      //   } // end for
	    //   $rs->MoveNext();
      // } //end while
	    // $rs->Close();
  
      //------------------------------------------------------------------------
      // MAKE DIGET
      $sqlbps = "select transdate,hd,tm,suppcode,partno,partname,balqty,qty1,qty2,qty3,qty4,
      qty5,qty6,qty7,qty8,qty9,qty10,qty11,qty12,qty13,qty14,qty15,qty16,
      qty17,qty18,qty19,qty20,qty21,qty22,qty23,qty24,qty25,qty26,qty27,
      qty28,qty29,qty30,qty31,qty32 from tdsact where SuppCode = '{$vsupp}' and HD='D' order by PartNo";
	    echo '<br>';
	    echo $sqlbps;
	    $rsbps = $pdo->query($sqlbps)->fetchAll();
      $kolomtotal = 0;
      foreach ($rsbps as $row) {
        $vpartno  = $row[4];
        $kolombal = $row[6];
        if ($vsq == '1') {
          $kolomnilai = $row[$kolomtgl];
        }
        if ($vsq == '2') {
          $kolomnilai = $row[$kolomtgl + 1];
        }
        if ($kolomtgl == 7) {
          if ($vsq == '1') {
            $kolomtotal = $kolombal + $kolomnilai;
          }
          if ($vsq == '2') {
            $kolomtotal = $kolomnilai;
          }
        }
        if ($kolomtgl > 7) {
          $kolomtotal = $kolomnilai;
        }
        if ($kolomtotal > 0) {
          $ppartno = $row[4];
          $ppartname = $row[5];

          $data_insdiget = [
            'supp' => $vsupp,
            'tgl' => $vblntglthn,
            'sq' => $vsq,
            'partno' => $ppartno,
            'qty' => $kolomtotal,
            'jamdel' => $jamdel,
            'jamsq' => '1',
          ];
          $sqlinsdiget = "insert into diget(supp,tgl,sq,partno,qty,jamdel,jamsq) 
            values(:supp,:tgl,:sq,:partno,:qty,:jamdel,:jamsq)";
          echo '<br>' . $sqlinsdiget;
          $rsinsdiget  = $pdo->prepare($sqlinsdiget)->execute($data_insdiget);
          // $rsinsdiget->Close();
        }    
      }
      // while(!$rsbps->EOF)
      // {
      //   $vpartno  = $rsbps->fields[4];
      //   $kolombal = $rsbps->fields[6];
      //   if ($vsq == '1')
      //   {
      //     $kolomnilai = $rsbps->fields[$kolomtgl];
      //   }
      //   if ($vsq == '2')
      //   {
      //     $kolomnilai = $rsbps->fields[$kolomtgl+1];
      //   }
      //   if($kolomtgl == 7)
      //   {
      //     if($vsq == '1')
      //     {
      //       $kolomtotal = $kolombal + $kolomnilai;
      //     }
      //     if($vsq == '2')
      //     {
      //       $kolomtotal = $kolomnilai;
      //     }
      //   }
	    //   if($kolomtgl > 7)
      //   {
      //     $kolomtotal = $kolomnilai;
      //   }  
      //   if($kolomtotal > 0)
      //   {
      //     $ppartno = $rsbps->fields[4];
      //     $ppartname = $rsbps->fields[5];
      //     $sqlinsdiget = "insert into diget(supp,tgl,sq,partno,qty,jamdel,jamsq) 
      //       values('{$vsupp}','{$vblntglthn}','{$vsq}','{$ppartno}','{$kolomtotal}','{$jamdel}','1')";
	    //     echo '<br>'.$sqlinsdiget;
	    //     $rsinsdiget  = $pdo->query($sqlinsdiget);
	    //     $rsinsdiget->Close();
	    //   }    
	    //   $rsbps->MoveNext();  
      // } // while
      // $rsbps->Close();


      // ---------------- batas make diget ------------------------------------
  
    } // end of if ($vsq == '1')
  
    //------------  update diget jika ada balance --------------------------
    $sqldibal = "select supp,partno,balqty from dibal where ( supp = '{$vsupp}' ) and ( balqty <> 0 )";
    $rsdibal  = $pdo->query($sqldibal)->fetchAll();
    foreach ($rsdibal as $row) {
      $balpartno = $row[1];
      $bal = $row[2];
      echo "<br>";
      echo "balance = " . $balpartno . "," . $bal;
      echo "<br>";
      //cek part di diget
      $sqlcekdiget = "select count(*) as ada from diget where ( supp = '{$vsupp}' ) and 
         ( partno = '{$balpartno}' ) and (tgl = '{$vblntglthn}') and (sq = '{$vsq}')";
      $rscekdiget  = $pdo->query($sqlcekdiget)->fetchAll();
      $adapart = $rscekdiget[0][0];
      // $rscekdiget->Close();
      $data_diadd = [
        'supp' => $vsupp,
        'tgl' => $vblntglthn,
        'sq' => $vsq,
        'partno' => $balpartno,
        'qty' => $bal
      ];
      if ($adapart == 0) {
        
        $sqldiadd = "insert into diget(supp,tgl,sq,partno,qty) 
          values(:supp,:tgl,:sq,:partno,:qty)";
        $rsdiadd  = $pdo->prepare($sqldiadd)->execute($data_diadd);
        // $rsdiadd->Close();
      } else {
        $sqldiadd = "update diget set qty = qty + :qty where ( supp = :supp ) 
          and ( partno = :partno) and ( tgl = :tgl ) and ( sq = :sq)";
        $rsdiadd  = $pdo->prepare($sqldiadd)->execute($data_diadd);
        // $rsdiadd->Close();
      }
    }
    
    // while(!$rsdibal->EOF)
    // {
    //   $balpartno = $rsdibal->fields[1];
    //   $bal = $rsdibal->fields[2];
    //   echo "<br>";
    //   echo "balance = " . $balpartno . "," . $bal;
    //   echo "<br>";
    //   //cek part di diget
    //   $sqlcekdiget = "select count(*) as ada from diget where ( supp = '{$vsupp}' ) and 
    //      ( partno = '{$balpartno}' ) and (tgl = '{$vblntglthn}') and (sq = '{$vsq}')";
    //   $rscekdiget  = $pdo->query($sqlcekdiget);
    //   $adapart = $rscekdiget->fields[0];
    //   $rscekdiget->Close();
	  //   if ($adapart == 0)
    //   {
    //     $sqldiadd = "insert into diget(supp,tgl,sq,partno,qty) 
    //       values('$vsupp','$vblntglthn','$vsq','$balpartno',$bal)";
    //     $rsdiadd  = $pdo->query($sqldiadd);
	  //     $rsdiadd->Close();    
    //   }
    //   else
    //   {
    //     $sqldiadd = "update diget set qty = qty + $bal where ( supp = '$vsupp' ) 
    //       and ( partno = '$balpartno') and ( tgl = '$vblntglthn' ) and ( sq = '$vsq')";   
    //     $rsdiadd  = $pdo->query($sqldiadd);
	  //     $rsdiadd->Close();    
    //   }
	  //   $rsdibal->MoveNext();
    // } // while($recdibal)
  
    // $rsdibal->Close();
    //------------------ end of update diget jika ada balance --------------------

    // variable hitung big part vs order balance
    
    //----------------- buat DI di ambil record dari diget ------------------------
    $sqldiget = "select supp,tgl,sq,partno,qty,jamdel,jamsq from diget where (supp = '{$vsupp}') 
      and (tgl='{$vblntglthn}') and (sq='$vsq') order by sq,partno,jamsq";
    $rsdiget  = $pdo->query($sqldiget)->fetchAll();

    foreach ($rsdiget as $row) {
      $getpartno = $row[3];
      $getjamdel = $row[5];
      $getjamsq  = $row[6];
      // variable hitung big part vs order balance
      $b = $row[4]; //quantity bigpart
      $cekkecil = 0; //cek jika sudah < 0 tdk proses ob selanjutnya       
      $jumord = 0; //jumlah order balance yg diambil
      $sqlordbal = "select PartNumber,convert(varchar,ReqDate,1),PONumber,OrderBalance 
        from ordbalvir where SuppCode = '{$vsupp}' and PartNumber = '{$getpartno}' 
        order by PartNumber,ReqDate";
      $rsob = $pdo->query($sqlordbal)->fetchAll();
      foreach ($rsob as $row) {
        $o = $row[3];

        // rumus ini diletakkan paling bawah :  $b = $b - $o;
        //jika qty big part lebih kecil atau sama dgn qty order balance dan > 0
        $sqlinsdi = "insert into di(supptglpo,supp,tgli,po,partno,qty,tgld,invoice,status,
            ditime,disq) values(:supptglpo,:supp,:tgli,:po, :partno,:qty,:tgld,'',:status,:ditime,:disq)";
        if ($b <= $o && $b > 0) {
          $supptglpo = trim($vsupp) . $vthn . $vbln . $vtgl . trim($vsq) . $row[2];
          // insert record

          // $sqlinsdi = "insert into di(supptglpo,supp,tgli,po,partno,qty,tgld,invoice,status,
          //   ditime,disq) values(:supptglpo,:supp,:tgli,:po,
          //   :partno,:qty,:tgld,'',:status,:ditime,:disq)";
          $rsinsdi  = $pdo->prepare($sqlinsdi)->execute([
            'supptglpo' => $supptglpo,
            'supp' => $vsupp,
            'tgli' => $row[1],
            'po' => $row[2],
            'partno' => $getpartno,
            'qty' => $b,
            'tgld' => $vblntglthn,
            'status' => '0',
            'ditime' => $getjamdel,
            'disq' => $vsq
          ]);
          // $rsinsdi->Close();
        }
        if ($b > $o) {
          $supptglpo = trim($vsupp) . $vthn . $vbln . $vtgl . trim($vsq) . $row[2];
          // insert record
          $rsinsdi  = $pdo->prepare($sqlinsdi)->execute([
            'supptglpo' => $supptglpo,
            'supp' => $vsupp,
            'tgli' => $row[1],
            'po' => $row[2],
            'partno' => $getpartno,
            'qty' => $row[3],
            'tgld' => $vblntglthn,
            'status' => '0',
            'ditime' => $getjamdel,
            'disq' => $vsq
          ]);
          $rsinsdi->Close();
        }
        //  rumus ini diletakkan paling bawah :
        $b = $b - $o;
      }
      // while (!$rsob->EOF) {
      //   $o = $rsob->fields[3];
      //   // rumus ini diletakkan paling bawah :  $b = $b - $o;
      //   //jika qty big part lebih kecil atau sama dgn qty order balance dan > 0
      //   if ($b <= $o && $b > 0) {
      //     $supptglpo = trim($vsupp) . $vthn . $vbln . $vtgl . trim($vsq) . $rsob->fields[2];
      //     // insert record
      //     $sqlinsdi = "insert into di(supptglpo,supp,tgli,po,partno,qty,tgld,invoice,status,
      //       ditime,disq) values('{$supptglpo}','{$vsupp}','{$rsob->fields[1]}','{$rsob->fields[2]}',
      //       '{$getpartno}','{$b}','{$vblntglthn}','','0','{$getjamdel}','{$vsq}')";
      //     $rsinsdi  = $pdo->query($sqlinsdi);
      //     $rsinsdi->Close();
      //   }
      //   if ($b > $o) {
      //     $supptglpo = trim($vsupp) . $vthn . $vbln . $vtgl . trim($vsq) . $rsob->fields[2];
      //     // insert record
      //     $sqlinsdi = "insert into di(supptglpo,supp,tgli,po,partno,qty,tgld,invoice,status,
      //       ditime,disq) values('{$supptglpo}','{$vsupp}','{$rsob->fields[1]}',
      //       '{$rsob->fields[2]}','{$getpartno}','{$rsob->fields[3]}','{$vblntglthn}',
      //       '','0','{$getjamdel}','{$vsq}')";
      //     $rsinsdi  = $pdo->query($sqlinsdi);
      //     $rsinsdi->Close();
      //   }
      //   //  rumus ini diletakkan paling bawah :
      //   $b = $b - $o;
      //   $rsob->MoveNext();
      // } 
    }
    // while(!$rsdiget->EOF)
    // {
    //   $getpartno = $rsdiget->fields[3];
    //   $getjamdel = $rsdiget->fields[5];
    //   $getjamsq  = $rsdiget->fields[6];
    //   // variable hitung big part vs order balance
    //   $b = $rsdiget->fields[4] ; //quantity bigpart
    //   $cekkecil = 0 ; //cek jika sudah < 0 tdk proses ob selanjutnya       
    //   $jumord = 0; //jumlah order balance yg diambil
    //   $sqlordbal = "select PartNumber,convert(varchar,ReqDate,1),PONumber,OrderBalance 
    //     from ordbalvir where SuppCode = '{$vsupp}' and PartNumber = '{$getpartno}' 
    //     order by PartNumber,ReqDate";
    //   $rsob = $pdo->query($sqlordbal);
    //   while(!$rsob->EOF)
    //   {
    //     $o = $rsob->fields[3];
    //     // rumus ini diletakkan paling bawah :  $b = $b - $o;
    //     //jika qty big part lebih kecil atau sama dgn qty order balance dan > 0
    //     if ($b <= $o && $b > 0)
    //     {
    //       $supptglpo = trim($vsupp) . $vthn . $vbln . $vtgl . trim($vsq) . $rsob->fields[2];
    //       // insert record
    //       $sqlinsdi = "insert into di(supptglpo,supp,tgli,po,partno,qty,tgld,invoice,status,
    //         ditime,disq) values('{$supptglpo}','{$vsupp}','{$rsob->fields[1]}','{$rsob->fields[2]}',
    //         '{$getpartno}','{$b}','{$vblntglthn}','','0','{$getjamdel}','{$vsq}')";
    //       $rsinsdi  = $pdo->query($sqlinsdi);
		//       $rsinsdi->Close();
    //     }
    //     if ($b > $o)
    //     {
    //       $supptglpo = trim($vsupp) . $vthn . $vbln . $vtgl . trim($vsq) . $rsob->fields[2];
    //       // insert record
    //       $sqlinsdi = "insert into di(supptglpo,supp,tgli,po,partno,qty,tgld,invoice,status,
    //         ditime,disq) values('{$supptglpo}','{$vsupp}','{$rsob->fields[1]}',
    //         '{$rsob->fields[2]}','{$getpartno}','{$rsob->fields[3]}','{$vblntglthn}',
    //         '','0','{$getjamdel}','{$vsq}')";
    //       $rsinsdi  = $pdo->query($sqlinsdi);
		//       $rsinsdi->Close();
    //     } 
	  //     //  rumus ini diletakkan paling bawah :
    //     $b = $b - $o;
	  //     $rsob->MoveNext();
    //   } 
	  //   $rsdiget->MoveNext();
    //   $rsob->Close();
    // }  // end of while diget
    // $rsdiget->Close();
    //--------------------- end of buat DI -------------------------------------

    $suppacak = ($vsupp * 1997) + 712;
    // yudi 30/07/2019
    // header("Location:jdiinv.php?s=$suppacak&t=$vtglbln");
    // pakai header bermasalah diganti dengan javascript
    echo "<script>window.location = 'jdiinv.php?s=$suppacak&t=$vtglbln'</script>";
  }	//	end of jika proses == 1      
}	// ----------- end of jenis supplier = J1  -------------------------------

// proses jika jenis supplier = J2 , delivery follow Big Part Schedule	
if ($suppdec == 'J2')
{
  $sqltgl = "select ". Helper::translateQueryGetdateToMysql("MySQL");
  $tgldb = $pdo->query($sqltgl)->fetchAll();
  $tglserver = $tgldb[0][0];
  // $tgldb->Close();
  $ythn = substr($tglserver,2,2);
  $ybln = substr($tglserver,5,2);
  $ytgl = substr($tglserver,8,2);
  $tglcek = $ybln . "/" . $ytgl . "/" . $ythn;
  $vblntglthn = $vbln . "/" . $vtgl . "/" . $vthn;
  $obsupp = trim($vsupp);
  //cek diget
  $sdhget = 0;
  $proses = 1;
  $sqprev = $vsq - 1;  // nilai sequence sebelumnya

  if ($sqprev > 0 )  // jika hasil nol tdk di proses cek sequence
  {
    $cekget = $obsupp . $vthn . $vbln . $vtgl . $sqprev . "%";
    $sqlcekget = "select count(*) as tget from di where (supptglpo like '{$cekget}') ";
    $rscekget  = $pdo->query($sqlcekget)->fetchAll();
	  $sdhget    = $rscekget[0][0];    
	  // $rscekget->Close();	
    if ($sdhget == 0)
    {
      $proses = 0;
      echo "<br>data sequence sebelumnya tdk ada....<br>"; 
      // header("Location:jdimaketgl.php?p=Data Delivery Instruction untuk sequence sebelumnya belum ada !!!...");
      echo "<script>window.location = 'dimaketgl.php?p=Data Delivery Instruction untuk sequence sebelumnya belum ada !!!...'</script>";
    }
    else
    {
      $proses = 1;
    }   
  }  // end of $sqprev > 0
  
  if ($proses == 1 )
  {
    //---------------- hapus tabel diget sebelum insert -------------------
    $data_deldiget = [
      'supp' => $vsupp,
      'tgl' => $vblntglthn,
      'sq' => $vsq
    ];
    $sqldeldiget = "delete from diget where (supp = :supp) and (tgl = :tgl) and (sq=:sq)";
    $rsdeldiget  = $pdo->prepare($sqldeldiget)->execute($data_deldiget);
	  // $rsdeldiget->Close();
    //----------------- end of hapus tabel diget ---------------------------
	  //------------- hapus di status 0 ---------------------------- 
    $vdel = trim($vsupp) . $vthn . $vbln . $vtgl . $vsq . '%';
    $data_delnob = [
      'supptglpo' => $vdel,
      'status' => '0'
    ];
    $sqldelnob = "delete from di where (supptglpo like :supptglpo) and (status = :status)";
    $rsdelnob  = $pdo->prepare($sqldelnob)->execute($data_delnob);
	  // $rsdelnob->Close();
    //---------- end of hapus di status 0 ------------------------
    // hapus digetsum
    // -----------------------------------------------------------
    $sqldelgetsum = "delete from digetsum where supp = :supp";
    $rsdelgetsum  = $pdo->prepare($sqldelgetsum)->execute(['supp'=> $obsupp]);
	  // $rsdelgetsum->Close();
    // --------------- end of hapus digetsum ----------------------
	  //---------------- ins data ke table getsum -------------------

    $sqlinsgetsum = "insert into digetsum(supp,partno,qty) select max(supp) as 
      supp,partno,sum(qty) as qty from diget where ( supp = '{$obsupp}' ) and 
      (tgl >= '{$tglcek}')  group by partno order by partno";
    $rsinsgetsum  = $pdo->prepare($sqlinsgetsum)->execute();
	  // $rsinsgetsum->Close();
    //--------------- end of ins data ke table getsum  ---------
	  // ---------------------------------------------------------
    // hapus diupload...
    // ---------------------------------------------------------
    $sqldiupdel = "delete from diupload where supp = '{$obsupp}'";
    $rsdiupdel  = $pdo->prepare($sqldiupdel)->execute();
	  // $rsdiupdel->Close();
    // ------------- end of hapus diupload ---------------------
	  //----------------------------------------------------------
    // ambil summary per partno dari data yg sudah di upload
    // dan kemudian input ke tabel diupload
    //----------------------------------------------------------
    $sqldiup = "insert into diupload(supp,partno,qty) select max(supp) as 
      supp,partno,sum(qty) as qty from di where ( supp = '{$obsupp}' ) and 
      (tgld >= '{$tglcek}') and ( status <> '0' ) group by partno order by partno";
    $rsdiup  = $pdo->prepare($sqldiup)->execute();
	  // $rsdiup->Close();
    //--------------- end of ins data ke table upload ----------
	  //----------------------------------------------------------
    // hapus dibal berdasarkan supplier
    //----------------------------------------------------------
    $sqldibaldel = "delete dibal where supp = '{$obsupp}'";
    $rsdibaldel  = $pdo->prepare($sqldibaldel)->execute();
	  // $rsdibaldel->Close();
    //---------------- end of hapus dibal ----------------------
	  //------------------------------------------------------------------
    // cari balance qty antara di yg sudah diupload dengan di original
    // kemudian insert data ke table dibal
    //------------------------------------------------------------------ 
    $sqldibal = "select digetsum.supp,digetsum.partno,digetsum.qty - diupload.qty 
      as balqty from digetsum INNER JOIN diupload ON digetsum.partno = diupload.partno 
      order by digetsum.partno";
    $rsdibal  = $pdo->query($sqldibal)->fetchAll();
    
    while (!$rsdibal->EOF)
    {
      $balsupp   = $rsdibal->fields[0];
      $balpartno = $rsdibal->fields[1];
      $balqty    = $rsdibal->fields[2];
      $sqlinsdibal = "insert into dibal(supp,partno,balqty) values('{$balsupp}',
        '{$balpartno}','{$balqty}')";
      $rsinsdibal  = $pdo->query($sqlinsdibal);
      $rsdibal->MoveNext();
    }
	  $rsinsdibal->Close();
	  $rsdibal->Close();
    //-------------- end of ins data ke table dibal -------------------- 
    //---------------  delete virtual order balance --------------------
    $sqldelvob = "delete from ordbalvir where suppcode = '{$vsupp}'";
    $rsdelvob  = $pdo->query($sqldelvob);
	  $rsdelvob->Close();
    //-----------------  end of delete virtual order balance --------------
    // copy record from ordbalact to ordbalvir
    $sqlinsvob = "insert into ordbalvir(transdate,suppcode,partnumber,partname,orderqty,
      reqdate,ponumber,posq,orderbalance,supprest,model,issuedate,potype,statuspart,remark,
      statusread) select transdate,suppcode,partnumber,partname,orderqty,
      reqdate,ponumber,posq,orderbalance,supprest,model,issuedate,potype,statuspart,remark,
      statusread from ordbalact where suppcode = '{$vsupp}'";
    $rsinsvob  = $pdo->query($sqlinsvob);
	  $rsinsvob->Close();
    // hapus ordbalactupd
    $sqldelobup = "delete from ordbalactupd where supp = '{$vsupp}'";
    $rsdelobup  = $pdo->query($sqldelobup);
	  $rsdelobup->Close();
    // mencari orderbalance dikurangi di yg sudah upload
    $sqlobvir = "select di.supp,di.po,ordbalvir.orderbalance - di.qty as balqty from 
      ordbalvir inner join di on ordbalvir.ponumber = di.po where (di.status <> '0') 
      and (di.supp = '{$vsupp}') and (di.tgld >= '{$tglcek}') order by partno,disq";
    $rsobvir  = $pdo->query($sqlobvir);
    while (!$rsobvir->EOF)
    {
      $sqlinsobup = "insert into ordbalactupd(supp,po,balqty) values('{$rsobvir->fields[0]}',
        '{$rsobvir->fields[1]}','{$rsobvir->fields[2]}')";
      $rsinsobup  = $pdo->query($sqlinsobup);
      $rsinsobup->Close();
      $rsobvir->Movenext();
    } //end of while
    
	  $rsobvir->Close();
	
	  // mencari po yg sudah dipakai
    $sqlobupd = "select supp,po,balqty from ordbalactupd where supp = '{$vsupp}'";
    $rsobupd  = $pdo->query($sqlobupd);
    while(!$rsobupd->EOF)
    {
      $csupp = $rsobupd->fields[0];
      $cpo   = $rsobupd->fields[1];
      $cqty  = $rsobupd->fields[2];
      if ($cqty == 0)
      {
        $sqlobvirdel = "delete ordbalvir where ponumber = '{$cpo}'";
        $rsobvirdel=mssql_query($sqlobvirdel,$con); 
      }
      if ($cqty > 0)
      {
        $sqlobvidel = "update ordbalvir set orderbalance = '{$cqty}' where ponumber = '{$cpo}'";
        $rsobvirdel = $pdo->query($sqlobvidel);
        $rsobvirdel->Close();
      }
	    $rsobupd->MoveNext();  
    }  // end of while 
    
    $rsobupd->Close();
    // ---------------------------------------------------------------------
    // Proses make DIGET hanya jika sequence = '1' / '2' ( normal 2X delivery)
    // ----------------------------------------------------------------------
    if ( $vsq == '1' || $vsq == '2' )
    {
      // cari tanggal dari header
      $sql="select transdate,hd,tm,suppcode,partno,partname,balqty,qty1,qty2,qty3,qty4,
      qty5,qty6,qty7,qty8,qty9,qty10,qty11,qty12,qty13,qty14,qty15,qty16,
      qty17,qty18,qty19,qty20,qty21,qty22,qty23,qty24,qty25,qty26,qty27,
      qty28,qty29,qty30,qty31,qty32 from TDSACT where SuppCode = '{$vsupp}' and HD = 'H' order by PartNo";
      $row=$pdo->query($sql);
      $test = 'kosong';
      $cek = $vtglj2;
      $tgl = $vtglj2;	
	    //echo "mencari index kolom yang aktif ...<br>";
      while(!$row->EOF)
      {
        for ($i = 0; $i <=40; $i++)
        {  
          $cek = substr($row->fields[$i],0,5);
          if ($cek == $tgl)
          {
            $kolomtgl = $i;
            if ($vsq == '1')
            {
              $jamdel = substr($row->fields[$i],6,2);
            }
            if ($vsq == '2')
            {
              $jamdel = substr($row->fields[$i+1],6,2);
            }
            break;
          }  
        } // end for
		    $row->MoveNext();
      } //end while
	    $row->Close();
	    //------------------------------------------------------------------------
      // MAKE DIGET
      $sqlbps = "select transdate,hd,tm,suppcode,partno,partname,balqty,qty1,qty2,qty3,qty4,
      qty5,qty6,qty7,qty8,qty9,qty10,qty11,qty12,qty13,qty14,qty15,qty16,
      qty17,qty18,qty19,qty20,qty21,qty22,qty23,qty24,qty25,qty26,qty27,
      qty28,qty29,qty30,qty31,qty32 from tdsact where SuppCode = '{$vsupp}' and HD='D' order by PartNo";
      $rowbps = $pdo->query($sqlbps);	  
      while(!$rowbps->EOF)
      {
        $vpartno  = $rowbps->fields[4];
        $kolombal = $rowbps->fields[6];
        if($vsq == '1')
        {
          $kolomnilai = $rowbps->fields[$kolomtgl];
        }
        if($vsq == '2')
        {
          $kolomnilai = $rowbps->fields[$kolomtgl+1];
        }
        if($kolomtgl == 7)
        {
          if($vsq == '1')
          {
            $kolomtotal = $kolombal + $kolomnilai;
          }
          if($vsq == '2')
          {
            $kolomtotal = $kolomnilai;
          }
        }
        if($kolomtgl > 7)
        {
          $kolomtotal = $kolomnilai;
        }  
        if($kolomtotal > 0)
        {
          $ppartno = $rowbps->fields[4];
          $ppartname = $rowbps->fields[5];
          $sqlinsdiget = "insert into diget(supp,tgl,sq,partno,qty,jamdel,jamsq) 
            values('{$vsupp}','{$vblntglthn}','{$vsq}','{$ppartno}','{$kolomtotal}',
            '{$jamdel}','{$vsq}')";
          $rsinsdiget=$pdo->query($sqlinsdiget);
        }
		    $rowbps->MoveNext();
      } // while
	    $rsinsdiget->Close();
	    $rowbps->Close();
    // ---------------- batas make diget ------------------------------------
    } // end of if ( $vsq == '1' || $vsq == '2' )
    // ---------------- batas proses diget jika sq = '1' / sq = '2' ---------
    //------------  update diget jika ada balance --------------------------
    $sqldibal = "select supp,partno,balqty from dibal where ( supp = '{$vsupp}' ) and ( balqty <> 0 )";
    $recdibal = $pdo->query($sqldibal);
	  while(!$recdibal->EOF)
    {
      $balpartno = $recdibal->fields[1];
      $bal = $recdibal->fields[2];
      //cek part di diget
      $sqlcekdiget = "select count(*) as ada from diget where ( supp = '{$vsupp}' ) 
        and ( partno = '{$balpartno}' ) and (tgl = '{$vblntglthn}') and (sq = '{$vsq}')";
      $reccekdiget = $pdo->query($sqlcekdiget);
      $reccekdiget->Close();
      $adapart = $reccekdiget[0];
	  
      if($adapart == 0)
      {
        $sqldiadd = "insert into diget(supp,tgl,sq,partno,qty) 
          values('{$vsupp}','{$vblntglthn}','{$vsq}','{$balpartno}','{$bal}')";
        $rsdiadd  = $pdo->query($sqldiadd);
        $rsdiadd->Close();
      }
      else
      {
        $sqldiadd = "update diget set qty = qty + $bal where ( supp = '$vsupp' ) and 
          ( partno = '$balpartno') and ( tgl = '$vblntglthn' ) and ( sq = '$vsq')";   
        $rsdiadd  = $pdo->query($sqldiadd);
      }
	    $recdibal->MoveNext();
    } // while($recdibal)
	
	
	  $recdibal->Close();
    //------------------ end of update diget jika ada balance --------------------
	
	  // variable hitung big part vs order balance	
	  //----------------- buat DI di ambil record dari diget ------------------------
    $sqldiget = "select supp,tgl,sq,partno,qty,jamdel,jamsq from diget where 
      (supp = '{$vsupp}') and (tgl='{$vblntglthn}') and (sq='{$vsq}') order by sq,partno,jamsq";
    $recdiget = $pdo->query($sqldiget);
    while(!$recdiget->EOF)
    {
      $getpartno = $recdiget->fields[3];
      $getjamdel = $recdiget->fields[5];
      $getjamsq  = $recdiget->fields[6];
      // variable hitung big part vs order balance
      $b = $recdiget->fields[4] ; //quantity bigpart
      $cekkecil = 0 ; //cek jika sudah < 0 tdk proses ob selanjutnya       
      $jumord = 0; //jumlah order balance yg diambil
      $sqlordbal = "select PartNumber,convert(varchar,ReqDate,1),PONumber,OrderBalance 
        from ordbalvir where SuppCode = '{$vsupp}' and PartNumber = '{$getpartno}' 
        order by PartNumber,ReqDate";
      $rowob = $pdo->query($sqlordbal);
      while(!$rowob->EOF)
      {
        $o = $rowob->fields[3];
        // rumus ini diletakkan paling bawah :  $b = $b - $o;
        //jika qty big part lebih kecil atau sama dgn qty order balance dan > 0
        if ($b <= $o && $b > 0)
        {
          $supptglpo = trim($vsupp) . $vthn . $vbln . $vtgl . trim($vsq) . $rowob->fields[2];
          // insert record
          $sqlinsdi = "insert into di(supptglpo,supp,tgli,po,partno,qty,tgld,invoice,status,
            ditime,disq) values('{$supptglpo}','{$vsupp}','{$rowob->fields[1]}',
            '{$rowob->fields[2]}','{$getpartno}','{$b}','{$vblntglthn}','','0','{$getjamdel}','{$vsq}')";
          $rsinsdi  = $pdo->query($sqlinsdi);
        }
        if ($b > $o)
        {
          $supptglpo = trim($vsupp) . $vthn . $vbln . $vtgl . trim($vsq) . $rowob->fields[2];
          // insert record
          $sqlinsdi = "insert into di(supptglpo,supp,tgli,po,partno,qty,tgld,invoice,status,ditime,disq)
            values('{$supptglpo}','{$vsupp}','{$rowob->fields[1]}','{$rowob->fields[2]}','{$getpartno}',
            '{$rowob->fields[3]}','{$vblntglthn}','','0','{$getjamdel}','{$vsq}')";
          $rsinsdi  = $pdo->query($sqlinsdi);
        }    
        //  rumus ini diletakkan paling bawah :
        $b = $b - $o;
		    $rowob->MoveNext();
      } // while($rowob)
      $rsinsdi->Close();
	    $rowob->Close();
	    $recdiget->MoveNext();
    }  // end of while diget
    $recdiget->Close();
    //--------------------- end of buat DI -------------------------------------
    $suppacak = ($vsupp * 1997) + 712;
    // header("Location:jdiinv.php?s=$suppacak&t=$vtglbln");
	  echo "<script>window.location = 'jdiinv.php?s=$suppacak&t=$vtglbln'</script>";
  } // end of if ($proses == 1 )
} // end of if ($suppdec == 'J2')

//---------------------------------------------------------------------------
// DELIVERY FOLLOW PO
if ($suppdec == 'N' || $suppdec == 'Y')
{ 
  $sqltgl = "select getdate()";
  $tgldb = $pdo->query($sqltgl);
  $tglserver = $tgldb->fields[0];
  $tgldb->Close();
  $ythn = substr($tglserver,2,2);
  $ybln = substr($tglserver,5,2);
  $ytgl = substr($tglserver,8,2);
  $tglcek = $ybln . "/" . $ytgl . "/" . $ythn;
  $vblntglthn = $vbln . "/" . $vtgl . "/" . $vthn;
  $obsupp = trim($vsupp);
  $cekid = $obsupp . $vthn . $vbln . $vtgl . "%";
  //cek diget
  $sdhget = 0;
  $cekget = $obsupp . $vthn . $vbln . $vtgl . "%";
  $sqlcekget = "select count(*) as tget from di where (supptglpo like '{$cekget}') and (status = '0')";
  $rscekget  = $pdo->query($sqlcekget);
  $sdhget = $rscekget->fields[0];	
  $rscekget->Close();
  if($sdhget > 0)
  {
    echo "<br>data sudah data...<br>"; 
    // header("Location:jdimaketgl.php?p=Data Delivery Instruction untuk tanggal tsb sudah ada, silahkan pilih menu Edit Delivery Instruction untuk melanjutkan...");
    echo "<script>window.location = 'jdimaketgl.php?p=Data Delivery Instruction untuk tanggal tsb sudah ada, silahkan pilih menu Edit Delivery Instruction untuk melanjutkan...'</script>";  
  }
  
  if ($sdhget==0)
  {
    // delete virtual order balance
    $sqldelvob = "delete from ordbalvir where suppcode = '{$vsupp}'";
    $rsdelvob  = $pdo->query($sqldelvob);
    $rsdelvob->Close();

	  // copy record from ordbalact to ordbalvir
    $sqlinsvob = "insert into ordbalvir(transdate,suppcode,partnumber,partname,orderqty,
    reqdate,ponumber,posq,orderbalance,supprest,model,issuedate,potype,statuspart,remark,
    statusread) select transdate,suppcode,partnumber,partname,orderqty,
      reqdate,ponumber,posq,orderbalance,supprest,model,issuedate,potype,statuspart,remark,
      statusread from ordbalact where suppcode = '{$vsupp}'";
    $rsinsvob  = $pdo->query($sqlinsvob);
	  $rsinsvob->Close();
	
	  // cukup disini tambahannya --> cek aktual sudah ok
    // hapus ordbalactupd
    $sqldelobup = "delete from ordbalactupd where supp = '{$vsupp}'";
    $rsdelobup  = $pdo->query($sqldelobup);
	  $rsdelobup->Close();
	
	  // mencari orderbalance dikurangi di yg sudah upload
    $sqlobvir = "select di.supp,di.po,ordbalvir.orderbalance - di.qty as balqty from 
      ordbalvir inner join di on ordbalvir.ponumber = di.po where (di.status <> '0') 
      and (di.supp = '{$vsupp}') and (di.tgld >= '{$tglcek}')";
    $recobvir  = $pdo->query($sqlobvir);
    while (!$recobvir->EOF)
    {
      $sqlinsobup = "insert into ordbalactupd(supp,po,balqty) 
        values('{$recobvir->fields[0]}','{$recobvir->fields[1]}','{$recobvir->fields[2]}')";
      $rsinsobup  = $pdo->query($sqlinsobup);
      $rsinsobup->Close();	
	    $recobvir->MoveNext();
    } //end of while
	
	  $recobvir->Close();
	
    // mencari po yg sudah dipakai
    $sqlobupd = "select supp,po,balqty from ordbalactupd where supp = '{$vsupp}'";
    $recobupd = $pdo->query($sqlobupd);
    while(!$recobupd->EOF)
    {
      $csupp = $recobupd->fields[0];
      $cpo   = $recobupd->fields[1];
      $cqty  = $recobupd->fields[2];
      if($cqty == 0)
      {
        $sqlobvirdel = "delete ordbalvir where ponumber = '{$cpo}'";
        $rsobvirdel  = $pdo->query($sqlobvirdel);
      }
      if ($cqty > 0)
      {
        $sqlobvirdel = "update ordbalvir set orderbalance = '{$cqty}' where ponumber = '{$cpo}'";
        $rsobvirdel   = $pdo->query($sqlobvirdel);
		    $rsobvirdel->Close();
      }
	    $recobupd->MoveNext();
    }    // end of while 
   
	  $recobupd->Close();
	
    // delete existing record
    $vdel = trim($vsupp) . $vthn . $vbln . $vtgl . '%';
    $sqldelnob = "delete from di where (supptglpo like '{$vdel}') and (status = '0')";
    $rsdelnob  = $pdo->query($sqldelnob);
    $rsdelnob->Close();
    $sqlnob="select SuppCode,PartNumber,OrderBalance,convert(varchar,ReqDate,1),PONumber 
      from ordbalvir where SuppCode = '{$vsupp}' order by ReqDate, PartNumber,PONumber";
    $rownob=$pdo->query($sqlnob);
	  while(!$rownob->EOF)
    {
      $bulan = substr($rownob->fields[3],0,2);
      $hari = substr($rownob->fields[3],3,2);
      $tahun = substr($rownob->fields[3],6,2);
      $tglob = $tahun . $bulan . $hari;
      $tglpilih = $vthn . $vbln . $vtgl;
      if ($tglob <= $tglpilih)  
      {
        $supptglpo =  trim($rownob->fields[0]) . $vthn . $vbln . $vtgl . $vsq . $rownob->fields[4] ;
        $sqlDateTime = $rownob->fields[3];
        $tgldel = $vbln . "/" . $vtgl . "/" . $vthn;
        $sqlinsnob = "insert into di(supptglpo,supp,tgli,po,partno,qty,tgld,invoice,status) 
          values('{$supptglpo}','{$vsupp}','{$sqlDateTime}','{$rownob->fields[4]}',
          '{$rownob->fields[1]}','{$rownob->fields[2]}','{$tgldel}','','0')";
        $rsinsnob  = $pdo->query($sqlinsnob);
		    $rsinsnob->Close();
      } 
	    $rownob->MoveNext();	  
    } 
	  $rownob->Close();
    
	  echo '<br>';
    echo '<a href="jditgl.php">edit di</a>';
    $suppacak = ( $vsupp * 1997 ) + 712;
    // header("Location:jdiinv.php?s=$suppacak&t=$vtglbln");
    echo "<script>window.location = 'jdiinv.php?s=$suppacak&t=$vtglbln'</script>";
	} // end of $sdhget==0  
}	// end of ($suppdec == 'N' || $suppdec == 'Y')

echo "</body></html>";
//	connection close db
$db->Close(); 
?>