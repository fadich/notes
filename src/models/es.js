import elasticsearch from 'elasticsearch'
//
// client = new elasticsearch.Client({
//   host: 'localhost:9242',
//   log: 'trace'
// })

let ES = function (parameters) {
  let config = (function (params) {
    if (!arguments) {
      throw new Error('No parameters set')
    }

    return {
      host: config.host || null,
      index: config.index || null,
      type: config.type || null,
      log: config.log || null
    }
  })(parameters)

  let page = 1
  let pages = 0
  let loading = false

  let client = new elasticsearch.Client({
    host: config.host,
    log: config.log || 'trace'
  })

  this.resetPage = function () {
    page = 1
    return this
  }

  this.nextPage = function () {
    page++
    return this
  }

  this.getPages = function () {
    return pages
  }

  this.isLastPage = function () {
    return pages >= page
  }

  this.search = function (body) {
    if (loading) {
      return
    }

    let params = {}
    let entities = []

    loading = true

    params.index = config.index
    params.size = 10
    params.from = (page - 1) * params.size

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

    loading = false

    return entities
  }

  this.add = function (body, cb) {
    this.client.create({
      index: this.index,
      type: this.type,
      id: Date.now(),
      body: body
    }, function (error, response) {
      cb(error, response)
    })
  }

  this.update = function (id, body, cb) {
    client.update({
      index: config.index,
      type: config.type,
      id: id,
      body: body
    }, function (error, response) {
      cb(error, response)
    })

    return this
  }
}

export default ES
