const PostModel = require('../../model/PostModel')

const resolver = {
    posts: async ({ limit, offset }, _, info) => {
        const fields = info.fieldNodes[0].selectionSet.selections.map(selection => selection.name.value)

        limit = Math.min(100, Math.max(0, limit || 10))
        offset = Math.max(0, offset)

        let filter = {}

        fields.forEach(field => filter[field] = 1)

        const posts = await PostModel.find(
            null, fields, { limit, skip: offset }
        )

        return posts.map(({ title, author, text, _id: id }) => ({ title, author, text, id}))
    },
    createPost: async ({ title, author, text }) => {
        const post = new PostModel({ title, author, text })

        await post.save()

        return { id: post._id }
    },
    patchPost: async ({ id, title, text }) => {
        if (id.length !== 24) {
            throw new Error('Invalid id')
        }

        let updates = {}

        title && (updates['title'] = title)
        text && (updates['text'] = text)

        const result = await PostModel.updateOne({ _id: id }, { '$set': updates })

        if (result.matchedCount === 0) {
            throw new Error('Post not found')
        }

        return { id }
    },
    deletePost: async ({ id }) => {
        if (id.length !== 24) {
            throw new Error('Invalid id')
        }

        const result = await PostModel.deleteOne({ _id: id })

        if (result.deletedCount === 0) {
            throw new Error('Post not found')
        }

        return { id }
    }
}


module.exports = resolver