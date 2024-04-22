<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once "bootstrap.php";
require_once "Helper.php";

// use Illuminate\Support\Carbon;
use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

// use Illuminate\Support\Facades\Request;


class PurchaseOrderController
{
    protected $request;
    protected $userLogin;
    public function __construct()
    {
        $this->request = \Illuminate\Http\Request::capture();
        $this->userLogin = $_SESSION['usr'];
    }

    public function getFilterBy()
    {
        $request = $this->request;
        if ($request->filter_by == '--Select Category--') {
            return "Filter NG, Call JKEI IT";
        }
        try {
            $getData = [];
            if ($request->filter_by != 'rdate') {
                $getData = MailPo::selectRaw("trim($request->filter_by) as $request->filter_by")
                    ->orderBy($request->filter_by, "asc");
            }
            if ($request->filter_by == 'rdate') {
                $getData = MailPo::selectRaw(Helper::translateQueryMonthlyToMysql($request->filter_by,"10", "MySQL") . " as $request->filter_by")
                    ->orderBy($request->filter_by, "desc");
            }
            $getData = $getData->where("supplier", trim($request->supplier))
                ->whereBetween("rdate", [$request->from_date, $request->end_date])
                ->distinct()
                ->get();
                // return Helper::getEloquentSqlWithBindings($getData);

            $data = [];
            foreach ($getData as $val) {
                $col = $request->filter_by;
                $data[] = trim($val->$col);
            }

            return [
                "success" => true,
                "message" => "Successfully Getting data",
                "data" => $data
            ];
        } catch (\Throwable $th) {
            return [
                "success" => false,
                "message" => $th
            ];
        }
    }

    public function getFilterBy_salah() //NOT USED
    {
        $request = $this->request;
        return $request;
        /**
        select transdate,supplier,status,confirmation,confirmdate,rejectreason 
        from mailpost where ( supplier = '" . $supp . "') and 
        ( transdate between '" . $tgl1 ."' and '" . $tgl2 . "') order by transdate
         */
        try {
            //code...
            $data = MailPo::select("idno", "rdate", "pono")
                ->where("supplier", $request->supplier)
                ->whereBetween("rdate", [$request->from_date, $request->end_date])
                ->whereIn($request->filter_by, $request->select_po)
                ->orderBy("rdate", "desc")
                ->get();

            return [
                "success" => true,
                "message" => "Successfully Getting data",
                "data" => $data
            ];
        } catch (\Throwable $th) {
            return [
                "success" => false,
                "message" => $th
            ];
        }

        // $data = [
        //     "repeated" => $poc_repeat,
        //     "pochange" => $poc_change
        //  ];

        //  return $data;
    }

    public function getDataPo()
    {
        $request = $this->request;

        try {
            //code...
            $getData = MailPo::select('id', 'rdate', 'idno', DB::raw("trim(supplier) as supplier"), DB::raw("trim(suppliername) as suppliername"), DB::raw("trim(pono) as pono"), DB::raw("COALESCE(status, 'UNREAD') AS status"), 'read_at', 'confirmed_status', 'confirmed_reason', 'confirmed_by', 'confirmed_at')
                ->leftJoin('mailpo_confirmation', function ($join) {
                    $join->on('mailpo.idno', 'mailpo_confirmation.transmission_no');
                })
                ->where('supplier', $request->supplier)
                ->whereBetween('rdate', [$request->from_date, $request->end_date])
                ->whereIn($request->filter_by, $request->select_po)
                ->get();
            // return Helper::getEloquentSqlWithBindings($getData);

            return [
                "success" => true,
                "message" => "successfully get data Mail PO",
                "data" => $getData
            ];
        } catch (\Throwable $th) {
            //throw $th;
            return [
                "success" => false,
                "message" => $th->getMessage(),
                "data" => null
            ];
        }
    }

    public function getDataPoST()
    {
        $request = $this->request;
        try {
            //code...
            $filter = '';
            $request->filter_by == "rdate" ? $filter = "transdate" : $filter = $request->filter_by;
            if ($filter == "transdate") {


                $getData = MailPoSt::select(
                    DB::raw(Helper::translateQueryMonthlyToMysql("transdate","10", "MySQL") . " as transdate"),
                    DB::raw("trim(mailpost.supplier) as supplier"),
                    'status',
                    DB::raw("isnull(confirmed.total_confirmed,0) as total_confirmed"),
                    DB::raw("isnull(rejected.total_rejected,0) as total_rejected"),
                    'total.total_po',
                    'updated'
                )
                    ->leftJoin(
                        DB::raw("(select count(supplier_confirmed_status) as total_confirmed,transmission_date,supplier_code from mailpo_confirmation where supplier_confirmed_status = 'CONFIRM' group by transmission_date,supplier_code) as confirmed"),
                        function ($join) {
                            $join->on('confirmed.transmission_date', '=', 'mailpost.TransDate');
                            $join->on('confirmed.supplier_code', '=', 'mailpost.Supplier');
                        }
                    )
                    ->leftJoin(
                        DB::raw("(select count(supplier_confirmed_status) as total_rejected,transmission_date,supplier_code from mailpo_confirmation where supplier_confirmed_status = 'REJECTED' group by transmission_date,supplier_code) as rejected"),
                        function ($join) {
                            // confirmed.transmission_date = mailpost.TransDate and confirmed.supplier_code = mailpost.Supplier
                            $join->on('confirmed.transmission_date', '=', 'mailpost.TransDate');
                            $join->on('confirmed.supplier_code', '=', 'mailpost.Supplier');
                        }
                    )
                    ->leftJoin(
                        DB::raw("(select count(pono) as total_po,rdate,Supplier from mailpo group by RDATE,Supplier) as total"),
                        function ($join) {
                            // confirmed.transmission_date = mailpost.TransDate and confirmed.supplier_code = mailpost.Supplier
                            $join->on('total.rdate', '=', 'mailpost.TransDate');
                            $join->on('total.supplier', '=', 'mailpost.Supplier');
                        }
                    )
                    ->where('mailpost.supplier', $request->supplier);

                $filter == 'transdate'
                    ? $getData = $getData->whereIn('mailpost.' . $filter, $request->select_po)
                    : $getData = $getData->whereBetween('mailpost.transdate', [$request->from_date, $request->end_date]);

                if ($filter != 'transdate') {
                    $getTransdate = MailPo::select('rdate')
                        ->where('supplier', $request->supplier)
                        ->whereBetween('rdate', [$request->from_date, $request->end_date])
                        ->where($filter, $request->select_po)
                        ->distinct('rdate')
                        ->pluck('rdate');
                    // return $getTransdate;
                    $getData = $getData->whereIn('mailpost.transdate', $getTransdate);
                }
                $data  = $getData->orderBy('mailpost.transdate', 'desc')->get();
            }
            else{
               return $this->getDataPoDetail();
               
            }
            return [
                "success" => true,
                "message" => "successfully get data Mail POST",
                "data" => $data
            ];
        } catch (\Throwable $th) {
            //throw $th;
            return [
                "success" => false,
                "message" => $th->getMessage(),
                "data" => null
            ];
        }
    }

    public function updateReadStatus()
    {
        $request = $this->request;
        // return $request->sid;
        $supplier = Supplier::select('SuppName')
            ->where('SuppCode', $request->sid)
            ->pluck('SuppName');

        if (!isset($_SESSION['usrsecure'])) {
            return [
                "success" => true,
                "supplier" => trim($supplier[0]),
                "message" => "no session"
            ];
        }
        if ($_SESSION['usrsecure'] != 3) {
            return [
                "success" => true,
                "supplier" => trim($supplier[0]),
                "message" => "not supplier"
            ];
        }

        $check_status = MailPoSt::where('Supplier', $request->sid)
            ->where('Transdate', $request->tglid)
            ->pluck('status');
        // return [
        //     "success" => false,
        //     "supplier" => $check_status
        // ];
        if ($check_status[0] == "unread") {
            try {
                //code...
                $updating = MailPoSt::where('Supplier', $request->sid)
                    ->where('Transdate', $request->tglid)
                    ->update(['Status' => 'read', 'updated' => Carbon::now()]);
                return [
                    "success" => true,
                    "supplier" => trim($supplier[0]),
                    "message" => $updating
                ];
            } catch (\Throwable $th) {
                //throw $th;
                return [
                    "success" => false,
                    "message" => $th->getMessage(),
                    "data" => null
                ];
            }
        }
        return [
            "success" => true,
            "supplier" => trim($supplier[0]),
            "message" => $check_status
        ];
    }

    public function getDataPoDetail()
    {
        $request = $this->request;
        
        try {
            //code...
            $getData = MailPo::select(
                'rdate',
                'idno',
                DB::raw("trim(pono) as pono"),
                'partno',
                'partname',
                'newqty',
                'newdate',
                'oldqty',
                'olddate',
                'price',
                'model',
                'potype',
                'mailpo_conf.supplier_confirmed_status',
                'mailpo_conf.supplier_confirmed_reason',
                'mailpo_conf.supplier_confirmed_by',
                'mailpo_conf.supplier_confirmed_at',
                'mailpo_conf.purch_confirmed_status',
                'mailpo_conf.purch_confirmed_reason',
                'mailpo_conf.purch_confirmed_by',
                'mailpo_conf.purch_confirmed_at',
                'mailpo_conf.mc_confirmed_status',
                'mailpo_conf.mc_confirmed_reason',
                'mailpo_conf.mc_confirmed_by',
                'mailpo_conf.mc_confirmed_at'
            )
            ->leftJoin(
                    DB::raw("(SELECT [id]
                            ,[transmission_no]
                            ,[transmission_date]
                            ,[supplier_code]
                            ,[po_number]
                            ,[status]
                            ,[read_at]
                            ,[supplier_confirmed_status]
                            ,[supplier_confirmed_reason]
                            ,[supplier_confirmed_by]
                            ,[supplier_confirmed_at]
                            ,[purch_confirmed_status]
                            ,[purch_confirmed_reason]
                            ,[purch_confirmed_by]
                            ,[purch_confirmed_at]
                            ,[mc_confirmed_status]
                            ,[mc_confirmed_reason]
                            ,[mc_confirmed_by]
                            ,[mc_confirmed_at]
                            ,[user_secure] 
                            FROM mailpo_confirmation where id in ((SELECT MAX(ID) AS id 
                                         FROM [mailpo_confirmation]
                                         GROUP BY [transmission_no]
                                         ,[transmission_date]
                                         ,[supplier_code]
                                         ,[po_number]))) as mailpo_conf
                            "),
                    'mailpo.idno', '=', 'mailpo_conf.transmission_no'
                );

                if($request->supplier){
                    $getData = $getData->where('mailpo.supplier', $request->supplier)
                    ->whereBetween('rdate', [$request->from_date, $request->end_date])
                    ->whereIn($request->filter_by,$request->select_po);
                }
                else{
                    $getData = $getData->whereIn('mailpo.rdate', [$request->tglid])
                    ->where('supplier', $request->sid);
                }
            if($request->pono){
                $getData = $getData->where('mailpo.pono',$request->pono);
            }
            // return Helper::getEloquentSqlWithBindings($getData);
            // ->whereIn('rdate', [$request->tglid])
            $getData = $getData->orderBy('idno','asc');
            // return Helper::getEloquentSqlWithBindings($getData);
            $getData = $getData->get();
            // return $getData;
            return [
                "success" => true,
                "message" => "successfully get data Mail PO",
                "data" => $getData
            ];
        } catch (\Throwable $th) {
            //throw $th;
            return [
                "success" => false,
                "message" => $th->getMessage(),
                "data" => null
            ];
        }
    }

    public function confirm() // used
    {
        $request = $this->request;
        $waktu = gmdate("Y-m-d H:i:s");
        // verified params
        // return [
        //     "success" => false,
        //     "message" => $request->additional,
        //     "data" => $request->data
        // ];
        $query_url = $request->additional;

        /**
            field mailpo_confirmation
            ,[transmission_no] = $parameters[0]->idno
            ,[transmission_date] = $parameters[0]->rdate
            ,[supplier_code] = $query->sid
            ,[po_number] = $parameters[0]->pono
            ,[supplier_confirmed_status] = $request->status
            ,[supplier_confirmed_reason] = $request->reason
            ,[supplier_confirmed_by] =  $_SESSION['usr'];
            ,[supplier_confirmed_at] = $waktu
            ,[user_secure] = $_SESSION['usrsecure'];
         */
        $allowedParams = [
            'idno' => 'transmission_no',
            'rdate' => 'transmission_date',
            'pono' => 'po_number'
        ];
        $mailPoConfirmation = new MailPoConfirmation;
        $confirmLog = new ConfirmLog;
                
        try {
            $insert_data = [];
            $update_data = [];
            // $action = "INSERT"; // "UPDATE"
            foreach ($request->data as $item) {
                foreach ($item as $key => $value) {
                    if (isset($allowedParams[$key])) {
                        $data[$allowedParams[$key]] = $value;
                    }
                }
                $data["supplier_code"] = $query_url['sid'];
                // if(usrsecure == 3)
                // status = status
                // if(usrsecure == 4)
                // status = "CONFIRM FOR ACCEPT"
                // if(usrsecure == 6)
                // status = "CONFIRM PUT BACK"

                $data["user_secure"] = $_SESSION['usrsecure'];

                $check_status = [];
                $updating = [];
                if($data["user_secure"] == 3){
                    $data["supplier_confirmed_status"] = $request->status;
                    $data["supplier_confirmed_reason"] = $request->reason;
                    $data["supplier_confirmed_by"] = $_SESSION['usr'];
                    $data["supplier_confirmed_at"] = $waktu;
                    $logging = [
                        'transmission_no' => $data['transmission_no'],
                        'transmission_date' => $data['transmission_date'],
                        'supplier_code' => $data['supplier_code'],
                        'po_number' => $data['po_number'],
                        'status' => $data['supplier_confirmed_status'],
                        'source' => "SUPPLIER",
                        'user_secure' => $data['user_secure'],
                        'confirmed_reason' => $data['supplier_confirmed_reason'],
                        'confirmed_by' => $data['supplier_confirmed_by'],
                        'confirmed_at' => $data['supplier_confirmed_at']
                    ];
                    $updating = [
                        'po_number' => $data['po_number'],
                        'supplier_confirmed_at' => $data['supplier_confirmed_at'],
                        'supplier_confirmed_by' => $data['supplier_confirmed_by'],
                        'supplier_confirmed_reason' => $data['supplier_confirmed_reason'],
                        'supplier_confirmed_status' => $data['supplier_confirmed_status'],
                        'supplier_code' => $data['supplier_code'],
                        'transmission_date' => $data['transmission_date'],
                        'transmission_no'=> $data['transmission_no'],
                        'user_secure' => $data['user_secure']
                    ];
                }
                elseif($data["user_secure"] == 4){
                    $data["purch_confirmed_status"] = $request->status;
                    $data["purch_confirmed_reason"] = $request->reason;
                    $data["purch_confirmed_by"] = $_SESSION['usr'];
                    $data["purch_confirmed_at"] = $waktu;
                    $logging = [
                        'transmission_no' => $data['transmission_no'],
                        'transmission_date' => $data['transmission_date'],
                        'supplier_code' => $data['supplier_code'],
                        'po_number' => $data['po_number'],
                        'status' => $data['purch_confirmed_status'],
                        'source' => "PURCH",
                        'user_secure' => $data['user_secure'],
                        'confirmed_reason' => $data['purch_confirmed_reason'],
                        'confirmed_by' => $data['purch_confirmed_by'],
                        'confirmed_at' => $data['purch_confirmed_at']
                    ];
                    $updating = [
                        'po_number' => $data['po_number'],
                        'purch_confirmed_at' => $data['purch_confirmed_at'],
                        'purch_confirmed_by' => $data['purch_confirmed_by'],
                        'purch_confirmed_reason' => $data['purch_confirmed_reason'],
                        'purch_confirmed_status' => $data['purch_confirmed_status'],
                        'supplier_code' => $data['supplier_code'],
                        'transmission_date' => $data['transmission_date'],
                        'transmission_no' => $data['transmission_no'],
                        'user_secure' => $data['user_secure']
                    ];
                } 
                elseif($data["user_secure"] == 6){
                    $data["mc_confirmed_status"] = $request->status;
                    $data["mc_confirmed_reason"] = $request->reason;
                    $data["mc_confirmed_by"] = $_SESSION['usr'];
                    $data["mc_confirmed_at"] = $waktu;
                    $logging = [
                        'transmission_no' => $data['transmission_no'],
                        'transmission_date' => $data['transmission_date'],
                        'supplier_code' => $data['supplier_code'],
                        'po_number' => $data['po_number'],
                        'status' => $data['mc_confirmed_status'],
                        'source' => "MC",
                        'user_secure' => $data['user_secure'],
                        'confirmed_reason' => $data['mc_confirmed_reason'],
                        'confirmed_by' => $data['mc_confirmed_by'],
                        'confirmed_at' => $data['mc_confirmed_at']
                    ];
                    $updating = [
                        'po_number' => $data['po_number'],
                        'mc_confirmed_at' => $data['mc_confirmed_at'],
                        'mc_confirmed_by' => $data['mc_confirmed_by'],
                        'mc_confirmed_reason' => $data['mc_confirmed_reason'],
                        'mc_confirmed_status' => $data['mc_confirmed_status'],
                        'supplier_code' => $data['supplier_code'],
                        'transmission_date' => $data['transmission_date'],
                        'transmission_no' => $data['transmission_no'],
                        'user_secure' => $data['user_secure']
                    ];
                }

                $check_status = MailPoConfirmation::select("id")
                ->where('po_number', $data['po_number'])
                ->where('transmission_date', $data['transmission_date'])
                ->where('transmission_no', $data['transmission_no'])
                ->where('supplier_code', $data['supplier_code'])
                ->orderBy("id","desc");
                $query = Helper::getEloquentSqlWithBindings($check_status);
                $check_status = $check_status->first();
                // return [
                //     "success" => false,
                //     "message" => "get check status",
                //     "data check" => $query,
                //     "where" => $check_status->id
                // ];
                if(!empty($check_status->id))
                {
                    try {
                        //code...
                        $mailPoConfirmation->whereId($check_status->id)->update($updating);
                    } catch (\Throwable $th) {
                        //throw $th;
                        return [
                            "success" => false,
                            "message" => $th,
                            "data check" => $check_status->id,
                            "data" => $updating,
                            "action" => "update"
                        ];
                    }
                }
                else{
                    $insert_data[] = $data;
                    try {
                        //code...
                        $mailPoConfirmation->insert($insert_data);
                    } catch (\Throwable $th) {
                        //throw $th;
                        return [
                            "success" => false,
                            "message" => $th,
                            "data check" => $check_status->id,
                            "data" => $insert_data,
                            "action" => "insert"
                        ];
                    }
                }
                try {
                    //code...
                    $confirmLog->insert($logging);
                } catch (\Throwable $th) {
                    //throw $th;
                    return [
                        "success" => false,
                        "message" => $th,
                        "data" => "logging",
                        "action" => "insert"
                    ];
                }
                
            }

            return [
                "success" => true,
                "message" => $logging['status'] . " Succesfully",
                "data" => isset($insert_data) ? $insert_data : $update_data
            ];
        } catch (\Throwable $th) {
            //throw $th;
            return [
                "success" => false,
                "message" => $th->getMessage(),
                "data" => null
            ];
        }
    }
    // public function confirmBySupplier() // not used
    // {
    //     $request = $this->request;
    //     $waktu = gmdate("Y-m-d H:i:s");
    //     // verified params
    //     // return [
    //     //     "success" => false,
    //     //     "message" => $request->additional,
    //     //     "data" => $request->data
    //     // ];
    //     $query_url = $request->additional;

    //     /**
    //         field mailpo_confirmation
    //         ,[transmission_no] = $parameters[0]->idno
    //         ,[transmission_date] = $parameters[0]->rdate
    //         ,[supplier_code] = $query->sid
    //         ,[po_number] = $parameters[0]->pono
    //         ,[supplier_confirmed_status] = $request->status
    //         ,[supplier_confirmed_reason] = $request->reason
    //         ,[supplier_confirmed_by] =  $_SESSION['usr'];
    //         ,[supplier_confirmed_at] = $waktu
    //         ,[user_secure] = $_SESSION['usrsecure'];
    //      */
    //     $allowedParams = [
    //         'idno' => 'transmission_no',
    //         'rdate' => 'transmission_date',
    //         'pono' => 'po_number'
    //     ];
    //     $mailPoConfirmation = new MailPoConfirmation;
    //     try {
    //         $insert_data = [];
    //         foreach ($request->data as $item) {
    //             foreach ($item as $key => $value) {
    //                 if (isset($allowedParams[$key])) {
    //                     $data[$allowedParams[$key]] = $value;
    //                 }
    //             }
    //             $data["supplier_code"] = $query_url['sid'];
    //             $data["supplier_confirmed_status"] = $request->status;
    //             $data["supplier_confirmed_reason"] = $request->reason;
    //             $data["supplier_confirmed_by"] = $_SESSION['usr'];
    //             $data["supplier_confirmed_at"] = $waktu;
    //             $data["user_secure"] = $_SESSION['usrsecure'];

    //             $insert_data[] = $data;
    //         }
    //         $mailPoConfirmation->insert($insert_data);
    //         return [
    //             "success" => false,
    //             "message" => "Confirm Succesfully",
    //             "data" => $insert_data
    //         ];
    //     } catch (\Throwable $th) {
    //         //throw $th;
    //         return [
    //             "success" => false,
    //             "message" => $th->getMessage(),
    //             "data" => null
    //         ];
    //     }
    // }
    // public function reject() // used
    // {
    //     $request = $this->request;
    //     $waktu = gmdate("Y-m-d H:i:s");
    //     $query_url = $request->additional;

    //     /**
    //         field mailpo_confirmation
    //         ,[transmission_no] = $parameters[0]->idno
    //         ,[transmission_date] = $parameters[0]->rdate
    //         ,[supplier_code] = $query->sid
    //         ,[po_number] = $parameters[0]->pono
    //         ,[supplier_confirmed_status] = $request->status
    //         ,[supplier_confirmed_reason] = $request->reason
    //         ,[supplier_confirmed_by] =  $_SESSION['usr'];
    //         ,[supplier_confirmed_at] = $waktu
    //         ,[user_secure] = $_SESSION['usrsecure'];
    //      */
    //     $allowedParams = [
    //         'idno' => 'transmission_no',
    //         'rdate' => 'transmission_date',
    //         'pono' => 'po_number'
    //     ];
    //     $mailPoConfirmation = new MailPoConfirmation;
    //     $confirmLog = new ConfirmLog;

    //     try {
    //         $insert_data = [];
    //         $update_data = [];
    //         // $action = "INSERT"; // "UPDATE"
    //         foreach ($request->data as $item) {
    //             foreach ($item as $key => $value) {
    //                 if (isset($allowedParams[$key])) {
    //                     $data[$allowedParams[$key]] = $value;
    //                 }
    //             }
    //             $data["supplier_code"] = $query_url['sid'];
    //             // if(usrsecure == 3)
    //             // status = status
    //             // if(usrsecure == 4)
    //             // status = "CONFIRM FOR ACCEPT"
    //             // if(usrsecure == 6)
    //             // status = "CONFIRM PUT BACK"

    //             $data["user_secure"] = $_SESSION['usrsecure'];

    //             $check_status = [];
    //             $updating = [];
    //             if ($data["user_secure"] == 3) {
    //                 $data["supplier_confirmed_status"] = $request->status;
    //                 $data["supplier_confirmed_reason"] = $request->reason;
    //                 $data["supplier_confirmed_by"] = $_SESSION['usr'];
    //                 $data["supplier_confirmed_at"] = $waktu;
    //                 $logging = [
    //                     'transmission_no' => $data['transmission_no'],
    //                     'transmission_date' => $data['transmission_date'],
    //                     'supplier_code' => $data['supplier_code'],
    //                     'po_number' => $data['po_number'],
    //                     'status' => $data['supplier_confirmed_status'],
    //                     'source' => "BUYER",
    //                     'user_secure' => $data['user_secure'],
    //                     'confirmed_reason' => $data['supplier_confirmed_reason'],
    //                     'confirmed_by' => $data['supplier_confirmed_by'],
    //                     'confirmed_at' => $data['supplier_confirmed_at']
    //                 ];
    //                 $updating = [
    //                     'po_number' => $data['po_number'],
    //                     'supplier_confirmed_at' => $data['supplier_confirmed_at'],
    //                     'supplier_confirmed_by' => $data['supplier_confirmed_by'],
    //                     'supplier_confirmed_reason' => $data['supplier_confirmed_reason'],
    //                     'supplier_confirmed_status' => $data['supplier_confirmed_status'],
    //                     'supplier_code' => $data['supplier_code'],
    //                     'transmission_date' => $data['transmission_date'],
    //                     'transmission_no' => $data['transmission_no'],
    //                     'user_secure' => $data['user_secure']
    //                 ];
    //             } elseif ($data["user_secure"] == 4) {
    //                 $data["purch_confirmed_status"] = $request->status;
    //                 $data["purch_confirmed_reason"] = $request->reason;
    //                 $data["purch_confirmed_by"] = $_SESSION['usr'];
    //                 $data["purch_confirmed_at"] = $waktu;
    //                 $logging = [
    //                     'transmission_no' => $data['transmission_no'],
    //                     'transmission_date' => $data['transmission_date'],
    //                     'supplier_code' => $data['supplier_code'],
    //                     'po_number' => $data['po_number'],
    //                     'status' => $data['purch_confirmed_status'],
    //                     'source' => "PURCH",
    //                     'user_secure' => $data['user_secure'],
    //                     'confirmed_reason' => $data['purch_confirmed_reason'],
    //                     'confirmed_by' => $data['purch_confirmed_by'],
    //                     'confirmed_at' => $data['purch_confirmed_at']
    //                 ];
    //                 $updating = [
    //                     'po_number' => $data['po_number'],
    //                     'purch_confirmed_at' => $data['purch_confirmed_at'],
    //                     'purch_confirmed_by' => $data['purch_confirmed_by'],
    //                     'purch_confirmed_reason' => $data['purch_confirmed_reason'],
    //                     'purch_confirmed_status' => $data['purch_confirmed_status'],
    //                     'supplier_code' => $data['supplier_code'],
    //                     'transmission_date' => $data['transmission_date'],
    //                     'transmission_no' => $data['transmission_no'],
    //                     'user_secure' => $data['user_secure']
    //                 ];
    //             } elseif ($data["user_secure"] == 6) {
    //                 $data["mc_confirmed_status"] = $request->status;
    //                 $data["mc_confirmed_reason"] = $request->reason;
    //                 $data["mc_confirmed_by"] = $_SESSION['usr'];
    //                 $data["mc_confirmed_at"] = $waktu;
    //                 $logging = [
    //                     'transmission_no' => $data['transmission_no'],
    //                     'transmission_date' => $data['transmission_date'],
    //                     'supplier_code' => $data['supplier_code'],
    //                     'po_number' => $data['po_number'],
    //                     'status' => $data['mc_confirmed_status'],
    //                     'source' => "MC",
    //                     'user_secure' => $data['user_secure'],
    //                     'confirmed_reason' => $data['mc_confirmed_reason'],
    //                     'confirmed_by' => $data['mc_confirmed_by'],
    //                     'confirmed_at' => $data['mc_confirmed_at']
    //                 ];
    //                 $updating = [
    //                     'po_number' => $data['po_number'],
    //                     'mc_confirmed_at' => $data['mc_confirmed_at'],
    //                     'mc_confirmed_by' => $data['mc_confirmed_by'],
    //                     'mc_confirmed_reason' => $data['mc_confirmed_reason'],
    //                     'mc_confirmed_status' => $data['mc_confirmed_status'],
    //                     'supplier_code' => $data['supplier_code'],
    //                     'transmission_date' => $data['transmission_date'],
    //                     'transmission_no' => $data['transmission_no'],
    //                     'user_secure' => $data['user_secure']
    //                 ];
    //             }

    //             $check_status = MailPoConfirmation::select("id")
    //             ->where('po_number', $data['po_number'])
    //             ->where('transmission_date', $data['transmission_date'])
    //             ->where('transmission_no', $data['transmission_no'])
    //             ->where('supplier_code', $data['supplier_code'])
    //             ->orderBy("id", "desc");
    //             $query = Helper::getEloquentSqlWithBindings($check_status);
    //             $check_status = $check_status->first();
    //             // return [
    //             //     "success" => false,
    //             //     "message" => "get check status",
    //             //     "data check" => $query,
    //             //     "where" => $check_status->id
    //             // ];
    //             if (!empty($check_status->id)) {
    //                 try {
    //                     //code...
    //                     $mailPoConfirmation->whereId($check_status->id)->update($updating);
    //                 } catch (\Throwable $th) {
    //                     //throw $th;
    //                     return [
    //                         "success" => false,
    //                         "message" => $th,
    //                         "data check" => $check_status->id,
    //                         "data" => $updating,
    //                         "action" => "update"
    //                     ];
    //                 }
    //             } else {
    //                 $insert_data[] = $data;
    //                 try {
    //                     //code...
    //                     $mailPoConfirmation->insert($insert_data);
    //                 } catch (\Throwable $th) {
    //                     //throw $th;
    //                     return [
    //                         "success" => false,
    //                         "message" => $th,
    //                         "data check" => $check_status->id,
    //                         "data" => $insert_data,
    //                         "action" => "insert"
    //                     ];
    //                 }
    //             }
    //             try {
    //                 //code...
    //                 $confirmLog->insert($logging);
    //             } catch (\Throwable $th) {
    //                 //throw $th;
    //                 return [
    //                     "success" => false,
    //                     "message" => $th,
    //                     "data" => "logging",
    //                     "action" => "insert"
    //                 ];
    //             }
    //         }

    //         return [
    //             "success" => true,
    //             "message" => $logging['status'] . " Succesfully",
    //             "data" => isset($insert_data) ? $insert_data : $update_data
    //         ];
    //     } catch (\Throwable $th) {
    //         //throw $th;
    //         return [
    //             "success" => false,
    //             "message" => $th->getMessage(),
    //             "data" => null
    //         ];
    //     }
    // }
    // public function rejectBySupplier() // not used
    // {
    //     $request = $this->request;
    //     $waktu = gmdate("Y-m-d H:i:s");
    //     // verified params
    //     // return [
    //     //     "success" => false,
    //     //     "message" => $request->additional,
    //     //     "data" => $request->data
    //     // ];
    //     $query_url = $request->additional;

    //     /**
    //         field mailpo_confirmation
    //         ,[transmission_no] = $parameters[0]->idno
    //         ,[transmission_date] = $parameters[0]->rdate
    //         ,[supplier_code] = $query->sid
    //         ,[po_number] = $parameters[0]->pono
    //         ,[supplier_confirmed_status] = $request->status
    //         ,[supplier_confirmed_reason] = $request->reason
    //         ,[supplier_confirmed_by] =  $_SESSION['usr'];
    //         ,[supplier_confirmed_at] = $waktu
    //         ,[user_secure] = $_SESSION['usrsecure'];
    //      */
    //     $allowedParams = [
    //         'idno' => 'transmission_no',
    //         'rdate' => 'transmission_date',
    //         'pono' => 'po_number'
    //     ];
    //     try {
    //         $insert_data = [];
    //         foreach ($request->data as $item) {
    //             foreach ($item as $key => $value) {
    //                 if (isset($allowedParams[$key])) {
    //                     $data[$allowedParams[$key]] = $value;
    //                 }
    //             }
    //             $data["supplier_code"] = $query_url['sid'];
    //             $data["supplier_confirmed_status"] = $request->status;
    //             $data["supplier_confirmed_reason"] = $request->reason;
    //             $data["supplier_confirmed_by"] = $_SESSION['usr'];
    //             $data["supplier_confirmed_at"] = $waktu;
    //             $data["user_secure"] = $_SESSION['usrsecure'];

    //             $insert_data[] = $data;
    //         }

    //         $mailPoConfirmation = new MailPoConfirmation;
    //         $mailPoConfirmation->insert($insert_data);
    //         return [
    //             "success" => false,
    //             "message" => "Reject Succesfully",
    //             "data" => $insert_data
    //         ];
    //     } catch (\Throwable $th) {
    //         //throw $th;
    //         return [
    //             "success" => false,
    //             "message" => $th->getMessage(),
    //             "data" => null
    //         ];
    //     }
    // }
}
