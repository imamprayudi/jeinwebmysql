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


class PurchaseOrderChangeController
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
        // return [
        //     "success" => false,
        //     "message" => "POC FILTER"
        // ];
        $request = $this->request;
        if ($request->filter_by == '--Select Category--') {
            return "Filter NG, Call JKEI IT";
        }
        try {
            $getData = [];
            if ($request->filter_by != 'rdate') {
                $getData = MailPoChange::selectRaw("trim($request->filter_by) as $request->filter_by")
                    ->orderBy($request->filter_by, "asc");
            }
            if ($request->filter_by == 'rdate') {
                $getData = MailPoChange::selectRaw(Helper::translateQueryMonthlyToMysql($request->filter_by,"10", "MySQL") ." as $request->filter_by")
                    ->orderBy($request->filter_by, "desc");
            }
            $getData = $getData->where("supplier", trim($request->supplier))
                ->whereBetween("rdate", [$request->from_date, $request->end_date])
                ->distinct()
                ->get();

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

    // public function getFilterBy_salah() //NOT USED
    // {
    //     $request = $this->request;
    //     return $request;
    //     /**
    //     select transdate,supplier,status,confirmation,confirmdate,rejectreason 
    //     from mailpost where ( supplier = '" . $supp . "') and 
    //     ( transdate between '" . $tgl1 ."' and '" . $tgl2 . "') order by transdate
    //      */
    //     try {
    //         //code...
    //         $data = MailPoChange::select("idno", "rdate", "pono")
    //             ->where("supplier", $request->supplier)
    //             ->whereBetween("rdate", [$request->from_date, $request->end_date])
    //             ->whereIn($request->filter_by, $request->select_po)
    //             ->orderBy("rdate", "desc")
    //             ->get();

    //         return [
    //             "success" => true,
    //             "message" => "Successfully Getting data",
    //             "data" => $data
    //         ];
    //     } catch (\Throwable $th) {
    //         return [
    //             "success" => false,
    //             "message" => $th
    //         ];
    //     }

    //     // $data = [
    //     //     "repeated" => $poc_repeat,
    //     //     "pochange" => $poc_change
    //     //  ];

    //     //  return $data;
    // }

    public function getDataPo()
    {
        $request = $this->request;

        try {
            //code...
            $getData = MailPoChange::select('id', 'rdate', 'idno', DB::raw("trim(supplier) as supplier"), DB::raw("trim(suppliername) as suppliername"), DB::raw("trim(pono) as pono"), DB::raw("COALESCE(status, 'UNREAD') AS status"), 'read_at', 'confirmed_status', 'confirmed_reason', 'confirmed_by', 'confirmed_at')
                ->leftJoin('mailpoc_confirmation', function ($join) {
                    $join->on('mailpoc.idno', 'mailpoc_confirmation.transmission_no');
                })
                ->where('supplier', $request->supplier)
                ->whereBetween('rdate', [$request->from_date, $request->end_date])
                ->whereIn($request->filter_by, $request->select_po)
                ->get();
            // return Helper::getEloquentSqlWithBindings($getData);

            return [
                "success" => true,
                "message" => "successfully get data Mail POC",
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

    public function getDataPoChangeST()
    {
        $request = $this->request;
        try {
            //code...
            $filter = '';
            $request->filter_by == "rdate" ? $filter = "transdate" : $filter = $request->filter_by;
            if ($filter == "transdate") {

                $getData = MailPoChangeSt::select(
                    DB::raw(Helper::translateQueryMonthlyToMysql("transdate","10", "MySQL") . " as transdate"),
                    DB::raw("trim(mailpocst.supplier) as supplier"),
                    'status',
                    DB::raw("isnull(confirmed.total_confirmed,0) as total_confirmed"),
                    DB::raw("isnull(rejected.total_rejected,0) as total_rejected"),
                    'total.total_po',
                    'updated'
                );

                $getData = $getData
                    ->leftJoin(
                        DB::raw("(select count(supplier_confirmed_status) as total_confirmed,transmission_date,supplier_code from mailpoc_confirmation where supplier_confirmed_status = 'CONFIRM' group by transmission_date,supplier_code) as confirmed"),
                        function ($join) {
                            $join->on('confirmed.transmission_date', '=', 'mailpocst.TransDate');
                            $join->on('confirmed.supplier_code', '=', 'mailpocst.Supplier');
                        }
                    );

                $getData = $getData
                    ->leftJoin(
                        DB::raw("(select count(supplier_confirmed_status) as total_rejected,transmission_date,supplier_code from mailpoc_confirmation where supplier_confirmed_status = 'REJECTED' group by transmission_date,supplier_code) as rejected"),
                        function ($join) {
                            // confirmed.transmission_date = mailpocst.TransDate and confirmed.supplier_code = mailpocst.Supplier
                            $join->on('confirmed.transmission_date', '=', 'mailpocst.TransDate');
                            $join->on('confirmed.supplier_code', '=', 'mailpocst.Supplier');
                        }
                    );

                $getData = $getData
                    ->leftJoin(
                        DB::raw("(select count(pono) as total_po,rdate,Supplier from mailpoc group by RDATE,Supplier) as total"),
                        function ($join) {
                            // confirmed.transmission_date = mailpocst.TransDate and confirmed.supplier_code = mailpocst.Supplier
                            $join->on('total.rdate', '=', 'mailpocst.TransDate');
                            $join->on('total.supplier', '=', 'mailpocst.Supplier');
                        }
                    )
                    ->where('mailpocst.supplier', $request->supplier);
                // $query_last = Helper::getEloquentSqlWithBindings($getData);
                $filter == 'transdate'
                    ? $getData = $getData->whereIn('mailpocst.' . $filter, $request->select_poc)
                    : $getData = $getData->whereBetween('mailpocst.transdate', [$request->from_date, $request->end_date]);

                if ($filter != 'transdate') {
                    $getTransdate = MailPoChange::select('rdate')
                        ->where('supplier', $request->supplier)
                        ->whereBetween('rdate', [$request->from_date, $request->end_date])
                        ->where($filter, $request->select_po)
                        ->distinct('rdate')
                        ->pluck('rdate');
                    // return $getTransdate;
                    $getData = $getData->whereIn('mailpocst.transdate', $getTransdate);
                }
                $data  = $getData->orderBy('mailpocst.transdate', 'desc');
                $data = $data->get();
            } else {
                return $this->getDataPoChangeDetail();
            }
            return [
                "success" => true,
                "message" => "successfully get data Mail POCST",
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

        $check_status = MailPoChangeSt::where('Supplier', $request->sid)
            ->where('Transdate', $request->tglid)
            ->pluck('status');
        // return [
        //     "success" => false,
        //     "supplier" => $check_status
        // ];
        if ($check_status[0] == "unread") {
            try {
                //code...
                $updating = MailPoChangeSt::where('Supplier', $request->sid)
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

    public function getDataPoChangeDetail()
    {
        $request = $this->request;

        try {
            //code...
            $getData = MailPoChange::select(
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
                'mailpoc_conf.supplier_confirmed_status',
                'mailpoc_conf.supplier_confirmed_reason',
                'mailpoc_conf.supplier_confirmed_by',
                'mailpoc_conf.supplier_confirmed_at',
                'mailpoc_conf.purch_confirmed_status',
                'mailpoc_conf.purch_confirmed_reason',
                'mailpoc_conf.purch_confirmed_by',
                'mailpoc_conf.purch_confirmed_at',
                'mailpoc_conf.mc_confirmed_status',
                'mailpoc_conf.mc_confirmed_reason',
                'mailpoc_conf.mc_confirmed_by',
                'mailpoc_conf.mc_confirmed_at'
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
                        FROM mailpoc_confirmation where id in ((SELECT MAX(ID) AS id 
                                        FROM [mailpoc_confirmation]
                                        GROUP BY [transmission_no]
                                        ,[transmission_date]
                                        ,[supplier_code]
                                        ,[po_number]))) as mailpoc_conf
                        "),
                'mailpoc.idno',
                '=',
                'mailpoc_conf.transmission_no'
            );

            if ($request->supplier) {
                $getData = $getData->where('mailpoc.supplier', $request->supplier)
                ->whereBetween('rdate', [$request->from_date, $request->end_date]);
                // ->whereIn($request->filter_by, [$request->select_poc]);
                // return $request->select_poc;
                // return $request->filter_by;

                // return Helper::getEloquentSqlWithBindings($getData);
            } else {
                $getData = $getData->whereIn('mailpoc.rdate', [$request->tglid])
                    ->where('supplier', $request->sid);
            }
            $getData = $getData->whereIn($request->filter_by, [$request->select_poc]);
            // if ($request->pono) {
            //     $getData = $getData->whereIn('mailpoc.pono', [$request->pono]);
            // }
            // return Helper::getEloquentSqlWithBindings($getData);
            // ->whereIn('rdate', [$request->tglid])
            $getData = $getData->orderBy('idno', 'asc');
            $getData = $getData->get();
            // return $getData;
            return [
                "success" => true,
                "message" => "successfully get data Mail POC",
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
            field mailpoc_confirmation
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
        $mailPoChangeConfirmation = new MailPoChangeConfirmation;
        $confirmLog = new ConfirmLogPOC;

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
                if ($data["user_secure"] == 3) {
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
                        'transmission_no' => $data['transmission_no'],
                        'user_secure' => $data['user_secure']
                    ];
                } elseif ($data["user_secure"] == 4) {
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
                } elseif ($data["user_secure"] == 6) {
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

                $check_status = MailPoChangeConfirmation::select("id")
                    ->where('po_number', $data['po_number'])
                    ->where('transmission_date', $data['transmission_date'])
                    ->where('transmission_no', $data['transmission_no'])
                    ->where('supplier_code', $data['supplier_code'])
                    ->orderBy("id", "desc");
                $query = Helper::getEloquentSqlWithBindings($check_status);
                $check_status = $check_status->first();
                // return [
                //     "success" => false,
                //     "message" => "get check status",
                //     "data check" => $query,
                //     "where" => $check_status->id
                // ];
                if (!empty($check_status->id)) {
                    try {
                        //code...
                        $mailPoChangeConfirmation->whereId($check_status->id)->update($updating);
                    } catch (\Throwable $th) {
                        //throw $th;
                        return [
                            "success" => false,
                            "message" => $th->getMessage(),
                            "data check" => $check_status->id,
                            "data" => $updating,
                            "action" => "update"
                        ];
                    }
                } else {
                    $insert_data[] = $data;
                    try {
                        //code...
                        $mailPoChangeConfirmation->insert($insert_data);
                    } catch (\Throwable $th) {
                        //throw $th;
                        return [
                            "success" => false,
                            "message" => $th->getMessage(),
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
                        "message" => $th->getMessage(),
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
}
