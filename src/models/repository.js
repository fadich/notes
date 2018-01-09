import axios from 'axios'

let Repository = function (parameters) {
  let config = (function (params) {
    if (!arguments) {
      throw new Error('No parameters set')
    }

    return {
      host: params.host || null,
      perPage: 10
    }
  })(parameters)

  let client = axios.create({
    baseURL: config.host,
    timeout: 2000,
    withCredentials: true,
    headers: {
      'Accept': 'application/json',
      'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
      'X-Requested-With': 'XMLHttpRequest'
    }
  })

  let serialize = function (obj) {
    let str = []
    for (let p in obj) {
      if (obj.hasOwnProperty(p)) {
        str.push(encodeURIComponent(p) + '=' + encodeURIComponent(obj[p]))
      }
    }

    return str.join('&')
  }

  this.search = function (query, page) {
    let params = {}

    params.query = query
    params['per-page'] = config.perPage
    params.page = page || 1

    return client.get('/?' + serialize(params))

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
    return client.post('/', serialize(body))
  }

  this.update = function (id, body) {
    return client.post('/' + id, serialize(body))
  }

  this.delete = function (id) {
    // return client.delete('/' + id)

    return client.post('/delete?id=' + id)
  }
}

export default Repository
