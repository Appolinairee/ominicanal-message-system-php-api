<?php

require_once __DIR__ . '/../models/Conversation.php';
require_once __DIR__ . '/../models/Message.php';
require_once __DIR__ . '/../models/User.php';

class MessageController
{
    private $db;
    private $conversationModel;
    private $messageModel;

    public function __construct()
    {
        $config = require './config.php';
        $database = new Database($config['db']);
        $this->db = $database->connect();

        $this->conversationModel = new Conversation($this->db);
        $this->messageModel = new Message($this->db);
    }

    public function addMessageToConversation($conversationId, $sender, $messageContent)
    {
        $conversation = $this->conversationModel->getById($conversationId);

        if (!$conversation) {
            return json_encode([
                'ok' => false,
                'error' => 'Conversation introuvable.'
            ]);
        }

        if ($conversation['status'] === 'closed') {
            return json_encode([
                'ok' => false,
                'error' => 'Impossible d\'ajouter un message à une conversation fermée.'
            ]);
        }

        $result = $this->messageModel->create($conversationId, $sender, $messageContent);

        return json_encode($result);
    }
}
