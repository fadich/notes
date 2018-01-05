let Repository = function (parameters) {
  let config = (function (params) {
    if (!arguments) {
      throw new Error('No parameters set')
    }

    return {
      host: params.host || null
    }
  })(parameters)

  let page = 1
  let pages = 0
  let loading = false

  // let client = 123;

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
    params.perPage = 10
    params.page = (page - 1) * params.size

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

    loading = false

    return entities
  }

  this.create = function (body, cb) {
    return this
  }

  this.update = function (id, body, cb) {
    return this
  }
}

export default Repository
