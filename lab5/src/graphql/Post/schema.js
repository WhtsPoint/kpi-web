const {buildSchema} = require("graphql/utilities");

const schema = buildSchema(`
    type Post {
        title: String,
        author: String,
        text: String,
        id: String
    }
    
    type PostId {
        id: String
    }

    type Query {
        posts(limit: Int, offset: Int): [Post]
    }
    
    type Mutation {
        createPost(title: String!, author: String!, text: String!): PostId
        patchPost(id: String!, title: String, text: String): PostId
        deletePost(id: String!): PostId
    }
`)

module.exports = schema