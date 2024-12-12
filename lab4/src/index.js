const express = require('express')
const PostModel = require('./model/PostModel')
const mongoose = require('mongoose')
const bodyParser = require('body-parser')
const cors = require('cors')

mongoose.connect(process.env.MONGODB_URI || 'mongodb://localhost:27017/app')

const app = express()

app.use(bodyParser.json())
app.use(cors({ origin: 'http://localhost:5173' }))

app.get('/api/posts', async (req, res) => {
    const limit = typeof req.query.limit === 'string' ? Math.min(100, Math.max(0, parseInt(req.query.limit))) : 10
    const offset = typeof req.query.offset === 'string' ? Math.max(0, parseInt(req.query.offset)) : 0

    const posts = await PostModel.find(null, null, { limit, skip: offset })

    res.json(posts.map(({ title, author, text, _id: id }) => ({ title, author, text, id})))
})

app.post('/api/post', async (req, res) => {
    const { title, author, text } = req.body || {}

    if (typeof title !== 'string' || typeof author !== 'string' || typeof text !== 'string') {
        res.contentType('application/problem+json').status(422).json({ error: 'Invalid payload' })

        return
    }

    const post = new PostModel({ title, author, text })

    await post.save()

    res.status(201).json({ id: post._id })
})

app.patch('/api/post/:id([a-z0-9]{24})', async (req, res) => {
    const { id } = req.params
    const { title, text } = req.body || {}
    let updates = {}

    if (typeof title === 'string') updates['title'] = title
    if (typeof text === 'string') updates['text'] = text

    const result = await PostModel.updateOne({ _id: id }, { '$set': updates })

    if (result.matchedCount === 0) {
        res.contentType('application/problem+json').status(404).json({ error: 'Post not found' })

        return
    }

    res.sendStatus(204)
})

app.delete('/api/post/:id([a-z0-9]{24})', async (req, res) => {
    const { id } = req.params

    const result = await PostModel.deleteOne({ _id: id })

    if (result.deletedCount === 0) {
        res.contentType('application/problem+json').status(404).json({ error: 'Post not found' })

        return
    }

    res.sendStatus(204)
})

app.listen(process.env.PORT || 3000)