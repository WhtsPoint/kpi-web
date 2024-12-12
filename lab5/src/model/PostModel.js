const {Schema, model} = require("mongoose");

const PostModel = model('Post', new Schema({
    id: Schema.ObjectId,
    title: String,
    author: String,
    text: String
}))

module.exports = PostModel