<?php
    include './controllers/LoginController.php';

    try {
    //code...
    $LoginController = new LoginController;
    // echo json_encode([
    //     'success' => false,
    //     'message' => "MASUK SINI",
    //     'trace' => "NTAHLAH"
    // ]);
    // return;
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {    
        echo json_encode($LoginController->validate(), JSON_NUMERIC_CHECK );
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