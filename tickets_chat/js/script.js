const socket = new WebSocket('ws://decomobil');

socket.onmessage = (event) => {
  const cambio = JSON.parse(event.data);
  actualizarParteDeLaUI(cambio);
};