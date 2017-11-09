<template>
    <div class="wrap">

        <div class="form-group">
            <input class="form-control" type="text" v-model="query" placeholder="Search query" @keyup="searchNotes">
        </div>

        <form @submit="addNote" @keydown="addNoteInput">
            <div class="field-wrap form-group">
                <input class="form-control" type="text" placeholder="Title" v-model="title">
            </div>
            <div class="comment">
                <div class="field-wrap">
                    <div class="textfield form-group">
                        <textarea class="form-control" placeholder="Note data" v-model="content"></textarea>
                    </div>
                </div>
            </div>
        </form>

        <div class="table">
            <div class="head">
                <div class="title">Title</div>
                <div class="comment">Comment</div>
            </div>
            <div class="body">
                <div class="body-row" v-for="note in notes">
                    <form @change="updateNote(note)">
                        <div class="title">
                            <div class="field-wrap form-group">
                                <!--<input v-model="note._source.title">-->

                                <textarea v-model="note._source.title" style="min-height: 150px"></textarea>
                            </div>
                        </div>
                        <div class="comment">
                            <div class="field-wrap">
                                <!--<textfield :value="note._source.content"></textfield>-->
                                <div class="textfield form-group">
                                    <textarea v-model="note._source.content"></textarea>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

import es from 'elasticsearch'
import Vue from 'vue'
import textHelper from '../helpers/textarea'

Vue.component('textfield', {
  template: `
    <div class="textfield">
      <a @click="toggleView" v-show="!editable">
        {{ value }}
      </a>
      <textarea
        :ref="'edit'"
        v-model="value"
        @mouseout="toggleView"
        v-show="editable">
      </textarea>
    </div>
  `,
  props: ['value'],
  data () {
    return {
      editable: false
    }
  },
  methods: {
    toggleView () {
      this.editable = !this.editable

      if (this.editable) {
        this.$refs.edit.focus()
      }
    }
  }
})

let list = {
  name: 'List',
  data () {
    return {
      title: '',
      content: '',
      query: '',
      page: 1,
      notes: [],
      client: null,
      index: 'notes',
      type: 'note'
    }
  },
  methods: {
    searchNotes () {
      let list = this
      let params = {}

      params.index = list.index
      params.size = 10
      params.from = (list.page - 1) * params.size

      params.body = {}
      if (list.query) {
        params.body.query = {
          bool: {
            must: {
              bool: {
                should: {
                  multi_match: {
                    query: list.query,
                    fuzziness: 'AUTO',
                    fields: ['title', 'content']
                  }
                }
              }
            }
          }
        }
      } else {
        params.body.sort = [{
          createdAt: { order: 'desc' }
        }]
      }

      list.client.search(params)
        .then(function (body) {
          list.notes = body.hits.hits
        }, function (error) {
          console.trace(error.message)
        })
    },
    addNote () {
      if (!this.title || !this.content) {
        return
      }

      let list = this
      let date = Date.now()
      let body = {
        title: this.title,
        content: this.content,
        createdAt: date,
        updatedAd: date
      }

      this.client.create({
        index: this.index,
        type: this.type,
        id: date,
        body: body
      }, function (error, response) {
        let code = error && error.hasOwnProperty('code') ? error.code : null

        if (code >= 300) {
          console.error(error, response)
          return false
        }

        list.title = ''
        list.content = ''
      })

      this.notes.unshift({
        _source: body
      })
      console.warn(this.notes)
      // Crutch. Waiting for insert...
      // Think that ES does not updating immediately
      setTimeout(function () {
        list.searchNotes()
      }, 1250)
    },
    addNoteInput (e) {
      if (e.keyCode === 13 && e.ctrlKey) {
        e.preventDefault()
        this.addNote()
      }
    },
    updateNote (note) {
      console.log(note)

      let body = {
        doc: {
          title: note._source.title,
          content: note._source.content,
          createdAt: note._source.date,
          updatedAd: note._source.date
        }
      }
      let id = note._id

      this.client.update({
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
  },
  mounted () {
    this.client = new es.Client({
      host: 'localhost:9242',
      log: 'trace'
    })

    this.searchNotes()

    setTimeout(function () {
      textHelper.autoresize()
    }, 500)
  }
}

export default list
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style lang="scss">
    .wrap {
        width: 100%;
        display: flex;
        flex-direction: column;

        .table {
            width: 100%;
            display: flex;
            flex-direction: column;
            border: 1px solid #d2d4d9;

            .head {
                width: 100%;
                display: flex;
                flex-direction: row;
                font-weight: bold;

                .title {
                    width: 15%;
                    min-width: 75px;
                    border-right: 1px solid #d2d4d9;
                    border-bottom: 1px solid #d2d4d9;

                    input {
                        overflow: hidden;
                    }
                }
                .comment {
                    width: 85%;
                    border-bottom: 1px solid #d2d4d9;

                    textarea {
                        border: none;
                    }
                }
            }

            .body {
                width: 100%;
                display: flex;
                flex-direction: column;

                .body-row {
                    width: 100%;
                    display: flex;
                    flex-direction: row;
                    border-bottom: 1px solid #d2d4d9;

                    &:last-child {
                        border: none;
                    }

                    form {
                        display: flex;
                        flex-direction: row;
                        width: 100%;
                        justify-content: center;

                        .title {
                            width: 15%;
                            min-width: 75px;
                            border-right: 1px solid #d2d4d9;
                        }
                        .comment {
                            width: 85%;
                        }
                    }
                }
            }
        }

        .field-wrap {
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;

            .textfield {
                width: 100%;
                display: flex;
                justify-content: center;
                align-items: center;
                text-align: left;

                textarea {
                    width: 100%;
                    height: 100%;
                    min-height: 150px;
                    max-width: 100%;
                    padding: 0;
                    margin: 0;

                    &:focus {
                        outline: none;
                    }
                }
            }

            input {
                width: 100%;

                &:focus {
                    outline: none;
                }
            }
        }
    }
</style>
