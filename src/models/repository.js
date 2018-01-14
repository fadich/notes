import axios from 'axios'

const IS_DEV = process.env.NODE_ENV === 'development'
const APP_URL = encodeURI(IS_DEV ? 'http://localhost:8080/#/' : 'https://notes.royallib.pw')
const BASE_URL = IS_DEV ? 'http://org.loc/' : 'https://www.org.royallib.pw/'
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

  // let serialize = function (obj) {
  //   let str = []
  //   for (let p in obj) {
  //     if (obj.hasOwnProperty(p)) {
  //       str.push(encodeURIComponent(p) + '=' + encodeURIComponent(obj[p]))
  //     }
  //   }
  //
  //   return str.join('&')
  // }

  this.search = function (query, page) {
    let res = {}

    res.items = list.slice((page - 1) * config.perPage, config.perPage)
    res.pages = Math.ceil(list.length / config.perPage)

    return res

    /*
    client.search(params, function () {
      if (page > 1) {
        for (let key in body.hits.hits) {
          let hit = body.hits.hits.hasOwnProperty(key) ? body.hits.hits[key] : null

          if (hit) {
            entities.push(hit)
          }
        }
      } else {
        pages = Math.ceil(body.hits.total / 10)
        entities = body.hits.hits
      }
    }, function (error) {
      console.trace(error.message)
    })
    */
  }

  this.create = function (body) {
    this.save([body])
    // return client.post('/', serialize(body))
  }

  this.update = function (id, body) {
    // return client.post('/' + id, serialize(body))
  }

  this.delete = function (id) {
    // return client.delete('/' + id)

    // return client.post('/delete?id=' + id)
  }

  this.save = function (data) {
    return client.post('/storage', {
      data: JSON.stringify(data)
    })
  }
}

export default Repository
