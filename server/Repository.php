<?php

class Repository {

    const STORAGE = 'storage.json';

    protected $minN = 3;
    protected $maxN = 5;
    protected $list = [];

    public function __construct(int $minN = 3, int $maxN = 5)
    {
        $this->list = json_decode(
            file_get_contents(static::STORAGE),
            true
        );

        if ($this->list === null) {
            throw new Exception("Error parsing json");
        }

        $this->minN = $minN;
        $this->maxN = $maxN;
    }

    public function __destruct()
    {
        $this->save();
    }

    /**
     * @return static
     */
    public function save()
    {
        file_put_contents(
            static::STORAGE,
            json_encode($this->list)
        );

        return $this;
    }

    /**
     * @return array
     */
    public function getList()
    {
        return $this->list;
    }

    /**
     * @param string $title
     * @param string $content
     *
     * @return static
     */
    public function addNote(string $title, string $content)
    {
        $id = (int)floor(microtime(true) * 1000);
        $this->list = [
            $id => [
                '_id'     => $id,
                'title'   => $title,
                'content' => $content,
            ],
        ] + $this->list;

        return $this;
    }

    /**
     * @param int $id
     * @param string $title
     * @param string $content
     *
     * @return static
     * @throws \Exception
     */
    public function updateNote(int $id, string $title, string $content)
    {
        if (!isset($this->list[$id])) {
            throw new Exception("Note #{$id} does not exists");
        }

        $this->list[$id]['title'] = $title;
        $this->list[$id]['content'] = $content;

        return $this;
    }

    /**
     * @param int $id
     *
     * @return static
     */
    public function deleteNote(int $id)
    {
        unset($this->list[$id]);

        return $this;
    }

    public function search(string $query = '', int $page = 1, int $perPage = 9)
    {
        $offset = $perPage * ($page - 1);

        if (!$query) {
            $list = array_slice($this->list, $offset, $perPage);

            return [
                'items' => $list,
                'pages' => ceil(count($this->list) / $perPage),
            ];
        }

        $grams = $this->getGrams($query);
        $list = [];

        foreach ($this->list as $item) {
            $score = 0.0;
            foreach ($grams as $gram) {
                $len = strlen($gram);
                $score += (substr_count(strtolower($item['title']), $gram) * $len);
                $score += (substr_count(strtolower($item['content']), $gram) * $len);
            }

            if ($score) {
                $item['_score'] = (float)$score;
                $list[] = $item;
            }
        }

        $num = count($list);
        for ($i = 0; $i < $num - 1; $i++) {
            $k = $i + 1;
            while ($k) {
                if ($list[$k]['_score'] > $list[$k - 1]['_score']) {
                    $temp = $list[$k - 1];
                    $list[$k - 1] = $list[$k];
                    $list[$k] = $temp;
                    $k--;
                    continue;
                }
                break;
            }
        }

        return [
            'items' => array_slice($list, $offset, $perPage),
            'pages' => ceil(count($list) / $perPage),
        ];
    }

    /**
     * @param string $word
     *
     * @return string[]
     */
    protected function getGrams(string $word)
    {
        $min = min($this->minN, $this->maxN);
        $max = max($this->minN, $this->maxN);
        $len = strlen($word);
        $grams = [];

        $min = ($min > $len) ? $len : $min;
        $max = ($max > $len) ? $len : $max;

        for ($cur = $min; $cur <= $max; $cur++) {
            foreach (range(0, $len - $cur) as $start) {
                $grams[] = substr($word, $start, $cur);
            }
        }

        return array_unique($grams);
    }

}
