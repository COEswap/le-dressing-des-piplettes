// EventBus pour la communication entre composants
export default {
  callbacks: {},
  
  on(event, callback) {
    if (!this.callbacks[event]) {
      this.callbacks[event] = [];
    }
    this.callbacks[event].push(callback);
  },
  
  off(event, callback) {
    if (this.callbacks[event]) {
      this.callbacks[event] = this.callbacks[event].filter(cb => cb !== callback);
    }
  },
  
  emit(event, ...args) {
    if (this.callbacks[event]) {
      this.callbacks[event].forEach(callback => callback(...args));
    }
  }
};
