# API Documentation

## Introduction

Cette API de gérer une messagerie omnicanale.
Pour démarrer le serveur, exécutez la commande suivante :
php -S localhost:8000

Assurez-vous de configurer correctement les paramètres de la base de données dans le fichier config.php.


## Résumé des Routes

POST /register : Inscription d'un utilisateur
POST /login : Connexion d'un utilisateur
POST /start-conversation : Démarre une conversation entre un client et un agent
GET /get-messages?conversation_id=? : Récupère tous les messages d'une conversation
POST /close-conversation : Ferme une conversation active
POST /add-message : Ajoute un message à une conversation existante
GET /api/dashboard : Récupère des statistiques sur les conversations
GET /api/dashboard?user_id=? : Récupère des statistiques pour un utilisateur spécifique


## Base URL

http://localhost:8000/api

## Points de terminaison

### 1. Inscription d'un utilisateur

**URL :** `/register`  
**Méthode :** `POST`  
**Description :** Crée un nouvel utilisateur avec un nom d'utilisateur et un mot de passe.

**Paramètres :**

| Nom       | Type   | Obligatoire | Description                                                              |
| --------- | ------ | ----------- | ------------------------------------------------------------------------ |
| username  | string | Oui         | L'adresse e-mail de l'utilisateur                                        |
| password  | string | Oui         | Le mot de passe de l'utilisateur                                         |
| user_type | string | Non         | Le type d'utilisateur (`client` ou `agent`). Par défaut, c'est `client`. |

**Exemple de requête :**

```http
POST /api/register
Content-Type: application/json

{
  "username": "test@gmail.com",
  "password": "testpassword",
  "user_type": "agent"
}
```

### 2. Connexion d'un utilisateur

**URL :** `/login`  
**Méthode :** `POST`  
**Description :** Authentifie un utilisateur en utilisant un nom d'utilisateur et un mot de passe. La réponse renvoie dans data, les informations de l'utilisateur connecté.

**Paramètres :**

| Nom      | Type   | Obligatoire | Description                       |
| -------- | ------ | ----------- | --------------------------------- |
| username | string | Oui         | L'adresse e-mail de l'utilisateur |
| password | string | Oui         | Le mot de passe de l'utilisateur  |

**Exemple de requête :**

```http
POST /api/login
Content-Type: application/json

{
  "username": "test@gmail.com",
  "password": "testpassword"
}
```

### 3. Démarrer une conversation

**URL :** `/start-conversation`  
**Méthode :** `POST`  
**Description :** Démarre une nouvelle conversation entre un client et un agent. Le contenu du message initial est obligatoire.
**Autorisation :** Cette requête nécessite une authentification. Les utilisateurs doivent inclure un en-tête `Authorization` contenant un credentials (Basic) dans leur requête.

**En-tête d'autorisation :**

````http
Authorization: Bearer <token>

**Paramètres :**

| Nom            | Type   | Obligatoire | Description                                                                 |
| -------------- | ------ | ----------- | --------------------------------------------------------------------------- |
| client_id      | string | Oui         | L'identifiant unique du client qui initie la conversation.                 |
| message_content | string | Oui         | Le contenu du message initial envoyé par le client. Ce champ est obligatoire. |
| channel        | string | Oui         | Le canal par lequel la conversation est initiée (ex. : chat, email, etc.). |

**Exemple de requête :**

```http
POST /api/start-conversation
Content-Type: application/json

{
  "client_id": "12345",
  "message_content": "Bonjour, j'ai besoin d'aide.",
  "channel": "chat"
}
````

## 4. Liste de tous les messages

**URL :** `/get-messages?conversation_id=?`  
**Méthode :** `GET`  
**Description :** Récupère tous les messages associés à une conversation, ainsi que les informations sur la conversation, et l'agent.

### Paramètres

| Nom               | Type   | Obligatoire | Description                                                                      |
| ----------------- | ------ | ----------- | -------------------------------------------------------------------------------- |
| `conversation_id` | string | Oui         | L'identifiant unique de la conversation dont on souhaite récupérer les messages. |

## 5. Fermer une conversation

**URL :** `/close-conversation`  
**Méthode :** `POST`  
**Description :** Permet à un agent de fermer une conversation active.

**Autorisation :** Cette requête nécessite une authentification. Les utilisateurs doivent inclure un en-tête `Authorization` contenant un credentials (Basic) dans leur requête.

**En-tête d'autorisation :**

```http
Authorization: Bearer <token>
## 5. Fermer une conversation

**URL :** `/close-conversation`
**Méthode :** `POST`
**Description :** Permet à un agent de fermer une conversation active.

**Autorisation :** Cette requête nécessite une authentification. Les utilisateurs doivent inclure un en-tête `Authorization` contenant un credentials (Basic) dans leur requête.

**En-tête d'autorisation :**
```

## 6. Ajouter un message à une conversation

**URL :** `/add-message`  
**Méthode :** `POST`  
**Description :** Permet d'ajouter un message à une conversation existante, à condition que celle-ci ne soit pas fermée.

**Autorisation :** Cette requête nécessite une authentification. Les utilisateurs doivent inclure un en-tête `Authorization` contenant un token (Basic) dans leur requête.

**Paramètres :**

| Nom             | Type   | Obligatoire | Description                                                               |
| --------------- | ------ | ----------- | ------------------------------------------------------------------------- |
| conversation_id | string | Oui         | L'identifiant unique de la conversation à laquelle le message est ajouté. |
| sender          | string | Oui         | Le type d'expéditeur (ex. : 'client' ou 'agent').                         |
| message_content | string | Oui         | Le contenu du message à ajouter. Ce champ est obligatoire.                |



## 7. Statistiques sur les conversations

**URL :** `/api/dashboard`  
**Méthode :** `GET`  
**Description :** Récupère des statistiques sur les conversations, y compris le nombre total de conversations, le temps d'attente moyen d'un client, le temps de traitement moyen et la durée moyenne des conversations.

**Autorisation :** Cette requête nécessite une authentification. Les utilisateurs doivent inclure un en-tête `Authorization` contenant un credentials (Bearer token) dans leur requête.



## 8. Statistiques sur les conversations d'un utilisateur

**URL :** `/api/dashboard` ou `/api/dashboard?user_id=?`  
**Méthode :** `GET`  
**Description :** Récupère des statistiques sur les conversations, soit pour tous les utilisateurs, soit pour un utilisateur spécifique s'il est spécifié, y compris le nombre total de conversations, le temps d'attente moyen, le temps de traitement moyen et la durée moyenne des conversations.

**Paramètres :**

| Nom       | Type    | Description                              |
|-----------|---------|------------------------------------------|
| `user_id` | integer | (Optionnel) L'identifiant de l'utilisateur. |

**Autorisation :** Cette requête nécessite une authentification. Les utilisateurs doivent inclure un en-tête `Authorization` contenant un credentials (Bearer token) dans leur requête.

**En-tête d'autorisation :**

```http
Authorization: Bearer <token>