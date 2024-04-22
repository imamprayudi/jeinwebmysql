<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once "bootstrap.php";
require_once "Helper.php";

use Illuminate\Support\Carbon;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Database\Schema\MySqlBuilder;

// use Illuminate\Support\Facades\Request;


class DashboardController
{
    protected $request;
    protected $userLogin;

    public function __construct()
    {
        $this->request = \Illuminate\Http\Request::capture();
        $this->userLogin = $_SESSION['usr'];
    }



    public function getMonthlyPoc()
    {
        $request = $this->request;

        $supplierGroup = $this->getSupplierGroup();
        $supplierGroup = $supplierGroup['suppcode'];

        // $monthly = '2023-05';
        $monthly = date('Y-m');  
        $getFullDate = date('F Y');

        $get_po = MailPo::whereRaw(Helper::translateQueryMonthlyToMysql("rdate","7","MySQL")." = '$monthly'")
                    ->whereIn('mailpo.supplier',$supplierGroup);
        
        $get_poc = MailPoChange::whereRaw(Helper::translateQueryMonthlyToMysql("rdate","7", "MySQL")." = '$monthly'")
                    ->whereIn('mailpoc.supplier', $supplierGroup);
        
        $po_mailpost = $get_po->join('mailpost',function ($join) {
                                $join->on('mailpost.transdate','mailpo.rdate');
                                $join->on('mailpost.supplier','mailpo.supplier');
                            });
        
        $poc_mailpost = $get_poc->join('mailpocst', function ($join) {
                            $join->on('mailpocst.transdate', 'mailpoc.rdate');
                            $join->on('mailpocst.supplier', 'mailpoc.supplier');
                        });
        // return Helper::getEloquentSqlWithBindings($po_mailpost);
        $total_po = $get_po->count();
        $total_poc = $get_poc->count();
        
        $total_po_unread = $po_mailpost
                            ->where("mailpost.status","UNREAD");
        $total_po_unread = $total_po_unread->count();
        
        $total_poc_unread = $poc_mailpost
                            ->where("mailpocst.status","UNREAD")
                            ->count();
        
        // $total_po_unconfirm  =  $get_po->join('', function ($join) {
        //                             $join->on('mailpost.transdate', 'mailpo.rdate');
        //                             $join->on('mailpost.supplier', 'mailpo.supplier');
        //                         })
        //             ->whereIn('mailpo.supplier',$supplierGroup)
        //             ->where("mailpost.status","READ")
        //             ->where("mailpost.confirmation","Not Yet Confirm")
        //             ->count();

        $total_po_unconfirm = MailPo::whereRaw(Helper::translateQueryMonthlyToMysql("rdate","7", "MySQL")." = '$monthly'")
                                ->leftJoin('mailpost', function ($join) {
                                    $join->on('mailpost.transdate', 'mailpo.rdate');
                                    $join->on('mailpost.supplier', 'mailpo.supplier');
                                })
                                ->leftJoin('mailpo_confirmation', function ($join) {
                                    $join->on('mailpo_confirmation.transmission_date', 'mailpo.rdate');
                                    $join->on('mailpo_confirmation.supplier_code', 'mailpo.supplier');
                                    $join->on('mailpo_confirmation.po_number', 'mailpo.pono');
                                })
                                ->where("mailpost.status", "READ")
                                ->whereIn('mailpo.supplier', $supplierGroup)
                                ->whereNull("mailpo_confirmation.transmission_no")
                                ->whereNull("mailpo_confirmation.transmission_date")
                                ->whereNull("mailpo_confirmation.supplier_code")
                                ->whereNull("mailpo_confirmation.po_number");                             
        $total_po_unconfirm = $total_po_unconfirm->count();

        $total_poc_unconfirm = MailPoChange::whereRaw(Helper::translateQueryMonthlyToMysql("rdate","7", "MySQL")." = '$monthly'")
                            ->leftJoin('mailpocst', function ($join) {
                                $join->on('mailpocst.transdate', 'mailpoc.rdate');
                                $join->on('mailpocst.supplier', 'mailpoc.supplier');
                            })
                                ->leftJoin('mailpoc_confirmation', function ($join) {
                                    $join->on('mailpoc_confirmation.transmission_date', 'mailpoc.rdate');
                                    $join->on('mailpoc_confirmation.supplier_code', 'mailpoc.supplier');
                                    $join->on('mailpoc_confirmation.po_number', 'mailpoc.pono');
                                })
                                ->where("mailpocst.status", "READ")
                                ->whereIn('mailpoc.supplier', $supplierGroup)
                                ->whereNull("mailpoc_confirmation.transmission_no")
                                ->whereNull("mailpoc_confirmation.transmission_date")
                                ->whereNull("mailpoc_confirmation.supplier_code")
                                ->whereNull("mailpoc_confirmation.po_number");
        $total_poc_unconfirm = $total_poc_unconfirm->count();

        $total_po_confirm = MailPo::whereRaw(Helper::translateQueryMonthlyToMysql("rdate","7", "MySQL")." = '$monthly'")
                            ->join('mailpost', function ($join) {
                                $join->on('mailpost.transdate', 'mailpo.rdate');
                                $join->on('mailpost.supplier', 'mailpo.supplier');
                            })
                            ->join('mailpo_confirmation', function ($join) {
                                $join->on('mailpo_confirmation.transmission_date', 'mailpo.rdate');
                                $join->on('mailpo_confirmation.supplier_code', 'mailpo.supplier');
                                $join->on('mailpo_confirmation.po_number', 'mailpo.pono');
                            })
                            ->where(function ($query) {
                                $query->where('mailpo_confirmation.supplier_confirmed_status', 'CONFIRM');
                                // ->orWhere('mailpo_confirmation.purch_confirmed_status', 'CONFIRM FOR ACCEPT')
                                // ->orWhere('mailpo_confirmation.mc_confirmed_status', 'CONFIRM PUT BACK');
                            })
                            ->where("mailpost.status", "READ")
                            ->whereIn('mailpo.supplier', $supplierGroup);
        // $query = Helper::getEloquentSqlWithBindings($total_po_confirm);
        $total_po_confirm = $total_po_confirm->count();

        $total_poc_confirm = MailPoChange::whereRaw(Helper::translateQueryMonthlyToMysql("rdate","7", "MySQL")." = '$monthly'")
        ->join('mailpocst', function ($join) {
            $join->on('mailpocst.transdate', 'mailpoc.rdate');
            $join->on('mailpocst.supplier', 'mailpoc.supplier');
        })
            ->join('mailpoc_confirmation', function ($join) {
                $join->on('mailpoc_confirmation.transmission_date', 'mailpoc.rdate');
                $join->on('mailpoc_confirmation.supplier_code', 'mailpoc.supplier');
                $join->on('mailpoc_confirmation.po_number', 'mailpoc.pono');
            })
            ->where(function ($query) {
                $query->where('mailpoc_confirmation.supplier_confirmed_status', 'CONFIRM');
                // ->orWhere('mailpo_confirmation.purch_confirmed_status', 'CONFIRM FOR ACCEPT')
                // ->orWhere('mailpo_confirmation.mc_confirmed_status', 'CONFIRM PUT BACK');
            })
            ->where("mailpocst.status", "READ")
            ->whereIn('mailpoc.supplier', $supplierGroup);
        $total_poc_confirm = $total_poc_confirm->count();
        
        $total_po_reject = MailPo::whereRaw(Helper::translateQueryMonthlyToMysql("rdate","7", "MySQL")." = '$monthly'")
        ->join('mailpost', function ($join) {
            $join->on('mailpost.transdate', 'mailpo.rdate');
            $join->on('mailpost.supplier', 'mailpo.supplier');
        })
            ->join('mailpo_confirmation', function ($join) {
                $join->on('mailpo_confirmation.transmission_date', 'mailpo.rdate');
                $join->on('mailpo_confirmation.supplier_code', 'mailpo.supplier');
                $join->on('mailpo_confirmation.po_number', 'mailpo.pono');
            })
            ->where("mailpost.status", "READ")
            ->where(function ($query) {
                $query->where('mailpo_confirmation.supplier_confirmed_status', 'REJECT');
                    // ->orWhere('mailpo_confirmation.purch_confirmed_status', 'CONFIRM FOR REJECTION')
                    // ->orWhere('mailpo_confirmation.mc_confirmed_status', 'REJECT PUT BACK');
            })
            ->whereIn('mailpo.supplier', $supplierGroup);
        $total_po_reject = $total_po_reject->count();

        $total_poc_reject = MailPoChange::whereRaw(Helper::translateQueryMonthlyToMysql("rdate","7", "MySQL")." = '$monthly'")
        ->join('mailpocst', function ($join) {
            $join->on('mailpocst.transdate', 'mailpoc.rdate');
            $join->on('mailpocst.supplier', 'mailpoc.supplier');
        })
            ->join('mailpoc_confirmation', function ($join) {
                $join->on('mailpoc_confirmation.transmission_date', 'mailpoc.rdate');
                $join->on('mailpoc_confirmation.supplier_code', 'mailpoc.supplier');
                $join->on('mailpoc_confirmation.po_number', 'mailpoc.pono');
            })
            ->where("mailpocst.status", "READ")
            ->where(function ($query) {
                $query->where('mailpoc_confirmation.supplier_confirmed_status', 'REJECT');
                    // ->orWhere('mailpoc_confirmation.purch_confirmed_status', 'CONFIRM FOR REJECTION')
                    // ->orWhere('mailpoc_confirmation.mc_confirmed_status', 'REJECT PUT BACK');
            })
            ->whereIn('mailpoc.supplier', $supplierGroup);
        $total_poc_reject = $total_poc_reject->count();


        return [
            'success' => true,
            'monthly'=>$getFullDate,
            "total_po" => $total_po,
            "total_poc" => $total_poc,
            "total_po_unread" => $total_po_unread,
            "total_poc_unread" => $total_poc_unread,
            "total_po_unconfirm" => $total_po_unconfirm,
            "total_poc_unconfirm" => $total_poc_unconfirm,
            "total_po_confirm" => $total_po_confirm,
            // "query_po_confirm" => $query,
            "total_poc_confirm" => $total_poc_confirm,
            "total_po_reject" => $total_po_reject,
            "total_poc_reject" => $total_poc_reject,
        ];
    }

    public function getSupplierGroup(){
        $suppliers = UserSupp::select('Userid','UserSupp.SuppCode','Supplier.SuppName')
                    ->join('Supplier', 'UserSupp.SuppCode', '=', 'Supplier.SuppCode')
                    ->where('UserId',trim($this->userLogin))
                    ->where('Supplier.status','active')
                    ->orderBy('Supplier.SuppName','asc')
                    ->get();
        //   select usersupp.UserId,usersupp.SuppCode,supplier.SuppName FROM [EDI].[dbo].UserSupp 
        //   inner join [EDI].[dbo].Supplier on usersupp.SuppCode = Supplier.SuppCode 
        //   where UserId = 'e7' order by suppname
        $suppcode = [];
        $data = [];
        foreach ($suppliers as $supp) {
            $temp = [];
            $temp['SuppCode'] = trim($supp['SuppCode']);
            $suppcode[] = trim($supp['SuppCode']);
            $temp['SuppName'] = trim($supp['SuppName']);
            $temp['Userid'] = trim($supp['Userid']);
            $data[] = $temp;
        }
        
        return [
            "success" => true,
            "suppcode" => $suppcode,
            "data" => $data
        ];
    }


    public function getPartNumber(){
        $request = $this->request;
  
        $viewpart = MailPo::select('partno')
                ->where('supplier',trim($request->supplier))
                ->whereBetween('rdate',[$request->from_date,
                                        $request->end_date])
                ->get();


                $partnumber = [];
                foreach ($viewpart as $part) {
                    $partnumber[] = trim($part->partno);
                }
                return $partnumber;

    }


    public function getPo(){
        $request = $this->request;
  
        $viewpo = MailPo::select('pono')
                ->where('supplier',trim($request->supplier))
                ->whereBetween('rdate',[$request->from_date,
                                        $request->end_date])
                ->get();


                $pono = [];
                foreach ($viewpo as $poview) {
                    $pono[] = trim($poview->pono);
                }
                return $pono;

    }

    public function getDataFilter(){
        $request = $this->request;
        $supplierGroup = $this->getSupplierGroup();
        $supplierGroup = $supplierGroup['suppcode'];
      
        $poc_repeat = MailPoChange::select('pono')
                            ->selectRaw('COUNT(pono) as repeated')
                            ->whereIn('supplier', $supplierGroup)
                            ->whereBetween('rdate',[$request->from_date,
                                        $request->end_date])
                            ->groupBy('pono')
                            ->havingRaw('count(pono) > 1')
                            ->orderBy('repeated', 'desc');

        $poc_change = MailPoChange::select('idno','rdate','actioncode','pono','partno','partname','newqty','newdate','oldqty','olddate','potype')
                            ->whereIn('supplier', $supplierGroup)
                            ->whereBetween('rdate',[$request->from_date, $request->end_date]);

        if(isset($request->select_po))
        {

            $select_po = [];
            foreach ($request->select_po as $po) {
                $select_po[] = trim($po);  
            }

            if($request->filter_by == 'part')
            {
                $poc_repeat = $poc_repeat->whereIn('partno',$select_po)->get();
                $poc_change = $poc_change->whereIn('partno',$select_po)->get();
            }
            if($request->filter_by == 'pono')
            {
                $poc_repeat = $poc_repeat->whereIn('pono',$select_po)->get();
                $poc_change = $poc_change->whereIn('pono',$select_po)->get();
            }              
           
        }
        
        if(!isset($request->select_po))         
        {
            $fromdate =  substr($request->from_date,0,7);
            $enddate  = substr($request->end_date, 0,7 ); 
    
            if($fromdate != $enddate){
                   return "start date dan end date harus sama !";
            }
            else{
                $poc_repeat = $poc_repeat->get();
                $poc_change = $poc_change->get();

            }
        }

        $data = [
            "repeated" => $poc_repeat,
            "pochange" => $poc_change,
         ];

         return $data;
    }

    public function getDataFilter_rev(){
        $request = $this->request;
        $supplierGroup = $this->getSupplierGroup();

      
        $poc_repeat = MailPoChange::select('pono')
                            ->selectRaw('COUNT(pono) as repeated')
                            ->whereIn('supplier', $supplierGroup)
                            ->whereBetween('rdate',[$request->from_date,
                                        $request->end_date])
                            ->groupBy('pono')
                            ->havingRaw('count(pono) > 1')
                            ->orderBy('repeated', 'desc');

        $poc_change = MailPoChange::select('idno','rdate','actioncode','pono','partno','partname','newqty','newdate','oldqty','olddate','potype')
                            ->whereIn('supplier', $supplierGroup)
                            ->whereBetween('rdate',[$request->from_date,
                                        $request->end_date]);

                                      

         $po = MailPo::select('idno','rdate','actioncode','pono','partno','partname','newqty','newdate','oldqty','olddate','potype')
                                        ->whereIn('supplier', $supplierGroup)
                                        ->whereBetween('rdate',[$request->from_date,
                                                    $request->end_date]);
       
                                                  
        if(isset($request->select_po))
        {

            $select_po = [];
            foreach ($request->select_po as $po) {
                $select_po[] = trim($po);  
            }

            if($request->filter_by == 'part')
            {
                                    $poc_repeat = $poc_repeat->whereIn('partno',$select_po)->get();
                                    $poc_change = $poc_change->whereIn('partno',$select_po)->get();
                                    $po = $po->whereIn('partno',$select_po)->get();
            }
            if($request->filter_by == 'pono')
            {
                                    $poc_repeat = $poc_repeat->whereIn('pono',$select_po)->get();
                                    $poc_change = $poc_change->whereIn('pono',$select_po);
                                  

                                    return  Helper::getEloquentSqlWithBindings($poc_change);  
                                 
            }             
           
        }
        
        if(!isset($request->select_po))         
        {
            $fromdate =  substr($request->from_date,0,7);
            $enddate  = substr($request->end_date, 0,7 ); 
    
            if($fromdate != $enddate){
                   return "failed";


            }
            else{
                $poc_repeat = $poc_repeat->get();
                $poc_change = $poc_change->get();
                $po = $po->get();

            }
        }

        $data = [
            "repeated" => $poc_repeat,
            "pochange" => $poc_change,
            "po"       =>$po
         ];

         return $data;   
    }

    public function getSummarySupplier(){
        $suppliers = $this->getSupplierGroup();
        $supplier_code = $suppliers['suppcode'];

        try {
            $data_max_id_po = MailPoConfirmation::select(DB::raw('MAX(id) as id'))
                                ->groupBy('po_number', 'supplier_code', 'transmission_no')
                                ->distinct()->pluck('id');
            // return $data_max_id_po_conf;
            $data_po = MailPoConfirmation::select(
                'transmission_no',
                'transmission_date',
                'supplier_code',
                'Supplier.SuppName',
                'po_number',
                'mailpo_confirmation.status',
                'read_at',
                'supplier_confirmed_status',
                'supplier_confirmed_reason',
                'supplier_confirmed_by',
                'supplier_confirmed_at',
                'purch_confirmed_status',
                'purch_confirmed_reason',
                'purch_confirmed_by',
                'purch_confirmed_at',
                'mc_confirmed_status',
                'mc_confirmed_reason',
                'mc_confirmed_by',
                'mc_confirmed_at'
                // DB::raw('MAX(supplier_confirmed_at) as supplier_confirmed_at')
            )
                ->leftJoin('Supplier', function ($join) {
                    $join->on('Supplier.SuppCode', '=', 'mailpo_confirmation.supplier_code');
                })
                ->whereIn('mailpo_confirmation.id', $data_max_id_po)
                ->whereIn('supplier_code', $supplier_code)
                ->where(function ($query) {
                    $query->where('supplier_confirmed_status', 'REJECT')
                    ->orWhere('purch_confirmed_status', 'CONFIRM FOR REJECTION')
                    ->orWhere('mc_confirmed_status', 'REJECT PUT BACK');
                })->get();

            $data_max_id_poc = MailPoChangeConfirmation::select(DB::raw('MAX(id) as id'))
                ->groupBy('po_number', 'supplier_code', 'transmission_no')
                ->distinct()->pluck('id');
            $data_poc = MailPoChangeConfirmation::select(
                'transmission_no',
                'transmission_date',
                'supplier_code',
                'Supplier.SuppName',
                'po_number',
                'mailpoc_confirmation.status',
                'read_at',
                'supplier_confirmed_status',
                'supplier_confirmed_reason',
                'supplier_confirmed_by',
                'supplier_confirmed_at',
                'purch_confirmed_status',
                'purch_confirmed_reason',
                'purch_confirmed_by',
                'purch_confirmed_at',
                'mc_confirmed_status',
                'mc_confirmed_reason',
                'mc_confirmed_by',
                'mc_confirmed_at'
            )
            ->leftJoin('Supplier', function ($join) {
                $join->on('Supplier.SuppCode', '=', 'mailpoc_confirmation.supplier_code');
            })
            ->whereIn('mailpoc_confirmation.id', $data_max_id_poc)
            ->whereIn('supplier_code', $supplier_code)
            ->where(function ($query) {
                $query->where('supplier_confirmed_status', 'REJECT')
                ->orWhere('purch_confirmed_status', 'CONFIRM FOR REJECTION')
                ->orWhere('mc_confirmed_status', 'REJECT PUT BACK');
            });
            $query_poc = Helper::getEloquentSqlWithBindings($data_poc);
            $data_poc = $data_poc->get();
            
            return [
                "success" => true,
                "message" => "Successfully Getting data Summary",
                "data_po" => $data_po,
                "query_poc" => $query_poc,
                "data_poc" => $data_poc
            ];
        } catch (\Throwable $th) {
            return [
                "success" => false,
                "message" => $th
            ];
        }
    }
}