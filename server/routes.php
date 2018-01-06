<?php

use \Psr\Http\Message\ServerRequestInterface;

function search(ServerRequestInterface $request, Repository $repository) {
    $params = $request->getQueryParams();
    $query = $params['query'] ?? '';

    $page = (int)($params['page'] ?? 1);
    $page = ($page < 1) ? 1 : $page;

    $perPage = (int)($params['per-page'] ?? 9);
    $perPage = ($perPage < 1) ? 1 : $perPage;
    $perPage = ($perPage > 100) ? 100 : $perPage;

    $items = $repository->search($query, $page, $perPage);

    return RequestHandler::response([
        $items,
    ]);
}

function insert(ServerRequestInterface $request, Repository $repository) {
    $body = $request->getParsedBody();
    if (!isset($body['title']) || !$body['title']) {
        return RequestHandler::response([
            'success' => false,
            'message' => 'Field "title" required',
        ], 400);
    }

    $repository->addNote($body['title'], $body['content'] ?? '')->save();
    $body['id'] = $repository->getLastInsertId();

    return RequestHandler::response([
        'success' => true,
        'message' => 'Successfully created',
        'item' => $body,
    ]);
}

function update(ServerRequestInterface $request, int $id, Repository $repository) {
    return RequestHandler::response(['Update.', func_get_args()]);
}

function delete(ServerRequestInterface $request, int $id, Repository $repository) {
    return RequestHandler::response(['Delete.', func_get_args()]);
}
