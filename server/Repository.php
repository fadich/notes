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
       $this->list[] = [
            'title' => $title,
            'content' => $content,
        ];

        return $this;
    }

    /**
     * @param int $id
     * @param string $title
     * @param string $content
     *
     * @return static
     */
    public function updateNote(int $id, string $title, string $content)
    {
        $this->list[$id] = [
            'title' => $title,
            'content' => $content,
        ];

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
            foreach ($list as $id => $item) {
                $list[$id] = array_merge($item, ['id' => $id]);
            }

            return $list;
        }

        $grams = $this->getGrams($query);
        $list = [];

        foreach ($this->list as $id => $item) {
            $score = 0.0;
            foreach ($grams as $gram) {
                $len = strlen($gram);
                $score += (float)(substr_count($item['title'], $gram) * $len);
                $score += (float)(substr_count($item['content'], $gram) * $len);
            }

            if ($score) {
                $list[] = [
                    'id' => $id,
                    'title' => $item['title'],
                    'content' => $item['content'],
                    '_score' => $score,
                ];
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

        return array_slice($list, $offset, $perPage);
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
