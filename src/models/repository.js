let Repository = function (parameters) {
  let config = (function (params) {
    if (!arguments) {
      throw new Error('No parameters set')
    }

    return {
      host: params.host || null
    }
  })(parameters)

  // let client = 123;

  this.search = function (query, page) {
    let params = {}

    console.log('Searching...')

    params.index = config.index
    params.page = (page - 1) * params.size
    params.perPage = 10

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
    console.log('Created')
    return this
  }

  this.update = function (id, body, cb) {
    console.log('Updated')
    return this
  }
}

export default Repository
