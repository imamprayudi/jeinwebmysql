<?php
    include './controllers/DashboardController.php';

    try {
    //code...
    $DashboardController = new DashboardController;
    // echo json_encode([
    //     'success' => false,
    //     'message' => "MASUK SINI",
    //     'trace' => "NTAHLAH"
    // ]);
    // return;

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        if (isset($_GET['method'])) {
            $method = $_GET['method'];
            echo json_encode($DashboardController->$method(), JSON_NUMERIC_CHECK);
        }
        // if(@$_GET['method'] == 'getMonthlyPoc'){
        //     echo json_encode($DashboardController->getMonthlyPoc(), JSON_NUMERIC_CHECK );
        // }
        // if(@$_GET['method'] == 'getPartNumber'){
        //     echo json_encode($DashboardController->getPartNumber(), JSON_NUMERIC_CHECK );
        // }

        // if(@$_GET['method'] == 'getPo'){
        //     echo json_encode($DashboardController->getPo(), JSON_NUMERIC_CHECK );
        // }

        // if(@$_GET['method'] == 'getDataFilter'){
        //     echo json_encode($DashboardController->getDataFilter(), JSON_NUMERIC_CHECK );
        // }
      
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