<?php

require_once './middleware/authMiddleware.php';
require_once './controllers/UserController.php';
require_once './controllers/ConversationController.php';
require_once './controllers/MessageController.php';
require_once './controllers/DashboardController.php';


$authMiddleware = new AuthMiddleware();
$userController = new UserController();
$conversationController = new ConversationController();
$messageController = new MessageController();
$dashboardController = new DashboardController();

$requestMethod = $_SERVER['REQUEST_METHOD'];
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ($requestUri !== '/api/register' && $requestUri !== '/api/login') {
    $authMiddleware->handle($_SERVER);
}

switch ($requestUri) {
    case '/api/register':
        if ($requestMethod === 'POST') {
            $data = json_decode(file_get_contents("php://input"), true);
            $userController->register($data['username'], $data['password'], $data['user_type']);
        } else {
            http_response_code(405);
            echo json_encode(['ok' => false, 'error' => 'Method not allowed']);
        }
        break;

    case '/api/login':
        if ($requestMethod === 'POST') {
            $data = json_decode(file_get_contents("php://input"), true);
            $userController->login($data['username'], $data['password']);
        } else {
            http_response_code(405);
            echo json_encode(['ok' => false, 'error' => 'Method not allowed']);
        }
        break;

    case '/api/start-conversation':
        if ($requestMethod === 'POST') {
            $data = json_decode(file_get_contents("php://input"), true);
            $clientId = $data['client_id'];
            $messageContent = $data['message_content'];
            $channel = $data['channel'];

            echo $conversationController->startConversation($clientId, $messageContent, $channel);
        } else {
            http_response_code(405);
            echo json_encode(['ok' => false, 'error' => 'Method not allowed']);
        }
        break;

    case '/api/get-messages':
        if ($requestMethod === 'GET') {
            $conversationId = $_GET['conversation_id'] ?? null;

            if ($conversationId === null) {
                http_response_code(400);
                echo json_encode(['ok' => false, 'error' => 'conversation_id is required']);
                break;
            }

            echo $conversationController->getMessagesByConversationId($conversationId);
        } else {
            http_response_code(405);
            echo json_encode(['ok' => false, 'error' => 'Method not allowed']);
        }
        break;


    case '/api/close-conversation':
        if ($requestMethod === 'POST') {
            $data = json_decode(file_get_contents("php://input"), true);
            $conversationId = $data['conversation_id'];

            echo $conversationController->closeConversation($conversationId);
        } else {
            http_response_code(405);
            echo json_encode(['ok' => false, 'error' => 'Method not allowed']);
        }
        break;

    case '/api/add-message':
        if ($requestMethod === 'POST') {
            $data = json_decode(file_get_contents("php://input"), true);
            $conversationId = $data['conversation_id'];
            $sender = $data['sender'];
            $messageContent = $data['message_content'];

            echo $messageController->addMessageToConversation($conversationId, $sender, $messageContent);
        } else {
            http_response_code(405);
            echo json_encode(['ok' => false, 'error' => 'Method not allowed']);
        }
        break;

    case '/api/dashboard':
        if ($requestMethod === 'GET') {
            $userId = isset($_GET['user_id']) ? intval($_GET['user_id']) : null;

            if ($userId) {
                echo $dashboardController->getUserStatistics($userId);
            } else {
                echo $dashboardController->getStats();
            }
        } else {
            http_response_code(405);
            echo json_encode(['ok' => false, 'error' => 'Method not allowed']);
        }
        break;


    default:
        http_response_code(404);
        echo json_encode(['ok' => false, 'error' => 'Not Found']);
        break;
}
