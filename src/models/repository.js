import axios from 'axios'

let Repository = function (parameters) {
  let list = []

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
    timeout: 5000,
    withCredentials: true,
    headers: {
      'Accept': 'application/json',
      'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
      'X-Requested-With': 'XMLHttpRequest'
    }
  })

  client.get('/get')
    .then(function (res) {
      list = JSON.parse(res.data.data)
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
    return client.post('/save', serialize({data: JSON.stringify(data)}))
  }
}

export default Repository
