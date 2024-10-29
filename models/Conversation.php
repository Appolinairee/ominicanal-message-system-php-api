<?php

class Conversation
{
    private $db;

    public function __construct($dbConnection)
    {
        $this->db = $dbConnection;
    }

    public function create($clientId, $agentId = null, $channel)
    {
        try {
            $stmt = $this->db->prepare("INSERT INTO conversations (client_id, agent_id, channel) VALUES (:client_id, :agent_id, :channel)");
            $stmt->bindParam(':client_id', $clientId);
            $stmt->bindParam(':agent_id', $agentId);
            $stmt->bindParam(':channel', $channel);
            $stmt->execute();

            $conversationId = intval($this->db->lastInsertId());

            return ['ok' => true, 'message' => 'Conversation créée avec succès.', 'conversation_id' => $conversationId];
        } catch (PDOException $e) {
            return [
                'ok' => false,
                'error' => 'Erreur de base de données : ' . $e->getMessage()
            ];
        }
    }

    public function close($conversationId)
    {
        try {
            $stmt = $this->db->prepare("UPDATE conversations SET status = 'closed', closed_at = CURRENT_TIMESTAMP WHERE id = :id");
            $stmt->bindParam(':id', $conversationId);
            $stmt->execute();

            return ['ok' => true, 'message' => 'Conversation fermée avec succès.'];
        } catch (PDOException $e) {
            return [
                'ok' => false,
                'error' => 'Erreur de base de données : ' . $e->getMessage()
            ];
        }
    }

    public function getByClientId($clientId)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM conversations WHERE client_id = :client_id");
            $stmt->bindParam(':client_id', $clientId);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [
                'ok' => false,
                'error' => 'Erreur de base de données : ' . $e->getMessage()
            ];
        }
    }

    public function getById($conversationId)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM conversations WHERE id = :id");
            $stmt->bindParam(':id', $conversationId);
            $stmt->execute();

            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [
                'ok' => false,
                'error' => 'Erreur de base de données : ' . $e->getMessage()
            ];
        }
    }
    public function selectRandomAgent($conversationId)
    {
        try {
            $stmt = $this->db->prepare("SELECT id FROM users WHERE user_type = 'agent'");
            $stmt->execute();
            $agents = $stmt->fetchAll(PDO::FETCH_COLUMN);

            if (empty($agents)) {
                return [
                    'ok' => false,
                    'error' => 'Aucun agent disponible.'
                ];
            }

            $agentId = $agents[array_rand($agents)];

            $this->assignAgent($conversationId, $agentId);

            return [
                'ok' => true,
                'agent_id' => $agentId
            ];
        } catch (PDOException $e) {
            return [
                'ok' => false,
                'error' => 'Erreur de base de données : ' . $e->getMessage()
            ];
        }
    }

    public function assignAgent($conversationId, $agentId)
    {
        try {
            $stmt = $this->db->prepare("UPDATE conversations SET agent_id = :agent_id WHERE id = :conversation_id");
            $stmt->bindParam(':agent_id', $agentId);
            $stmt->bindParam(':conversation_id', $conversationId);
            $stmt->execute();

            return ['ok' => true];
        } catch (PDOException $e) {
            return [
                'ok' => false,
                'error' => 'Erreur de base de données : ' . $e->getMessage()
            ];
        }
    }

    public function getTotalConversations()
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM conversations");
        $stmt->execute();
        return $stmt->fetchColumn();
    }


    public function getTotalConversationsByChannel()
    {
        $stmt = $this->db->prepare("SELECT channel, COUNT(*) as total FROM conversations GROUP BY channel");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAverageResponseTime()
    {
        $stmt = $this->db->prepare("SELECT AVG(EXTRACT(EPOCH FROM (started_at - created_at))) as average FROM conversations WHERE started_at IS NOT NULL");
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function getAverageProcessingTime()
    {
        $stmt = $this->db->prepare("SELECT AVG(EXTRACT(EPOCH FROM (closed_at - created_at))) as average FROM conversations WHERE status = 'closed'");
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function getAverageConversationDuration()
    {
        $stmt = $this->db->prepare("SELECT AVG(EXTRACT(EPOCH FROM (closed_at - created_at))) as average FROM conversations WHERE status = 'closed'");
        $stmt->execute();
        return $stmt->fetchColumn();
    }



    public function getTotalConversationsByUser($userId)
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM conversations WHERE agent_id = :userId OR client_id = :userId");
        $stmt->bindParam(':userId', $userId);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function getTotalConversationsByChannelAndUser($userId)
    {
        $stmt = $this->db->prepare("SELECT channel, COUNT(*) as total FROM conversations WHERE agent_id = :userId OR client_id = :userId GROUP BY channel");
        $stmt->bindParam(':userId', $userId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAverageResponseTimeByUser($userId)
    {
        $stmt = $this->db->prepare("SELECT AVG(EXTRACT(EPOCH FROM (started_at - created_at))) as average FROM conversations WHERE (agent_id = :userId OR client_id = :userId) AND started_at IS NOT NULL");
        $stmt->bindParam(':userId', $userId);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function getAverageProcessingTimeByUser($userId)
    {
        $stmt = $this->db->prepare("SELECT AVG(EXTRACT(EPOCH FROM (closed_at - created_at))) as average FROM conversations WHERE (agent_id = :userId OR client_id = :userId) AND status = 'closed'");
        $stmt->bindParam(':userId', $userId);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function getAverageConversationDurationByUser($userId)
    {
        $stmt = $this->db->prepare("SELECT AVG(EXTRACT(EPOCH FROM (closed_at - created_at))) as average FROM conversations WHERE (agent_id = :userId OR client_id = :userId) AND status = 'closed'");
        $stmt->bindParam(':userId', $userId);
        $stmt->execute();
        return $stmt->fetchColumn();
    }
}
