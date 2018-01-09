<template>
    <div class="wrap">

        <div class="form-group">
            <input class="form-control"
                   type="search"
                   placeholder="Search query..."
                   v-model="query"
                   @keyup="searchNotes(true)">
        </div>

        <form @submit="addNote" @keydown="addNoteInput">
            <div class="field-wrap form-group">
                <input class="form-control" type="text" placeholder="Title" v-model="title">
            </div>
            <div class="comment">
                <div class="field-wrap">
                    <div class="textfield form-group">
                        <textarea class="form-control input-padding" placeholder="Note data" v-model="content"></textarea>
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
                    <form @change="updateNote(note)" @keydown="noteForm(note, $event)">
                        <div class="title">
                            <div class="field-wrap form-group">
                                <textarea v-model="note.title" style="min-height: 150px"></textarea>
                            </div>
                        </div>
                        <div class="comment">
                            <div class="field-wrap">
                                <div class="textfield form-group">
                                    <textarea v-model="note.content"></textarea>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <a @click="loadMore()" @mouseover="loadMore()" class="load-more" v-if="page < pages">Load more</a>

        <hr>
    </div>
</template>

<script>

import '../helpers/textarea'
import Vue from 'vue'
import Repository from '../models/repository'

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

const HOST = 'http://localhost:8010/'

let list = {
  name: 'List',
  data () {
    return {
      title: '',
      content: '',
      query: '',
      page: 1,
      loading: false,
      pages: 1,
      notes: [],
      repository: null
    }
  },
  methods: {
    searchNotes (reset) {
      if (reset) {
        this.notes = []
        this.page = 1
      }

      let t = function (response) {
        let data = response.data[0]

        if (data) {
          this.notes = Array.concat(this.notes, data.items)
          this.pages = data.pages
        }
      }

      this.repository.search(this.query, this.page)
        .then(t.bind(this))
        .catch(function (error) {
          console.log(error)
        })
    },
    addNote (e) {
      if (e) {
        e.preventDefault()
      }

      if (!this.title || !this.content) {
        return
      }

      let body = {
        title: this.title,
        content: this.content
      }

      this.repository.create(body)

      this.title = ''
      this.content = ''

      this.notes.unshift(body)

      this.page = 1
    },
    addNoteInput (e) {
      if (e.keyCode === 13 && e.ctrlKey) {
        e.preventDefault()
        this.addNote()
      }
    },
    updateNote (note) {
      let body = {
        title: note.title,
        content: note.content,
        createdAt: note.date,
        updatedAd: note.date
      }

      let id = note.id

      this.repository.update(id, body)
    },
    noteForm (note, ev) {
      if (ev.code === 'Delete' && (ev.shiftKey || ev.ctrlKey)) {
        this.deleteNote(note)
      }
      if (ev.code === 'Enter' && ev.ctrlKey) {
        this.updateNote(note)
      }
    },
    deleteNote (note) {
      if (confirm('Are you sure?')) {
        this.repository.delete(note.id)
      }
    },
    loadMore () {
      this.loading = true
      this.page++
      this.searchNotes()
    }
  },
  mounted () {
    this.repository = new Repository({
      host: HOST
    })

    this.searchNotes()
    this.page++
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

        textarea {
            overflow: hidden;
        }

        .table {
            width: 100%;
            display: flex;
            flex-direction: column;
            border: 1px solid #d2d4d9;
            border-radius: 5px 5px 0 0;
            margin: 0;

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

                        textarea {
                            border: 0;
                            overflow: hidden;
                            resize: none;
                            line-height: 21px;

                            &:focus {
                                outline: none;
                            }
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
                }
            }

            input, textarea {
                width: 100%;

                &:focus {
                    outline: none;
                    box-shadow: none;
                }
            }
        }

        .load-more {
            cursor: pointer;
            height: 100px;
            padding: 40px;
        }

        .input-padding {
            padding: .375rem .75rem !important;
        }

        input[type="search"] {
            border: none;
            border-bottom: 1px solid #CCC;
            border-radius: 0;

            &:focus {
                outline: none;
                box-shadow: none;
                transition: 0.5s;
                border-bottom: 1px solid #55F;

                &::placeholder {
                    color: #DDD;
                    transition: 0.5s;
                }
            }

            &::placeholder {
                color: #AAA;
                transition: 0.3s;
            }
        }
    }
</style>
