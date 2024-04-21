<?php
    include './controllers/PurchaseOrderController.php';
    include './controllers/DashboardController.php';

    try {
    //code...
    $PurchaseOrderController = new PurchaseOrderController;
    $DashboardController = new DashboardController;

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {  
        if(@$_GET['method'] == 'getSupplierGroup'){
            echo json_encode($DashboardController->getSupplierGroup(), JSON_NUMERIC_CHECK );
            return;
        }
        if(isset($_GET['method']))
        {
            $method = $_GET['method'];
            echo json_encode($PurchaseOrderController->$method(), JSON_NUMERIC_CHECK );
        }

    }
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {  
        if(isset($_GET['method']))
        {
            $method = $_GET['method'];
            echo json_encode($PurchaseOrderController->$method(), JSON_NUMERIC_CHECK );
        }
    }
} catch (\Exception $th) {
    //throw $th;
    http_response_code(400);

    echo json_encode([
        'success' => false,
        'message' => $th->getMessage(),
        'trace' => $th->getTrace()
    ]);
}

    
?>