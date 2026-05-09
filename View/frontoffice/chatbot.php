<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GreenBot - Assistant Green Assurance</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #e8f0e8 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .chat-wrapper {
            max-width: 600px;
            width: 90%;
            margin: 50px auto;
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .chat-header {
            background: #6b8e23;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .chat-header h2 { font-size: 1.4rem; }
        .chat-header p { font-size: 0.85rem; opacity: 0.9; margin-top: 5px; }

        #chatMessages {
            height: 450px;
            overflow-y: auto;
            padding: 20px;
            background: #f9f9f9;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .message {
            max-width: 80%;
            padding: 12px 16px;
            border-radius: 15px;
            font-size: 0.95rem;
            line-height: 1.5;
        }

        .message.bot {
            background: #ffffff;
            color: #333;
            align-self: flex-start;
            border: 1px solid #e0e0e0;
            border-bottom-left-radius: 2px;
        }

        .message.user {
            background: #6b8e23;
            color: white;
            align-self: flex-end;
            border-bottom-right-radius: 2px;
        }

        .message.error {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffc107;
            align-self: flex-start;
        }

        #typingIndicator {
            display: none;
            padding: 10px 20px;
            font-style: italic;
            font-size: 0.85rem;
            color: #6b8e23;
        }

        .chat-input-area {
            padding: 20px;
            background: white;
            border-top: 1px solid #eee;
            display: flex;
            gap: 10px;
        }

        .chat-input-area input {
            flex: 1;
            padding: 12px 20px;
            border: 1px solid #ddd;
            border-radius: 25px;
            outline: none;
            font-size: 0.95rem;
            transition: 0.3s;
        }

        .chat-input-area input:focus { border-color: #6b8e23; }

        .chat-input-area input:disabled { background: #f5f5f5; cursor: not-allowed; }

        .chat-input-area button {
            background: #6b8e23;
            color: white;
            border: none;
            width: 45px;
            height: 45px;
            border-radius: 50%;
            cursor: pointer;
            transition: 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .chat-input-area button:hover:not(:disabled) { background: #556b2f; transform: scale(1.05); }
        .chat-input-area button:disabled { background: #aaa; cursor: not-allowed; transform: none; }

        #chatMessages::-webkit-scrollbar { width: 6px; }
        #chatMessages::-webkit-scrollbar-thumb { background: #ccc; border-radius: 10px; }
    </style>
</head>
<body>

<div class="chat-wrapper">
    <div class="chat-header">
        <h2><i class="fas fa-leaf"></i> GreenBot</h2>
        <p>Votre assistant assurance éco-responsable 24/7</p>
    </div>

    <div id="chatMessages">
        <div class="message bot">
            Bonjour ! Je suis GreenBot 🌿. Comment puis-je vous aider dans vos démarches d'assurance aujourd'hui ?
        </div>
    </div>

    <div id="typingIndicator">
        <i class="fas fa-circle-notch fa-spin"></i> GreenBot réfléchit...
    </div>

    <div class="chat-input-area">
        <input type="text" id="userInput" placeholder="Posez votre question ici..." onkeypress="handleKey(event)">
        <button id="sendBtn" onclick="sendMessage()"><i class="fas fa-paper-plane"></i></button>
    </div>
</div>

<script>
    const chatMessages = document.getElementById('chatMessages');
    const typingIndicator = document.getElementById('typingIndicator');
    const userInput = document.getElementById('userInput');
    const sendBtn = document.getElementById('sendBtn');

    // ✅ Adaptez ce chemin selon votre structure de dossiers
    const API_URL = '../api/chatbot.php';

    async function sendMessage() {
        const message = userInput.value.trim();
        if (!message) return;

        // Désactiver l'interface pendant la requête
        setLoading(true);

        appendMessage(message, 'user');
        userInput.value = '';

        typingIndicator.style.display = 'block';
        scrollToBottom();

        try {
            const response = await fetch(API_URL, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ message: message })
            });

            const data = await response.json();
            typingIndicator.style.display = 'none';

            if (data.success) {
                appendMessage(data.response, 'bot');
            } else {
                appendMessage("❌ Erreur : " + (data.error || "Inconnue"), 'error');
            }
        } catch (error) {
            typingIndicator.style.display = 'none';
            appendMessage("❌ Impossible de contacter le serveur. Vérifiez qu'Apache est bien lancé.", 'error');
            console.error('Fetch Error:', error);
        } finally {
            setLoading(false);
        }
    }

    function setLoading(isLoading) {
        userInput.disabled = isLoading;
        sendBtn.disabled = isLoading;
        if (!isLoading) userInput.focus();
    }

    function appendMessage(text, type) {
        const msgDiv = document.createElement('div');
        msgDiv.className = `message ${type}`;
        // Convertir les sauts de ligne et les ** en gras
        msgDiv.innerHTML = text
            .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
            .replace(/\n/g, '<br>');
        chatMessages.appendChild(msgDiv);
        scrollToBottom();
    }

    function scrollToBottom() {
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    function handleKey(e) {
        if (e.key === 'Enter') sendMessage();
    }
</script>

</body>
</html>