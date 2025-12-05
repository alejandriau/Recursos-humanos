
<style>
        .chat-container {
            width: 95%;
            height: 90vh;
            display: flex;
            flex-direction: column;
            background: rgba(15, 23, 42, 0.9);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow:
                0 10px 30px rgba(0, 0, 0, 0.5),
                0 0 0 1px rgba(99, 102, 241, 0.2);
            overflow: hidden;
            position: relative;
        }

        .chat-header {
            padding: 20px 25px;
            background: rgba(15, 23, 42, 0.95);
            border-bottom: 1px solid rgba(99, 102, 241, 0.15);
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .avatar {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
            box-shadow: 0 4px 10px rgba(99, 102, 241, 0.3);
        }

        .header-info h1 {
            font-size: 1.4rem;
            font-weight: 700;
            color: white;
            letter-spacing: -0.5px;
        }

        .header-info p {
            font-size: 0.9rem;
            color: #94a3b8;
            margin-top: 3px;
            font-weight: 300;
        }

        .status-indicator {
            display: flex;
            align-items: center;
            gap: 8px;
            background: rgba(16, 185, 129, 0.1);
            padding: 6px 12px;
            border-radius: 20px;
            border: 1px solid rgba(16, 185, 129, 0.2);
        }

        .status-dot {
            width: 10px;
            height: 10px;
            background: #10b981;
            border-radius: 50%;
            box-shadow: 0 0 8px rgba(16, 185, 129, 0.5);
        }

        .status-text {
            font-size: 0.85rem;
            color: #10b981;
            font-weight: 500;
        }

        .chat-body {
            flex: 1;
            padding: 25px;
            overflow-y: auto;
            background: rgba(15, 23, 42, 0.8);
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .message {
            max-width: 80%;
            padding: 16px 20px;
            border-radius: 18px;
            font-size: 0.95rem;
            line-height: 1.5;
            position: relative;
            animation: fadeIn 0.3s ease-out;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .user {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            color: white;
            margin-left: auto;
            text-align: right;
            border-top-right-radius: 5px;
            border-bottom-left-radius: 18px;
        }

        .bot {
            background: rgba(30, 41, 59, 0.8);
            color: #e2e8f0;
            border: 1px solid rgba(99, 102, 241, 0.2);
            align-self: flex-start;
            border-top-left-radius: 5px;
            border-bottom-right-radius: 18px;
        }

        .message-time {
            font-size: 0.7rem;
            margin-top: 8px;
            opacity: 0.7;
            text-align: right;
            color: rgba(255, 255, 255, 0.6);
        }

        .chat-footer {
            padding: 20px;
            background: rgba(15, 23, 42, 0.95);
            border-top: 1px solid rgba(99, 102, 241, 0.15);
        }

        .chat-input {
            display: flex;
            gap: 12px;
            position: relative;
        }

        #message {
            flex: 1;
            padding: 16px 20px;
            padding-right: 50px;
            border: none;
            border-radius: 16px;
            background: rgba(30, 41, 59, 0.8);
            font-size: 0.95rem;
            color: #e2e8f0;
            transition: all 0.2s ease;
            box-shadow:
                inset 0 1px 3px rgba(0, 0, 0, 0.1),
                0 0 0 1px rgba(99, 102, 241, 0.2);
        }

        #message:focus {
            outline: none;
            background: rgba(30, 41, 59, 1);
            box-shadow:
                inset 0 1px 4px rgba(0, 0, 0, 0.15),
                0 0 0 2px rgba(99, 102, 241, 0.4);
        }

        #message::placeholder {
            color: #64748b;
        }

        .chat-input button {
            background: linear-gradient(to right, #6366f1, #8b5cf6);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 16px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);
        }

        .chat-input button:hover {
            background: linear-gradient(to right, #4f46e5, #7c3aed);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(99, 102, 241, 0.4);
        }

        .chat-input button:active {
            transform: translateY(0);
        }

        .typing-indicator {
            display: none;
            align-self: flex-start;
            background: rgba(30, 41, 59, 0.8);
            padding: 15px 20px;
            border-radius: 18px;
            border: 1px solid rgba(99, 102, 241, 0.2);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            color: #94a3b8;
            font-style: italic;
        }

        .typing-dots {
            display: flex;
            gap: 5px;
        }

        .typing-dot {
            width: 8px;
            height: 8px;
            background: #94a3b8;
            border-radius: 50%;
            animation: typing 1.2s infinite;
        }

        .typing-dot:nth-child(2) {
            animation-delay: 0.2s;
        }

        .typing-dot:nth-child(3) {
            animation-delay: 0.4s;
        }

        @keyframes typing {
            0%, 60%, 100% { transform: translateY(0); }
            30% { transform: translateY(-5px); }
        }

        .chat-title {
            text-align: center;
            font-size: 1.6rem;
            margin-bottom: 5px;
            color: #c084fc;
            font-weight: 700;
            text-shadow: 0 0 10px rgba(192, 132, 252, 0.5);
            letter-spacing: -0.5px;
        }

        .chat-title i {
            margin-right: 10px;
            color: #8b5cf6;
        }

        #chatbox {
            height: 400px;
            overflow-y: auto;
            border-radius: 15px;
            padding: 15px;
            background: rgba(15, 23, 42, 0.5);
            margin-bottom: 15px;
            border: 1px solid rgba(99, 102, 241, 0.2);
        }

        /* Decoración de partículas */
        .particles {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: -1;
        }

        .particle {
            position: absolute;
            background: rgba(99, 102, 241, 0.15);
            border-radius: 50%;
            animation: float 15s infinite linear;
        }

        @keyframes float {
            0% { transform: translateY(0) translateX(0) rotate(0deg); opacity: 0; }
            10% { opacity: 1; }
            90% { opacity: 0.5; }
            100% { transform: translateY(-100px) translateX(100px) rotate(360deg); opacity: 0; }
        }

        /* Scrollbar styling */
        #chatbox::-webkit-scrollbar {
            width: 6px;
        }

        #chatbox::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.1);
        }

        #chatbox::-webkit-scrollbar-thumb {
            background: rgba(99, 102, 241, 0.5);
            border-radius: 10px;
        }

        #chatbox::-webkit-scrollbar-thumb:hover {
            background: rgba(99, 102, 241, 0.8);
        }

        /* Animación de entrada */
        @keyframes slideIn {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .chat-container {
            animation: slideIn 0.5s ease-out;
        }
</style>

    <div class="chat-container">
        <div class="chat-header">
            <div class="header-left">
                <div class="avatar">
                    <i class="fas fa-robot"></i>
                </div>
                <div class="header-info">
                    <h1>Bueno Bozo</h1>
                    <p>Asistente Virtual Inteligente</p>
                </div>
            </div>
            <div class="status-indicator">
                <div class="status-dot"></div>
                <div class="status-text">En línea</div>
            </div>
        </div>

        <div class="chat-title">
            <i class="fas fa-comments"></i>Asistente Virtual
        </div>

        <div id="chatbox">
            <div class="bot message">
                ¡Hola! Soy Bueno Bozo, tu asistente virtual. Estoy aquí para ayudarte con cualquier consulta que tengas. ¿En qué puedo asistirte hoy?
                <div class="message-time">10:30 AM</div>
            </div>
        </div>

        <div class="chat-footer">
            <div class="chat-input">
                <input type="text" id="message" placeholder="Escribe tu mensaje...">
                <button onclick="sendMessage()">Enviar</button>
            </div>
        </div>
    </div>

<script>

</script>


