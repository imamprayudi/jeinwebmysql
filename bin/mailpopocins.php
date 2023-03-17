<?php
	/*
	****	create by Imam Prayudi
	****	on Mar 2020
	****	remark: 
	*/  
	include('/var/www/html/ADODB/con_ediweb.php');
    
    // Get AltNo from jeinpoc
	$rs = $db->Execute("UPDATE mailpoctoday as m INNER JOIN jeinpoctoday AS j
    ON m.pono = j.pono set m.altno = j.altno where j.altno is not null;");
    $rs->Close();	
    echo 'Update mailpoctoday success<br />';
    
    // Insert data to mailpo from mailpotoday
    $rs = $db->Execute("insert into mailpo(idno, rdate, rtime, supplier, suppliername, actioncode, pono, 
      partno, partname, newqty, newdate, price, model, potype) 
      select idno, rdate, rtime, supplier, suppliername, actioncode, pono, 
      partno, partname, newqty, newdate, price, model, potype from mailpotoday;");
    $rs->Close();
    echo 'Insert into mailpo success<br />';

    // Insert into mailpoc from mailpoctoday
    $rs = $db->Execute("insert into mailpoc(idno,rdate,rtime,supplier,suppliername,
      actioncode,pono,partno,partname,newqty,newdate,oldqty,olddate,price,model,potype,altno)
      select idno,rdate,rtime,supplier,suppliername,actioncode,pono,partno,partname,newqty,
      newdate,oldqty,olddate,price,model,potype,altno from mailpoctoday;");
    $rs->Close();
    echo 'Insert into mailpoc success<br />';

    // insert data to mailpost from mailpotoday
    $rs = $db->Execute("insert into mailpost(transdate,supplier) 
      select distinct rdate,supplier from mailpotoday;");
    $rs->Close();
    echo 'Insert into mailpost success<br />';

    // insert data to mailpocst from mailpoctoday
    $rs = $db->Execute("insert into mailpocst(transdate,supplier) 
      select distinct rdate,supplier from mailpoctoday;");
    $rs->Close();
    echo 'Insert into mailpocst success<br />';


	$db->Close();
?>
