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
  this.update = function (id, body, cb) {
    client.update({
      index: this.index,
      type: this.type,
      id: id,
      body: body
    }, function (error, response) {
      cb(error, response)
    })
  }
}
