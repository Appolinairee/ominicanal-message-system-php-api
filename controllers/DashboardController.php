<?php

require_once __DIR__ . '/../models/Conversation.php';
require_once __DIR__ . '/../models/Message.php';

class DashboardController
{
    private $db;
    private $conversationModel;

    public function __construct()
    {
        $config = require './config.php';
        $database = new Database($config['db']);
        $this->db = $database->connect();
        $this->conversationModel = new Conversation($this->db);
    }

    public function getStats()
    {
        try {
            $totalConversations = $this->conversationModel->getTotalConversations();

            $totalConversationsByChannel = $this->conversationModel->getTotalConversationsByChannel();
            $averageResponseTime = $this->conversationModel->getAverageResponseTime();
            $averageProcessingTime = $this->conversationModel->getAverageProcessingTime();
            $averageConversationDuration = $this->conversationModel->getAverageConversationDuration();

            return json_encode([
                'ok' => true,
                'data' => [
                    'total_conversations' => $totalConversations,
                    'total_conversations_by_channel' => $totalConversationsByChannel,
                    'average_response_time' => $averageResponseTime,
                    'average_processing_time' => $averageProcessingTime,
                    'average_conversation_duration' => $averageConversationDuration
                ]
            ]);
        } catch (PDOException $e) {
            return json_encode([
                'ok' => false,
                'error' => 'Erreur de base de données : ' . $e->getMessage()
            ]);
        }
    }


    public function getUserStatistics($userId)
    {
        try {
            $totalConversations = $this->conversationModel->getTotalConversationsByUser($userId);
            $totalConversationsByChannel = $this->conversationModel->getTotalConversationsByChannelAndUser($userId);
            $averageResponseTime = $this->conversationModel->getAverageResponseTimeByUser($userId);
            $averageProcessingTime = $this->conversationModel->getAverageProcessingTimeByUser($userId);
            $averageConversationDuration = $this->conversationModel->getAverageConversationDurationByUser($userId);

            return json_encode([
                'ok' => true,
                'data' => [
                    'total_conversations' => $totalConversations,
                    'total_conversations_by_channel' => $totalConversationsByChannel,
                    'average_response_time' => $averageResponseTime,
                    'average_processing_time' => $averageProcessingTime,
                    'average_conversation_duration' => $averageConversationDuration,
                ]
            ]);
        } catch (PDOException $e) {
            return json_encode([
                'ok' => false,
                'error' => 'Erreur de base de données : ' . $e->getMessage()
            ]);
        }
    }
}
