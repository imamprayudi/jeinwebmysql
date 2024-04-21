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


class PurchaseOrderChangeController_OLD
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
                $getData = MailPoChange::selectRaw("trim($request->filter_by) as $request->filter_by")
                    ->orderBy($request->filter_by, "asc");
            }
            if ($request->filter_by == 'rdate') {
                
                $getData = MailPoChange::selectRaw(Helper::translateQueryMonthlyToMysql($request->filter_by, "10") . " as $request->filter_by")
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

    public function getFilterBy_salah()
    {
        $request = $this->request;
        return $request;
        /**
        select transdate,supplier,status,confirmation,confirmdate,rejectreason 
        from mailpoChangest where ( supplier = '" . $supp . "') and 
        ( transdate between '" . $tgl1 ."' and '" . $tgl2 . "') order by transdate
         */
        try {
            //code...
            $data = MailPoChange::select("idno", "rdate", "pono")
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

    public function getDataPoChangeST()
    {
        $request = $this->request;
        try {
            //code...
            $filter = '';
            $request->filter_by == "rdate" ? $filter = "transdate" : $filter = $request->filter_by;
            if ($filter == "transdate") {


                $getData = MailPoChangeSt::select(
                    
                    DB::raw(Helper::translateQueryMonthlyToMysql("transdate", "10") . " as transdate"),
                    DB::raw("trim(mailpocst.supplier) as supplier"),
                    'status',
                    DB::raw("isnull(confirmed.total_confirmed,0) as total_confirmed"),
                    DB::raw("isnull(rejected.total_rejected,0) as total_rejected"),
                    'total.total_po',
                    'updated'
                )
                    ->leftJoin(
                        DB::raw("(select count(supplier_confirmed_status) as total_confirmed,transmission_date,supplier_code from mailpoc_confirmation where supplier_confirmed_status = 'CONFIRM' group by transmission_date,supplier_code) as confirmed"),
                        function ($join) {
                            $join->on('confirmed.transmission_date', '=', 'mailpocst.TransDate');
                            $join->on('confirmed.supplier_code', '=', 'mailpocst.Supplier');
                        }
                    )
                    ->leftJoin(
                        DB::raw("(select count(supplier_confirmed_status) as total_rejected,transmission_date,supplier_code from mailpoc_confirmation where supplier_confirmed_status = 'REJECTED' group by transmission_date,supplier_code) as rejected"),
                        function ($join) {
                            // confirmed.transmission_date = mailpocst.TransDate and confirmed.supplier_code = mailpocst.Supplier
                            $join->on('confirmed.transmission_date', '=', 'mailpocst.TransDate');
                            $join->on('confirmed.supplier_code', '=', 'mailpocst.Supplier');
                        }
                    )
                    ->leftJoin(
                        DB::raw("(select count(pono) as total_po,rdate,Supplier from mailpo group by RDATE,Supplier) as total"),
                        function ($join) {
                            // confirmed.transmission_date = mailpocst.TransDate and confirmed.supplier_code = mailpocst.Supplier
                            $join->on('total.rdate', '=', 'mailpocst.TransDate');
                            $join->on('total.supplier', '=', 'mailpocst.Supplier');
                        }
                    )
                    ->where('mailpocst.supplier', $request->supplier);

                $filter == 'transdate'
                    ? $getData = $getData->whereIn('mailpocst.' . $filter, $request->select_po)
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
                $data  = $getData->orderBy('mailpocst.transdate', 'desc')->get();
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

        $check_status = MailPoChangeSt::where('Supplier', $request->sid)
            ->where('Transdate', $request->tglid)
            ->pluck('status');

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

    public function getDataPoDetail()
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
                'mailpoc_confirmation.supplier_confirmed_status',
                'mailpoc_confirmation.supplier_confirmed_reason',
                'mailpoc_confirmation.supplier_confirmed_by',
                'mailpoc_confirmation.supplier_confirmed_at',
                'mailpoc_confirmation.purch_confirmed_status',
                'mailpoc_confirmation.purch_confirmed_reason',
                'mailpoc_confirmation.purch_confirmed_by',
                'mailpoc_confirmation.purch_confirmed_at',
                'mailpoc_confirmation.mc_confirmed_status',
                'mailpoc_confirmation.mc_confirmed_reason',
                'mailpoc_confirmation.mc_confirmed_by',
                'mailpoc_confirmation.mc_confirmed_at'
            )
                ->leftJoin('mailpoc_confirmation', function ($join) {
                    $join->on('mailpocst.idno', 'mailpoc_confirmation.transmission_no');
                });
                if($request->supplier){
                    $getData = $getData->where('supplier', $request->supplier)
                    ->whereBetween('rdate', [$request->from_date, $request->end_date])
                    ->whereIn($request->filter_by,$request->select_po);
                }
                else{
                    $getData = $getData->whereIn('rdate', [$request->tglid])
                    ->where('supplier', $request->sid);
                }
            // ->whereIn('rdate', [$request->tglid])
            $getData = $getData->get();
            // return Helper::getEloquentSqlWithBindings($getData);
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

    public function confirmBySupplier()
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
        try {
            $insert_data = [];
            foreach ($request->data as $item) {
                foreach ($item as $key => $value) {
                    if (isset($allowedParams[$key])) {
                        $data[$allowedParams[$key]] = $value;
                    }
                }
                $data["supplier_code"] = $query_url['sid'];
                $data["supplier_confirmed_status"] = $request->status;
                $data["supplier_confirmed_reason"] = $request->reason;
                $data["supplier_confirmed_by"] = $_SESSION['usr'];
                $data["supplier_confirmed_at"] = $waktu;
                $data["user_secure"] = $_SESSION['usrsecure'];

                $insert_data[] = $data;
            }

            $mailPoConfirmation = new MailPoConfirmation;
            $mailPoConfirmation->insert($insert_data);
            return [
                "success" => false,
                "message" => "Confirm Succesfully",
                "data" => $insert_data
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
    public function rejectBySupplier()
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
        try {
            $insert_data = [];
            foreach ($request->data as $item) {
                foreach ($item as $key => $value) {
                    if (isset($allowedParams[$key])) {
                        $data[$allowedParams[$key]] = $value;
                    }
                }
                $data["supplier_code"] = $query_url['sid'];
                $data["supplier_confirmed_status"] = $request->status;
                $data["supplier_confirmed_reason"] = $request->reason;
                $data["supplier_confirmed_by"] = $_SESSION['usr'];
                $data["supplier_confirmed_at"] = $waktu;
                $data["user_secure"] = $_SESSION['usrsecure'];

                $insert_data[] = $data;
            }

            $mailPoConfirmation = new MailPoConfirmation;
            $mailPoConfirmation->insert($insert_data);
            return [
                "success" => false,
                "message" => "Reject Succesfully",
                "data" => $insert_data
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
