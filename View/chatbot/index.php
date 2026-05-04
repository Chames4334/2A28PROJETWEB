<?php
// Chatbot Interface View
?>
<style>
.chatbot-container {
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 1000;
}

.chatbot-toggle {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(135deg, #6FAF4C, #A67C52);
    border: none;
    color: white;
    font-size: 24px;
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    transition: transform 0.3s;
}

.chatbot-toggle:hover {
    transform: scale(1.1);
}

.chatbot-box {
    display: none;
    position: absolute;
    bottom: 80px;
    right: 0;
    width: 380px;
    height: 500px;
    background: white;
    border-radius: 15px;
    box-shadow: 0 5px 25px rgba(0,0,0,0.2);
    overflow: hidden;
}

.chatbot-box.active {
    display: flex;
    flex-direction: column;
}

.chatbot-header {
    background: linear-gradient(135deg, #6FAF4C, #A67C52);
    color: white;
    padding: 15px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.chatbot-header h3 {
    margin: 0;
    font-size: 16px;
}

.chatbot-close {
    background: none;
    border: none;
    color: white;
    font-size: 20px;
    cursor: pointer;
}

.chatbot-messages {
    flex: 1;
    padding: 15px;
    overflow-y: auto;
    background: #f8f9fa;
}

.message {
    max: 80%;
    padding: 10px 15px;
    border-radius: 15px;
    margin-bottom: 10px;
    font-size: 14px;
    line-height: 1.4;
}

.message.user {
    background: #6FAF4C;
    color: white;
    margin-left: auto;
    border-bottom-right-radius: 3px;
}

.message.assistant {
    background: white;
    color: #333;
    border: 1px solid #ddd;
    margin-right: auto;
    border-bottom-left-radius: 3px;
}

.message.typing {
    background: #e9ecef;
    color: #666;
    font-style: italic;
}

.chatbot-input {
    padding: 15px;
    border-top: 1px solid #ddd;
    display: flex;
    gap: 10px;
}

.chatbot-input input {
    flex: 1;
    padding: 10px 15px;
    border: 1px solid #ddd;
    border-radius: 25px;
    outline: none;
    font-size: 14px;
}

.chatbot-input input:focus {
    border-color: #6FAF4C;
}

.chatbot-input button {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #6FAF4C;
    border: none;
    color: white;
    cursor: pointer;
    transition: background 0.3s;
}

.chatbot-input button:hover {
    background: #5a8f3d;
}

.chatbot-input button:disabled {
    background: #ccc;
    cursor: not-allowed;
}

.chatbot-errors {
    padding: 10px 15px;
    background: #f8d7da;
    color: #721c24;
    font-size: 12px;
    display: none;
}
</style>

<div class="chatbot-container">
    <button class="chatbot-toggle" onclick="toggleChatbot()">
        💬
    </button>
    
    <div class="chatbot-box" id="chatbotBox">
        <div class="chatbot-header">
            <h3>🤖 Assistant AS Assurance</h3>
            <button class="chatbot-close" onclick="toggleChatbot()">×</button>
        </div>
        
        <div class="chatbot-errors" id="chatbotErrors"></div>
        
        <div class="chatbot-messages" id="chatbotMessages">
            <div class="message assistant">
                Bonjour! 👋<br><br>
                Je suis l'assistant virtuel d'AS Assurance.<br><br>
                Je peux vous aider à:<br>
                • Déclarer un sinistre<br>
                • Suivre votre dossier<br>
                • Obtenir des informations<br>
                • Contacter le service client<br><br>
                Comment puis-je vous aider?
            </div>
        </div>
        
        <div class="chatbot-input">
            <input type="text" id="chatbotInput" placeholder="Tapez votre message..." 
                   onkeypress="if(event.key==='Enter')sendMessage()">
            <button id="chatbotSendBtn" onclick="sendMessage()">➤</button>
        </div>
    </div>
</div>

<script>
let conversationId = null;
let isSending = false;

function toggleChatbot() {
    const box = document.getElementById('chatbotBox');
    box.classList.toggle('active');
}

function sendMessage() {
    if (isSending) return;
    
    const input = document.getElementById('chatbotInput');
    const message = input.value.trim();
    
    if (!message) return;
    
    isSending = true;
    document.getElementById('chatbotSendBtn').disabled = true;
    
    // Add user message
    addMessage(message, 'user');
    input.value = '';
    
    // Show typing indicator
    showTyping();
    
    // Send to backend
    const formData = new FormData();
    formData.append('message', message);
    if (conversationId) {
        formData.append('conversation_id', conversationId);
    }
    
    fetch('index.php?action=chatbot_send', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        hideTyping();
        
        if (data.success) {
            // Update conversation ID
            if (data.conversation_id) {
                conversationId = data.conversation_id;
            }
            
            // Add assistant response
            addMessage(data.response, 'assistant');
        } else {
            showError(data.error || data.fallback || 'Erreur de communication');
        }
    })
    .catch(error => {
        hideTyping();
        showError('Erreur de connexion. Veuillez réessayer.');
    })
    .finally(() => {
        isSending = false;
        document.getElementById('chatbotSendBtn').disabled = false;
    });
}

function addMessage(text, sender) {
    const container = document.getElementById('chatbotMessages');
    const div = document.createElement('div');
    div.className = 'message ' + sender;
    div.innerHTML = text.replace(/\n/g, '<br>');
    container.appendChild(div);
    container.scrollTop = container.scrollHeight;
}

function showTyping() {
    const container = document.getElementById('chatbotMessages');
    const div = document.createElement('div');
    div.className = 'message typing';
    div.id = 'typingIndicator';
    div.innerHTML = '⏳ Assistant en train d\'écrire...';
    container.appendChild(div);
    container.scrollTop = container.scrollHeight;
}

function hideTyping() {
    const indicator = document.getElementById('typingIndicator');
    if (indicator) {
        indicator.remove();
    }
}

function showError(message) {
    const errorDiv = document.getElementById('chatbotErrors');
    errorDiv.textContent = message;
    errorDiv.style.display = 'block';
    
    setTimeout(() => {
        errorDiv.style.display = 'none';
    }, 5000);
}
</script>

<!-- Floating button for pages that include this -->
<?php if (!isset($hideChatbotButton)): ?>
<script>
// Auto-show chatbot button on all pages
document.addEventListener('DOMContentLoaded', function() {
    // Already included via PHP include
});
</script>
<?php endif; ?>