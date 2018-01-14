import axios from 'axios'

const IS_DEV = process.env.NODE_ENV === 'development'
const APP_URL = encodeURI(IS_DEV ? 'http://localhost:8080/#/' : 'https://notes.royallib.pw')
const BASE_URL = IS_DEV ? 'http://org.loc/' : 'https://www.auth.royallib.pw/'
const AUTH_URL = BASE_URL + 'auth/?land-to=' + APP_URL

let Repository = function (parameters) {
  let list = []

  let config = (function (params) {
    if (!arguments) {
      throw new Error('No parameters set')
    }

    return {
      host: BASE_URL,
      perPage: 10
    }
  })(parameters)

  let client = axios.create({
    baseURL: config.host,
    timeout: 5000,
    withCredentials: true,
    headers: {
      'Accept': 'application/json',
      'Content-Type': 'application/json; charset=UTF-8',
      'X-Requested-With': 'XMLHttpRequest'
    }
  })

  client.get('/storage')
    .then(function (res) {
      list = JSON.parse(res.data.data)
    })
    .catch(function (res) {
      if (res.message === 'Request failed with status code 403') {
        window.location.replace(AUTH_URL)
      }
    })

  this.search = function (query, page) {
    let res = {}

    res.items = list.slice((page - 1) * config.perPage, config.perPage)
    res.pages = Math.ceil(list.length / config.perPage)

    return res
  }

  this.create = function (body) {
    body['_id'] = Date.now()
    list.push(body)

    this.save(list)
  }

  this.update = function (index, body) {
    list[index]['title'] = body.title
    list[index]['content'] = body.content

    this.save(list)
  }

  this.delete = function (index) {
    list.splice(index, 1)

    this.save(list)
  }

  this.save = function (data) {
    return client.post('/storage', {
      data: JSON.stringify(data)
    })
  }
}

export default Repository
