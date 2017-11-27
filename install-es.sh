curl -XDELETE localhost:9242/notes?pretty

curl -XPUT localhost:9242/notes?pretty

curl -XPOST localhost:9242/notes/_close?pretty

curl -XPUT localhost:9242/notes/_settings?pretty -d '{
  "settings": {
    "analysis": {
    "filter": {
        "autocomplete_filter": {
          "type": "ngram",
          "min_gram": "3",
          "max_gram": "10"
        },
        "word_joiner": {
          "type": "word_delimiter",
          "catenate_all": true
        }
      },
      "analyzer": {
        "my_analyzer": {
          "type": "custom",
          "tokenizer": "standard",
          "filter": [
            "word_joiner",
            "lowercase",
            "porter_stem"
          ]
        }
      }
    }
  }
}'

curl -XPOST localhost:9242/notes/_open?pretty

elasticdump \
  --input=./es-backup/notes-mapping.json \
  --output=http://localhost:9242/notes \
  --type=mapping

#
# Dump mapping to file
#
#elasticdump \
#  --input=http://localhost:9242/notes \
#  --output=./es-backup/notes-index-$(date +'%Y%m%d%H%M%S')-mapping.json \
#  --type=mapping
#elasticdump \
#  --input=http://localhost:9242/notes \
#  --output=./es-backup/notes-index-$(date +'%Y%m%d%H%M%S')-data.json \
#  --type=data

# Import

#elasticdump \
#  --input=./es-backup/notes-index-$DATE-data.json \
#  --output=http://localhost:9242/notes \
#  --type=data
