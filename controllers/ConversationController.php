<?php

require_once __DIR__ . '/../models/Conversation.php';
require_once __DIR__ . '/../models/Message.php';
require_once __DIR__ . '/../models/User.php';

class ConversationController
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


    public function startConversation($clientId, $messageContent, $channel)
    {
        if (empty($messageContent)) {
            return json_encode([
                'ok' => false,
                'error' => 'Le contenu du message est obligatoire.'
            ]);
        }

        $userModel = new User($this->db);
        $client = $userModel->getById($clientId);

        if (!$client) {
            return json_encode([
                'ok' => false,
                'error' => 'Client introuvable.'
            ]);
        }

        $validChannels = ['whatsApp', 'telegram', 'messenger'];
        if (!in_array($channel, $validChannels)) {
            return json_encode([
                'ok' => false,
                'error' => 'Channel is required. Accepted values are: ' . implode(', ', $validChannels) . '.'
            ]);
        }

        $conversationResult = $this->conversationModel->create($clientId, null, $channel);

        if (!$conversationResult['ok']) {
            return json_encode($conversationResult);
        }

        $conversationId = $conversationResult['conversation_id'];

        $agentResult = $this->conversationModel->selectRandomAgent($conversationId);

        if (!$agentResult['ok']) {
            return json_encode($agentResult);
        }

        $messageResult = $this->messageModel->create($conversationId, 'client', $messageContent);

        return json_encode($messageResult);
    }


    //Récupérer la conversation, les infos sur le client et sur l'agent
    public function getMessagesByConversationId($conversationId)
    {
        $conversation = $this->conversationModel->getById($conversationId);

        if (!$conversation) {
            return json_encode([
                'ok' => false,
                'error' => 'Conversation introuvable.'
            ]);
        }

        $messages = $this->messageModel->getByConversationId($conversationId);

        $userModel = new User($this->db);
        $agent = $userModel->getById($conversation['agent_id']);

        if (empty($messages)) {
            return json_encode([
                'ok' => true,
                'data' => [
                    'messages' => [],
                    'conversation' => $conversation,
                    'agent' => $agent,
                ]
            ]);
        }

        return json_encode([
            'ok' => true,
            'data' => [
                'messages' => $messages,
                'conversation' => $conversation,
                'agent' => $agent,
            ]
        ]);
    }



    //vérifier que le user est de type agent d'abord
    public function closeConversation($conversationId)
    {
        try {
            $conversation = $this->conversationModel->getById($conversationId);

            if (!$conversation) {
                return json_encode([
                    'ok' => false,
                    'error' => 'Conversation introuvable.'
                ]);
            }

            $result = $this->conversationModel->close($conversationId);
            return json_encode($result);
        } catch (PDOException $e) {
            return [
                'ok' => false,
                'error' => 'Erreur de base de données : ' . $e->getMessage()
            ];
        }
    }
}
