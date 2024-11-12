window.onload = () => {
    const socket = new WebSocket(`ws://127.0.0.1:9502`);

    socket.addEventListener('message', (event) => {
        console.log(event.data)
    })
}