<?php
  /*
  ****  Application Name : soaendup.php
  ****  
	****	create by IMAM PRAYUDI
	****	on 10 JANUARI 2020
    ****	remark: -
            
	*/  
	include('/var/www/html/ADODB/con_ediweb.php');
    echo '<br />Starting....Please wait....';
    date_default_timezone_set('Asia/Bangkok');
    $tgljam = date('Y-m-d H:i:s');
    echo 'Starting Process : ';
    echo $tgljam;
    //	read text file 1 baris untuk insert ke soadate
    $hitung = 0;
    $lanjut = TRUE;
    $fh = fopen('/var/www/html/uploads/pdi01webend.txt','r');
    $line = fgets($fh);
    $transdate 	= substr($line,0,4).'-'.substr($line,5,2).'-'.substr($line,8,2);
    echo '  Transdate : ' . $transdate ;
    echo '<br />';
    
    try{
      $rs = $db->Execute("insert into soaenddate(tanggal) select '{$transdate}'");      
      $rs->Close();
    }
    catch (exception $e)
    {   
        $lanjut = FALSE; 
        echo ' ada error / data sudah ada...';
        echo '<br />';
    }
   
    fclose($fh);
    
	$fh = fopen('/var/www/html/uploads/pdi01webend.txt','r');
	while (($line = fgets($fh)) AND ($lanjut == TRUE)) {
      set_time_limit(0);
      $hitung++;
      $transdate = substr($line,0,4).'-'.substr($line,5,2).'-'.substr($line,8,2);
      $hd = substr($line,10,2);
      $tm = substr($line,12,2);
      $blnthn = substr($line,14,7);
      $suppcode = substr($line,22,7);
      $ok = substr($line,30,1);
      $tglbln = substr($line,31,2);
      $tgltgl = substr($line,34,2);
      $tglthn = substr($line,37,2);
      $tgl = '20' . $tglthn . '-' . $tglbln . '-' . $tgltgl;
      $po = substr($line,39,7);
      $posq = substr($line,46,2);
      $invoice = substr($line,48,15);
      $partno = substr($line,63,15);
      $pname = substr($line,78,20);
      $partname = str_replace("'","",$pname);
      $qty = substr($line,98,8);
      $price = substr($line,106,10);
      $amount = substr($line,116,15);
      $dncnd = substr($line,131,15);
      $lastpay = substr($line,146,15);
      $purchase = substr($line,161,15);
      $dncns = substr($line,176,15);
      $netpur = substr($line,191,15);
      $vat = substr($line,206,15);
      $salesvat = substr($line,221,15);
      $payment = substr($line,236,15);
      $ini = substr($line,252,13);
      $col027 = substr(265,25);
      $col028 = substr(290,13);
      $video = substr(303,1);
      $term15 = substr(304,15);
      $term30 = substr(319,15);
      $term45 = substr(334,15);
      $term60 = substr(349,15);
      $term75 = substr(364,15);
      $term90 = substr(379,15);
      $termtotal = substr(394,15);
    
      $sqlsoa = "insert into soaend(transdate,hd,tm,blnthn,suppcode,ok,tgl,po,posq,
        invoice,partno,partname,qty,price,amount,dncnd,lastpay,
        purchase,dncns,netpur,vat,salesvat,payment,this,col027,col028,
        video,term15,term30,term45,term60,term75,term90,termtotal) 
        select '{$transdate}','{$hd}','{$tm}','{$blnthn}','{$suppcode}',
        '{$ok}','{$tgl}','{$po}','{$posq}','{$invoice}','{$partno}',
        '{$partname}','{$qty}','{$price}','{$amount}','{$dncnd}','{$lastpay}',
        '{$purchase}','{$dncns}','{$netpur}','{$vat}','{$salesvat}','{$payment}',
        '{$ini}','{$col027}','{$col028}','{$video}',
        '{$term15}','{$term30}','{$term45}','{$term60}',
        '{$term75}','{$term90}','{$termtotal}';";

      $rs = $db->Execute($sqlsoa); 
      $rs->Close(); 
    }

	fclose($fh);
    echo 'Finish jumlah record : ' . $hitung;
    echo ' ';
    echo 'End Process : ' . $tgljam;
?>
