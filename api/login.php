$requestData = file_get_contents('php://input');


$data = json_decode($requestData, true);


if (!isset($data['username']) || !isset($data['password'])) {
    http_response_code(400);
    echo json_encode(['message' => 'Missing required fields']);
    exit;
}
