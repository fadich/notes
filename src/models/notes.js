import elasticsearch from 'elasticsearch'

client = new elasticsearch.Client({
  host: 'localhost:9242',
  log: 'trace'
})

let es = function (host, log) {
  let client = new elasticsearch.Client({
    host: host,
    log: log || 'trace'
  })

  this.search = function () {
    return client.search(params)
  }
  this.update = function (id, body) {
    client.update({
      index: this.index,
      type: this.type,
      id: id,
      body: body
    }, function (error, response) {
      let code = error && error.hasOwnProperty('code') ? error.code : null

      if (code >= 300) {
        console.error(error, response)
        return false
      }
    })
  }
}
