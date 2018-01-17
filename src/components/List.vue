<template>
    <div class="wrap">

        <div class="form-group">
            <input class="form-control"
                   type="search"
                   placeholder="Search query..."
                   v-model="query"
                   @keyup="searchNotes(true)">
        </div>

        <form class="note-form" @submit="addNote" @keydown="addNoteInput">
            <div class="field-wrap form-group field-title hidden">
                <textarea class="form-control input-padding"
                          placeholder="Note title"
                          v-model="title"
                          @focus="eb.showForm($event)"
                          @focusout="eb.hideForm($event)"
                          rows="1">
                </textarea>
            </div>
            <div class="field-wrap">
                <div class="textfield form-group">
                    <textarea class="form-control input-padding"
                              placeholder="Note content"
                              v-model="content"
                              @focus="eb.showForm($event)"
                              @focusout="eb.hideForm($event)">
                    </textarea>
                </div>
            </div>
        </form>

        <hr>

        <div class="table">
            <div class="body">
                <div class="body-row" v-for="(note, index) in notes">
                    <form class="note-form" @change="updateNote(index, note)" @keydown="noteForm(index, note, $event)">
                        <div class="field-wrap form-group field-title" :class="note.title ? '' : 'hidden'">
                            <textarea class="form-control input-padding"
                                      placeholder="Note title"
                                      v-model="note.title"
                                      @focus="eb.showForm($event)"
                                      @focusout="eb.hideForm($event)"
                                      rows="1">
                            </textarea>
                        </div>
                        <div class="field-wrap visible">
                            <div class="textfield form-group">
                                <textarea class="form-control input-padding"
                                          placeholder="Note content"
                                          v-model="note.content"
                                          @focus="eb.showForm($event)"
                                          @focusout="eb.hideForm($event)">
                                </textarea>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <a @click="loadMore()" class="load-more" v-if="page < pages">Load more</a>

        <br>
    </div>
</template>

<script>

import eb from '../helpers/elementBehaviours'
import Repository from '../models/repository'

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
      repository: null,
      eb: eb
    }
  },
  methods: {
    searchNotes (reset) {
      if (reset) {
        this.notes = []
        this.page = 1
      }

      let data = this.repository.search(this.query, this.page)

      if (data) {
        this.notes = Array.concat(this.notes, data.items)
        this.pages = data.pages
      }
    },
    addNote (e) {
      if (e) {
        e.preventDefault()
      }

      if (!(this.title || this.content)) {
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
    },
    addNoteInput (e) {
      if (e.keyCode === 13 && e.ctrlKey) {
        e.preventDefault()
        this.addNote()
      }
    },
    updateNote (index, note) {
      let body = {
        title: note.title,
        content: note.content
      }

      this.repository.update(index, body)
    },
    noteForm (index, note, ev) {
      if (ev.code === 'Delete' && (ev.shiftKey || ev.ctrlKey)) {
        this.deleteNote(index)
      }
      if (ev.code === 'Enter' && ev.ctrlKey) {
        this.updateNote(index, note)
      }
    },
    deleteNote (index) {
      if (confirm('Are you sure?')) {
        this.repository.delete(index)

        this.notes.splice(index, 1)
      }
    },
    loadMore () {
      this.loading = true
      this.page++
      this.searchNotes()
    }
  },
  mounted () {
    this.repository = new Repository()

    let searchInt = setInterval(function () {
      if (this.repository.isReady()) {
        this.searchNotes()
        clearInterval(searchInt)
      }
    }.bind(this), 100)
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

        .note-form {
            display: flex;
            flex-direction: column;
            width: 100%;
            justify-content: center;

            textarea {
                /*border: 0;*/
                overflow: hidden;
                resize: none;
                line-height: 21px;
                border-radius: 0;

                &:focus {
                    outline: none;
                    transition: 0.5s;
                    box-shadow: none;
                    border: solid 1px #0a3d82 !important;
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
                        min-height: 70px;
                        max-width: 100%;
                        padding: 0;
                        margin: 0;
                    }
                }
            }

            .field-title {
                textarea {
                    border-bottom: solid 1px rgba(0, 0, 0, 0);
                }
            }

            .hidden {
                display: none;
            }

            &.collapse {
                transition: 0.5s;
                .hidden {
                    display: block !important;
                }
            }
        }

        .table {
            width: 100%;
            display: flex;
            flex-direction: column;
            border: 0;
            margin: 0;

            .body {
                width: 100%;
                display: flex;
                flex-direction: column;

                .body-row {
                    width: 100%;
                    display: flex;
                    flex-direction: row;
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
            border-bottom: 1px solid #ced4da;
            border-radius: 0;

            &:focus {
                outline: none;
                box-shadow: none;
                transition: 0.5s;
                border-bottom: 1px solid #0a3d82;

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

        hr {
            width: 100%;
            height: 10px;
        }
    }
</style>
