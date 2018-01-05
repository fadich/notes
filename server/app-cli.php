<?php

include 'Repository.php';

$rep = new Repository(2,2);

hint();

while (1) {
    $insert = readline('Query: ');
    switch ($insert) {
        case '\i':
            insert($rep);
            break;
        case '\h':
            hint();
            break;
        case '\e':
            writeln("Saving data...");
            $rep->save();

            exit();
        default:
            $list = $rep->search($insert);
            viewList($list);
    }
}

function writeln(string $message = '') {
    echo "{$message}\n";
}

function hint() {
    writeln("There are simple commands which can be used.");
    writeln("\i  -  Insert new note.");
    writeln("\\e -  Exit program.");
    writeln("Or just type a query...");
    writeln();
}

function insert(Repository $rep) {
    $insert = readline('Inset note ("title [|| content]):');
    $sep = explode('||', $insert);
    $rep->addNote(
        trim($sep[0]),
        trim($sep[1] ?? '')
    )->save();

    writeln("Note saved!");
}

function viewList(array $list) {
    $row = function (array $fields) {
        $id = $fields[0] ?? $fields['id'] ?? '--';
        $title = $fields[1] ?? $fields['title'] ?? '--';
        $content = $fields[2] ?? $fields['content'] ?? '--';
        $score = $fields[3] ?? $fields['_score'] ?? '--';

        writeln(styledString($id, 5) . styledString($title, 15) . styledString($content, 32) . styledString($score, 6));
    };

    $row([
        'ID',
        'Title',
        'Content',
        'Score',
    ]);

    foreach ($list as $item) {
        $row($item);
    }
}

function styledString(string $str, $len = 24) {
    $sLen = strlen($str);
    if ($sLen > 12) {
        $str = substr($str, 0, $len - 3);
        $str .= '...';
    } else {
        for ($i = $sLen; $i <= $len; $i++) {
            $str .= ' ';
        }
    }

    return $str;
}
