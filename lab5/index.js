const express = require("express");
const { createHandler } = require("graphql-http/lib/use/express")
const schema = require("./src/graphql/Post/schema");
const resolver = require("./src/graphql/Post/resolver");
const mongoose = require("mongoose");

const app = express()

mongoose.connect(process.env.MONGODB_URI || 'mongodb://localhost:27017/app')

app.all(
    '/graphql',
    createHandler({
        schema: schema,
        rootValue: resolver,
        formatError: (error) => {
            return { message: error }
        }
    })
)

app.listen(4000)
console.log("Running a GraphQL API server at http://localhost:4000/graphql")