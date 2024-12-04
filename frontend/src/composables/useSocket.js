import { ref, onMounted, onUnmounted } from 'vue';
import { io } from 'socket.io-client';
import { useAuth } from './useAuth';
import { useNotifications } from './useNotifications';

const socket = ref(null);
const isConnected = ref(false);
const reconnectAttempts = ref(0);
const maxReconnectAttempts = 5;

export function useSocket() {
  const { user, getToken } = useAuth();
  const { showNotification } = useNotifications();

  const initSocket = async () => {
    try {
      const token = await getToken();
      
      socket.value = io(import.meta.env.VITE_SOCKET_URL, {
        auth: {
          token
        },
        reconnection: true,
        reconnectionAttempts: maxReconnectAttempts,
        reconnectionDelay: 1000,
        reconnectionDelayMax: 5000,
        timeout: 20000
      });

      setupSocketListeners();
    } catch (error) {
      console.error('Socket initialization failed:', error);
      showNotification('Failed to connect to chat server', 'error');
    }
  };

  const setupSocketListeners = () => {
    if (!socket.value) return;

    socket.value.on('connect', () => {
      isConnected.value = true;
      reconnectAttempts.value = 0;
      console.log('Socket connected');
    });

    socket.value.on('disconnect', (reason) => {
      isConnected.value = false;
      console.log('Socket disconnected:', reason);
      
      if (reason === 'io server disconnect') {
        // Server initiated disconnect, attempt to reconnect
        socket.value.connect();
      }
    });

    socket.value.on('connect_error', (error) => {
      console.error('Socket connection error:', error);
      reconnectAttempts.value++;

      if (reconnectAttempts.value >= maxReconnectAttempts) {
        showNotification('Unable to connect to chat server. Please try again later.', 'error');
      }
    });

    socket.value.on('error', (error) => {
      console.error('Socket error:', error);
      showNotification('An error occurred with the chat connection', 'error');
    });
  };

  const joinChat = (chatId) => {
    if (!isConnected.value || !socket.value) return;
    socket.value.emit('join_chat', { chatId });
  };

  const leaveChat = (chatId) => {
    if (!isConnected.value || !socket.value) return;
    socket.value.emit('leave_chat', { chatId });
  };

  const sendMessage = (chatId, content) => {
    if (!isConnected.value || !socket.value) return;
    
    socket.value.emit('message', {
      chatId,
      content,
      sender: user.value?._id,
      timestamp: new Date()
    });
  };

  const emitTyping = (chatId, isTyping) => {
    if (!isConnected.value || !socket.value) return;
    socket.value.emit('typing', { chatId, isTyping });
  };

  const onNewMessage = (callback) => {
    if (!socket.value) return;
    socket.value.on('new_message', callback);
  };

  const onTypingStatus = (callback) => {
    if (!socket.value) return;
    socket.value.on('typing_status', callback);
  };

  const onAgentAssigned = (callback) => {
    if (!socket.value) return;
    socket.value.on('agent_assigned', callback);
  };

  const onAgentLeft = (callback) => {
    if (!socket.value) return;
    socket.value.on('agent_left', callback);
  };

  const onChatClosed = (callback) => {
    if (!socket.value) return;
    socket.value.on('chat_closed', callback);
  };

  const disconnect = () => {
    if (socket.value) {
      socket.value.disconnect();
      socket.value = null;
      isConnected.value = false;
    }
  };

  onMounted(() => {
    if (user.value) {
      initSocket();
    }
  });

  onUnmounted(() => {
    disconnect();
  });

  return {
    isConnected,
    joinChat,
    leaveChat,
    sendMessage,
    emitTyping,
    onNewMessage,
    onTypingStatus,
    onAgentAssigned,
    onAgentLeft,
    onChatClosed,
    disconnect
  };
} 