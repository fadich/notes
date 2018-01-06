import axios from 'axios'

let Repository = function (parameters) {
  let config = (function (params) {
    if (!arguments) {
      throw new Error('No parameters set')
    }

    return {
      host: params.host || null
    }
  })(parameters)

  let client = axios.create({
    baseURL: config.host,
    timeout: 2000,
    withCredentials: true,
    headers: {
      'Accept': 'application/json',
      'Content-Type': 'application/json',
      'X-Requested-With': 'XMLHttpRequest'
    }
  })

  this.search = function (query, page) {
    let params = {}

    console.log('Searching...')

    params.index = config.index
    params.page = (page - 1) * params.size
    params.perPage = 10

    client.get('/', params)
      .then(function (response) {
        console.log(response)
      })
      .catch(function (error) {
        console.log(error)
      })

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

    return {
      items: [],
      pages: 0
    }
  }

  this.create = function (body, cb) {
    console.log('Creating...')
    return this
  }

  this.update = function (id, body, cb) {
    console.log('Updating...')
    return this
  }
}

export default Repository
