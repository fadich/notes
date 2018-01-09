<?php

use \Psr\Http\Message\ServerRequestInterface;

function ping () {
    $args = func_get_args();
    /** @var \RequestHandler $handler */
    $handler = end($args);

    return RequestHandler::response(['OK'], 200, $handler->headers);
}

function search(ServerRequestInterface $request, Repository $repository, RequestHandler $handler) {
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
    ], 200, $handler->headers);
}

function insert(ServerRequestInterface $request, Repository $repository, RequestHandler $handler) {
    $body = $request->getParsedBody();
    if (!isset($body['title']) || !$body['title']) {
        return RequestHandler::response([
            'success' => false,
            'message' => 'Field "title" required',
        ], 400, $handler->headers);
    }

    $repository->addNote($body['title'], $body['content'] ?? '')->save();
    $body['id'] = $repository->getLastInsertId();

    return RequestHandler::response([
        'success' => true,
        'message' => 'Successfully created',
        'item' => $body,
    ], 200, $handler->headers);
}

function update(ServerRequestInterface $request, int $id, Repository $repository, RequestHandler $handler) {
    $body = $request->getParsedBody();
    if (!isset($body['title']) || !$body['title']) {
        return RequestHandler::response([
            'success' => false,
            'message' => 'Field "title" required',
        ], 400, $handler->headers);
    }

    try {
        $repository->updateNote($id, $body['title'], $body['content'] ?? '')->save();
    } catch (Exception $e) { // CATCH
        return RequestHandler::response([
            'success' => false,
            'message' => $e->getMessage(),
        ], 400, $handler->headers);
    }

    return RequestHandler::response([
        'success' => true,
        'message' => 'Successfully updated',
        'item' => $body,
    ], 200, $handler->headers);
}

function delete(ServerRequestInterface $request, int $id, Repository $repository, RequestHandler $handler) {
    return RequestHandler::response(['Delete.', func_get_args()], 200, $handler->headers);
}
