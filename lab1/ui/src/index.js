const chatList = document.getElementById('chatList-names')
const userList = document.getElementById('userList-names')
const messageList = document.getElementById('messageList')

function serverFactory() {
    return new WebSocket('ws://localhost:9502')
}

function resolveMessage(data) {
    try {
        const body = JSON.parse(data)

        const { action, message, error } = body

        if (error) {
            console.error(error)

            return null
        }

        return { action, message }
    } catch {
        return null
    }
}

function chatElementFactory(name, onClick) {
    const el = document.createElement('li')

    el.innerHTML = name
    el.addEventListener('click', onClick)

    return el
}

function messageElementFactory({ text, author, createdAt }) {
    const el = document.createElement('li')

    el.classList.add('message')
    el.innerHTML = `${author.name}: ${text}`

    return el
}

function userElementFactory({ id, name }) {
    const el = document.createElement('li')

    el.setAttribute('id', id)
    el.innerHTML = name

    return el
}

function attachPingRoomsEvent(server, onChatClick) {
    server.addEventListener('open', function () {
        server.send(JSON.stringify({ action: 'ping_chats', message: {} }))
    })

    server.addEventListener('message', function (event) {
        const data = resolveMessage(event.data)

        if (data === null || data['action'] !== 'ping_chats') {
            return
        }

        const { message: { chats } } = data

        chatList.innerHTML = ''

        for (const chat of chats) {
            chatList.appendChild(chatElementFactory(chat, () => onChatClick(chat)))
        }
    })
}

function attachRoomConnectEvent(server) {
    server.addEventListener('message', function (event) {
        const data = resolveMessage(event.data)

        if (data === null || data['action'] !== 'connect_to_chat') {
            return
        }

        const { message: { chat_state: { users } }} = data

        clearUsers()
        clearMessages()
        renderUsers(users)
    })
}

function attachMessageEvent(server) {
    server.addEventListener('message', function (event) {
        const data = resolveMessage(event.data)

        if (data === null || data['action'] !== 'new_message') {
            return
        }

        const { message } = data

        messageList.append(messageElementFactory(message))
    })
}

function attachNewUserConnectedEvent(server) {
    server.addEventListener('message', function (event) {
        const data = resolveMessage(event.data)

        if (data === null || data['action'] !== 'new_connected_user') {
            return
        }

        console.log(data)

        const { message: { user } } = data

        renderUsers([user])
    })
}

function attachDisconnectUserEvent(server) {
    server.addEventListener('message', function (event) {
        const data = resolveMessage(event.data)

        if (data === null || data['action'] !== 'disconnected_user') {
            return
        }

        console.log(data)

        const { message: { user_id } } = data

        purgeUser(user_id)
    })
}

function renderUsers(users) {
    userList.append(...users.map((user) => userElementFactory(user)))
}

function purgeUser(id) {
    [...userList.children].find(user => user.matches(`li[id="${id}"]`))?.remove()
}

function clearUsers() {
    userList.innerHTML = ''
}

function clearMessages() {
    messageList.innerHTML = ''
}

function connectToChatByServer(server) {
    return (chat) => server.send(JSON.stringify({ action: 'connect_to_chat', message: { chat_name: chat } }))
}

function sendMessageWith(server) {
    return (message) => {
        server.send(JSON.stringify({ action: 'send_message', message: { text: message } }))
    }
}

window.onload = async () => {
    const server = serverFactory()
    const connectToChat = connectToChatByServer(server)
    const sendMessage = sendMessageWith(server)

    attachPingRoomsEvent(server, connectToChat)
    attachRoomConnectEvent(server)
    attachMessageEvent(server)
    attachNewUserConnectedEvent(server)
    attachDisconnectUserEvent(server)

    document.getElementById('sendButton').addEventListener('click', ev => {
        sendMessage(document.getElementById('inputField').value)
    })
}