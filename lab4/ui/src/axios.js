import ax from "axios";

const axios = ax.create({
    baseURL: 'http://localhost:3000',
})

export default axios