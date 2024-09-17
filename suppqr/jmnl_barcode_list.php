<?php
	/*
	****	modify by Mohamad Yunus
	****	on 23 Jan 2017
	****	revise: pengecekan po number harus 7 angka di button view
	*/

	session_start();
	$session_userid = $_SESSION['usr'];
	
	if(isset($_GET['suppcode']))
	{
		$supp = $_GET['suppcode'];
	}
	
	if ($_SERVER['REQUEST_METHOD'] != 'POST')
	{
		// bukan dari posting
	}
	else
	{
		$supp = $_POST['suppcode'];
	
	}
	
	include('koneksimysql.php');
	$rs = $db->Execute("select kategori from supplier where suppcode = '". $supp ."'");
	$kategori = $rs->fields[0];
	$rs->Close();
	$db->Close();
?>
<!DOCTYPE HTML>
<html>
	<head>
		<title> JEIN - Print Barcode Label </title>
		<link href="../assets/css/styles.css" rel="stylesheet" type="text/css">
    <script src="../assets/js/jquery.js"></script>
		<link rel="shortcut icon" href= "../assets/gambar/receiving.ico"/>
		<link rel="stylesheet" type="text/css" href="../assets/css/styles.css">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		
		<script type="text/javascript" src="../../extjs-4.2.2/ext-all.js"></script>
		<link rel="stylesheet" type="text/css" href="../../extjs-4.2.2/resources/css/ext-all-gray.css" />
		<script type="text/javascript">
			Ext.Loader.setPath('Ext.ux', '../../extjs-4.2.2/examples/ux');

			Ext.require([
			  'Ext.ux.statusbar.StatusBar'
			]);
		
			Ext.onReady(function(){
				Ext.QuickTips.init();
				
				//	All about function
				//	***
					var required = '<span style="color:red;font-weight:bold" data-qtip="Required">*</span>';
				//	----***----  //
				
				
				//	All about json data
				//	***
					Ext.define('cbx_supplier', {
						extend	: 'Ext.data.Model',
						fields	: [ 'suppcode', 'suppname' ]
					});
					var ds_cbx_supplier = Ext.create('Ext.data.Store', {
						model		: 'cbx_supplier',
						autoLoad	: true,
						fields		: [ 'suppcode', 'suppname' ],
						proxy		: {
							type	: 'ajax',
							url		: 'json/json_cbx_supplier.php',
							reader	: {
								type			: 'json',
								root			: 'rows',
								totalProperty	: 'totalCount'
							}
						}
					});
					ds_cbx_supplier.proxy.setExtraParam('supp', <?=$supp?> );
					ds_cbx_supplier.loadPage(1);
					
					
					Ext.define('cbx_partno', {
						extend	: 'Ext.data.Model',
						fields	: [ 'partno' ]
					});
					var ds_cbx_partno = Ext.create('Ext.data.Store', {
						model		: 'cbx_partno',
						autoLoad	: true,
						fields		: [ 'partno' ],
						proxy		: {
							type	: 'ajax',
							url		: 'json/json_cbx_partno.php',
							reader	: {
								type			: 'json',
								root			: 'rows',
								totalProperty	: 'totalCount'
							}
						}
					});
					
					
					Ext.define('cbx_pono', {
						extend	: 'Ext.data.Model',
						fields	: [ 'pono' ]
					});
					var ds_cbx_pono = Ext.create('Ext.data.Store', {
						model		: 'cbx_pono',
						autoLoad	: true,
						fields		: [ 'pono' ],
						proxy		: {
							type	: 'ajax',
							url		: 'json/json_cbx_pono.php',
							reader	: {
								type			: 'json',
								root			: 'rows',
								totalProperty	: 'totalCount'
							}
						}
					});
					
					
					Ext.define('qty', {
						extend	: 'Ext.data.Model',
						fields	: [ 'qty' ]
					});
					var ds_qty = Ext.create('Ext.data.Store', {
						model		: 'qty',
						autoLoad	: true,
						fields		: [ 'qty' ],
						proxy		: {
							type	: 'ajax',
							url		: 'json/json_qty.php',
							reader	: {
								type			: 'json',
								root			: 'rows',
								totalProperty	: 'totalCount'
							}
						},
						listeners: {
							load: function(store, records) {
								var record = store.getAt(0);
								Ext.getCmp("fi-form").loadRecord(record);
							}
						}
					});
				//	----***----  //
				
				
				Ext.create('Ext.form.Panel', {
					renderTo	: 'fi-form',
					id			: 'fi-form',
					width		: 500,
					frame		: true,
					title		: 'Select Supplier',
					bodyPadding	: '10 10 0',

					defaults	: {
						anchor		: '100%',
						msgTarget	: 'side',
						labelWidth	: 150,
						labelStyle	: 'font-weight:bold'
					},

					items: [
						{
							xtype				: 'combo',
							id					: 'suppname',
							name				: 'suppname',
							fieldLabel			: 'Supplier Name',
							queryMode			: 'local',
							displayField		: 'suppname',
							valueField			: 'suppcode',
							editable			: false,
							allowBlank			: false,
							afterLabelTextTpl	: required,
							store				: ds_cbx_supplier,
							listConfig			: {
								getInnerTpl	: function() {
									return '<div> {suppname} - {suppcode} </div>';
								}
							},
							listeners 			: {
								afterrender	: function(field) {
									field.focus(false, 1000);
								},
								change: function(f,new_val) {
									Ext.getCmp('suppcode').setValue( Ext.getCmp('suppname').getValue() );								
									Ext.getCmp('name').setValue( Ext.getCmp('suppname').getRawValue() );								
									
									ds_cbx_partno.proxy.setExtraParam('suppcode', Ext.getCmp('suppname').getValue() );
									ds_cbx_partno.loadPage(1);
									
									Ext.getCmp('part').setValue('');
									Ext.getCmp('po').setValue('');
									Ext.getCmp('qty').setValue('');
									Ext.getCmp('invno').setValue('');
								}
							}
						}, {
							xtype				: 'textfield',
							id					: 'suppcode',
							name				: 'suppcode',
							fieldLabel			: 'Supplier Code',
							readOnly			: true
						}, {
							xtype				: 'hiddenfield',
							id					: 'name',
							name				: 'name',
							fieldLabel			: 'Supplier Name',
							readOnly			: true
						}, {
							xtype				: 'combo',
							id					: 'part',
							name				: 'part',
							fieldLabel			: 'Part No',
							queryMode			: 'proxy',
							displayField		: 'partno',
							valueField			: 'partno',
							editable			: true,
							allowBlank			: false,
							afterLabelTextTpl	: required,
							store				: ds_cbx_partno,
							listeners 			: {
								change: function(f,new_val) {
									Ext.getCmp('po').setValue('');
									Ext.getCmp('qty').setValue('');
								
									ds_cbx_partno.proxy.setExtraParam('partno', Ext.getCmp('part').getValue() );
									ds_cbx_partno.loadPage(1);
									
									ds_cbx_pono.proxy.setExtraParam('suppcode', Ext.getCmp('suppname').getValue() );
									ds_cbx_pono.proxy.setExtraParam('partno', Ext.getCmp('part').getValue() );
									ds_cbx_pono.loadPage(1);
								}
							}
						},
					<?php if($kategori == 2){ ?>
						{
							xtype				: 'textfield',
							id					: 'ulcode',
							name				: 'ulcode',
							fieldLabel			: 'UL Code'
						}, 
					<?php } ?>
					<?php if($kategori == 4){ ?>
						{
							xtype				: 'textfield',
							id					: 'invno',
							name				: 'invno',
							fieldLabel			: 'Invoice No',
							maxlength			: 15,
							listeners			: {
								change	: function(f,new_val) {
									f.setValue(new_val.toUpperCase());
								}
							}
						},
					<?php }else{ ?>
						{
							xtype				: 'textfield',
							id					: 'mtrl',
							name				: 'mtrl',
							fieldLabel			: 'Material'
						},
					<?php } ?>
						{
							xtype				: 'combo',
							id					: 'po',
							name				: 'po',
							fieldLabel			: 'PO No',
							queryMode			: 'proxy',
							displayField		: 'pono',
							valueField			: 'pono',
							editable			: true,
							allowBlank			: false,
							afterLabelTextTpl	: required,
							store				: ds_cbx_pono,
							listeners 			: {
								change: function(f,new_val) {
									ds_cbx_pono.proxy.setExtraParam('pono', Ext.getCmp('po').getValue() );
									ds_cbx_pono.loadPage(1);
									
									ds_qty.proxy.setExtraParam('suppcode', Ext.getCmp('suppname').getValue() );
									ds_qty.proxy.setExtraParam('partno', Ext.getCmp('part').getValue() );
									ds_qty.proxy.setExtraParam('pono', Ext.getCmp('po').getValue() );
									ds_qty.loadPage(1);
								}
							}
						}, {
							xtype				: 'numberfield',
							id					: 'qty',
							name				: 'qty',
							fieldLabel			: 'QTY',
							allowBlank			: false,
							afterLabelTextTpl	: required
						}, {
							xtype				: 'datefield',
							id					: 'deldate',
							name				: 'deldate',
							fieldLabel			: 'Delivery Date',
							format				: 'd-M-Y',
							editable			: true,
							value				: Ext.Date.format(new Date(), 'd-M-Y')
						}, {
							xtype				: 'datefield',
							id					: 'proddate',
							name				: 'proddate',
							fieldLabel			: 'Prod. Date',
							format				: 'd-M-Y',
							editable			: true,
							value				: Ext.Date.format(new Date(), 'd-M-Y')
						}, 
					<?php if($kategori == 4){ ?>
						{
							xtype				: 'textfield',
							id					: 'et',
							name				: 'et',
							fieldLabel			: 'Elec Test'
						},
					<?php }else{ ?>
						{
							xtype		: 'checkboxgroup',
							fieldLabel	: 'Shift',
							items		: [
								{
									xtype		: 'radiofield',
									name		: 'shift',
									inputValue 	: 'B',
									boxLabel	: 'B'
								}, {
									xtype		: 'radiofield',
									name		: 'shift',
									inputValue 	: 'A',
									boxLabel	: 'A'
								}, {
									xtype		: 'radiofield',
									name		: 'shift',
									inputValue 	: 'C',
									boxLabel	: 'C'
								}, {
									xtype		: 'radiofield',
									name		: 'shift',
									inputValue 	: 'N',
									boxLabel	: 'N'
								}
							]
						},
					<?php } ?>
					<?php if($kategori == 4){ ?>
					<?php }else{ ?>
						{
							xtype				: 'textfield',
							id					: 'qcc',
							name				: 'qcc',
							fieldLabel			: 'QC Check'
						}
					<?php } ?>
					],

					buttons: [
						{
							text		: 'View',
							formBind	: true,
							handler		: function(){

							
								var part 		= Ext.getCmp('part').getValue();
								var suppname 	= Ext.getCmp('name').getValue();
								var suppcode 	= Ext.getCmp('suppcode').getValue();
								var po 			= Ext.getCmp('po').getValue();
								var qty 		= Ext.getCmp('qty').getValue();
								var deldate 	= Ext.Date.format(Ext.getCmp('deldate').getValue(), 'd-M-Y');
								var proddate 	= Ext.Date.format(Ext.getCmp('proddate').getValue(), 'd-M-Y');
								
								if(po.length == 7){
									var global = 'jmnl_barcode_vw.php?part='+part+'&suppname='+suppname+'&suppcode='+suppcode+'&po='+po+'&qty='+qty+'&deldate='+deldate+'&proddate='+proddate;
																
									<?php if($kategori == 4){ ?>
										var invno 	= Ext.getCmp('invno').getValue();
										var et 		= Ext.getCmp('et').getValue();
										window.open(global+'&invno='+invno+'&et='+et+'');
									<?php }else{
											if($kategori == 2){
										?>
												var ulcode 	= Ext.getCmp('ulcode').getValue();
												var mtrl 	= Ext.getCmp('mtrl').getValue();
												var shift 	= Ext.ComponentQuery.query('[name=shift]')[0].getGroupValue();
												var qcc 	= Ext.getCmp('qcc').getValue();
												window.open(global+'&ulcode='+ulcode+'&mtrl='+mtrl+'&shift='+shift+'&qcc='+qcc+'');
									<?php	}else{ ?>
												var mtrl 	= Ext.getCmp('mtrl').getValue();
												var shift 	= Ext.ComponentQuery.query('[name=shift]')[0].getGroupValue();
												var qcc 	= Ext.getCmp('qcc').getValue();
												window.open(global+'&mtrl='+mtrl+'&shift='+shift+'&qcc='+qcc+'');
									<?php	}
										}
									?>
								}
								else{
									Ext.Msg.show({
										title		: 'Failure - Print Label Barcode',
										icon		: Ext.Msg.ERROR,
										msg			: 'PO Number must be 7 number or character !!!',
										buttons		: Ext.Msg.OK
									});
								}
								
								
							}
						}
					]
				});
			});
		</script>
	</head>
	
	<body>

	
			<div id="kepala">

			
<?php
			include("jmenusuppcss.php");
			echo '<div id="section">';
			echo '<br />';
			echo '<img src="../assets/gambar/jvc.gif" alt="JVC KENWOOD CORPORATION" style="float:left;width:220px;height:35px;">';
			echo 'PT.JVC ELECTRONICS INDONESIA ';
			echo '<br />';
			echo 'PRINT BARCODE LABEL';
			echo '<br /><br />';
			echo '</div>';
?>
			
			<div id="section">
				<div id="fi-form"></div>
			</div>
		</div>		
	</body>
</html>