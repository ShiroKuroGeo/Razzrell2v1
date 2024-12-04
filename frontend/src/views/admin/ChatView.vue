`<template>
  <div class="chat-view">
    <div class="chat-sidebar">
      <div class="sidebar-header">
        <button @click="goBack" class="back-btn">
          <i class="fas fa-arrow-left"></i>
          Back to Chat Management
        </button>
      </div>

      <div class="chat-info">
        <h3>Chat Information</h3>
        
        <div class="info-section">
          <h4>Customer Details</h4>
          <div class="info-item">
            <i class="fas fa-user"></i>
            <div>
              <div class="label">Name</div>
              <div class="value">{{ chat.customer.name }}</div>
            </div>
          </div>
          <div class="info-item">
            <i class="fas fa-envelope"></i>
            <div>
              <div class="label">Email</div>
              <div class="value">{{ chat.customer.email }}</div>
            </div>
          </div>
          <div class="info-item">
            <i class="fas fa-phone"></i>
            <div>
              <div class="label">Phone</div>
              <div class="value">{{ chat.customer.phone || 'Not provided' }}</div>
            </div>
          </div>
        </div>

        <div class="info-section">
          <h4>Chat Details</h4>
          <div class="info-item">
            <i class="fas fa-hashtag"></i>
            <div>
              <div class="label">ID</div>
              <div class="value">{{ chat._id }}</div>
            </div>
          </div>
          <div class="info-item">
            <i class="fas fa-tag"></i>
            <div>
              <div class="label">Category</div>
              <div class="value">{{ chat.category }}</div>
            </div>
          </div>
          <div class="info-item">
            <i class="fas fa-flag"></i>
            <div>
              <div class="label">Priority</div>
              <div class="value">{{ chat.priority }}</div>
            </div>
          </div>
          <div class="info-item">
            <i class="fas fa-clock"></i>
            <div>
              <div class="label">Created</div>
              <div class="value">{{ formatDate(chat.createdAt) }}</div>
            </div>
          </div>
          <div class="info-item">
            <i class="fas fa-history"></i>
            <div>
              <div class="label">Last Activity</div>
              <div class="value">{{ formatDate(chat.lastActivity) }}</div>
            </div>
          </div>
        </div>

        <div class="info-section">
          <h4>Agent Details</h4>
          <div v-if="chat.agent" class="agent-details">
            <img :src="chat.agent.avatar" :alt="chat.agent.name" class="agent-avatar">
            <div>
              <div class="agent-name">{{ chat.agent.name }}</div>
              <div class="agent-email">{{ chat.agent.email }}</div>
            </div>
          </div>
          <div v-else class="no-agent">
            No agent assigned
          </div>
        </div>

        <div class="actions">
          <button
            v-if="chat.status !== 'closed'"
            @click="showTransferModal = true"
            class="action-btn"
          >
            <i class="fas fa-exchange-alt"></i>
            Transfer Chat
          </button>
          <button
            v-if="chat.status !== 'closed'"
            @click="showCloseModal = true"
            class="action-btn danger"
          >
            <i class="fas fa-times"></i>
            Close Chat
          </button>
          <button @click="exportChat" class="action-btn">
            <i class="fas fa-download"></i>
            Export Chat
          </button>
        </div>
      </div>
    </div>

    <div class="chat-main">
      <div v-if="loading" class="loading-state">
        <LoadingAnimation />
      </div>

      <ChatHistory
        v-else-if="chat"
        :chat="chat"
        @message-sent="handleMessageSent"
      />

      <div v-else class="not-found">
        Chat not found or you don't have permission to view it
      </div>
    </div>

    <TransferChatModal
      v-if="showTransferModal"
      :chat="chat"
      @close="showTransferModal = false"
      @transfer="handleTransfer"
    />

    <CloseChatModal
      v-if="showCloseModal"
      @close="showCloseModal = false"
      @confirm="handleClose"
    />
  </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { useNotifications } from '@/composables/useNotifications';
import LoadingAnimation from '@/components/ui/LoadingAnimation.vue';
import ChatHistory from '@/components/chat/ChatHistory.vue';
import TransferChatModal from '@/components/chat/TransferChatModal.vue';
import CloseChatModal from '@/components/chat/CloseChatModal.vue';

export default {
  name: 'ChatView',
  components: {
    LoadingAnimation,
    ChatHistory,
    TransferChatModal,
    CloseChatModal
  },
  setup() {
    const route = useRoute();
    const router = useRouter();
    const { showNotification } = useNotifications();

    // State
    const loading = ref(true);
    const chat = ref(null);
    const showTransferModal = ref(false);
    const showCloseModal = ref(false);

    // Methods
    const loadChat = async () => {
      try {
        const response = await fetch(`/api/chats/${route.params.id}`);
        if (!response.ok) throw new Error('Chat not found');
        
        const data = await response.json();
        chat.value = data;
      } catch (error) {
        showNotification('Error loading chat', 'error');
      } finally {
        loading.value = false;
      }
    };

    const goBack = () => {
      router.push('/admin/chats');
    };

    const handleMessageSent = () => {
      loadChat(); // Refresh chat data after new message
    };

    const handleTransfer = async (data) => {
      try {
        const response = await fetch(`/api/chats/${chat.value._id}/transfer`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(data)
        });

        if (!response.ok) throw new Error('Failed to transfer chat');

        showNotification('Chat transferred successfully', 'success');
        showTransferModal.value = false;
        await loadChat();
      } catch (error) {
        showNotification('Error transferring chat', 'error');
      }
    };

    const handleClose = async (data) => {
      try {
        const response = await fetch(`/api/chats/${chat.value._id}/close`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(data)
        });

        if (!response.ok) throw new Error('Failed to close chat');

        showNotification('Chat closed successfully', 'success');
        showCloseModal.value = false;
        await loadChat();
      } catch (error) {
        showNotification('Error closing chat', 'error');
      }
    };

    const exportChat = async () => {
      try {
        const response = await fetch(`/api/chats/${chat.value._id}/export`);
        const blob = await response.blob();
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `chat-${chat.value._id}-${new Date().toISOString()}.txt`;
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);
      } catch (error) {
        showNotification('Error exporting chat', 'error');
      }
    };

    const formatDate = (date) => {
      return new Date(date).toLocaleString();
    };

    // Lifecycle
    onMounted(() => {
      loadChat();
    });

    return {
      loading,
      chat,
      showTransferModal,
      showCloseModal,
      goBack,
      handleMessageSent,
      handleTransfer,
      handleClose,
      exportChat,
      formatDate
    };
  }
};
</script>

<style scoped>
.chat-view {
  display: flex;
  height: 100%;
  background: #F9FAFB;
}

.chat-sidebar {
  width: 320px;
  background: white;
  border-right: 1px solid #E5E7EB;
  display: flex;
  flex-direction: column;
}

.sidebar-header {
  padding: 16px;
  border-bottom: 1px solid #E5E7EB;
}

.back-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 8px 12px;
  background: none;
  border: none;
  color: #4F46E5;
  font-size: 14px;
  cursor: pointer;
  transition: color 0.2s;
}

.back-btn:hover {
  color: #4338CA;
}

.chat-info {
  flex: 1;
  padding: 16px;
  overflow-y: auto;
}

.chat-info h3 {
  margin: 0 0 16px 0;
  font-size: 18px;
  color: #111827;
}

.info-section {
  margin-bottom: 24px;
}

.info-section h4 {
  margin: 0 0 12px 0;
  font-size: 14px;
  color: #6B7280;
}

.info-item {
  display: flex;
  align-items: flex-start;
  gap: 12px;
  margin-bottom: 12px;
}

.info-item i {
  width: 16px;
  color: #9CA3AF;
}

.label {
  font-size: 12px;
  color: #6B7280;
  margin-bottom: 2px;
}

.value {
  font-size: 14px;
  color: #111827;
  word-break: break-all;
}

.agent-details {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px;
  background: #F3F4F6;
  border-radius: 8px;
}

.agent-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  object-fit: cover;
}

.agent-name {
  font-size: 14px;
  font-weight: 500;
  color: #111827;
}

.agent-email {
  font-size: 12px;
  color: #6B7280;
}

.no-agent {
  padding: 12px;
  background: #F3F4F6;
  border-radius: 8px;
  font-size: 14px;
  color: #6B7280;
  text-align: center;
}

.actions {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.action-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  padding: 8px;
  background: #F3F4F6;
  border: none;
  border-radius: 6px;
  color: #374151;
  font-size: 14px;
  cursor: pointer;
  transition: all 0.2s;
}

.action-btn:hover {
  background: #E5E7EB;
}

.action-btn.danger {
  background: #FEE2E2;
  color: #991B1B;
}

.action-btn.danger:hover {
  background: #FEE2E2;
}

.chat-main {
  flex: 1;
  display: flex;
  flex-direction: column;
  background: white;
  margin: 16px;
  border-radius: 8px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}

.loading-state {
  flex: 1;
  display: flex;
  justify-content: center;
  align-items: center;
}

.not-found {
  flex: 1;
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 32px;
  font-size: 16px;
  color: #6B7280;
  text-align: center;
}

@media (max-width: 768px) {
  .chat-view {
    flex-direction: column;
  }

  .chat-sidebar {
    width: 100%;
    height: auto;
    border-right: none;
    border-bottom: 1px solid #E5E7EB;
  }

  .chat-info {
    padding: 12px;
  }

  .chat-main {
    margin: 12px;
  }
}
</style>` 