import axios from 'axios'

const IS_DEV = process.env.NODE_ENV === 'development'
const APP_URL = encodeURI(IS_DEV ? 'http://localhost:8080/#/' : 'https://notes.royallib.pw')
const BASE_URL = IS_DEV ? 'http://org.loc/' : 'https://www.auth.royallib.pw/'
const AUTH_URL = BASE_URL + 'auth/?land-to=' + APP_URL

let Repository = function (parameters) {
  let tempList = []
  let ready = false
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

  let _list = localStorage.getItem('_list')

  _list = JSON.parse(_list)

  if (_list && Array.isArray(_list)) {
    list = _list
    ready = true
  }

  client.get('/storage')
    .then(function (res) {
      list = JSON.parse(res.data.data)
      localStorage.setItem('_list', res.data.data)
      ready = true

      IS_DEV && console.log(list)
    })
    .catch(function (res) {
      if (res.message === 'Request failed with status code 403') {
        window.location.replace(AUTH_URL)
      }
    })

  this.search = function (query, page) {
    let res = {}
    let start = (page - 1) * config.perPage

    query = query.toLowerCase()

    if (page === 1) {
      tempList = list.slice(0, list.length)

      if (query.length) {
        let grams = this.getGrams(query, query.length - 2, query.length)
        IS_DEV && console.log(grams)

        tempList = []

        for (let i in list) {
          let item = list[i]
          let score = 0.0
          let maxGram = 0

          for (let j in grams) {
            let gram = grams[j]
            let countT = item.title.toLowerCase().split(gram).length - 1
            let countC = item.content.toLowerCase().split(gram).length - 1

            if (maxGram < gram.length) {
              maxGram = gram.length
            }
            score += gram.length * (countT + countC)
          }

          if (score) {
            item['_score'] = score - Math.abs(maxGram - (item.title.length + item.content.length)) * 0.01
            tempList.push(item)
          }
        }

        for (let i = 0; i < tempList.length - 1; i++) {
          let k = i + 1
          while (k) {
            if (tempList[k]['_score'] > tempList[k - 1]['_score']) {
              let temp = tempList[k - 1]
              tempList[k - 1] = tempList[k]
              tempList[k] = temp
              k--
              continue
            }
            if (tempList[k]['_score'] === tempList[k - 1]['_score']) {
              let len1 = tempList[k].title.length + tempList[k].content.length
              let len2 = tempList[k - 1].title.length + tempList[k - 1].content.length

              if (len1 < len2) {
                let temp = tempList[k - 1]
                tempList[k - 1] = tempList[k]
                tempList[k] = temp
                k--
                continue
              }
            }
            break
          }
        }
      }
    }

    res.items = tempList.slice(start, config.perPage + start)
    res.pages = Math.ceil(tempList.length / config.perPage)

    return res
  }

  this.create = function (body) {
    body['_id'] = Date.now()
    list.unshift(body)

    IS_DEV && console.log(list)

    this.save(list)
  }

  this.update = function (index, body) {
    list[index]['title'] = body.title
    list[index]['content'] = body.content

    IS_DEV && console.log(list)

    this.save(list)
  }

  this.delete = function (index) {
    list.splice(index, 1)

    IS_DEV && console.log(list)

    this.save(list)
  }

  this.save = function () {
    let _list = JSON.stringify(list)

    localStorage.setItem('_list', _list)

    return client.post('/storage', {
      data: _list
    })
  }

  this.isReady = function () {
    return ready
  }

  this.getGrams = function (word, min, max) {
    let len = word.length
    let grams = []

    min = min || 2
    max = max || min
    min = (min < 1) ? 1 : min
    max = (max < 1) ? 1 : min

    min = Math.min(min, max)
    max = Math.max(min, max)

    min = (min > len) ? len : min
    max = (max > len) ? len : max

    for (let size = min; size <= max; size++) {
      for (let cur = 0; cur <= len - size; cur++) {
        grams.push(word.slice(cur, cur + size))
      }
    }

    return grams.filter(function (value, index, self) {
      return self.indexOf(value) === index
    })
  }
}

export default Repository
